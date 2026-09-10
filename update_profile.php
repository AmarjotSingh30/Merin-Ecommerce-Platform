<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "merin");

$user_id = $_SESSION['user_id'];

if(isset($_FILES['image'])){

    $imageName = time() . "_" . $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp, "uploads/" . $imageName);

    mysqli_query($conn, "UPDATE users SET image='$imageName' WHERE id=$user_id");

    // update session
    $_SESSION['user_image'] = $imageName;

    header("Location: profile.php");
    exit;
}
?>
