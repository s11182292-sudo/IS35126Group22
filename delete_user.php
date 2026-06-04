<?php

require '../session.php';
require '../security.php';
require '../auth_check.php';
require '../role_check.php';
require '../db.php';

requireRole('admin');

/*
|--------------------------------------------------------------------------
| ONLY ALLOW POST REQUEST
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method");
}

/*
|--------------------------------------------------------------------------
| CSRF CHECK
|--------------------------------------------------------------------------
*/
if (
    !isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    die("CSRF validation failed");
}

/*
|--------------------------------------------------------------------------
| VALIDATE USER ID
|--------------------------------------------------------------------------
*/
$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id) {
    die("Invalid user ID");
}

/*
|--------------------------------------------------------------------------
| PREVENT ADMIN FROM DELETING THEMSELF
|--------------------------------------------------------------------------
*/
if ($id == $_SESSION['user_id']) {
    die("You cannot delete your own account");
}

/*
|--------------------------------------------------------------------------
| CHECK USER EXISTS
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT fullname, role
    FROM users
    WHERE id = ?
");

$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found");
}

/*
|--------------------------------------------------------------------------
| DELETE USER
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    DELETE FROM users
    WHERE id = ?
");

$stmt->execute([$id]);

/*
|--------------------------------------------------------------------------
| AUDIT LOG
|--------------------------------------------------------------------------
*/
$action = sprintf(
    "Admin %s deleted user '%s' (Role: %s, ID: %d)",
    $_SESSION['fullname'],
    $user['fullname'],
    $user['role'],
    $id
);

$pdo->prepare("
    INSERT INTO audit_logs (user_id, action)
    VALUES (?, ?)
")->execute([
    $_SESSION['user_id'],
    $action
]);

/*
|--------------------------------------------------------------------------
| REDIRECT BACK
|--------------------------------------------------------------------------
*/
header("Location: manage_users.php?msg=deleted");
exit();
