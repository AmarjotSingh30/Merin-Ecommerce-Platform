<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_image = $_SESSION['user_image'] ?? 'default.png';
?>

<style>
    /* Profile dropdown container */
.user-dropdown {
    position: relative;
    display: inline-block;
}

.user-btn {
    background: none;
    border: none;
    cursor: pointer;
}

.user-pic {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
}

/* Dropdown box */
.dropdown-menu {
    position: absolute;
    top: 50px;
    right: 0;
    background: #fff;
    width: 170px;
    border-radius: 8px;
    box-shadow: 0px 4px 14px rgba(0, 0, 0, 0.15);
    display: none;           /* hidden by default */
    padding: 10px 0;
    z-index: 1000;
}

.dropdown-menu a {
    display: block;
    padding: 10px 15px;
    font-size: 14px;
    color: #333;
    text-decoration: none;
}

.dropdown-menu a:hover {
    background: #f4f4f4;
}

</style>

<div class="nav-user">

    <?php if(isset($_SESSION['user_id'])): ?>

        <div class="user-dropdown">
            <button class="user-btn">
               <img src="/6_MONTHS_Training/e_commerce_project/uploads/<?php 
echo htmlspecialchars($_SESSION['user_image'] ?? 'default.png'); ?>" 
class="user-pic" alt="Profile">

            </button>

            <div class="dropdown-menu">
                <a href="profile.php">My Profile</a>
                <a href="users_orders.php">My Orders</a>
                <a href="wishlist.php">Wishlist</a>
                <a href="logout.php" class="logout">Logout</a>
            </div>
        </div>

    <?php else: ?>

        <a href="/6_MONTHS_Training/e_commerce_project/experiments_for_loginsignup/login.php">
            <i class="fa-regular fa-user" id="icon_1"></i>
        </a>

    <?php endif; ?>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    
    const btn = document.querySelector(".user-btn");
    const menu = document.querySelector(".dropdown-menu");

    if (btn) {
        btn.addEventListener("click", function (e) {
            e.stopPropagation(); 
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener("click", function () {
        if (menu) menu.style.display = "none";
    });

});
</script>

