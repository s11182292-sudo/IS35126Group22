<?php

require __DIR__ . '/session.php';
require __DIR__ . '/auth_check.php';
require __DIR__ . '/role_check.php';
require __DIR__ . '/db.php';

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed");
    }

    $id = (int) $_POST['id'];

    if ($id > 0) {

        $stmt = $pdo->prepare("DELETE FROM travel_packages WHERE id = ?");
        $stmt->execute([$id]);

        // audit log
        $pdo->prepare("
            INSERT INTO audit_logs (user_id, action)
            VALUES (?, ?)
        ")->execute([
            $_SESSION['user_id'],
            "Deleted package ID $id"
        ]);
    }

    header("Location: manage_packages.php");
    exit();
}
