<?php
require_once __DIR__ . '/../db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "<p>Invalid item.</p>";
    echo '<div class="back-container"><a href="javascript:history.back()" class="back-btn">← Go Back</a></div>';
    exit;
}

$stmt = $db->prepare("SELECT * FROM items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo "<p>Item not found.</p>";
    echo '<div class="back-container"><a href="javascript:history.back()" class="back-btn">← Go Back</a></div>';
    exit;
}

// Optional: decode multiple images if stored as JSON
$images = !empty($item['image_url']) ? explode(',', $item['image_url']) : ['assets/placeholder.png'];
?>

<section class="view-item">
  <div class="view-card">
    <!-- Image Section -->
    <div class="view-image">
      <?php foreach ($images as $img): ?>
        <img src="<?= htmlspecialchars($img) ?>" alt="Item Image">
      <?php endforeach; ?>
    </div>

    <!-- Details Section -->
    <div class="view-details">
      <h2><?= htmlspecialchars($item['title']) ?></h2>
      <p class="category"><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
      <p class="type"><strong>Type:</strong> <?= htmlspecialchars($item['type']) ?></p>
      <p class="location"><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
      <p class="description"><?= nl2br(htmlspecialchars($item['description'])) ?></p>

      <div class="contact-box">
        <h3>Contact Finder/Owner</h3>
        <p><strong>Phone:</strong> <?= htmlspecialchars($item['phone'] ?? 'N/A') ?></p>
      </div>

      <div class="back-container">
        <a href="javascript:history.back()" class="back-btn">← Go Back</a>
      </div>
    </div>
  </div>
</section>

<style>
.view-item {
    padding: 30px;
    display: flex;
    justify-content: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.view-card {
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
    max-width: 1000px;
    background: #fff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.view-image {
    flex: 1;
    min-width: 300px;
    display: flex;
    flex-direction: column;
    gap: 15px;
    overflow-x: auto; /* for multiple images */
}

.view-image img {
    width: 100%;
    height: auto;
    object-fit: contain;
    border-radius: 12px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease;
}

.view-image img:hover {
    transform: scale(1.05);
}

.view-details {
    flex: 1;
    min-width: 300px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.view-details h2 {
    font-size: 1.8rem;
    color: #2575fc;
    margin-bottom: 10px;
}

.view-details p {
    font-size: 0.95rem;
    line-height: 1.4;
    color: #555;
}

.contact-box {
    margin-top: 15px;
    padding: 15px;
    background: #f4f7fb;
    border-radius: 12px;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
}

.contact-box h3 {
    margin-bottom: 8px;
    color: #6a11cb;
}

.back-container {
    margin-top: 20px;
    text-align: center;
}

.back-btn {
    text-decoration: none;
    padding: 8px 18px;
    background: linear-gradient(to right, #2575fc, #6a11cb);
    color: white;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.back-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 117, 252, 0.4);
}

/* Responsive adjustments */
@media (max-width: 800px) {
    .view-card {
        flex-direction: column;
        gap: 25px;
        padding: 20px;
    }
}
</style>
