<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// ===== Google OAuth Config =====
$clientId = '161070596230-s74fonajkp7qi33hm9psurbonpnol4sc.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/got/index.php?page=google_auth';

// Step 1: Handle Google redirect with code
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Exchange code for access token
    $tokenResponse = file_get_contents("https://oauth2.googleapis.com/token", false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query([
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code'
            ])
        ]
    ]));

    $tokenData = json_decode($tokenResponse, true);
    $accessToken = $tokenData['access_token'] ?? null;

    if ($accessToken) {
        // Get user info
        $userInfo = file_get_contents("https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token={$accessToken}");
        $googleUser = json_decode($userInfo, true);

        $googleEmail = $googleUser['email'];
        $googleName = $googleUser['name'];

        // Check if user exists
        $stmt = $db->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$googleEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Register new Google user
            $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$googleName, $googleEmail, '', 'user']);
            $userId = $db->lastInsertId();
        } else {
            $userId = $user['id'];
        }

        // Start session
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $googleName;
        $_SESSION['role'] = $user['role'] ?? 'user';

        // Redirect to post_item.php for user role
        header("Location: index.php?page=post_item");
        exit;
    } else {
        echo "<p>Failed to get access token. Check your Client ID and Secret.</p>";
    }

} else {
    // Step 2: Redirect user to Google OAuth login
    $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
        'client_id' => $clientId,
        'redirect_uri' => $redirectUri,
        'response_type' => 'code',
        'scope' => 'email profile',
        'access_type' => 'online',
        'prompt' => 'select_account'
    ]);

    header("Location: " . $authUrl);
    exit;
}
