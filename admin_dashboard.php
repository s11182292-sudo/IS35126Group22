<?php

require __DIR__ . '/auth_check.php';
require __DIR__ . '/role_check.php';

requireRole('admin');
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<!-- Logout Button -->
<div class="position-fixed top-0 end-0 m-3" style="z-index: 9999;">
    <a href="logout.php" class="btn btn-danger">
        Logout
    </a>
</div>

<div class="container mt-5">

<h1 class="mb-4">👑 Admin Dashboard</h1>

<p class="mb-4">
Welcome, <?= htmlspecialchars($_SESSION['fullname']); ?>
</p>

<div class="row g-3">

    <!-- Manage Packages -->
    <div class="col-md-4">

        <a href="manage_packages.php" class="text-decoration-none">

            <div class="card p-4 bg-primary text-white shadow">

                <h4>Manage Packages</h4>
                <p class="mb-0">Add, edit, delete travel packages</p>

            </div>

        </a>

    </div>

    <!-- Manage Users -->
    <div class="col-md-4">

        <a href="manage_users.php" class="text-decoration-none">

            <div class="card p-4 bg-success text-white shadow">

                <h4>Manage Users</h4>
                <p class="mb-0">View and manage system users</p>

            </div>

        </a>

    </div>
	<!--Reports-->
	
	<div class="col-md-4">

    <a href="reports.php" class="text-decoration-none">

        <div class="card p-4 bg-warning text-white shadow">

            <h4>Reports</h4>
            <p class="mb-0">View system statistics</p>

        </div>

    </a>

</div>

    <!-- Audit Logs -->
    <div class="col-md-4">

        <a href="audit_logs.php" class="text-decoration-none">

            <div class="card p-4 bg-dark text-white shadow">

                <h4>Audit Logs</h4>
                <p class="mb-0">Track system activity</p>

            </div>

        </a>

    </div>

</div>

</div>

</body>
</html>
