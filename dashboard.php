<?php

require '../config/session.php';
require '../config/auth_check.php';
require '../config/role_check.php';
require '../config/db.php';

requireRole('agent');

/*
|--------------------------------------------------------------------------
| CSRF TOKEN
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| FETCH ONLY PENDING BOOKINGS
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT b.*, u.fullname, p.package_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN travel_packages p ON b.package_id = p.id
    WHERE b.status = 'Pending'
    ORDER BY b.id DESC
");

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>

<title>Agent Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">🛫 Agent Dashboard</span>
    <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
</nav>

<div class="container mt-4">

<h2>Welcome, <?= htmlspecialchars($_SESSION['fullname']); ?></h2>

<p class="text-muted">Process pending customer bookings</p>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-striped">

<thead>
<tr>
    <th>ID</th>
    <th>Customer</th>
    <th>Package</th>
    <th>Date</th>
    <th>Persons</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php if (count($bookings) === 0): ?>

<tr>
    <td colspan="7" class="text-center text-muted">
        No pending bookings
    </td>
</tr>

<?php else: ?>

<?php foreach ($bookings as $b): ?>

<tr>

    <td><?= $b['id']; ?></td>
    <td><?= htmlspecialchars($b['fullname']); ?></td>
    <td><?= htmlspecialchars($b['package_name']); ?></td>
    <td><?= $b['travel_date']; ?></td>
    <td><?= $b['persons']; ?></td>

    <td>
        <span class="badge bg-warning">
            <?= $b['status']; ?>
        </span>
    </td>

    <td>

        <!-- APPROVE -->
        <form method="POST"
              action="process_booking.php"
              class="d-inline">

            <input type="hidden" name="id" value="<?= $b['id']; ?>">
            <input type="hidden" name="action" value="approve">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= $_SESSION['csrf_token']; ?>">

            <button class="btn btn-success btn-sm"
                    onclick="return confirm('Approve this booking?')">
                Approve
            </button>

        </form>

        <!-- REJECT -->
        <form method="POST"
              action="process_booking.php"
              class="d-inline">

            <input type="hidden" name="id" value="<?= $b['id']; ?>">
            <input type="hidden" name="action" value="reject">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= $_SESSION['csrf_token']; ?>">

            <button class="btn btn-danger btn-sm"
                    onclick="return confirm('Reject this booking?')">
                Reject
            </button>

        </form>

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