<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);

    // Connect to DB
    $conn = mysqli_connect("localhost","root","","merin");
    if (!$conn) die("Database connection failed!");

    // Check if email exists
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Generate reset token
        $token = bin2hex(random_bytes(16));

        // Update token in DB
        $update = "UPDATE users SET token='$token' WHERE email='$email'";
        mysqli_query($conn, $update);

        // Send reset email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'c8ba9bed5121f4';
            $mail->Password   = 'ecb3e546217e8f';
            $mail->Port       = 2525;

            $mail->setFrom('no-reply@yourwebsite.com', 'Your Website');
            $mail->addAddress($email, $user['name']);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $resetLink = "http://localhost/6_MONTHS_Training/e_commerce_project/experiments_for_loginsignup/reset.php?email=$email&token=$token";
            $mailContent = "
                <h3>Hello {$user['name']}</h3>
                <p>Click the link below to reset your password:</p>
                <a href='$resetLink'>$resetLink</a>
                <p>If you didn't request this, ignore this email.</p>
            ";
            $mail->Body = $mailContent;
            $mail->send();

            $message = "✔ Password reset email sent! Check Mailtrap Inbox.";
        } catch (Exception $e) {
            $message = "❌ Could not send email. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        $message = "❌ Email not registered!";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body { font-family: Arial,sans-serif; background:#f0f2f5; display:flex; justify-content:center; align-items:center; height:100vh; margin:0;}
        .card { background:#fff; padding:30px 40px; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.1); width:400px; text-align:center;}
        .card input { width:100%; padding:12px 15px; margin:8px 0; border-radius:5px; border:1px solid #ccc; font-size:15px;}
        .card button { width:100%; padding:12px; background:#6c7ae0; color:#fff; border:none; border-radius:5px; font-size:16px; cursor:pointer; margin-top:10px;}
        .card button:hover { background:#5560c1; }
        .message { margin:15px 0; padding:12px; border-radius:5px; font-weight:bold; animation:fadeIn 0.5s ease-in-out;}
        .message.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb;}
        .message.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb;}
        @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
        small { display:block; margin-top:15px; color:#666; }
    </style>
</head>
<body>

<div class="card">
    <img src="images/merin_nobg.png" alt="Logo" width="120" style="margin-bottom:20px;">
    <?php if(!empty($message)): ?>
        <div class="message <?php echo strpos($message,'✔') !== false ? 'success':'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <button type="submit">Send Reset Link</button>
    </form>
    <small><a href="login.php">Back to Login</a></small>
</div>

</body>
</html>
