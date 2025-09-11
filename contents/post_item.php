<?php
// contents/post_item.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'db.php';

$userId = $_SESSION['user_id'] ?? null;
$message = '';

// redirect if not logged in
if (!$userId) {
    header("Location: index.php?page=login");
    exit;
}

// handle new item submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_item'])) {
    $type = $_POST['type'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $phone = preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? ''); // phone only

    $imagePath = '';

    // uploaded file
    if (!empty($_FILES['image']['name'])) {
        $dir = __DIR__ . '/../uploads/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $imgName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES['image']['name']));
        $target = $dir . $imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imagePath = 'uploads/' . $imgName;
        }
    }
    // else base64 captured image
    elseif (!empty($_POST['captured_image'])) {
        $data = $_POST['captured_image'];
        if (preg_match('/^data:image\/\w+;base64,/', $data)) {
            $data = preg_replace('#^data:image/\w+;base64,#i', '', $data);
            $data = base64_decode($data);
            $dir = __DIR__ . '/../uploads/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $imgName = time() . '_captured.png';
            $filePath = $dir . $imgName;
            file_put_contents($filePath, $data);
            $imagePath = 'uploads/' . $imgName;
        }
    }

    $stmt = $db->prepare("INSERT INTO items 
        (user_id, type, title, description, category, location, phone, image_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $type, $title, $desc, $category, $location, $phone, $imagePath]);

    $message = "Item posted successfully!";
}

// deletion
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];

    // first get image path
    $stmt = $db->prepare("SELECT image_url FROM items WHERE id=? AND user_id=?");
    $stmt->execute([$id, $userId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // delete image from folder
        if (!empty($item['image_url'])) {
            $filePath = __DIR__ . '/../' . $item['image_url'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        // delete from database
        $stmt = $db->prepare("DELETE FROM items WHERE id=? AND user_id=?");
        $stmt->execute([$id, $userId]);
        $message = "Item and its photo deleted successfully!";
    }
}

// fetch items
$stmt = $db->prepare("SELECT * FROM items WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="assets/post_item.css">

<div class="post-item-container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>!</h2>
    <p class="welcome-note">Post found items — upload a picture and we'll try to read the ID/number/name for you.</p>

    <?php if ($message): ?>
        <p class="success-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="post-item-form" id="postForm">
        <input type="hidden" name="submit_item" value="1">

        <label>Upload Photo</label>
        <input type="file" name="image" accept="image/*">

        <label>Or Take a Picture</label>
        <input type="hidden" name="captured_image" id="capturedImageInput">
        <button type="button" onclick="openCamera()">Open Camera</button>
        <video id="cameraStream" autoplay style="display:none;"></video>
        <canvas id="cameraCanvas" style="display:none;"></canvas>
        <button type="button" id="captureBtn" style="display:none;" onclick="capturePhoto()">Capture</button>

        <div id="preview"></div>

       
        <input type="hidden" name="type" value="found">

        <label>Identification (auto-filled if found)</label>
        <input type="text" name="title" id="titleInput" placeholder="NIN / License No / Number plate">

        <label>Name (auto-filled if found)</label>
        <input type="text" name="description" id="descInput" placeholder="Full name">

        <label>Category</label>
        <input type="text" name="category" placeholder="e.g., IDs, Driving Licence, Number Plate">

        <label>Location</label>
        <input type="text" name="location" placeholder="Where it was lost/found">

        <!-- ✅ Phone contact -->
        <label>Phone Contact</label>
        <input type="tel" name="phone" placeholder="+256700000000" pattern="[\+0-9]{7,20}">

        <button type="submit" class="btn-submit">Submit Item</button>
    </form>

    <h3 class="section-title">Your Found Items</h3>
    <div class="user-items">
        <?php if ($items): foreach ($items as $item): ?>
            <div class="item-card">
                <?php if (!empty($item['image_url'])): ?>
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="item image">
                <?php endif; ?>
                <h4><?= htmlspecialchars($item['title']) ?> <span class="muted">(<?= htmlspecialchars(ucfirst($item['type'])) ?>)</span></h4>
                <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?> | 
                   <strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
                <?php if (!empty($item['phone'])): ?>
                    <p><strong>Phone:</strong> <a href="tel:<?= htmlspecialchars($item['phone']) ?>"><?= htmlspecialchars($item['phone']) ?></a></p>
                <?php endif; ?>
                <div class="item-actions">
                    <a class="edit-btn" href="index.php?page=edit_item&id=<?= $item['id'] ?>">Edit</a>
                    <a class="delete-btn" href="index.php?page=post_item&delete_id=<?= $item['id'] ?>" onclick="return confirm('Delete this item and its photo?')">Delete</a>
                </div>
            </div>
        <?php endforeach; else: ?>
            <p class="no-items">You haven't posted any items yet.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function openCamera() {
    const video = document.getElementById('cameraStream');
    const captureBtn = document.getElementById('captureBtn');
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
            video.style.display = 'block';
            captureBtn.style.display = 'block';
        })
        .catch(err => {
            alert("Camera access denied: " + err);
        });
}

function capturePhoto() {
    const video = document.getElementById('cameraStream');
    const canvas = document.getElementById('cameraCanvas');
    const input = document.getElementById('capturedImageInput');
    const preview = document.getElementById('preview');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const dataURL = canvas.toDataURL('image/png');
    input.value = dataURL;

    preview.innerHTML = '<img src="' + dataURL + '" style="max-width:200px; margin-top:10px;">';

    // stop camera
    const stream = video.srcObject;
    const tracks = stream.getTracks();
    tracks.forEach(track => track.stop());
    video.style.display = 'none';
    document.getElementById('captureBtn').style.display = 'none';
}
</script>
