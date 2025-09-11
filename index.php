<?php
session_start();
require_once 'db.php';

$page = $_GET['page'] ?? 'home';
$ajax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

// Only include header/footer if not AJAX
if (!$ajax) {
    require_once __DIR__ . '/includes/header.php';
}

// Load page content
$file = __DIR__ . "/contents/$page.php";
if (file_exists($file)) {
    include $file;
} else {
    echo "<h2>404 - Page not found</h2>";
}

// Only include footer if not AJAX
if (!$ajax) {
    require_once __DIR__ . '/includes/footer.php';
}
