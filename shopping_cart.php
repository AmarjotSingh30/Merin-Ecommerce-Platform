<?php
session_start();
require 'config.php';  // your DB connection

// ----------------------
// CONFIG: available coupons
// ----------------------
$available_coupons = [
    "SAVE10"  => ["type" => "percent", "value" => 10],
    "OFF20"   => ["type" => "percent", "value" => 20],
    "FLAT100" => ["type" => "flat",    "value" => 100],
];

// ensure session structures
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['applied_coupon'])) $_SESSION['applied_coupon'] = null;

// reference
$cart = &$_SESSION['cart'];

// ----------------------
// HANDLE POSTS: remove / update cart / apply coupon
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Remove item (sent by JS clicking X -> sets remove_index and submits)
    if (!empty($_POST['remove_index'])) {
        $rem_index = (int)$_POST['remove_index'];
        if (isset($cart[$rem_index])) {
            unset($cart[$rem_index]);
            $cart = array_values($cart); // reindex
        }
    }

    // Update quantities (submitted when user clicks update cart)
    if (isset($_POST['update_cart']) && !empty($_POST['qty']) && is_array($_POST['qty'])) {
        
        foreach ($_POST['qty'] as $index => $q) {
            $q = (int)$q;
            if ($q < 1) $q = 1;
            if (isset($cart[$index])) $cart[$index]['quantity'] = $q;
        }
    }

    // Apply coupon (stores applied coupon in session)
    if (isset($_POST['apply_coupon'])) {
        $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
        if ($code !== '' && array_key_exists($code, $available_coupons)) {
            $_SESSION['applied_coupon'] = $code;
            $coupon_message = "Coupon applied: {$code}";
        } else {
            $_SESSION['applied_coupon'] = null;
            $coupon_message = "Invalid coupon code!";
        }
    }
}

// ----------------------
// Helper: fetch product
// ----------------------
function fetchProduct($conn, $pid) {
    $qry = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
    $qry->execute([$pid]);
    return $qry->fetch(PDO::FETCH_ASSOC);
}

// ----------------------
// Build items and totals (server-side authoritative)
// ----------------------
$items = [];
$subtotal = 0.0;

foreach ($cart as $idx => $entry) {
    $prod = fetchProduct($conn, $entry['product_id']);
    if (!$prod) continue;
    $qty = max(1, (int)($entry['quantity'] ?? 1));
    $price = (float)$prod['price'];
    $item_total = $price * $qty;
    $subtotal += $item_total;

    $items[] = [
        'index' => $idx,
        'product_id' => $entry['product_id'],
        'name' => $prod['name'],
        'image' => $prod['product_image_1'],
        'price' => $price,
        'quantity' => $qty,
        'color' => $entry['color'] ?? '',
        'size' => $entry['size'] ?? '',
        'item_total' => $item_total
    ];
}

// coupon server-side calculation
$discount = 0.0;
$discount_text = "";
$applied_coupon = $_SESSION['applied_coupon'] ?? null;
if ($applied_coupon && isset($available_coupons[$applied_coupon])) {
    $c = $available_coupons[$applied_coupon];
    if ($c['type'] === 'percent') {
        $discount = ($subtotal * $c['value']) / 100;
        $discount_text = $c['value'] . "% OFF";
    } else { // flat
        $discount = $c['value'];
        $discount_text = "₹" . number_format($c['value'], 2) . " OFF";
    }
}
$total = max(0, $subtotal - $discount);

// SAVE VALUES FOR CHECKOUT PAGE
$_SESSION['subtotal'] = $subtotal;
$_SESSION['discount'] = $discount;
$_SESSION['total'] = $total;

