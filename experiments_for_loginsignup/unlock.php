<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$message = "";

if(isset($_GET['email']) && isset($_GET['token'])){
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Connect to DB
    $conn = mysqli_connect("localhost", "root", "", "merin");
    if(!$conn) die("DB connection failed!");

    // Verify token
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email=? AND token=?");
    mysqli_stmt_bind_param($stmt, "ss", $email, $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) === 1){
        // Reset lock and attempts
        $stmt2 = mysqli_prepare($conn, "UPDATE users SET locked=0, login_attempts=0, token=NULL WHERE email=?");
        mysqli_stmt_bind_param($stmt2, "s", $email);
        if(mysqli_stmt_execute($stmt2)){
            $message = "✔ Your account is now unlocked! You can <a href='login.php'>login</a>.";
        } else {
            $message = "❌ Could not unlock your account. Try again later.";
        }
    } else {
        $message = "❌ Invalid or expired unlock link.";
    }

    mysqli_close($conn);

} else {
    $message = "❌ Invalid request.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unlock Account</title>
    <style>
        body { font-family: Arial; background:#f0f2f5; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
        .unlock-card { background:#fff; padding:30px 40px; border-radius:10px; box-shadow:0 8px 20px rgba(0,0,0,0.1); width:400px; text-align:center; }
        .message { margin:15px 0; padding:12px; border-radius:5px; font-weight:bold; text-align:center; }
        .message.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .message.error { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        a { color:#6c7ae0; text-decoration:none; font-weight:bold; }
    </style>
</head>
<body>
    <div class="unlock-card">
        <h2>Unlock Account</h2>
        <div class="message <?php echo strpos($message,'✔')!==false ? 'success':'error'; ?>">
            <?php echo $message; ?>
        </div>
    </div>
</body>
</html>
