<?php
date_default_timezone_set('Pacific/Fiji');
require 'session.php';
require 'db.php';

/*
|--------------------------------------------------------------------------
| AUDIT LOG (OPTIONAL BUT HIGHLY RECOMMENDED)
|--------------------------------------------------------------------------
*/
if (isset($_SESSION['user_id'])) {

    $pdo->prepare("
        INSERT INTO audit_logs (user_id, action, created_at)
        VALUES (?, ?, NOW())
    ")->execute([
        $_SESSION['user_id'],
        "User logged out"
    ]);
}

/*
|--------------------------------------------------------------------------
| CLEAR SESSION DATA
|--------------------------------------------------------------------------
*/
$_SESSION = [];

/*
|--------------------------------------------------------------------------
| DESTROY SESSION
|--------------------------------------------------------------------------
*/
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

/*
|--------------------------------------------------------------------------
| REMOVE SESSION COOKIE (SECURE CLEANUP)
|--------------------------------------------------------------------------
*/
if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/*
|--------------------------------------------------------------------------
| REDIRECT TO LOGIN
|--------------------------------------------------------------------------
*/
header("Location: login.php");
exit();
