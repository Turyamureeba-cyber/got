<?php
$id = $_GET['id'] ?? 0;
$stmt = $db->prepare("SELECT * FROM items WHERE id=?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { echo "<p>Item not found!</p>"; return; }
?>

<h2><?= htmlspecialchars($item['title']) ?></h2>
<?php if($item['image_url']): ?>
  <img src="<?= $item['image_url'] ?>" alt="Item" style="max-width:300px;">
<?php endif; ?>
<p><strong>Type:</strong> <?= $item['type'] ?></p>
<p><strong>Description:</strong> <?= $item['description'] ?></p>
<p><strong>Category:</strong> <?= $item['category'] ?></p>
<p><strong>Location:</strong> <?= $item['location'] ?></p>
<p><small>Posted on <?= $item['created_at'] ?></small></p>
