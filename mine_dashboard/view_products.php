<!-- php code here -->
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'vendor'])) {
    header("Location: ../experiments_for_loginsignup/login.php");
    exit;
}


$sql = "SELECT * FROM product WHERE is_deleted=0";
$result = mysqli_query($conn, $sql);
?>

<!-- php code here -->


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>view products</title>
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

   <!-- Dashboard (Admin + Vendor) -->
<div class="menu-item">
    <i class="fa-solid fa-chart-line"></i>
    <a href="dashboard.php">Dashboard</a>
</div>

<!-- Add Products (Admin + Vendor) -->
<?php if (in_array($_SESSION['user_role'], ['admin', 'vendor'])): ?>
    <div class="menu-item">
        <i class="fa-solid fa-plus"></i>
        <a href="add_products.php">Add Products</a>
    </div>
<?php endif; ?>

<!-- Products (Admin + Vendor) -->
<?php if (in_array($_SESSION['user_role'], ['admin', 'vendor'])): ?>
    <div class="menu-item">
        <i class="fa-solid fa-box"></i>
        <a href="view_products.php">Products</a>
    </div>
<?php endif; ?>

<!-- ADMIN ONLY -->
<?php if ($_SESSION['user_role'] === 'admin'): ?>

    <div class="menu-item">
        <i class="fa-solid fa-shop"></i>
        <a href="vendors.php">Vendors</a>
    </div>

    <div class="menu-item">
        <i class="fa-solid fa-users"></i>
        <a href="users.php">Users</a>
    </div>

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

    <!-- table-->
     <!-- add php here and make this form look better  -->
    <div class="table-container">
      <h2>Product List</h2>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Title</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>

          <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?= $row['product_id']; ?></td>

          <td>
            <img src="uploads/<?= $row['product_image_1']; ?>" 
                 style="width:60px; height:60px; object-fit:cover; border-radius:8px;">
          </td>

          <td><?= $row['name']; ?></td>
          <td><?= $row['product_title']; ?></td>
          <td>
                <?= $row['desc']; ?>

          </td>
          <td>$<?= $row['price']; ?></td>
          <td><?= $row['total_stock']; ?></td>

          <td>

             <div class="action-btns">

<?php if ($_SESSION['user_role'] === 'admin'): ?>

    <!-- ADMIN: Edit + Delete -->
    <a href="edit_products.php?edit=<?= $row['product_id']; ?>" class="edit-btn">
        <i class="fa-solid fa-pen-to-square"></i>
    </a> 

    <a href="delete_products.php?delete=<?= $row['product_id']; ?>" 
       class="delete-btn"
       onclick="return confirm('Are you sure you want to delete this product?');">
        <i class="fa-solid fa-trash"></i>
    </a>

<?php else: ?>

    <!-- VENDOR: Restricted -->
    <span class="restricted-text">
        <i class="fa-solid fa-lock"></i> Restricted
    </span>

<?php endif; ?>

</div>


          </td>
        </tr>
      <?php } ?>


          <!-- More product rows can be added here -->
        </tbody>
      </table>
    </div>
    <!-- table -->
    
    
    <div class="footer">
        <p style="color:white; text-align:center; padding-top:30px;">&copy;  Merin — All Rights Reserved</p>  
  </div>



</div>

<script src="script.js"></script>


</body>
</html>
