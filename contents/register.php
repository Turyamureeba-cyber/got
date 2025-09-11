<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $phone, $password]); // using phone in email field for simplicity
        $message = "Registration successful! You can now log in.";
    } catch (PDOException $e) {
        $message = "Error: Phone number already exists.";
    }
}
?>

<div class="auth-container">
    <h2>Create Your Account</h2>

    <?php if($message): ?>
        <p class="auth-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Registration Form -->
    <form method="POST">
        <label>Full Name:</label>
        <input type="text" name="name" required placeholder="Enter your full name">

        <label>Phone Number:</label>
        <input type="tel" name="phone" required placeholder="Enter your phone number">

        <label>Password:</label>
        <input type="password" name="password" required placeholder="Create a password">

        <button type="submit">Register</button>
    </form>

    <p class="or">OR</p>

    <!-- Google Registration -->
    <a href="index.php?page=google_auth" class="google-btn">Register with Google</a>

    <p class="register-link">
        Already have an account? <a href="index.php?page=login">Login here</a>
    </p>
</div>
