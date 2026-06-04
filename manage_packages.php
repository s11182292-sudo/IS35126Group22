<?php

require '../session.php';
require '../security.php';
require '../auth_check.php';
require '../role_check.php';
require '../db.php';

requireRole('admin');

/*
|--------------------------------------------------------------------------
| SESSION TIMEOUT (15 minutes inactivity)
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

$timeout = 15 * 60;

if (time() - $_SESSION['last_activity'] > $timeout) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$_SESSION['last_activity'] = time();

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
| ADD PACKAGE
|--------------------------------------------------------------------------
*/
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF CHECK
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed");
    }

    // INPUT VALIDATION
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $destination = trim($_POST['destination']);

    $price = filter_input(
        INPUT_POST,
        'price',
        FILTER_VALIDATE_FLOAT
    );

    if (strlen($name) < 3) {
        $message = "Package name must be at least 3 characters.";
    }

    elseif (strlen($destination) < 3) {
        $message = "Destination is required.";
    }

    elseif ($price === false || $price <= 0) {
        $message = "Invalid price.";
    }

    else {

        // INSERT PACKAGE
        $stmt = $pdo->prepare("
            INSERT INTO travel_packages
            (package_name, description, destination, price)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $description,
            $destination,
            $price
        ]);

        // AUDIT LOG
        $action = sprintf(
            "Admin %s added package '%s' (%s)",
            $_SESSION['fullname'],
            $name,
            $destination
        );

        $pdo->prepare("
            INSERT INTO audit_logs (user_id, action)
            VALUES (?, ?)
        ")->execute([
            $_SESSION['user_id'],
            $action
        ]);

        // REGENERATE CSRF TOKEN
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $message = "Package added successfully!";
    }
}

/*
|--------------------------------------------------------------------------
| FETCH PACKAGES
|--------------------------------------------------------------------------
*/
$packages = $pdo->query("
    SELECT * FROM travel_packages
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Packages</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-3">
    <span class="navbar-brand">Admin Panel - Manage Packages</span>
    <a href="../logout.php" class="btn btn-danger">Logout</a>
</nav>

<div class="container mt-4">

    <h2 class="mb-4">🌴 Manage Travel Packages</h2>

    <div class="mb-4">
        <a href="dashboard.php" class="btn btn-secondary">Dashboard</a>
        <a href="manage_users.php" class="btn btn-info text-white">Users</a>
        <a href="manage_bookings.php" class="btn btn-success">Bookings</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- ADD FORM -->
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">
            Add New Package
        </div>

        <div class="card-body">

            <form method="POST">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label>Package Name</label>
                    <input type="text" name="name"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description"
                              class="form-control"
                              rows="4"
                              required></textarea>
                </div>

                <div class="mb-3">
                    <label>Destination</label>
                    <input type="text" name="destination"
                           class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Price</label>
                    <input type="number" step="0.01"
                           name="price"
                           class="form-control"
                           required>
                </div>

                <button class="btn btn-primary w-100">
                    Add Package
                </button>

            </form>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            Existing Packages
        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Destination</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (count($packages) === 0): ?>

                    <tr>
                        <td colspan="6" class="text-center">
                            No packages found
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($packages as $p): ?>

                        <tr>
                            <td><?= $p['id'] ?></td>

                            <td>
                                <?= htmlspecialchars($p['package_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['destination']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($p['description']) ?>
                            </td>

                            <td>
                                $<?= number_format($p['price'], 2) ?>
                            </td>

                            <td>
                                <a href="edit_package.php?id=<?= $p['id'] ?>"
                                   class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="delete_package.php"
                                      class="d-inline">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= $p['id'] ?>">

                                    <input type="hidden"
                                           name="csrf_token"
                                           value="<?= $_SESSION['csrf_token'] ?>">

                                    <button class="btn btn-danger btn-sm"
                                            onclick="return confirm('Delete package?')">
                                        Delete
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
