<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Connect to DB
    $conn = mysqli_connect("localhost","root","","merin");
    if(!$conn) die("DB connection failed");

    // Fetch user by email
    $query = "SELECT * FROM users WHERE email=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);

        // 1) Check if account is locked
        if($user['locked'] == 1){
            $message = "❌ Your account is locked due to multiple failed login attempts. Check your email to unlock.";
        }
        else {
            // 2) Verify password
            if(password_verify($password, $user['password'])){

                // 3) Check if email is verified
                if($user['verified'] == 0){
                    $message = "❌ Email not verified! Please check your inbox.";
                } else {
                    // Login success: reset attempts & start session
                    mysqli_query($conn, "UPDATE users SET login_attempts=0 WHERE email='$email'");
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    header("Location: dashboard.php");
                    exit;
                }

            } else {
                // Increment attempts
$attempts = $user['login_attempts'] + 1;

// Initialize locked and token
$locked = 0;
$token = $user['token']; // keep old token if not locked

// Check if account should be locked
if($attempts >= 5){
    $locked = 1;
    $token = bin2hex(random_bytes(16)); // generate unlock token

    // Send unlock email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'c8ba9bed5121f4';
        $mail->Password   = 'ecb3e546217e8f';
        $mail->Port       = 2525;

        $mail->setFrom('no-reply@yourwebsite.com','Your Website');
        $mail->addAddress($email, $user['name']);
        $mail->isHTML(true);
        $mail->Subject = 'Unlock Your Account';
        $unlockLink = "http://localhost/.../unlock.php?email=$email&token=$token";
        $mail->Body = "<p>Your account was locked due to multiple failed login attempts.</p>
                       <a href='$unlockLink'>Unlock Account</a>";
        $mail->send();
        $message = "❌ Wrong password. Account locked! Check your email to unlock.";
    } catch(Exception $e){
        $message = "❌ Wrong password. Account locked but email not sent!";
    }
} else {
    $message = "❌ Wrong password! Attempt $attempts of 5.";
}

// Update DB once, after everything is ready
$updateQuery = "UPDATE users SET login_attempts=?, locked=?, token=? WHERE email=?";
$stmt2 = mysqli_prepare($conn, $updateQuery);
mysqli_stmt_bind_param($stmt2, "iiss", $attempts, $locked, $token, $email);
mysqli_stmt_execute($stmt2);


                // Update attempts & lock status
                $updateQuery = "UPDATE users SET login_attempts=?, locked=?, token=? WHERE email=?";
                $stmt2 = mysqli_prepare($conn, $updateQuery);
                mysqli_stmt_bind_param($stmt2, "iiss", $attempts, $locked, $token, $email);
                mysqli_stmt_execute($stmt2);
            }
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
    <title>Resend Verification Email</title>
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
    <div 
        style="
            margin:15px 0; 
            padding:12px; 
            border-radius:5px; 
            font-weight:bold; 
            background: <?php echo strpos($message,'✔')!==false ? '#d4edda' : '#f8d7da'; ?>; 
            color: <?php echo strpos($message,'✔')!==false ? '#155724' : '#721c24'; ?>;
            border: 1px solid <?php echo strpos($message,'✔')!==false ? '#c3e6cb' : '#f5c6cb'; ?>;
        ">
        <?php echo $message; ?>
    </div>
<?php endif; ?>


    <form method="POST">
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <button type="submit">Resend Verification Email</button>
    </form>

    <small><a href="login.php">Back to Login</a></small>
</div>

</body>
</html>
