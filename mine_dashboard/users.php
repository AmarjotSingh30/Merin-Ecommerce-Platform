<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "merin");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'vendor'])) {
    header("Location: ../experiments_for_loginsignup/login.php");
    exit;
}

/* -------------------------
   ACTIVATE / DEACTIVATE CUSTOMER (ADMIN ONLY)
--------------------------*/
if (isset($_GET['toggle_user']) && $_SESSION['user_role'] === 'admin') {

    $user_id = (int)$_GET['toggle_user'];

    // Get current status
    $check = mysqli_query($conn, "
        SELECT status FROM users 
        WHERE id=$user_id AND role='customer'
    ");

    if ($row = mysqli_fetch_assoc($check)) {

        $newStatus = ($row['status'] === 'active') ? 'inactive' : 'active';

        mysqli_query($conn, "
            UPDATE users 
            SET status='$newStatus' 
            WHERE id=$user_id AND role='customer'
        ");

        $_SESSION['flash_message'] = "Customer " . ucfirst($newStatus) . " successfully";
        $_SESSION['flash_type'] = ($newStatus === 'active') ? 'success' : 'danger';
    }

    header("Location: users.php");
    exit;
}

/* -------------------------
   FETCH CUSTOMERS
--------------------------*/
$users = mysqli_query($conn, "
    SELECT id, name, email, phone, image, status 
    FROM users 
    WHERE role='customer'
    ORDER BY id ASC
");

if (!$users) {
    die("User Query Failed: " . mysqli_error($conn));
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<style>

/* .section-title{
  margin:20px 0;
  font-size:20px;
  font-weight:600;
}

.order-status-table{
  width:100%;
  border-collapse:collapse;
  background:#fff;
}

.order-status-table th,
.order-status-table td{
  padding:12px;
  border-bottom:1px solid #eee;
  text-align:left;
} */

.status.active{
  background-color: white;
  color:green;
  font-weight:600;
}

.status.inactive{
    background-color: white;
  color:red;
  font-weight:600;
}

/* .btn{
  padding:6px 12px;
  border-radius:6px;
  text-decoration:none;
  font-size:13px;
} */

/* .btn.success{
  text-decoration: none;
  background:#28a745;
  color:white;
}

.btn.danger{
  background:#dc3545;
  color:white;
} */

/* Toast Notification */
.toast {
  position: fixed;
  top: 25px;
  right: 25px;
  background: #fff;
  padding: 15px 22px;
  border-radius: 10px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  font-size: 15px;
  z-index: 9999;
  animation: slideIn 0.5s ease;
}

.toast.success {
  border-left: 5px solid #28a745;
}

.toast.danger {
  border-left: 5px solid #dc3545;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}


</style>
<body>

    
<?php if (isset($_SESSION['flash_message'])): ?>
<div class="toast <?= $_SESSION['flash_type']; ?>">
    <?= $_SESSION['flash_message']; ?>
</div>
<?php
unset($_SESSION['flash_message']);
unset($_SESSION['flash_type']);
endif;
?>

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

    <div class="menu-item"><i class="fa-solid fa-chart-line"></i><a href="dashboard.php">Dashboard</a></div>
    <div class="menu-item"><i class="fa-solid fa-plus"></i><a href="add_products.php">Add Products</a></div>
    <div class="menu-item"><i class="fa-solid fa-box"></i><a href="view_products.php">Products</a></div>
    <div class="menu-item"><i class="fa-solid fa-shop"></i><a href="vendors.php">Vendors</a></div>
    <div class="menu-item"><i class="fa-solid fa-users"></i><a href="users.php">Users</a></div>
    <div class="menu-item"><i class="fa-solid fa-bag-shopping"></i><a href="view_orders.php">Orders</a></div>

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

    <!-- VENDORS TABLE -->
<div class="table-container">

  <h2>Users List</h2>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Email</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
    <?php while ($v = mysqli_fetch_assoc($users)) { ?>
      <tr>

        <td><?= $v['id']; ?></td>

        <td>
          <img src="uploads/<?= htmlspecialchars($v['image'] ?? 'default.png'); ?>"
               style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
        </td>

        <td><?= htmlspecialchars($v['name']); ?></td>
        <td><?= htmlspecialchars($v['email']); ?></td>

        <td>
          <span class="status <?= strtolower($v['status']); ?>">
            <?= ucfirst($v['status']); ?>
          </span>
        </td>

        <td>
          <div class="action-btns">

            <?php if ($_SESSION['user_role'] === 'admin'): ?>
              <a href="?toggle_user=<?= $v['id']; ?>"
                 class="<?= $v['status'] === 'active' ? 'delete-btn' : 'edit-btn'; ?>">
                <?= $v['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
              </a>
            <?php else: ?>
              —
            <?php endif; ?>

          </div>
        </td>

      </tr>
    <?php } ?>
    </tbody>

  </table>
</div>
<!-- VENDORS TABLE -->

    
    <div class="footer">
        <p style="color:white; text-align:center; padding-top:30px;">&copy;  Merin — All Rights Reserved</p>  
  </div>



</div>

<script src="script.js"></script>
<script>
    setTimeout(() => {
  const toast = document.querySelector('.toast');
  if (toast) toast.remove();
}, 3000);
</script>
</body>
</html>
