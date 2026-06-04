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
| PROCESS BOOKING (SECURE POST)
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF CHECK
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed");
    }

    // VALIDATE INPUT
    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );

    $action = $_POST['action'] ?? '';

    if (!$id || !in_array($action, ['approve', 'reject'])) {
        die("Invalid request");
    }

    // SET STATUS
    $status = ($action === 'approve')
        ? 'Approved'
        : 'Rejected';

    // UPDATE BOOKING
    $stmt = $pdo->prepare("
        UPDATE bookings
        SET status = ?
        WHERE id = ?
    ");

    $stmt->execute([$status, $id]);

    // AUDIT LOG
    $log = sprintf(
        "Agent %s %s booking ID %d",
        $_SESSION['fullname'],
        $status,
        $id
    );

    $pdo->prepare("
        INSERT INTO audit_logs (user_id, action)
        VALUES (?, ?)
    ")->execute([
        $_SESSION['user_id'],
        $log
    ]);

    // Redirect to avoid form resubmission
    header("Location: process_booking.php?msg=updated");
    exit();
}

/*
|--------------------------------------------------------------------------
| FETCH BOOKINGS
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT b.*, u.fullname, p.package_name
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN travel_packages p ON b.package_id = p.id
    ORDER BY b.id DESC
");

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<title>Process Bookings</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">🛫 Agent Booking Panel</span>
    <a href="../auth/logout.php" class="btn btn-danger btn-sm">Logout</a>
</nav>

<div class="container mt-4">

<h2>Process Bookings</h2>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success">
        Booking updated successfully
    </div>
<?php endif; ?>

<div class="card shadow">

<div class="card-body">

<table class="table table-striped table-bordered">

<thead>
<tr>
    <th>User</th>
    <th>Package</th>
    <th>Date</th>
    <th>Persons</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php if (count($bookings) === 0): ?>

<tr>
    <td colspan="6" class="text-center text-muted">
        No bookings found
    </td>
</tr>

<?php else: ?>

<?php foreach($bookings as $b): ?>

<tr>

    <td><?= htmlspecialchars($b['fullname']); ?></td>
    <td><?= htmlspecialchars($b['package_name']); ?></td>
    <td><?= htmlspecialchars($b['travel_date']); ?></td>
    <td><?= (int)$b['persons']; ?></td>

    <td>
        <span class="badge bg-info">
            <?= htmlspecialchars($b['status'] ?? 'Pending'); ?>
        </span>
    </td>

    <td>

        <!-- APPROVE -->
        <form method="POST" class="d-inline">

            <input type="hidden" name="id"
                   value="<?= $b['id']; ?>">

            <input type="hidden" name="action"
                   value="approve">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= $_SESSION['csrf_token']; ?>">

            <button class="btn btn-success btn-sm"
                    onclick="return confirm('Approve booking?')">
                Approve
            </button>

        </form>

        <!-- REJECT -->
        <form method="POST" class="d-inline">

            <input type="hidden" name="id"
                   value="<?= $b['id']; ?>">

            <input type="hidden" name="action"
                   value="reject">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= $_SESSION['csrf_token']; ?>">

            <button class="btn btn-danger btn-sm"
                    onclick="return confirm('Reject booking?')">
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