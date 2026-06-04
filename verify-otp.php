<?php
date_default_timezone_set('Pacific/Fiji');
require 'session.php';
require 'db.php';

date_default_timezone_set('Pacific/Fiji');

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF CHECK
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed");
    }

    if (!isset($_SESSION['temp_user_id'])) {
        die("Session expired. Please login again.");
    }

    $otp = trim($_POST['otp']);

    if (!preg_match('/^[0-9]{6}$/', $otp)) {
        $message = "Invalid OTP format";
    } else {

        $user_id = $_SESSION['temp_user_id'];

        // 🔴 CHECK FAILED ATTEMPTS (NEW SECURITY)
        if (!isset($_SESSION['otp_attempts'])) {
            $_SESSION['otp_attempts'] = 0;
        }

        if ($_SESSION['otp_attempts'] >= 5) {
            unset($_SESSION['temp_user_id']);
            die("Too many failed OTP attempts. Please login again.");
        }

        // CHECK OTP
        $stmt = $pdo->prepare("
            SELECT *
            FROM otp_verifications
            WHERE user_id = ?
              AND otp_code = ?
              AND verified = 0
              AND expires_at > NOW()
            ORDER BY id DESC
            LIMIT 1
        ");

        $stmt->execute([$user_id, $otp]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {

            // ✅ MARK OTP AS VERIFIED (IMPORTANT FIX)
            $pdo->prepare("
                UPDATE otp_verifications
                SET verified = 1
                WHERE id = ?
            ")->execute([$data['id']]);

            // GET USER
            $stmt = $pdo->prepare("
                SELECT id, fullname, role
                FROM users
                WHERE id = ?
            ");

            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {

                // RESET OTP ATTEMPTS
                unset($_SESSION['otp_attempts']);

                // CREATE FINAL SESSION
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];

                session_regenerate_id(true);

                unset($_SESSION['temp_user_id']);

                // NEW CSRF TOKEN
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                // CLEAN OLD OTPs (OPTIONAL BUT GOOD)
                $pdo->prepare("
                    DELETE FROM otp_verifications
                    WHERE user_id = ? AND verified = 1
                ")->execute([$user_id]);

                header("Location: redirect.php");
                exit();
            }

        } else {

            // ❌ FAILED OTP ATTEMPT
            $_SESSION['otp_attempts']++;

            $message = "Invalid or expired OTP. Attempts: " . $_SESSION['otp_attempts'];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-5">

<div class="card shadow p-4">

<h3>🔐 OTP Verification</h3>

<p>Enter the 6-digit code sent to your email.</p>

<?php if ($message): ?>
<div class="alert alert-danger">
    <?= htmlspecialchars($message) ?>
</div>
<?php endif; ?>

<form method="POST">

<input type="hidden"
       name="csrf_token"
       value="<?= $_SESSION['csrf_token']; ?>">

<input type="text"
       name="otp"
       class="form-control mb-3"
       placeholder="Enter 6-digit OTP"
       maxlength="6"
       required>

<button class="btn btn-primary w-100">
    Verify OTP
</button>

</form>

</div>

</div>
</div>
</div>

</body>
</html>
