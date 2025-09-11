<?php
require_once 'db.php';

$page_num = intval($_GET['page_num'] ?? 1);
$limit = 10;
$offset = ($page_num - 1) * $limit;
$type = $_GET['type'] ?? 'all';

$sql = "SELECT * FROM items WHERE status='active'";
$params = [];

if ($type === 'lost' || $type === 'found') {
    $sql .= " AND type = :type";
    $params[':type'] = $type;
}

$sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);

// Bind limit and offset
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// Bind type only if set
if (isset($params[':type'])) {
    $stmt->bindValue(':type', $params[':type'], PDO::PARAM_STR);
}

$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no results, echo nothing so JS knows to stop
if (!$items) {
    exit;
}

foreach ($items as $item): ?>
    <div class="item-card">
        <?php if (!empty($item['image_url'])): ?>
            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Item Image">
        <?php else: ?>
            <img src="assets/placeholder.png" alt="No Image">
        <?php endif; ?>
        <h4><?= htmlspecialchars($item['title']) ?></h4>
        <p><?= htmlspecialchars(substr($item['description'], 0, 100)) ?>...</p>
        <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
        <p><strong>Type:</strong> <?= htmlspecialchars($item['type']) ?></p>
        <div class="item-actions">
            <a href="#" class="edit-btn view-item-btn" data-id="<?= $item['id']; ?>">View Details</a>

        </div>
    </div>
<?php endforeach; ?>
