<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lost & Found Uganda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/script.js" defer></script>
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
            <img src="assets/logo.png" alt="Lost & Found Logo">
        </div>
        <h1>Lost & Found Uganda</h1>
        <nav>
            <a href="index.php?page=home">Home</a>
            <a href="index.php?page=lost_item">Lost</a>
            <a href="index.php?page=post_item">Found</a>
            <a href="index.php?page=search">Search</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="welcome-text">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="index.php?page=logout">Logout</a>
            <?php else: ?>
                <a href="index.php?page=login">Login</a>
                <a href="index.php?page=register">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main>
    <div class="container">
