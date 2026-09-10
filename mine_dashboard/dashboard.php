<?php

session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'vendor'])) {
    header("Location: ../experiments_for_loginsignup/login.php");
    exit;
}

/* -------------------------
   TOTAL PRODUCTS
--------------------------*/
$q1 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM product");
$totalProducts = mysqli_fetch_assoc($q1)['total'] ?? 0;

/* -------------------------
   TOTAL ORDERS
--------------------------*/
$q2 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders");
$totalOrders = mysqli_fetch_assoc($q2)['total'] ?? 0;

/* -------------------------
   TOTAL brands
--------------------------*/
$q2 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM brands");
$totalbrands = mysqli_fetch_assoc($q2)['total'] ?? 0;

/* -------------------------
   TOTAL categories
--------------------------*/
$q2 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM category");
$totalcategories = mysqli_fetch_assoc($q2)['total'] ?? 0;


$query = "SELECT order_id, customer_name, order_amount, payment_mode, payment_status
          FROM orders
          ORDER BY created_at DESC
          LIMIT 4";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("SQL Error: " . mysqli_error($conn));
}




?>




<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="container">

  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">

    <div class="logo">Merin</div>

   <div class="profile-box">
    <div class="profile-img">
        <img src="uploads/<?php echo htmlspecialchars($_SESSION['user_image']); ?>" alt="Profile">
    </div>

    <div class="profile-text">
        <h3><?php echo htmlspecialchars($_SESSION['user_name']); ?></h3>
        <p><?php echo htmlspecialchars(ucfirst($_SESSION['user_role'])); ?></p>
    </div>

</div>


    <div class="menu-title">Navigation</div>

<!-- Dashboard: Admin + Vendor -->
<div class="menu-item">
    <i class="fa-solid fa-chart-line"></i>
    <a href="dashboard.php">Dashboard</a>
</div>

<!-- Add Products: Admin + Vendor -->
<?php if (in_array($_SESSION['user_role'], ['admin', 'vendor'])): ?>
    <div class="menu-item">
        <i class="fa-solid fa-plus"></i>
        <a href="add_products.php">Add Products</a>
    </div>
<?php endif; ?>

<?php if (in_array($_SESSION['user_role'], ['admin', 'vendor'])): ?>
 <div class="menu-item">
        <i class="fa-solid fa-box"></i>
        <a href="view_products.php">Products</a>
    </div>
<?php endif; ?>



<!-- ADMIN ONLY ITEMS -->
<?php if ($_SESSION['user_role'] === 'admin'): ?>

    <div class="menu-item">
        <i class="fa-solid fa-shop"></i>
        <a href="vendors.php">Vendors</a>
    </div>

    <div class="menu-item">
        <i class="fa-solid fa-users"></i>
        <a href="users.php">Users</a>
    </div>

    <!-- <div class="menu-item">
        <i class="fa-solid fa-box"></i>
        <a href="view_products.php">Products</a>
    </div> -->


    <div class="menu-item">
        <i class="fa-solid fa-bag-shopping"></i>
        <a href="view_orders.php">Orders</a>
    </div>

<?php endif; ?>


  </div>

  <!-- RIGHT SECTION -->
  <div class="right-section">

    <div class="topbar">
      <i class="fa-solid fa-bars hamburger" id="hamburger"></i>

      <div class="search-box">
        <i class="fa-solid fa-search"></i>
         <input type="text" id="dashboardSearch" placeholder="Search…" autocomplete="off">
        <!-- <input type="text" placeholder="Search…"> -->
      </div>

     <script>
const USER_ROLE = "<?php echo trim($_SESSION['user_role']); ?>".toLowerCase();

