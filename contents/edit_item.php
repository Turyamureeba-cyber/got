<?php

$userId = $_SESSION['user_id'];
$message = '';

// Get item ID from URL
if (!isset($_GET['id'])) {
    header("Location: index.php?page=post_item");
    exit;
}

$itemId = $_GET['id'];

// Fetch item to edit
$stmt = $db->prepare("SELECT * FROM items WHERE id=? AND user_id=?");
$stmt->execute([$itemId, $userId]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header("Location: index.php?page=post_item");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $category = $_POST['category'];
    $location = $_POST['location'];

    // Upload new image if provided
    $imagePath = $item['image_url'];
    if (!empty($_FILES['image']['name'])) {
        $dir = "uploads/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $imgName = time().'_'.basename($_FILES['image']['name']);
        $target = $dir.$imgName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $imagePath = $target;
        }
    }

    // Update item in DB
    $stmt = $db->prepare("UPDATE items SET type=?, title=?, description=?, category=?, location=?, image_url=? WHERE id=? AND user_id=?");
    $stmt->execute([$type, $title, $desc, $category, $location, $imagePath, $itemId, $userId]);

    $message = "Item updated successfully!";
    // Refresh data
    $stmt = $db->prepare("SELECT * FROM items WHERE id=? AND user_id=?");
    $stmt->execute([$itemId, $userId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="edit-item-container">
    <h2>Edit Item: <?= htmlspecialchars($item['title']) ?></h2>

    <?php if ($message): ?>
        <p class="success-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="post-item-form">
        <label>Type:</label>
        <select name="type" required>
            <option value="lost" <?= $item['type'] === 'lost' ? 'selected' : '' ?>>Lost</option>
            <option value="found" <?= $item['type'] === 'found' ? 'selected' : '' ?>>Found</option>
        </select>

        <label>Identification:</label>
        <input type="text" name="title" required value="<?= htmlspecialchars($item['title']) ?>">

        <label>Description:</label>
        <textarea name="description"><?= htmlspecialchars($item['description']) ?></textarea>

        <label>Category:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($item['category']) ?>">

        <label>Location:</label>
        <input type="text" name="location" value="<?= htmlspecialchars($item['location']) ?>">

        <?php if ($item['image_url']): ?>
            <label>Current Image:</label>
            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Item Image" style="width:100%; max-width:300px; border-radius:10px; margin-bottom:10px;">
        <?php endif; ?>

        <label>Change Image (optional):</label>
        <input type="file" name="image">

        <button type="submit">Update Item</button>
        <a href="index.php?page=post_item" class="cancel-btn">Go Back</a>
    </form>
</div>
