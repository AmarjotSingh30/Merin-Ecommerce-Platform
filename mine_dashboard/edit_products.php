<!-- php code -->
<?php
session_start();
// include "db.php";
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "merin";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

  $id=$_GET['edit'];
  $qry_edit=mysqli_query($conn,"SELECT product.*, size.S,size.M,size.L,size.XL,size.XXL,color.red,color.blue,color.green,color.white,color.black,color.orange FROM product JOIN size ON product.product_id=size.product_id JOIN color ON product.product_id=color.product_id WHERE product.product_id='$id'");
    $row_edit=mysqli_fetch_assoc($qry_edit);
  if(isset($_POST['complete_edit'])){
    $name=$_POST['name'];
    $product_title=$_POST['product_title'];
    $category_id=$_POST['category_id'];
    $category_id=$_POST['category_id'];
    $product_type=$_POST['product_type'];
    $total_stock=$_POST['total_stock'];
    $price=$_POST['price'];
    $product_image_1=$_FILES['product_image_1']['name'];
    $product_image_2=$_FILES['product_image_2']['name'];
    $product_image_3=$_FILES['product_image_3']['name'];
    $tmp_name_1=$_FILES['product_image_1']['tmp_name'];
    $tmp_name_2=$_FILES['product_image_2']['tmp_name'];
    $tmp_name_3=$_FILES['product_image_3']['tmp_name'];
    $path_1="../images/".$product_image_1;
    $path_2="../images/".$product_image_2;
    $path_3="../images/".$product_image_3;
    $desc=$_POST['desc'];
    $S=(isset($_POST['S'])) ? 'S' : 0;
    $M=(isset($_POST['M'])) ? 'M' : 0;
    $L=(isset($_POST['L'])) ? 'L' : 0;
    $XL=(isset($_POST['XL'])) ? 'XL' : 0;
    $XXL=(isset($_POST['XXL'])) ? 'XXL' : 0;
    $red=(isset($_POST['Red'])) ? 'Red' : 0;
    $blue=(isset($_POST['Blue'])) ? 'Blue' : 0;
    $green=(isset($_POST['Green'])) ? 'Green' : 0;
    $white=(isset($_POST['White'])) ? 'White' : 0;
    $black=(isset($_POST['Black'])) ? 'Black' : 0;
    $orange=(isset($_POST['Orange'])) ? 'Orange' : 0;
    $updated_at=date('Y-m-d H:i:s');
    if(!empty($product_image_1)){
      move_uploaded_file($tmp_name_1,$path_1);
      $qry_products=mysqli_query($conn,"UPDATE product SET name='$name',category_id='$category_id',price='$price',product_image_1='$product_image_1',product_title='$product_title',product_type='$product_type',`desc`='$desc',total_stock='$total_stock',updated_at='$updated_at' WHERE product_id='$id'");
    }else{
      move_uploaded_file($tmp_name_1,$path_1);
      $qry_products=mysqli_query($conn,"UPDATE product SET name='$name',category_id='$category_id',price='$price',product_title='$product_title',product_type='$product_type',`desc`='$desc',total_stock='$total_stock',updated_at='$updated_at' WHERE product_id='$id'");
    }
    if(!empty($product_image_2)){
      move_uploaded_file($tmp_name_2,$path_2);
      $qry_products=mysqli_query($conn,"UPDATE product SET name='$name',category_id='$category_id',price='$price',product_image_2='$product_image_2',product_title='$product_title',product_type='$product_type',`desc`='$desc',total_stock='$total_stock',updated_at='$updated_at' WHERE product_id='$id'");
    }else{
      move_uploaded_file($tmp_name_2,$path_2);
      $qry_products=mysqli_query($conn,"UPDATE product SET name='$name',category_id='$category_id',price='$price',product_title='$product_title',product_type='$product_type',`desc`='$desc',total_stock='$total_stock',updated_at='$updated_at' WHERE product_id='$id'");
    }
    if(!empty($product_image_3)){
      move_uploaded_file($tmp_name_3,$path_3);
      $qry_products=mysqli_query($conn,"UPDATE product SET name='$name',category_id='$category_id',price='$price',product_image_3='$product_image_3',product_title='$product_title',product_type='$product_type',`desc`='$desc',total_stock='$total_stock',updated_at='$updated_at' WHERE product_id='$id'");
    }else{
      move_uploaded_file($tmp_name_3,$path_3);
      $qry_products=mysqli_query($conn,"UPDATE product SET name='$name',category_id='$category_id',price='$price',product_title='$product_title',product_type='$product_type',`desc`='$desc',total_stock='$total_stock',updated_at='$updated_at' WHERE product_id='$id'");
    }
    $qry_size=mysqli_query($conn,"UPDATE size SET S='$S',M='$M',L='$L',XL='$XL',XXL='$XXL' WHERE product_id='$id'");
    $qry_color=mysqli_query($conn,"UPDATE color SET red='$red',blue='$blue',green='$green',white='$white',black='$black',orange='$orange' WHERE product_id='$id'");
    if($qry_products){
      echo "<script>alert('PRODUCT INFORMATION CHANGED SUCCESSFULLY!')
        window.location.href='view_products.php';
      </script>";
    }else{
      echo "<script>alert('ERRORRR!')</script>";
    }
  }
