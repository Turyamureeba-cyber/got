<?php
require_once 'db.php';

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit  = isset($_GET['limit'])  ? intval($_GET['limit'])  : 6;

// Make sure errors show in dev
error_reporting(E_ALL);
ini_set('display_errors', 1);

$stmt = $db->prepare("SELECT * FROM items ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($items);