// total items for cart icon badge
$total_items = 0;
foreach ($items as $it) $total_items += $it['quantity'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shopping_cart</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
          integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Circle background behind cart icon */
.cart-container {
    position: relative;
    display: inline-block;
}

.cart-icon {
    font-size: 19px;
}

.cart-badge {
    position: absolute;
    top: -6px;
    right: -7px;
    background: red; /* your theme color */
    color: #fff;
    padding: 1px 6px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
    line-height: 1.2;
}


    </style>
</head>
<body>
    <div class="shop-main">
        <!-- top bar omitted (same as your layout) -->
        <div class="top-bar">
            <div class="top-left">
                <p>Free shipping, 30-day return or refund guarantee.</p>
            </div>
            <div class="top-right">
                <a href="#">Gift Card</a>
                <a href="#">Track Order</a>
                <a href="#">Contact Us</a>
            </div>
        </div>

        <!-- navbar -->
        <nav class="navbar">
            <div class="logo"><img src="images/merin_nobg.png" alt="logo"></div>
            <div class="nav-links" id="navLinks">
                <ul>
                    <li><a href="#">HOME</a></li>
                    <li><a href="shop.html">SHOP</a></li>
                    <li><a href="#">BLOG</a></li>
                    <li><a href="#">PAGES</a></li>
                    <li><a href="#">CONTACT</a></li>
                </ul>
            </div>
            <div class="icons">
                <!-- <i class="fa-solid fa-magnifying-glass" id="icon_1"></i> -->
                 
               <a href="shopping_cart.php" style="color:inherit;text-decoration:none;">
    <div class="cart-container">
        <i class="fa-solid fa-cart-shopping cart-icon"></i>

        <?php if ($total_items > 0): ?>
            <span class="cart-badge"><?php echo $total_items; ?></span>
        <?php endif; ?>
    </div>
</a>


                            <!-- navbar-user -->
                            <?php include 'navbar_user.php'; ?>
                             <!-- navbar-user end -->

                <div class="hamburger" id="hamburger"><span></span><span></span><span></span></div>
            </div>
        </nav>
        <script>
            const hamburger = document.getElementById("hamburger");
            const navLinks = document.getElementById("navLinks");
            hamburger.addEventListener("click", () => navLinks.classList.toggle("active"));
        </script>

        <!-- shop box1 (same layout) -->
        <div class="shop-box1">
            <!-- <span class="shop-tag">Category: All Products</span> -->
            <h4 class="shop-title">Shopping Cart</h4>
            <div class="shop-trust"><i class="fa-solid fa-shield-check"></i> 100% Genuine Products</div>
            <p class="shop-breadcrumb"><a href="index.php">Home</a> <i class="fa-solid fa-angle-right"></i> <span>Shopping cart</span></p>
            <div class="shop-offer-banner">🔥 This Week: Extra 15% OFF on selected items</div>
        </div>

        <!-- shop_cart_content-box -->
        <div class="shop_cart_content-box">
            <div class="shop_cart_content-box-inbox1">
                <div class="shop_cart_content-box-inbox1-dbox1">
                    <p id="shop-cart-product">Product</p>
                    <p id="shop-cart-quantity">Quantity</p>
                    <p id="shop-cart-total">Total</p>
                </div>

                <?php if (empty($items)): ?>
                    <p style="padding:20px;">Your cart is empty.</p>
                <?php else: ?>
                    <!-- main form: update/remove -->
                    <form method="POST" id="cartForm">
                        <?php foreach ($items as $item): ?>
                            <div class="shop_cart_content-box-inbox1-dbox2" data-index="<?php echo $item['index']; ?>">
                                <div class="shop_cart_content-box-inbox1-dbox2-imgbox">
                                    <img src="images/<?php echo htmlspecialchars($item['image']); ?>">
                                </div>

                                <div class="shop_cart_content-box-inbox1-dbox2-textbox">
                                    <p class="dbox2-textbox-text1"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <!-- changed id to class for price element to avoid duplicate ids -->
                                    <p class="dbox2-price">$<?php echo number_format($item['price'], 2); ?></p>
                                    <?php if ($item['color'] !== '' || $item['size'] !== ''): ?>
                                        <p style="font-size:0.9em;color:#555;">
                                            <?php echo ($item['color'] !== '') ? "Color: " . htmlspecialchars($item['color']) : ''; ?>
                                            <?php echo ($item['size'] !== '') ? " Size: " . htmlspecialchars($item['size']) : ''; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <div class="shop_cart_content-box-inbox1-dbox2-quantity">
                                    <button class="minus" type="button">‹</button>
                                    <span class="qty-number"><?php echo $item['quantity']; ?></span>
                                    <button class="plus" type="button">›</button>

                                    <!-- Hidden input to send qty to server -->
                                    <input type="hidden" name="qty[<?php echo $item['index']; ?>]" class="qty-hidden" value="<?php echo $item['quantity']; ?>">
                                </div>

                                <div class="shop_cart_content-box-inbox1-dbox2-total">
                                    <p class="item-total">$<?php echo number_format($item['item_total'], 2); ?></p>
                                </div>

                                <div class="shop_cart_content-box-inbox1-dbox2-icon">
                                    <div class="background-circle remove-item" style="cursor:pointer;" data-index="<?php echo $item['index']; ?>">
                                        <i class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <button id="Continue_Shopping" type="button" onclick="window.location='shop.php'">Continue Shopping</button>
                        <button id="update_cart" type="submit" name="update_cart">update cart</button>

                        <input type="hidden" name="remove_index" id="remove_index" value="">
                    </form>
                <?php endif; ?>
            </div>

            <!-- inbox2 -->
            <div class="shop_cart_content-box-inbox2">
                <p id="Discount_codes">Discount codes</p>

                <!-- Coupon form: submits to same page -->
                <form method="POST" id="couponForm">
                    <input type="text" name="coupon_code" placeholder="coupon code" required value="<?php echo htmlspecialchars($applied_coupon ?? ''); ?>">
                    <button type="submit" name="apply_coupon">APPLY</button>
                </form>

                <?php if (!empty($coupon_message)): ?>
                    <p style="color:green;font-size:14px;margin-top:5px;"><?php echo htmlspecialchars($coupon_message); ?></p>
                <?php endif; ?>

                <div class="shop_cart_content-box-inbox2-dbox1">
                    <h4>CART TOTAL</h4>

                    <p id="subtotal">subtotal</p>
                    <p id="subtotal-price">$<?php echo number_format($subtotal, 2); ?></p>

                    <?php if ($discount > 0): ?>
                        <p style="margin-top:5px;">Discount</p>
                        <p style="color:green;">- $<?php echo number_format($discount, 2); ?> <?php echo $discount_text ? "({$discount_text})" : ""; ?></p>
                    <?php endif; ?>

                    <p id="total">total</p>
                    <p id="total-price">$<?php echo number_format($total, 2); ?></p>


                    
<a href="<?php echo isset($_SESSION['user_id']) ? 'checkout.php' : 'experiments_for_loginsignup/login.php'; ?>">
    <button id="proceed">Proceed to Checkout</button>
</a>







                    <!-- <a href="checkout.php"><button id="proceed">Proceed to checkout</button></a> -->
                </div>
            </div>
        </div>

        <!-- footer (unchanged) -->
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
                <div class="footer-inbox-dbox1">
                    <h3>Shopping</h3>
                    <p>Clothing Store</p>
                    <p>Men's Fashion</p>
                    <p>Accessories</p>
                    <p>Sale</p>
                </div>
                <div class="footer-inbox-dbox1">
                    <h3>Top Products</h3>
                    <p>Managed Website</p>
                    <p>Power Tools</p>
                    <p>Marketing Service</p>
                    <p>Best Deals</p>
                </div>
                <div class="footer-inbox-dbox1">
                    <h3>NEWSLETTER</h3>
                    <p>Be the first to know about new arrivals, look books, sales & promos!</p>
                    <div class="newsletter-box">
                        <input type="email" placeholder="Your email"><i class="fa-solid fa-envelope"></i>
                    </div>
                </div>
            </div>
            <p id="copyright">© Merin — All Rights Reserved</p>
        </div>
    </div>

<script>
// Attach plus/minus handlers for all rows
document.querySelectorAll(".shop_cart_content-box-inbox1-dbox2").forEach((row) => {
    const qtySpan = row.querySelector(".qty-number");
    const plus = row.querySelector(".plus");
    const minus = row.querySelector(".minus");
    const hiddenInput = row.querySelector(".qty-hidden");
    const priceEl = row.querySelector(".dbox2-price");
    const itemTotalEl = row.querySelector(".item-total");

    // parse initial values
    let count = parseInt(qtySpan.textContent) || 1;
    const unitPrice = parseFloat(priceEl.textContent.replace("$", ""));

    function setCount(newCount) {
        count = Math.max(1, newCount);
        qtySpan.textContent = count;
        if (hiddenInput) hiddenInput.value = count;
        // recalc item total
        const it = unitPrice * count;
        if (itemTotalEl) itemTotalEl.textContent = "$" + it.toFixed(2);
        updateTotals(); // update sidebar totals
    }

    plus.addEventListener("click", () => setCount(count + 1));
    minus.addEventListener("click", () => setCount(count - 1));
});

// Recalculate totals (client-side) including coupon
function updateTotals() {
    let subtotal = 0;
    document.querySelectorAll(".shop_cart_content-box-inbox1-dbox2").forEach((row) => {
        const price = parseFloat(row.querySelector(".dbox2-price").textContent.replace("$", ""));
        const qty = parseInt(row.querySelector(".qty-number").textContent) || 1;
        const totalBox = row.querySelector(".item-total");
        const itemTotal = price * qty;
        if (totalBox) totalBox.textContent = "$" + itemTotal.toFixed(2);
        subtotal += itemTotal;
    });

    // Get coupon info from the server-rendered data (we'll read applied coupon text present on page)
    // For simplicity, we determine coupon behavior via server-side values rendered into the DOM.
    
    //---------ERROR FIXING 
    let discount = 0;
    const appliedCoupon = "<?php echo $applied_coupon ? addslashes($applied_coupon) : ''; ?>";
    if (appliedCoupon) {
        // Find coupon configuration in a small JS map that mirrors server coupons.
        const coupons = {
            <?php
            // Render coupon JS map: type & value
            $first = true;
            foreach ($available_coupons as $code => $cdata) {
                if (!$first) echo ",";
                $first = false;
                echo "\n'" . addslashes($code) . "': {type: '" . addslashes($cdata['type']) . "', value: " . json_encode($cdata['value']) . "}";
            }
            ?>
        };
        const c = coupons[appliedCoupon];
        if (c) {
            if (c.type === 'percent') discount = (subtotal * c.value) / 100;
            else discount = c.value;
        }
    }
//------------ERROR FIXING


    // Update UI
    document.getElementById("subtotal-price").textContent = "$" + subtotal.toFixed(2);
    const totalAfter = Math.max(0, subtotal - discount);
    document.getElementById("total-price").textContent = "$" + totalAfter.toFixed(2);

    // If discount node exists, update it (server already printed discount when page loaded; but update for client-side changes)
    // We look for the discount paragraph by checking for green discount text; if not present, we add it dynamically.
    const discountNodes = document.querySelectorAll(".shop_cart_content-box-inbox2-dbox1 p");
    // (Keep it simple: we won't attempt to insert new markup; server already shows discount area when coupon applied.)
}

// initialize totals on load
updateTotals();

// Remove item handler (submits the main form)
document.querySelectorAll(".remove-item").forEach((el) => {
    el.addEventListener("click", () => {
        const idx = el.dataset.index;
        if (idx !== undefined) {
            document.getElementById("remove_index").value = idx;
            document.getElementById("cartForm").submit();
        }
    });
});
</script>
</body>
</html>