document.getElementById("dashboardSearch").addEventListener("keydown", function(e) {
    if (e.key === "Enter") {
        const query = this.value.toLowerCase().trim();

        const pages = [
            { key: "add product", url: "add_products.php", roles: ["admin", "vendor"] },
            { key: "new product", url: "add_products.php", roles: ["admin", "vendor"] },

            { key: "products", url: "view_products.php", roles: ["admin", "vendor"] },
            { key: "product",  url: "view_products.php", roles: ["admin", "vendor"] },

            { key: "vendors", url: "vendors.php", roles: ["admin"] },
            { key: "vendor",  url: "vendors.php", roles: ["admin"] },

            { key: "users", url: "users.php", roles: ["admin"] },
            { key: "user",  url: "users.php", roles: ["admin"] },

            { key: "orders", url: "view_orders.php", roles: ["admin"] },
            { key: "order",  url: "view_orders.php", roles: ["admin"] },

            { key: "dashboard", url: "dashboard.php", roles: ["admin", "vendor"] }
        ];

        for (let page of pages) {
            if (query === page.key || query.includes(page.key)) {
                if (!page.roles.includes(USER_ROLE)) {
                    alert("You do not have permission to access this page.");
                    return;
                }
                window.location.href = page.url;
                return;
            }
        }

        alert("No matching page found");
    }
});
</script>




      <i class="fa-solid fa-moon theme-toggle" id="themeToggle"></i>

      <div class="profile-right">

        <div class="profile-right-img">
           <img src="uploads/<?php echo htmlspecialchars($_SESSION['user_image']); ?>" alt="Profile">
        </div>

        <i class="fa-solid fa-caret-down" id="dropdownToggle"></i>

        <ul class="dropdown" id="dropdownMenu">
        <a href="profile.php"><li>Profile Management</li></a>
          <a href="logout.php"><li>Logout</li></a>
        </ul>

      </div>
    </div>

    <!-- CARDS -->
    <div class="cards">
      <div class="card">
        <i class="fa-solid fa-box"></i>
        <h2>Total Products</h2>
        <p><?= $totalProducts ?></p>
      </div>

      <div class="card">
        <i class="fa-solid fa-bag-shopping"></i>
        <h2>Total Orders</h2>
        <p><?= $totalOrders ?></p>
      </div>

      <div class="card">
        <i class="fa-solid fa-font-awesome"></i>
        <h2>Brands</h2>
        <p><?= $totalbrands ?></p>
      </div>

      <div class="card">
        <i class="fa-solid fa-money-bill-wave"></i>
        <h2>Categories</h2>
        <p><?= $totalcategories ?></p>
      </div>
    </div>


    <!-- table -->
    <table class="order-status-table">
  <thead>
    <tr>
      <th>Client Name</th>
      <th>Order No</th>
      <th>Product Cost</th>
      <th>Payment Mode</th>
      <th>Payment Status</th>
    </tr>
  </thead>
  <tbody>
     <?php while($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?= htmlspecialchars($row['customer_name']) ?></td>
        <td>#<?= $row['order_id'] ?></td>
        <td>₹<?= number_format($row['order_amount'], 2) ?></td>
        <td><?= htmlspecialchars($row['payment_mode']) ?></td>
        <td>
          <button class="status <?= strtolower($row['payment_status']) ?>">
            <?= ucfirst($row['payment_status']) ?>
          </button>
        </td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

    <!-- table -->

    <div class="right-section-3-cards">
      <!-- here i need 3 boxes in one main box 1st box show messages design it ur way 2.box kinda portfolio slide 3rd box full functional todo list in which user can add and delete  -->
       <!-- 1. Messages Box -->
  <div class="card messages-box">
    <h3>Messages</h3>
    <ul class="messages-list">
      <li><strong>John:</strong> Hey! How are you?</li>
      <li><strong>Jane:</strong> Meeting at 4 PM.</li>
      <li><strong>Mike:</strong> Project files uploaded.</li>
    </ul>
  </div>

  <!-- 2. Portfolio Slide Box -->
  <div class="card portfolio-box">
    <h3>Portfolio</h3>
    <div class="portfolio-slider">
      <div class="slide active"><img src="https://picsum.photos/300/150?random=1" alt="1"></div>
      <div class="slide"><img src="https://picsum.photos/300/150?random=2" alt="2"></div>
      <div class="slide"><img src="https://picsum.photos/300/150?random=3" alt="3"></div>
    </div>
    <div class="slider-controls">
      <button id="prevSlide"><i class="fa-solid fa-angle-left"></i></button>
      <button id="nextSlide"><i class="fa-solid fa-angle-right"></i></button>
      <!-- <button id="prevSlide">&lt;</button>
      <button id="nextSlide">&gt;</button> -->
    </div>
  </div>

  <!-- 3. To-Do List Box -->
  <div class="card todo-box">
    <h3>To-Do List</h3>
    <div class="todo-input">
      <input type="text" id="todoInput" placeholder="Add new task...">
      <button id="addTodo">Add</button>
    </div>
    <ul id="todoList"></ul>
  </div>

    </div>

    <div class="right-section-visitor-box">
      <!-- here i need two boxes in one main box on left side show visitor name their country flag ratio on right side box a map  -->
       <!-- Left: Visitor List -->
  <div class="visitor-list-box">
    <h3>Visitors</h3>
    <ul class="visitor-list">
      <li><img src="https://flagcdn.com/us.svg" alt="USA"> John Doe</li>
      <li><img src="https://flagcdn.com/in.svg" alt="India"> Priya Singh</li>
      <li><img src="https://flagcdn.com/gb.svg" alt="UK"> Michael Smith</li>
      <li><img src="https://flagcdn.com/ca.svg" alt="Canada"> Sarah Lee</li>
      <li><img src="https://flagcdn.com/au.svg" alt="Australia"> Liam Brown</li>
    </ul>
  </div>

  <!-- Right: Map -->
  <div class="visitor-map-box">
    <h3>Visitor Map</h3>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d52918450.40025157!2d-161.85240697328845!3d35.94976132466603!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x54eab584e432360b%3A0x1c3bb99243deb742!2sUnited%20States!5e0!3m2!1sen!2sin!4v1765779009310!5m2!1sen!2sin" 
      width="600" 
      height="300" 
      style="border:0;" 
      allowfullscreen="" 
      loading="lazy" 
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
    <!-- <iframe
      src="https://www.google.com/maps/d/embed?mid=1RkJwPjzQO4a0HhT5xTg6B1g9hZc&hl=en"
      width="100%"
      height="300"
      style="border:0;"
      allowfullscreen=""
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade">
    </iframe> -->
  </div>

    </div>
    
    <div class="footer">
        <p style="color:white; text-align:center; padding-top:30px;">&copy;  Merin — All Rights Reserved</p>  
  </div>



</div>

<script src="script.js"></script>


</body>
</html>