?>
<!-- php code -->

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>update products</title>
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

    <div class="menu-item"><i class="fa-solid fa-chart-line"></i><a href="dashboard.php">Dashboard</a></div>
    <div class="menu-item"><i class="fa-solid fa-shop"></i><a href="#">Vendors</a></div>
    <div class="menu-item"><i class="fa-solid fa-users"></i><a href="#">Users</a></div>
    <div class="menu-item"><i class="fa-solid fa-plus"></i><a href="add_products.php">Add Products</a></div>
    <div class="menu-item"><i class="fa-solid fa-box"></i><a href="view_products.php">Products</a></div>
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

        
    <div class="add-product-container">
    <h2>Update Product</h2>

    <form class="product-form" action="" method="POST" enctype="multipart/form-data">

        <!-- Product Name -->
        <div class="input-group">
            <label>Product Name</label>
            <input type="text" name="name" placeholder="" required value="<?= $row_edit['name'] ?>">
        </div>

        <!-- Product Title -->
        <div class="input-group">
            <label>Product Title</label>
            <input type="text" name="product_title" placeholder="" required value="<?= $row_edit['product_title'] ?>">
        </div>

        <!-- Category -->
        <div class="input-group">
            <label>Select Category</label>
            <select name="category_id" required>
                <?php  
                  $qry_category=mysqli_query($conn,"SELECT * FROM category");
                    while($row=mysqli_fetch_assoc($qry_category)){
                        echo "<option value='".$row['category_id']."'>".$row['category_name']."</option>";
                    }
                ?>
                <!-- <option value="" selected disabled>Choose category</option>
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
                <option value="11">bags</option> -->
            </select>
        </div>

        <!-- Product Type -->
        <div class="input-group">
            <label>Product Type</label>
            <select name="product_type" required>
                <!-- php -->
                <!-- product type -->
                <option value="Men"<?php if($row_edit['product_type'] == 'Men'){
                        echo "selected=selected";
                    } ?> value='Men' >Men</option>
                                                
                <option value="Women"<?php if($row_edit['product_type'] == 'Women'){
                        echo "selected=selected";
                    } ?> value='Women' >Women</option>

                <option value="Shoes"<?php if($row_edit['product_type'] == 'Shoes'){
                        echo "selected=selected";
                    } ?> value='Shoes' >Shoes</option>

                <option value="Watches"<?php if($row_edit['product_type'] == 'Watches'){
                        echo "selected=selected";
                } ?> value='Watches' >Watches</option>

                <option value="Perfumes"<?php if($row_edit['product_type'] == 'Perfumes'){
                        echo "selected=selected";
                } ?> value='Perfumes' >Perfumes</option>

                <option value="Bags"<?php if($row_edit['product_type'] == 'Bags'){
                        echo "selected=selected";
                 } ?> value='Bags' >Bags</option>

                <!-- product type -->
                <!--php -->
            </select>
        </div>

        <!-- Price -->
        <div class="input-group">
            <label>Product Price ($)</label>
            <input type="number" name="price" min="0" oninput="this.value = Math.max(this.value, 0)" placeholder="" required value="<?= $row_edit['price'] ?>">
        </div>

        <!-- Stock -->
        <div class="input-group">
            <label>Total Stock</label>
            <input type="number" name="total_stock" min="0" oninput="this.value = Math.max(this.value, 0)" placeholder="" required value="<?= $row_edit['total_stock'] ?>">
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
                    <!-- php -->
                     <?php  
                        $qry_size=mysqli_query($conn,"SELECT S,M,L,XL,XXL FROM size WHERE product_id='$id'"); 
                            $row_size=mysqli_fetch_assoc($qry_size);
                     ?>
                    <!-- php -->

                    <label>
                        <?php 
                        if(!empty($row_size['S'])){
                            echo "<input type='checkbox' class='check_box' name='S' value='".$row_size['S']."' checked><b class='word'>S</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box' name='S' value='".$row_size['S']."'><b class='word'>S</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="sizes[]" value="S"> S -->
                    </label>

                    <label>
                        <?php 
                        if(!empty($row_size['M'])){
                            echo "<input type='checkbox' class='check_box' name='M' value='".$row_size['M']."' checked><b class='word'>M</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box' name='M' value='".$row_size['M']."'><b class='word'>M</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="sizes[]" value="M"> M -->
                    </label>

                    <label>
                        <?php 
                        if(!empty($row_size['L'])){
                            echo "<input type='checkbox' class='check_box' name='L' value='".$row_size['L']."' checked><b class='word'>L</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box' name='L' value='".$row_size['L']."'><b class='word'>L</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="sizes[]" value="L"> L -->
                    </label>

                    <label>
                        <?php 
                       if(!empty($row_size['XL'])){
                            echo "<input type='checkbox' class='check_box' name='XL' value='".$row_size['XL']."' checked><b class='word'>XL</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box' name='XL' value='".$row_size['XL']."'><b class='word'>XL</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="sizes[]" value="XL"> XL -->
                    </label>

                    <label>
                        <?php 
                       if(!empty($row_size['XXL'])){
                            echo "<input type='checkbox' class='check_box' name='XXL' value='".$row_size['XXL']."' checked><b class='word'>XXL</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box' name='XXL' value='".$row_size['XXL']."'><b class='word'>XXL</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="sizes[]" value="XXL"> XXL -->
                    </label>

                </div>
            </div>

         <!-- size -->

         <!-- color -->
          <!-- Colors -->
            <div class="input-group">
                <label>Available Colors</label>
                <div class="check-grid">
                    <!-- php code -->
                        <?php  
                         $qry_color=mysqli_query($conn,"SELECT red,blue,green,white,black,orange FROM color WHERE product_id='$id'"); 
                        $row=mysqli_fetch_assoc($qry_color);
                        ?>
                    <!-- php code -->

                    <label>
                        <?php
                        if(!empty($row['red'])){
                            echo "<input type='checkbox' class='check_box-1' name='Red' value='".$row['red']."' checked> <b class='word'>Red</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box-1' name='Red' value='".$row['red']."'> <b class='word'>Red</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="colors[]" value="Red"> Red -->
                    </label>

                    <label>
                        <?php
                        if(!empty($row['blue'])){
                            echo "<input type='checkbox' class='check_box-2' name='Blue' value='".$row['blue']."' checked> <b class='word'>Blue</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box-2' name='Blue' value='".$row['blue']."'> <b class='word'>Blue</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="colors[]" value="Blue"> Blue -->
                    </label>

                    <label>
                        <?php
                        if(!empty($row['green'])){
                            echo "<input type='checkbox' class='check_box-3' name='Green' value='".$row['green']."' checked> <b class='word'>Green</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box-3' name='Green' value='".$row['green']."'> <b class='word'>Green</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="colors[]" value="Green"> Green -->
                    </label>

                    <label>
                        <?php
                        if(!empty($row['white'])){
                            echo "<input type='checkbox' class='check_box-4' name='White' value='".$row['white']."' checked> <b class='word'>White</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box-4' name='White' value='".$row['white']."'> <b class='word'>White</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="colors[]" value="White"> White -->
                    </label>

                    <label>
                        <?php
                        if(!empty($row['black'])){
                            echo "<input type='checkbox' class='check_box-5' name='Black' value='".$row['black']."' checked> <b class='word'>Black</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box-5' name='Black' value='".$row['black']."'> <b class='word'>Black</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="colors[]" value="Black"> Black -->
                    </label>

                    <label>
                        <?php
                        if(!empty($row['orange'])){
                            echo "<input type='checkbox' class='check_box-6' name='Orange' value='".$row['orange']."' checked> <b class='word'>Orange</b>";
                        }else{
                            echo "<input type='checkbox' class='check_box-6' name='Orange' value='".$row['orange']."'> <b class='word'>Orange</b>";
                        }
                        ?>
                        <!-- <input type="checkbox" name="colors[]" value="Orange"> Orange -->
                    </label>

                </div>
            </div>

          <!-- color -->

        <!-- Description -->
        <div class="input-group">
            <label>Product Description</label>
            <textarea name="desc" rows="4" placeholder="Enter full product description" required><?= $row_edit['desc'] ?></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" name="complete_edit" class="add-product-btn">Confirm changes</button>

    </form>
</div>

   
    
    <div class="footer">
        <p style="color:white; text-align:center; padding-top:30px;">&copy;  Merin — All Rights Reserved</p>  
  </div>



</div>

<script src="script.js"></script>


</body>
</html>
