<!-- php code -->
<?php
session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "merin");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'vendor'])) {
    header("Location: ../experiments_for_loginsignup/login.php");
    exit;
}



// On form submit
if (isset($_POST['submit'])) {

    // Collect normal inputs
    $name = $_POST['name'];
    $product_title = $_POST['product_title'];
    $category_id = $_POST['category_id'];
    $product_type = $_POST['product_type'];
    $price = $_POST['price'];
    $total_stock = $_POST['total_stock'];
    $desc = $_POST['description'];
    $created_at = date("Y-m-d H:i:s");

    // Size values
    $S  = isset($_POST['S']) ? 1 : 0;
    $M  = isset($_POST['M']) ? 1 : 0;
    $L  = isset($_POST['L']) ? 1 : 0;
    $XL = isset($_POST['XL']) ? 1 : 0;
    $XXL = isset($_POST['XXL']) ? 1 : 0;

    // Color values
    $red = isset($_POST['red']) ? 1 : 0;
    $blue = isset($_POST['blue']) ? 1 : 0;
    $green = isset($_POST['green']) ? 1 : 0;
    $white = isset($_POST['white']) ? 1 : 0;
    $black = isset($_POST['black']) ? 1 : 0;
    $orange = isset($_POST['orange']) ? 1 : 0;

    // Handle image uploads
    $uploadDir = "uploads/";
    if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }

    function uploadImage($fileKey, $uploadDir) {
        if (!empty($_FILES[$fileKey]['name'])) {
            $fileName = time() . "_" . basename($_FILES[$fileKey]["name"]);
            $targetPath = $uploadDir . $fileName;
            move_uploaded_file($_FILES[$fileKey]["tmp_name"], $targetPath);
            return $fileName;
        }
        return "";
    }

    $product_image_1 = uploadImage("product_image_1", $uploadDir);
    $product_image_2 = uploadImage("product_image_2", $uploadDir);
    $product_image_3 = uploadImage("product_image_3", $uploadDir);

    // Insert product table
    $qry_products = mysqli_query($conn,
        "INSERT INTO product(name, category_id, price, product_image_1, product_image_2, product_image_3, 
        product_title, product_type, created_at, `desc`, total_stock)
        VALUES('$name','$category_id','$price','$product_image_1','$product_image_2','$product_image_3',
        '$product_title','$product_type','$created_at','$desc','$total_stock')"
    );

    // Get last inserted ID
    $id = mysqli_insert_id($conn);

    // Insert size table
    $qry_size = mysqli_query($conn,
        "INSERT INTO size(product_id, S, M, L, XL, XXL)
        VALUES('$id', '$S', '$M', '$L', '$XL', '$XXL')"
    );

    // Insert color table
    $qry_color = mysqli_query($conn,
        "INSERT INTO color(product_id, red, blue, green, white, black, orange)
        VALUES('$id', '$red', '$blue', '$green', '$white', '$black', '$orange')"
    );

    if ($qry_products && $qry_size && $qry_color) {
        echo "<script>alert('Product Added Successfully!');</script>";
    } else {
        echo "<script>alert('Error Occurred While Adding Product');</script>";
    }
}
?>


<!-- php code -->

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add products</title>
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

        
    <div class="add-product-container">
    <h2>Add New Product</h2>

    <form class="product-form" action="" method="POST" enctype="multipart/form-data">

        <!-- Product Name -->
        <div class="input-group">
            <label>Product Name</label>
            <input type="text" name="name" placeholder="Enter product name" required>
        </div>

        <!-- Product Title -->
        <div class="input-group">
            <label>Product Title</label>
            <input type="text" name="product_title" placeholder="Enter product title" required>
        </div>

        <!-- Category -->
        <div class="input-group">
            <label>Select Category</label>
            <select name="category_id" required>
                <option value="" selected disabled>Choose category</option>
                <option value="1">tshirt</option>
                <option value="2">shirt</option>
                <option value="3">jeans</option>
                <option value="4">shorts</option>
                <option value="5">joggers</option>
                <option value="6">coat</option>
                <option value="7">jacket</option>
                <option value="8">shoes</option>
                <option value="9">watches</option>
                <option value="10">perfumes</option>
                <option value="11">bags</option>
            </select>
        </div>

        <!-- Product Type -->
        <div class="input-group">
            <label>Product Type</label>
            <select name="product_type" required>
                <option selected disabled>Select type</option>
                <option>Men</option>
                <option>Women</option>
                <option>shoes</option>
                <option>watches</option>
                <option>perfumes</option>
                <option>bags</option>
            </select>
        </div>

        <!-- Price -->
        <div class="input-group">
            <label>Product Price ($)</label>
            <input type="number" name="price" min="0" oninput="this.value = Math.max(this.value, 0)" placeholder="0.00" required>
        </div>

        <!-- Stock -->
        <div class="input-group">
            <label>Total Stock</label>
            <input type="number" name="total_stock" min="0" oninput="this.value = Math.max(this.value, 0)" placeholder="Enter stock quantity" required>
        </div>

        <!-- Image Upload -->
        <div class="input-group">
            <label>Upload Product Images (3)</label>
            <div class="image-upload-wrapper">
                <input type="file" name="product_image_1" accept="image/*" required>
                <input type="file" name="product_image_2" accept="image/*" required>
                <input type="file" name="product_image_3" accept="image/*" required>
            </div>
        </div>

        <!-- size -->
         <!-- Sizes -->
            <div class="input-group">
                <label>Available Sizes</label>
                <div class="check-grid">
                    <!-- name error i guess -->
                    <label><input type="checkbox" name="S" value="1"> S</label>
                    <label><input type="checkbox" name="M" value="1"> M</label>
                    <label><input type="checkbox" name="L" value="1"> L</label>
                    <label><input type="checkbox" name="XL" value="1"> XL</label>
                    <label><input type="checkbox" name="XXL" value="1"> XXL</label>
                </div>
            </div>

         <!-- size -->

         <!-- color -->
          <!-- Colors -->
            <div class="input-group">
                <label>Available Colors</label>
                <div class="check-grid">
                    <!-- name error i guess -->
                    <label><input type="checkbox" name="red" value="1"> Red</label>
                    <label><input type="checkbox" name="blue" value="1"> Blue</label>
                    <label><input type="checkbox" name="green" value="1"> Green</label>
                    <label><input type="checkbox" name="white" value="1"> White</label>
                    <label><input type="checkbox" name="black" value="1"> Black</label>
                    <label><input type="checkbox" name="orange" value="1"> Orange</label>
                </div>
            </div>

          <!-- color -->

        <!-- Description -->
        <div class="input-group">
            <label>Product Description</label>
            <textarea name="description" rows="4" placeholder="Enter full product description" required></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" name="submit" class="add-product-btn">Add Product</button>

    </form>
</div>

   
    
    <div class="footer">
        <p style="color:white; text-align:center; padding-top:30px;">&copy;  Merin — All Rights Reserved</p>  
  </div>



</div>

<script src="script.js"></script>


</body>
</html>
