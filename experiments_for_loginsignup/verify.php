<?php
// ---------------------------------------------
// EMAIL VERIFICATION PAGE
// ---------------------------------------------

// Connect to DB
$conn = mysqli_connect("localhost", "root", "", "merin");

if (!$conn) {
    die("Database connection failed!");
}

// Check if email & token exist in URL
if (isset($_GET['email']) && isset($_GET['token'])) {

    $email = $_GET['email'];
    $token = $_GET['token'];

    // Check if user exists with this email + token
    $query = "SELECT * FROM users WHERE email='$email' AND token='$token' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        // Update user to verified
        $update = "UPDATE users SET verified=1, token=NULL WHERE email='$email'";
        mysqli_query($conn, $update);

        echo "<h2 style='color:green; text-align:center; margin-top:50px;'>
                ✔ Your email has been verified successfully!
              </h2>
              <p style='text-align:center;'><a href='login.php'>Click here to Login</a></p>";
    }
    else {
        echo "<h2 style='color:red; text-align:center; margin-top:50px;'>
                ❌ Invalid or Expired Verification Link!
              </h2>";
    }

} else {
    echo "<h2 style='color:red; text-align:center; margin-top:50px;'>
            ❌ Missing verification parameters!
          </h2>";
}

mysqli_close($conn);
?>
