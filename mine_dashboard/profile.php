<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: experiments_for_loginsignup/login.php");
    exit;
}

// Direct DB connection
try {
    $conn = new PDO("mysql:host=localhost;dbname=auth_system", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch logged-in user
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("<h3 style='color:red;'>User not found!</h3>");
}

// Navbar user image
$user_image = htmlspecialchars($user['image'] ?? 'default.png');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - <?php echo htmlspecialchars($user['name']); ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f6f8;
        margin: 0;
        padding: 0;
    }
    /* Navbar */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 50px;
        background-color: #6c7ae0;
        color: white;
    }
    .navbar .logo img { height: 40px; }
    .nav-user {
        position: relative;
    }
    .user-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
    }
    .user-pic {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
    }
    .dropdown-menu {
        position: absolute;
        top: 50px;
        right: 0;
        background: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-radius: 8px;
        display: none;
        flex-direction: column;
        width: 180px;
        z-index: 100;
    }
    .dropdown-menu a {
        padding: 12px 15px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s;
    }
    .dropdown-menu a:hover {
        background: #f0f0f0;
    }

    /* Profile card */
    .profile-container {
        max-width: 900px;
        margin: 50px auto;
        display: flex;
        gap: 40px;
        padding: 30px;
    }
    .profile-card {
        background: #fff;
        flex: 1;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        padding: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .profile-card img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #6c7ae0;
        margin-bottom: 20px;
    }
    .profile-card h2 {
        margin: 0;
        color: #333;
    }
    .profile-card p {
        color: #666;
        margin: 5px 0;
    }
    .profile-info {
        background: #fff;
        flex: 2;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        padding: 30px;
    }
    .profile-info h3 {
        margin-bottom: 20px;
        color: #6c7ae0;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child { border-bottom: none; }

    .edit-profile-btn{
    margin-top:18px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 18px;
    background:#6c7ae0;
    color:#fff;
    text-decoration:none;
    border-radius:20px;
    font-size:14px;
    font-weight:500;
    transition:0.3s ease;
}

.edit-profile-btn i{
    font-size:13px;
}

.edit-profile-btn:hover{
    background:#5a66c8;
    transform:translateY(-1px);
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

<!-- Navbar -->
<div class="navbar">
    <div class="logo">
        <a href="dashboard.php" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i> Go Back
    </a>
        <!-- <img src="images/logo.png" alt="Logo"> -->
    </div>
    <div class="nav-user">
        <button class="user-btn">
            <img src="uploads/<?php echo $user_image; ?>" alt="Profile" class="user-pic">
        </button>
        <div class="dropdown-menu">
            <a href="profile.php">My Profile</a>
            <!-- <a href="orders.php">My Orders</a> -->
            <!-- <a href="wishlist.php">Wishlist</a> -->
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>
</div>

<!-- Profile Section -->
<div class="profile-container">
    <div class="profile-card">
        <img src="uploads/<?php echo $user_image; ?>" alt="Profile Picture">
        <h2><?php echo htmlspecialchars($user['name']); ?></h2>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <p>Role: <?php echo htmlspecialchars($user['role']); ?></p>

        <a href="edit_profile.php" class="edit-profile-btn">
            <i class="fa-solid fa-pen"></i> Edit Profile
        </a>

    </div>

    <div class="profile-info">
        <h3>Account Details</h3>
        <div class="info-row"><span>Name</span><span><?php echo htmlspecialchars($user['name']); ?></span></div>
        <div class="info-row"><span>Email</span><span><?php echo htmlspecialchars($user['email']); ?></span></div>
        <div class="info-row"><span>Role</span><span><?php echo htmlspecialchars($user['role']); ?></span></div>
        <div class="info-row"><span>Verified</span><span><?php echo $user['verified'] ? 'Yes' : 'No'; ?></span></div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const userBtn = document.querySelector(".user-btn");
    const dropdown = document.querySelector(".dropdown-menu");

    if(userBtn){
        userBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === "flex" ? "none" : "flex";
            dropdown.style.flexDirection = "column";
        });

        document.addEventListener("click", () => {
            dropdown.style.display = "none";
        });

        dropdown.addEventListener("click", (e) => {
            e.stopPropagation();
        });
    }
});
</script>

</body>
</html>
