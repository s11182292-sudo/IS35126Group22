<?php
date_default_timezone_set('Pacific/Fiji');
require __DIR__ . '/session.php';
require __DIR__ . '/google-config.php';

if (!isset($_GET['code'])) {
    die("No authorization code received.");
}

// Exchange code for token
$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    die("Google login failed: " . $token['error']);
}

$client->setAccessToken($token['access_token']);

// Get user info
$oauth = new Google\Service\Oauth2($client);
$userInfo = $oauth->userinfo->get();

// Example user data
$email = $userInfo->email;
$name  = $userInfo->name;
$google_id = $userInfo->id;

// TODO: connect to DB and create/login user
require '../db.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // register new user
    $stmt = $pdo->prepare("INSERT INTO users (fullname, email, role) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, 'customer']);

    $user_id = $pdo->lastInsertId();
} else {
    $user_id = $user['id'];
}

// login session
session_regenerate_id(true);

$_SESSION['user_id'] = $user_id;
$_SESSION['fullname'] = $name;
$_SESSION['role'] = 'customer';

// redirect
header("Location: cus_dashboard.php");
exit;
