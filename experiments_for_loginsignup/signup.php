<?php
// ----------------------------
// SIGNUP + EMAIL VERIFICATION
// ----------------------------

// Include PHPMailer (Composer autoload)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$message = "";

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get user input safely
    $name   = trim($_POST["name"]);
    $email  = trim($_POST["email"]);
    $pass   = $_POST["password"];
    $cpass  = $_POST["confirm_password"];

    // -------- UPLOAD USER IMAGE --------
$imageName = $_FILES["image"]["name"];
$imageTmp = $_FILES["image"]["tmp_name"];

// generate unique filename
$uniqueImage = time() . "_" . rand(1000,9999) . "_" . $imageName;

// location to store file
$uploadPath = "user_images/" . $uniqueImage;

// move file to folder
move_uploaded_file($imageTmp, $uploadPath);


    // ----------- VALIDATION -------------
    if ($pass !== $cpass) {
        $message = "❌ Passwords do not match!";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid Email Format!";
    } else if (strlen($pass) < 8) {
        $message = "❌ Password must be at least 8 characters!";
    } else if (!preg_match('/[0-9]/', $pass)) {
        $message = "❌ Password must contain at least one number!";
    } else if (!preg_match('/[A-Za-z]/', $pass)) {
        $message = "❌ Password must contain at least one letter!";
    } else {

        // ----------- DATABASE CONNECTION -------------
        $conn = mysqli_connect("localhost", "root", "", "merin");
        if (!$conn) {
            die("Database connection failed!");
        }

        // Check duplicate email
        $checkQuery = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($result) > 0) {
            $message = "❌ Email already registered!";
        } else {

            // ----------- HASH PASSWORD -------------
            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

            // ----------- GENERATE TOKEN -------------
            $token = bin2hex(random_bytes(16));
            $verified = 0;

            // ----------- INSERT USER -------------
            $defaultRole = "customer";
$defaultImage = "default.png"; // your default user profile

$insertQuery = "INSERT INTO users (name, email, password, verified, token, role, image)
VALUES ('$name', '$email', '$hashedPassword', '$verified', '$token', '$defaultRole', '$uniqueImage')";

            // $insertQuery = "INSERT INTO users (name, email, password, verified, token)
            //                 VALUES ('$name', '$email', '$hashedPassword', '$verified', '$token')";

            if (mysqli_query($conn, $insertQuery)) {

                // ----------- SEND VERIFICATION EMAIL -------------
                $mail = new PHPMailer(true);

                try {
                    // SMTP Settings (Mailtrap example)
                    $mail->isSMTP();
                    $mail->Host       = 'sandbox.smtp.mailtrap.io';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'c8ba9bed5121f4';
                    $mail->Password   = 'ecb3e546217e8f';
                    $mail->Port       = 2525; // use 587 if 2525 blocked

                    $mail->setFrom('no-reply@yourwebsite.com', 'Your Website');
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Verify Your Email Address';

                    // Verification link
                    $verifyLink = "http://localhost/6_MONTHS_Training/e_commerce_project/experiments_for_loginsignup/verify.php?email=$email&token=$token";
                    $mailContent = "
                        <h3>Hello $name</h3>
                        <p>Click the link below to verify your email:</p>
                        <a href='$verifyLink'>$verifyLink</a>
                        <p>Thank you!</p>
                    ";
                    $mail->Body = $mailContent;

                    $mail->send();
                    $message = "✔ Verification email sent! Check Mailtrap Inbox.";
                } catch (Exception $e) {
                    $message = "❌ Could not send email. Mailer Error: {$mail->ErrorInfo}";
                }

            } else {
                $message = "❌ Error creating account!";
            }
        }

        mysqli_close($conn);
    }
}
?>

<!-- ----------------- HTML FORM ----------------- -->
<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>
    <style>
        /* General Page Styles */
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Card container */
        .signup-card {
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }

        /* Logo */
        .signup-card img {
            width: 120px;
            margin-bottom: 20px;
        }

        /* Form inputs */
        .signup-card input {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        /* Submit button */
        .signup-card button {
            width: 100%;
            padding: 12px;
            background-color: #6c7ae0; /* Primary color */
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .signup-card button:hover {
            background-color: #5560c1;
        }

        /* Messages */
        .message {
            margin: 15px 0;
            padding: 12px;
            border-radius: 5px;
            font-weight: bold;
            animation: fadeIn 0.5s ease-in-out;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        /* Small text */
        .signup-card small {
            display: block;
            margin-top: 15px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="signup-card">
    <!-- Replace the src with your own logo -->
    <img src="images/merin_nobg.png" alt="Website Logo">

    <!-- PHP messages -->
    <?php if(!empty($message)): ?>
        <div class="message <?php echo strpos($message,'✔') !== false ? 'success':'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Signup Form -->
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Enter Name" required>
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
         <input type="file" name="image" required>
        <button type="submit">Signup</button>
    </form>

    <small>Already have an account? <a href="login.php">Login here</a></small>
</div>

</body>
</html>
