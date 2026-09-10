<?php
$message = "";
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPass = $_POST['password'];
    $confirmPass = $_POST['confirm_password'];

    if ($newPass !== $confirmPass) {
        $message = "❌ Passwords do not match!";
    } else if (strlen($newPass) < 8) {
        $message = "❌ Password must be at least 8 characters!";
    } else {
        $conn = mysqli_connect("localhost","root","","merin");
        if (!$conn) die("DB failed");

        $query = "SELECT * FROM users WHERE email='$email' AND token='$token'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            $update = "UPDATE users SET password='$hashed', token='' WHERE email='$email'";
            mysqli_query($conn, $update);
            $message = "✔ Password reset successful! <a href='login.php'>Login here</a>";
        } else {
            $message = "❌ Invalid token or email!";
        }
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
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

    <?php if($email && $token): ?>
    <form method="POST">
        <input type="password" name="password" placeholder="Enter New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit">Reset Password</button>
    </form>
    <?php else: ?>
        <small>Invalid reset link.</small>
    <?php endif; ?>
</div>

</body>
</html>
