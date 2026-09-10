<?php
session_start();

/* ---------------------------
   AUTH CHECK
---------------------------- */
if (!isset($_SESSION['user_id'])) {
    header("Location: experiments_for_loginsignup/login.php");
    exit;
}

/* ---------------------------
   DB CONNECTION (PDO)
---------------------------- */
try {
    $conn = new PDO("mysql:host=localhost;dbname=merin", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

/* ---------------------------
   FETCH USER
---------------------------- */
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found");
}

/* ---------------------------
   UPDATE PROFILE
---------------------------- */
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    /* IMAGE UPLOAD */
    $image_name = $user['image'];

    if (!empty($_FILES['image']['name'])) {

        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_image = uniqid("user_") . "." . $ext;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $new_image)) {
            $image_name = $new_image;
            $_SESSION['user_image'] = $image_name;
        }
    }

    /* UPDATE DB */
    $update = $conn->prepare(
        "UPDATE users SET name = ?, email = ?, image = ? WHERE id = ?"
    );
    $update->execute([$name, $email, $image_name, $user_id]);

    $_SESSION['user_name'] = $name;
    $success = "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

<style>
body{
    font-family:Arial, sans-serif;
    background:#f4f6f8;
    margin:0;
}
.edit-container{
    max-width:600px;
    margin:60px auto;
    background:#fff;
    padding:30px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}
.edit-container h2{
    margin-bottom:20px;
    color:#6c7ae0;
}
.form-group{
    margin-bottom:18px;
}
.form-group label{
    display:block;
    font-size:14px;
    margin-bottom:6px;
    color:#555;
}
.form-group input{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #ddd;
    outline:none;
}
.form-group input:focus{
    border-color:#6c7ae0;
}
.save-btn{
    margin-top:10px;
    padding:10px 25px;
    background:#6c7ae0;
    border:none;
    color:#fff;
    border-radius:25px;
    cursor:pointer;
    font-size:14px;
}
.save-btn:hover{
    background:#5a66c8;
}
.profile-preview{
    text-align:center;
    margin-bottom:20px;
}
.profile-preview img{
    width:90px;
    height:90px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #6c7ae0;
}
.msg-success{
    color:green;
    margin-bottom:15px;
}

.back-btn{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:10px 22px;
    border-radius:25px;
    background:#f0f0f0;
    color:#555;
    text-decoration:none;
    font-size:14px;
    border:1px solid #ddd;
    transition:0.2s ease;
}

.back-btn:hover{
    background:#e4e4e4;
    color:#333;
}

</style>
</head>

<body>

<div class="edit-container">

    <h2>Edit Profile</h2>

    <?php if($success): ?>
        <p class="msg-success"><?= $success ?></p>
    <?php endif; ?>

    <div class="profile-preview">
        <img src="uploads/<?= htmlspecialchars($user['image'] ?? 'default.png') ?>">
    </div>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="form-group">
            <label>Profile Image</label>
            <input type="file" name="image">
        </div>

        <button type="submit" class="save-btn">
            <i class="fa-solid fa-check"></i> Save Changes
        </button>

         <a href="profile.php" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i> Go Back
    </a>

    </form>

</div>

</body>
</html>
