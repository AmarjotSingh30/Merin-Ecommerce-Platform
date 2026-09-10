<?php
session_start();
require 'config.php';


// Bring values from cart page
$applied_coupon = $_SESSION['applied_coupon'] ?? null;
$discount_from_cart = $_SESSION['discount'] ?? 0;
$total_from_cart = $_SESSION['total'] ?? 0;
$subtotal_from_cart = $_SESSION['subtotal'] ?? 0;

// -------------------------------------
// 1. FETCH USER DATA (PDO VERSION)
// -------------------------------------
$userData = [
    "name" => "",
    "first_name" => "",
    "last_name" => "",
    "address" => "",
    "city" => "",
    "state" => "",
    "zip_code" => "",
    "phone" => "",
    "email" => ""
];

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];  // correct variable

    // correct query
    $stmt = $conn->prepare("
        SELECT name,first_name, last_name, address, city, state, zip_code, phone, email
        FROM auth_system.users
        WHERE id = ?
    ");

    $stmt->execute([$uid]);   // correct execution variable

    $userData = $stmt->fetch(PDO::FETCH_ASSOC) ?: $userData;
}



// FIX: use REFERENCE so updated values come from shopping_cart.php
$cart = &$_SESSION['cart'];

// Use values from shopping_cart.php
$subtotal = $_SESSION['subtotal'] ?? 0;
$discount = $_SESSION['discount'] ?? 0;
$total_amount = $_SESSION['total'] ?? 0;

$coupon_applied = $_SESSION['applied_coupon'] ? 1 : 0;


// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {

    if (empty($cart)) die("Cart is empty.");

    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $zip_code = $_POST['zip_code'] ?? '';
    $payment_mode = $_POST['payment_mode'] ?? '';

    $customer_name = trim($first_name . " " . $last_name);
    $is_coupon = $coupon_applied ? 1 : 0;

    // Insert Order
    $stmt = $conn->prepare("INSERT INTO orders 
        (customer_name,email,phone,address,zip_code,order_amount,payment_mode,
         order_status,payment_status,created_at,is_coupon)
         VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', 'Pending', NOW(), ?)");
    $stmt->execute([
        $customer_name, $email, $phone, $address,
        $zip_code, $total_amount, $payment_mode, $is_coupon
    ]);

    $order_id = $conn->lastInsertId();

    // Insert order details
    $stmt_details = $conn->prepare(
        "INSERT INTO order_details (order_id, product_id, product_amount, quantity) 
         VALUES (?, ?, ?, ?)"
    );


    // changed here for shopping_cart_icon
    foreach ($cart as $item) {
    // Fetch price from database
    // $stmt_price = $conn->prepare("SELECT price FROM product WHERE product_id = ?");

    // changes here 12-12-25 for porduct name from mini cart
    $stmt_price = $conn->prepare("SELECT name, price FROM product WHERE product_id = ?");
    // changes here 12-12-25
    $stmt_price->execute([$item['product_id']]);
    $product = $stmt_price->fetch(PDO::FETCH_ASSOC);
    $price = $product['price'] ?? 0; // fallback if product not found
    // changes here 12-12-25
    $product_name = $product['name'] ?? '';
    // changes here 12-12-25


    $stmt_details->execute([
        $order_id,
        $item['product_id'],
        $price,             // use fetched price
        $item['quantity']
    ]);
}
    // changed here for shopping cart icon

    // Redirect to confirm_order page
    header("Location: confirm_order.php?order_id=" . $order_id);
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="responsive.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
</head>
<body>
<div class="shop-main">

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-left"><p>Free shipping, 30-day return or refund guarantee.</p></div>
        <div class="top-right"><a href="#">Gift Card</a> <a href="#">Track Order</a> <a href="#">Contact Us</a></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo"><img src="images/merin_nobg.png" alt="logo"></div>
        <div class="nav-links" id="navLinks">
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="shop.php">SHOP</a></li>
                <li><a href="#">BLOG</a></li>
                <li><a href="#">PAGES</a></li>
                <li><a href="#">CONTACT</a></li>
            </ul>
        </div>
        <div class="icons">
            <!-- <i class="fa-solid fa-magnifying-glass"></i>
            <i class="fa-solid fa-cart-shopping"></i> -->


            <!-- navbar-user -->
                            <?php include 'navbar_user.php'; ?>
                             <!-- navbar-user end -->

</div>






            <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
        </div>
    </nav>

<script>
document.getElementById("hamburger").addEventListener("click", () => {
    document.getElementById("navLinks").classList.toggle("active");
});
</script>





<!-- Header -->
<div class="shop-box1">
    <!-- <span class="shop-tag">Category: All Products</span> -->
    <h4 class="shop-title">Checkout</h4>
    <p class="shop-breadcrumb"><a href="index.php">Home</a> <i class="fa-solid fa-angle-right"></i> <span>Checkout</span></p>
</div>

<!-- Checkout Content -->
<div class="checkout-box">

    <!-- LEFT -->
    <div class="checkout-left">
        <form method="POST">

        <div class="coupon-box">
            <p><i class="fa-solid fa-tag"></i>Forget to apply coupon?</p>
             <a href="shopping_cart.php">Click here to apply</a>
            <!-- <a href="javascript:void(0);" id="showCouponForm">Click here to apply</a> -->

            <div id="couponForm" style="display:none; margin-top:10px;">
                <input type="text" name="coupon_code" placeholder="Enter coupon code" value="<?= htmlspecialchars($coupon_code) ?>">
                <button type="submit" name="apply_coupon">APPLY</button>
                <?php if($message): ?>
                    <p style="color:red;margin-top:5px;"><?= htmlspecialchars($message) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <h3 class="section-title">Billing Details</h3>

        <div class="two-input">
            <div class="input-group"><label>First Name</label><input type="text" name="first_name" required value="<?php echo htmlspecialchars($userData['name']); ?>"></div>
            <div class="input-group"><label>Last Name</label><input type="text" name="last_name" required></div>
        </div>

        <div class="input-group"><label>Address</label><input type="text" name="address" required></div>
        <div class="input-group"><label>City</label><input type="text" name="city" required></div>
        <div class="input-group"><label>State</label><input type="text" name="state" required></div>
        <div class="input-group"><label>Postcode / ZIP</label><input type="text" name="zip_code" required></div>

        <div class="two-input">
            <div class="input-group"><label>Phone</label><input type="text" name="phone" required></div>
            <div class="input-group"><label>Email</label><input type="email" name="email" required value="<?php echo htmlspecialchars($userData['email']); ?>"></div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="checkout-right">
        <h3 class="order-title">YOUR ORDER</h3>
        <div class="divider"></div>


        <!-- changed here for shopping cart icon -->
        <div class="order-items">
    <?php foreach($cart as $item): ?>
        <?php
            // Fetch name + price from DB
            $stmt = $conn->prepare("SELECT name, price FROM product WHERE product_id = ?");
            $stmt->execute([$item['product_id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            $product_name = $product['name'] ?? 'Product';
            $price = $product['price'] ?? 0;
        ?>
        <div class="row">
            <p><?= htmlspecialchars($product_name) ?> x <?= $item['quantity'] ?></p>
            <span>$<?= number_format($item['quantity'] * $price, 2) ?></span>
        </div>
    <?php endforeach; ?>
</div>


        <!-- shopping_cart icon -->
        <div class="divider"></div>
        <div class="order-total">

             <div class="row">
        <p>Subtotal</p>
        <span>$<?= number_format($_SESSION['subtotal'] ?? 0, 2) ?></span>
    </div>

    <?php if (!empty($_SESSION['applied_coupon'])): ?>
    <div class="row">
        <p>Discount</p>
        <span style="color:green;">-$<?= number_format($_SESSION['discount'] ?? 0, 2) ?></span>
    </div>
    <?php endif; ?>

    <div class="row total-bold">
        <p>Total</p>
        <span>$<?= number_format($_SESSION['total'] ?? 0, 2) ?></span>
    </div>


        </div>

        <div class="divider"></div>

        <div class="payment-box">
            <label><input type="radio" name="payment_mode" value="Paypal" required> Cash On delivery</label>
            <!-- <label><input type="radio" name="payment_mode" value="Paypal" required> Cash On delivery</label> -->


                    <!-- <p>pay with trust</p> -->
                </div>
                <button type="submit" name="place_order" class="place-order">Place Order</button>
            </form>
            <?php if(isset($success_message)): ?>
                <p style="color:green; font-weight:bold; margin-top:10px;"><?= htmlspecialchars($success_message) ?></p>
            <?php endif; ?>
        </div>

    </div>

    <!-- Footer (unchanged) -->
    <div class="footer">
        <div class="footer-inbox1">
            <div class="footer-inbox-dbox1">
                <div class="footer-social">
                    <i class="fa-brands fa-instagram"></i>
                    <i class="fa-brands fa-facebook-f"></i>
                    <i class="fa-brands fa-x-twitter"></i>
                </div>
                <h4>Merin <span id="dot"></span></h4>
                <p>The customer is at the heart of our unique business model, which includes design.</p>
                <div class="card-img-box"><img src="images/payment.png" alt="Payment Methods"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("showCouponForm").addEventListener("click", function() {
    var form = document.getElementById("couponForm");
    form.style.display = (form.style.display === "none") ? "block" : "none";
});
</script>
</body>
</html>

<!-- orders(order_id,customer_name,email,phone,address,zip_code,order_amount,payment_mode,txn_id,order_status,
payment_status,created_at,updated_at,is_coupon)

order_details(id,order_id,product_id,product_amount,quantity) -->