<?php
date_default_timezone_set('Pacific/Fiji');

require '../config/session.php';
require '../config/db.php';

$message = "";

/*
|--------------------------------------------------------------------------
| CSRF TOKEN SETUP
|--------------------------------------------------------------------------
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/*
|--------------------------------------------------------------------------
| HANDLE REGISTRATION
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
    |--------------------------------------------------------------------------
    | CSRF VALIDATION
    |--------------------------------------------------------------------------
    */
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed");
    }

    /*
    |--------------------------------------------------------------------------
    | INPUT SANITIZATION
    |--------------------------------------------------------------------------
    */
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($fullname) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } else {

        /*
        |--------------------------------------------------------------------------
        | CHECK IF EMAIL EXISTS
        |--------------------------------------------------------------------------
        */
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            $message = "Email already exists.";
        } else {

            /*
            |--------------------------------------------------------------------------
            | PASSWORD HASHING
            |--------------------------------------------------------------------------
            */
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {

                /*
                |--------------------------------------------------------------------------
                | INSERT USER (SECURE)
                |--------------------------------------------------------------------------
                */
                $stmt = $pdo->prepare("
                    INSERT INTO users (fullname, email, password, role)
                    VALUES (?, ?, ?, 'customer')
                ");

                $stmt->execute([
                    $fullname,
                    $email,
                    $hashedPassword
                ]);

                header("Location: login.php?registered=1");
                exit();

            } catch (PDOException $e) {
                $message = "Registration failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow">

                <div class="card-header bg-primary text-white">
                    <h3>Create Account</h3>
                </div>

                <div class="card-body">

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <!-- CSRF TOKEN -->
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                        <div class="mb-3">
                            <label>Full Name</label>
                            <input type="text" name="fullname" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Register
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>