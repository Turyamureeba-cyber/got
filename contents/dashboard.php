<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<p>Access denied. Admins only.</p>";
    return;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM items WHERE id=?");
    $stmt->execute([$id]);
    echo "<p>Item deleted!</p>";
}

if (isset($_GET['recover'])) {
    $id = (int)$_GET['recover'];
    $stmt = $db->prepare("UPDATE items SET status='recovered' WHERE id=?");
    $stmt->execute([$id]);
    echo "<p>Item marked as recovered!</p>";
}

$stmt = $db->query("SELECT * FROM items ORDER BY created_at DESC");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Admin Dashboard</h2>
<p>Welcome, <?= $_SESSION['user_name'] ?>!</p>
<table border="1" cellpadding="5" cellspacing="0">
<tr>
  <th>ID</th><th>Title</th><th>Type</th><th>Status</th><th>Actions</th>
</tr>
<?php foreach($items as $item): ?>
<tr>
  <td><?= $item['id'] ?></td>
  <td><?= htmlspecialchars($item['title']) ?></td>
  <td><?= $item['type'] ?></td>
  <td><?= $item['status'] ?></td>
  <td>
    <a href="index.php?page=dashboard&recover=<?= $item['id'] ?>">Recover</a> | 
    <a href="index.php?page=dashboard&delete=<?= $item['id'] ?>" onclick="return confirmDelete()">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</table>
