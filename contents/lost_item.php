<?php
// contents/lost_item.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db.php';

$userId = $_SESSION['user_id'] ?? null;
$message = '';

// Redirect if not logged in
if (!$userId) {
    header("Location: index.php?page=login");
    exit;
}

// Handle new lost item submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_item'])) {
    $type = 'lost'; // fixed type
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $phone = preg_replace('/[^0-9+]/', '', $_POST['phone'] ?? ''); // phone only
    $imagePath = '';

    // Handle uploaded file
    if (!empty($_FILES['image']['name'])) {
        $dir = __DIR__ . '/../uploads/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $imgName = time() . '_' . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES['image']['name']));
        $target = $dir . $imgName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imagePath = 'uploads/' . $imgName;
        }
    }

    // Save to database
    $stmt = $db->prepare("INSERT INTO items 
        (user_id, type, title, description, category, location, phone, image_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $type, $title, $desc, $category, $location, $phone, $imagePath]);

    $message = "Lost item posted successfully!";
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];

    // Get image path
    $stmt = $db->prepare("SELECT image_url FROM items WHERE id=? AND user_id=?");
    $stmt->execute([$id, $userId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($item) {
        // Delete image from folder
        if (!empty($item['image_url'])) {
            $filePath = __DIR__ . '/../' . $item['image_url'];
            if (file_exists($filePath)) unlink($filePath);
        }
        // Delete from DB
        $stmt = $db->prepare("DELETE FROM items WHERE id=? AND user_id=?");
        $stmt->execute([$id, $userId]);
        $message = "Lost item and its photo deleted successfully!";
    }
}

// Fetch user's lost items
$stmt = $db->prepare("SELECT * FROM items WHERE user_id=? AND type='lost' ORDER BY id DESC");
$stmt->execute([$userId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="assets/post_item.css">

<div class="post-item-container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>!</h2>
    <p class="welcome-note">Post you lost items — upload a photo and provide details so we can help you find them.</p>

    <?php if ($message): ?>
        <p class="success-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="post-item-form">
        <input type="hidden" name="submit_item" value="1">
        <input type="hidden" name="type" value="lost">

        <label>Upload Photo</label>
        <input type="file" name="image" accept="image/*">

        <label>Identification</label>
        <input type="text" name="title" placeholder="NIN / License No / Number plate" required>

        <label>Name</label>
        <input type="text" name="description" placeholder="Full name" required>

        <label>Category</label>
        <input type="text" name="category" placeholder="e.g., IDs, Driving Licence, Number Plate" required>

        <label>Location</label>
        <input type="text" name="location" placeholder="Where it was lost" required>

        <label>Phone Contact</label>
        <input type="tel" name="phone" placeholder="+256700000000" pattern="[\+0-9]{7,20}" required>

        <button type="submit" class="btn-submit">Submit Lost Item</button>
    </form>

    <h3 class="section-title">Your Lost Items</h3>
    <div class="user-items">
        <?php if ($items): foreach ($items as $item): ?>
            <div class="item-card">
                <?php if (!empty($item['image_url'])): ?>
                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Lost item image">
                <?php endif; ?>
                <h4><?= htmlspecialchars($item['title']) ?></h4>
                <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?> |
                   <strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
                <?php if (!empty($item['phone'])): ?>
                    <p><strong>Phone:</strong> <a href="tel:<?= htmlspecialchars($item['phone']) ?>"><?= htmlspecialchars($item['phone']) ?></a></p>
                <?php endif; ?>
                <div class="item-actions">
                    <a class="edit-btn" href="index.php?page=edit_item&id=<?= $item['id'] ?>">Edit</a>
                    <a class="delete-btn" href="index.php?page=lost_item&delete_id=<?= $item['id'] ?>" 
                       onclick="return confirm('Delete this lost item and its photo?')">Delete</a>
                </div>
            </div>
        <?php endforeach; else: ?>
            <p class="no-items">You haven't posted any lost items yet.</p>
        <?php endif; ?>
    </div>
</div>
