<?php

require 'session.php';
require 'auth_check.php';
require 'role_check.php';
require 'db.php';

requireRole('customer');

/*
|--------------------------------------------------------------------------
| SESSION SAFETY CHECK
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| CSRF TOKEN (future-safe consistency)
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| FETCH BOOKINGS
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT b.*, p.package_name
    FROM bookings b
    JOIN travel_packages p ON b.package_id = p.id
    WHERE b.user_id = ?
    ORDER BY b.id DESC
");

$stmt->execute([$user_id]);

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<title>My Bookings</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">My Bookings</span>

    <div>
        <a href="dashboard.php" class="btn btn-secondary btn-sm">Dashboard</a>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>

<div class="container mt-4">

<h2>📦 My Bookings</h2>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-striped">

<thead>
<tr>
    <th>Package</th>
    <th>Date</th>
    <th>Persons</th>
    <th>Status</th>
</tr>
</thead>

<tbody>

<?php if (count($bookings) === 0): ?>

<tr>
    <td colspan="4" class="text-center text-muted">
        No bookings found
    </td>
</tr>

<?php else: ?>

<?php foreach($bookings as $b): ?>

<tr>
    <td><?= htmlspecialchars($b['package_name']); ?></td>
    <td><?= htmlspecialchars($b['travel_date']); ?></td>
    <td><?= (int)$b['persons']; ?></td>

    <td>
        <span class="badge bg-info">
            <?= htmlspecialchars($b['status'] ?? 'Pending'); ?>
        </span>
    </td>
</tr>

<?php endforeach; ?>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</body>
</html>
