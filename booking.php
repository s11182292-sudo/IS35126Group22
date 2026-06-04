<?php

require '../session.php';
require '../security.php';
require '../auth_check.php';
require '../role_check.php';
require '../db.php';

requireRole('customer');

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
| VALIDATE PACKAGE ID
|--------------------------------------------------------------------------
*/
$package_id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$package_id) {
    header("Location: packages.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| FETCH PACKAGE
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT * FROM travel_packages WHERE id = ?
");

$stmt->execute([$package_id]);

$package = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$package) {
    header("Location: packages.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| HANDLE BOOKING
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

    $user_id = $_SESSION['user_id'];

    $travel_date = $_POST['travel_date'] ?? '';
    $persons = filter_input(
        INPUT_POST,
        'persons',
        FILTER_VALIDATE_INT
    );

    // BASIC VALIDATION
    if (!$travel_date || !$persons || $persons < 1) {
        die("Invalid booking data");
    }

    // Prevent past date booking
    if ($travel_date < date('Y-m-d')) {
        die("Travel date cannot be in the past");
    }

    /*
    |--------------------------------------------------------------------------
    | INSERT BOOKING
    |--------------------------------------------------------------------------
    */
    $stmt = $pdo->prepare("
        INSERT INTO bookings
        (user_id, package_id, travel_date, persons)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id,
        $package_id,
        $travel_date,
        $persons
    ]);

    /*
    |--------------------------------------------------------------------------
    | AUDIT LOG
    |--------------------------------------------------------------------------
    */
    $pdo->prepare("
        INSERT INTO audit_logs (user_id, action)
        VALUES (?, ?)
    ")->execute([
        $user_id,
        "Booked package ID $package_id"
    ]);

    header("Location: my_booking.php?success=1");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Book Package</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <h2>
            Book: <?= htmlspecialchars($package['package_name']); ?>
        </h2>

        <a href="packages.php" class="btn btn-secondary">
            Back
        </a>

    </div>

    <div class="card shadow p-4">

        <form method="POST">

            <!-- FIXED CSRF TOKEN -->
            <input type="hidden"
                   name="csrf_token"
                   value="<?= $_SESSION['csrf_token']; ?>">

            <div class="mb-3">

                <label class="form-label">Travel Date</label>

                <input type="date"
                       name="travel_date"
                       class="form-control"
                       required>

            </div>

            <div class="mb-3">

                <label class="form-label">Number of Persons</label>

                <input type="number"
                       name="persons"
                       min="1"
                       class="form-control"
                       required>

            </div>

            <button type="submit" class="btn btn-primary w-100">
                Confirm Booking
            </button>

        </form>

    </div>

</div>

</body>
</html>
