<?php

require __DIR__ . '/session.php';
require __DIR__ . '/auth_check.php';
require __DIR__ . '/role_check.php';

requireRole('customer');

/*
|--------------------------------------------------------------------------
| SESSION SAFETY CHECK
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| CSRF TOKEN (for future forms like booking)
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Customer Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<!-- LOGOUT -->
<div class="position-fixed top-0 end-0 m-3" style="z-index: 9999;">
    <a href="logout.php" class="btn btn-danger">
        Logout
    </a>
</div>

<div class="container mt-5">

<h1>🌴 Customer Dashboard</h1>

<p class="text-muted">
Welcome <?= htmlspecialchars($_SESSION['fullname']); ?>
</p>

<div class="row g-3">

    <!-- Browse Packages -->
    <div class="col-md-4">

        <a href="packages.php" class="text-decoration-none">

            <div class="card p-4 bg-primary text-white shadow">

                <h4>Browse Packages</h4>
                <p class="mb-0">View available travel packages</p>

            </div>

        </a>

    </div>

    <!-- Book Tour -->
    <div class="col-md-4">

        <a href="booking.php" class="text-decoration-none">

            <div class="card p-4 bg-success text-white shadow">

                <h4>Book Tour</h4>
                <p class="mb-0">Make a new booking</p>

            </div>

        </a>

    </div>

    <!-- My Bookings -->
    <div class="col-md-4">

        <a href="my_booking.php" class="text-decoration-none">

            <div class="card p-4 bg-info text-white shadow">

                <h4>My Bookings</h4>
                <p class="mb-0">View your bookings</p>

            </div>

        </a>

    </div>

</div>

</div>

</body>
</html>
