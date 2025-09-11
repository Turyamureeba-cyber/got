<?php
$search = isset($_GET['q']) ? $_GET['q'] : '';
$sql = "SELECT * FROM items WHERE status='active'";
if ($search) {
    $sql .= " AND (title LIKE :search OR description LIKE :search)";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':search', "%$search%");
    $stmt->execute();
} else {
    $stmt = $db->query($sql);
}
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">
    <h2>Lost Something? Found Something?</h2>
    <p>Connecting Ugandans to reunite with the lost items fast and securely.</p>
    <form action="index.php?page=search" method="get">
      <input type="hidden" name="page" value="search">
      <input type="text" name="q" placeholder="Search lost items...">
      <button type="submit">Search</button>
    </form>
  </div>
  </section>
  <div class="back-container">
    <a href="javascript:void(0)" onclick="goBack()" class="back-btn">← Go Back</a>
</div>

<script>
function goBack() {
    // Check if we have previous history from the same site
    if (window.history.length > 1) {
        window.history.back();
    } else {
        // Fallback to home page
        window.location.href = 'home.php';
    }
}
</script>

<div class="grid">
<?php foreach($items as $item): ?>
  <div class="card">
    <?php if($item['image_url']): ?>
      <img src="<?= $item['image_url'] ?>" alt="Item">
    <?php endif; ?>
    <h3><?= htmlspecialchars($item['title']) ?></h3>
    <p><?= htmlspecialchars($item['description']) ?></p>
    <a href="index.php?page=item_details&id=<?= $item['id'] ?>">View Details</a>
  </div>
<?php endforeach; ?>
</div>
