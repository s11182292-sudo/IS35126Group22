<?php

require __DIR__ . '/session.php';
require __DIR__ . '/security.php';
require __DIR__ . '/auth_check.php';
require __DIR__ . '/role_check.php';
require __DIR__ . '/db.php';

requireRole('admin');

/* Fetch logs */
$logs = $pdo->query("
    SELECT *
    FROM audit_logs
    ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Audit Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Audit Logs</span>
    <a href="logout.php" class="btn btn-danger">Logout</a>
</nav>

<div class="container mt-4">

<h2>📜 System Audit Logs</h2>

<table class="table table-bordered table-striped">

<thead>
<tr>
    <th>ID</th>
    <th>Action</th>
    <th>User ID</th>
    <th>Date</th>
</tr>
</thead>

<tbody>

<?php foreach ($logs as $log): ?>

<tr>
    <td><?= $log['id'] ?></td>
    <td><?= htmlspecialchars($log['action']) ?></td>
    <td><?= $log['user_id'] ?></td>
    <td><?= $log['created_at'] ?></td>
</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</body>
</html>
