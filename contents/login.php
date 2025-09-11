<?php

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);  // using phone instead of email
    $password = trim($_POST['password']);

    $stmt = $db->prepare("SELECT * FROM users WHERE email=?"); // phone stored in email column
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if($user['role'] === 'user'){
            header("Location: index.php?page=post_item");
        } else {
            header("Location: index.php?page=dashboard");
        }
        exit;
    } else {
        $message = "Invalid phone number or password."; // Error message
    }
}
?>

<div class="auth-container">
    <h2>Login to Lost & Found Uganda</h2>

    <?php if($message): ?>
        <p class="auth-message" style="color:red; font-weight:bold; margin-bottom:15px;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Phone Number:</label>
        <input type="tel" name="phone" required placeholder="Enter your phone number">

        <label>Password:</label>
        <input type="password" name="password" required placeholder="Enter your password">

        <button type="submit">Login</button>
    </form>

    <p class="or">OR</p>

    <!-- Google Login -->
    <a href="index.php?page=google_auth" class="google-btn">Login with Google</a>

    <p class="register-link">
        Don't have an account? <a href="index.php?page=register">Register here</a>
    </p>
</div>
