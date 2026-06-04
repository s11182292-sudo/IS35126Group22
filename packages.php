<?php

require 'session.php';
require 'db.php';
require 'auth_check.php';

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
| FETCH PACKAGES (clean separation)
|--------------------------------------------------------------------------
*/
$stmt = $pdo->query("
    SELECT * FROM travel_packages
    ORDER BY id DESC
");

$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

<title>Tour Packages</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">🌴 Fiji Tour Packages</span>

    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-success btn-sm">Login</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container mt-5">

<h2 class="mb-4 text-center">🌴 Explore Fiji Tour Packages</h2>

<div class="row">

<?php if (count($packages) === 0): ?>

    <p class="text-center text-muted">
        No packages available
    </p>

<?php else: ?>

<?php foreach($packages as $p): ?>

<div class="col-md-4 mb-4">

    <div class="card shadow h-100">

        <img src="island.jpeg"
             class="card-img-top"
             style="height:200px; object-fit:cover;"
             alt="Tour image">

        <div class="card-body">

            <h5 class="card-title">
                <?= htmlspecialchars($p['package_name']); ?>
            </h5>

            <p class="card-text">
                <?= htmlspecialchars($p['description']); ?>
            </p>

            <h4 class="text-primary">
                $<?= number_format((float)$p['price'], 2); ?>
            </h4>

            <!-- BOOK BUTTON -->
            <?php if (isset($_SESSION['user_id'])): ?>

                <a href="booking.php?id=<?= (int)$p['id']; ?>"
                   class="btn btn-warning w-100">

                    Book Now

                </a>

            <?php else: ?>

                <a href="login.php"
                   class="btn btn-secondary w-100">

                    Login to Book

                </a>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>

</div>

</body>
</html>
