<?php
date_default_timezone_set('Pacific/Fiji');
require 'session.php';
require 'db.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;

$message = "";

/*
|--------------------------------------------------------------------------
| LOGIN HANDLER
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $captcha = $_POST['g-recaptcha-response'] ?? '';

    if (!$email || empty($password)) {
        $message = "Please enter valid email and password.";
    } elseif (empty($captcha)) {
        $message = "Please complete the CAPTCHA.";
    } else {

        /*
        |--------------------------------------------------------------------------
        | VERIFY GOOGLE reCAPTCHA
        |--------------------------------------------------------------------------
        */
        $secretKey = $_ENV['RECAPTCHA_SECRET'];

        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret="
            . $secretKey
            . "&response="
            . $captcha
        );

        $captchaResponse = json_decode($verify);

        if (!$captchaResponse->success) {
            $message = "CAPTCHA verification failed.";
        } else {

            /*
            |--------------------------------------------------------------------------
            | FIND USER
            |--------------------------------------------------------------------------
            */
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $message = "Invalid email or password.";
            } else {

                /*
                |--------------------------------------------------------------------------
                | CHECK LOCKOUT
                |--------------------------------------------------------------------------
                */
                if (!empty($user['lock_until']) && strtotime($user['lock_until']) > time()) {
                    $message = "Account locked. Try again after 15 minutes.";
                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | PASSWORD CHECK
                    |--------------------------------------------------------------------------
                    */
                    if (password_verify($password, $user['password'])) {

                        // reset attempts
                        $pdo->prepare("
                            UPDATE users 
                            SET failed_attempts = 0, lock_until = NULL 
                            WHERE id = ?
                        ")->execute([$user['id']]);

                        /*
                        |--------------------------------------------------------------------------
                        | OTP GENERATION
                        |--------------------------------------------------------------------------
                        */
                        $otp = strval(random_int(100000, 999999));
                        $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

                        $pdo->prepare("
                            INSERT INTO otp_verifications (user_id, otp_code, expires_at)
                            VALUES (?, ?, ?)
                        ")->execute([
                            $user['id'],
                            $otp,
                            $expires
                        ]);

                        /*
                        |--------------------------------------------------------------------------
                        | SEND OTP EMAIL
                        |--------------------------------------------------------------------------
                        */
                        $mail = new PHPMailer(true);

                        try {
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = $_ENV['SMTP_EMAIL'];
                            $mail->Password = $_ENV['SMTP_PASS'];
                            $mail->SMTPSecure = 'tls';
                            $mail->Port = 587;

                            $mail->setFrom('YOUR_EMAIL@gmail.com', 'Tourism System');
                            $mail->addAddress($user['email']);

                            $mail->Subject = "Your OTP Code";
                            $mail->Body = "Your OTP is: $otp (valid for 10 minutes)";

                            $mail->send();

                        } catch (Exception $e) {
                            $message = "OTP email failed.";
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | TEMP SESSION
                        |--------------------------------------------------------------------------
                        */
                        session_regenerate_id(true);

                        $_SESSION['temp_user_id'] = $user['id'];
                        $_SESSION['temp_role'] = $user['role'];
                        $_SESSION['temp_name'] = $user['fullname'];

                        // audit log
                        $pdo->prepare("
                            INSERT INTO audit_logs (user_id, action, created_at)
                            VALUES (?, ?, NOW())
                        ")->execute([
                            $user['id'],
                            "OTP login initiated"
                        ]);

                        header("Location: verify-otp.php");
                        exit();

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | FAILED LOGIN ATTEMPTS
                        |--------------------------------------------------------------------------
                        */
                        $attempts = (int)$user['failed_attempts'] + 1;
                        $lock_until = null;

                        if ($attempts >= 5) {
                            $lock_until = date("Y-m-d H:i:s", strtotime("+15 minutes"));
                            $attempts = 0;
                        }

                        $pdo->prepare("
                            UPDATE users 
                            SET failed_attempts = ?, lock_until = ?
                            WHERE id = ?
                        ")->execute([
                            $attempts,
                            $lock_until,
                            $user['id']
                        ]);

                        $message = "Invalid email or password.";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow p-4">

                <h3 class="text-center mb-3">Login</h3>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <!-- reCAPTCHA -->
                    <div class="g-recaptcha mb-3" data-sitekey="6LdONQotAAAAAGPdnTewleLCfXH2hVD6y6BNT_z8"></div>

                    <button class="btn btn-primary w-100">
                        Login
                    </button>

                </form>

                <hr>

                <a href="google-login.php" class="btn btn-danger w-100 mb-3">
                    Login with Google
                </a>

                <p class="text-center mb-0">
                    Don’t have an account?
                    <a href="register.php">Register</a>
                </p>

            </div>

        </div>

    </div>

</div>

</body>
</html>
