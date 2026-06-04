<?php

require 'session.php';
require 'security.php';
require 'auth_check.php';
require 'role_check.php';
require 'db.php';

requireRole('admin');

/*
|--------------------------------------------------------------------------
| BASIC SESSION SAFETY CHECK
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| SYSTEM STATS
|--------------------------------------------------------------------------
*/
$users = $pdo->query("
    SELECT COUNT(*) FROM users
")->fetchColumn();

$packages = $pdo->query("
    SELECT COUNT(*) FROM travel_packages
")->fetchColumn();

$bookings = $pdo->query("
    SELECT COUNT(*) FROM bookings
")->fetchColumn();

/*
|--------------------------------------------------------------------------
| REVENUE CALCULATION (SAFE NULL HANDLING)
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT SUM(p.price)
    FROM bookings b
    JOIN travel_packages p ON b.package_id = p.id
    WHERE b.status = 'Approved'
");

$revenue = $stmt->fetchColumn();

if ($revenue === null) {
    $revenue = 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">System Reports</span>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</nav>

<div class="container mt-4">

<h2>📊 System Reports Dashboard</h2>

<div class="row mt-4">

    <div class="col-md-3">
        <div class="card bg-primary text-white p-3">
            <h5>Total Users</h5>
            <h2><?= htmlspecialchars($users) ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white p-3">
            <h5>Total Packages</h5>
            <h2><?= htmlspecialchars($packages) ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white p-3">
            <h5>Total Bookings</h5>
            <h2><?= htmlspecialchars($bookings) ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-dark text-white p-3">
            <h5>Total Revenue</h5>
            <h2>$<?= number_format($revenue, 2) ?></h2>
        </div>
    </div>

</div>

</div>

</body>
</html>
