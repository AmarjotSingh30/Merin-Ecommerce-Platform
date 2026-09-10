<?php
session_start();
require 'config.php';

// Validate product ID
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) die('Invalid product id');

// Fetch product
$qry = $conn->prepare("SELECT p.*, c.category_name AS category_name FROM product AS p 
                       JOIN category AS c ON p.category_id = c.category_id 
                       WHERE p.product_id = ?");
$qry->execute([$id]);
$product = $qry->fetch(PDO::FETCH_ASSOC);
if (!$product) die('Product not found');

// Server-side product price
$product_price = isset($product['price']) ? (float)$product['price'] : 0.0;

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity   = max(1, (int)($_POST['quantity'] ?? 1));
    $color      = trim($_POST['color'] ?? '');
    $size       = trim($_POST['size'] ?? '');
    $price      = $product_price; // server-side authoritative price
    $name       = $product['name']; // product name

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    // If same product+options exists, increment quantity
    $found = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['product_id'] === $product_id && $cartItem['color'] === $color && $cartItem['size'] === $size) {
            $cartItem['quantity'] += $quantity;
            $cartItem['price'] = $price; // update price if changed
            $found = true;
            break;
        }
    }
    unset($cartItem);

    if (!$found) {
        $_SESSION['cart'][] = [
            'product_id' => $product_id,
            'name'       => $name,
            'quantity'   => $quantity,
            'color'      => $color,
            'size'       => $size,
            'price'      => $price
        ];
    }

    header("Location: shopping_cart.php");
    exit();
}

// Cart count for header
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>product_details</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .color-swatch {
            display: inline-block;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1px solid #ccc;
            cursor: pointer;
            margin-right: 5px;
            vertical-align: middle;
        }
        .color-swatch:hover {
            border: 2px solid #000;
        }
        .color-swatch.active { outline: 2px solid #333; transform: scale(1.05); }
        .size.active { background:#333; color:#fff; padding:4px 8px; border-radius:4px; cursor:pointer; margin-right:6px; display:inline-block; }
        .size { cursor:pointer; padding:4px 8px; border:1px solid #ccc; border-radius:4px; margin-right:6px; display:inline-block; }
        /* small badge for cart count */
        .cart-badge {
            display:inline-block;
            background:#e53935;
            color:#fff;
            font-size:12px;
            padding:2px 6px;
            border-radius:12px;
            vertical-align: top;
            margin-left:6px;
        }
    </style>
</head>
<body>
   <div class="shop-main">
        
         <!-- box 1 -->
        <!-- Top Info Bar -->
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

        <!-- box 1 -->

         <!-- navbar -->
        <nav class="navbar">
            <div class="logo">
                <img src="images/merin_nobg.png" alt="logo">
            </div>

                        <!-- links -->
                        <div class="nav-links" id="navLinks">
                            <ul>
                                <li><a href="index.php">HOME</a></li>
                                <li><a href="shop.php">SHOP</a></li>
                                <li><a href="#">BLOG</a></li>
                                <li><a href="#">PAGES</a></li>
                                <li><a href="#">CONTACT</a></li>
                            </ul>
                        </div>

                        <!-- icons -->
                        <div class="icons">

                           

                            <!-- Shopping Cart -->
                              <!-- MINI CART HEADER ICON -->
                            <div class="cart-wrapper">
                                <i class="fa-solid fa-cart-shopping" id="cartIcon"></i>
                                <span id="cart-count"><?php echo $cartCount ?? 0; ?></span>

                                <div class="cart-menu" id="cartMenu">
                                    <h3>Your Cart</h3>
                                    <div id="cart-items"></div>

                                    <div class="cart-total">
                                        <p>Total: ₹<span id="cart-total-price">0</span></p>
                                    </div>

                                    <a href="shopping_cart.php" class="checkout-btn">View Cart</a>
                                </div>
                            </div>

                            <!-- Shopping cart end -->

                            <!-- navbar-user -->
                            <?php include 'navbar_user.php'; ?>

                            <!-- <i class="fa-regular fa-user" id="icon_1"></i> -->
                             <!-- navbar-user end -->

                            <!-- hamburger -->
                            <div class="hamburger" id="hamburger">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </div>
                    </nav>
                    <!-- navbar -->   


         <div class="lux-product-viewer">

    <!-- Vertical Thumbnails -->
    <div class="lux-thumbs">
        <img src="images/<?php echo htmlspecialchars($product['product_image_1']) ?>" onmouseover="luxChange(this)" class="lux-thumb active">
        <img src="images/<?php echo htmlspecialchars($product['product_image_2']) ?>" onmouseover="luxChange(this)" class="lux-thumb">
        <img src="images/<?php echo htmlspecialchars($product['product_image_3']) ?>" onmouseover="luxChange(this)" class="lux-thumb">
        <img src="images/<?php echo htmlspecialchars($product['product_image_1']) ?>" onmouseover="luxChange(this)" class="lux-thumb">
    </div>

    <!-- Main Display -->
    <div class="lux-main-box">
        <img id="luxMainImg" src="images/<?php echo htmlspecialchars($product['product_image_1']) ?>" class="lux-main-img">
        <div class="lux-zoom"></div>
    </div>
    <script>
        function luxChange(el) {
            document.getElementById("luxMainImg").src = el.src;
            document.querySelectorAll(".lux-thumb").forEach(t => 
                t.classList.remove("active")
            );
            el.classList.add("active");
        }
    </script>
</div>



        <!-- product details box2 -->
            <div class="product_details_box2">
                <h3 id="product_name"><?php echo htmlspecialchars($product['name']) ?></h3>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i><p id="product_reviews"> - 5 reviews</p><br>
                <p id="final_price">$<?php echo htmlspecialchars(number_format($product_price, 2)) ?></p>
                <p id="final_discounted_price"><del>$<?php echo htmlspecialchars(number_format($product_price + 20, 2)) ?></del></p>
                <p id="product_description"> <?php echo htmlspecialchars($product['product_title']) ?></p>

                <!--///// color and size selctor///// -->
                <div class="product-options">
                <!-- Color Selector -->
                <div class="color-options">
                    <h4>Color:<p id="selectedColor">Selected: None</p></h4>
                    <div class="colors">
                    <?php  
                        $qry_color = $conn->prepare("SELECT red, blue, green, white, black, orange FROM color WHERE product_id = ?");
                        $qry_color->execute([$product['product_id']]);
                        $row_color = $qry_color->fetch(PDO::FETCH_ASSOC);
                        if (!empty($row_color)) {
                            foreach ($row_color as $colorName => $colorValue) {
                                if (!empty($colorValue)) {
                                    // use data-color to store name/value; display as swatch
                                    $safeVal = htmlspecialchars($colorValue);
                                    echo "<span class='color-swatch' data-color='{$safeVal}' title='{$safeVal}' style='background: {$safeVal};'></span>";
                                }
                            }
                        }
                    ?>
                    </div>
                </div>

                <!-- Size Selector -->
                <div class="size-options">
                    <h4>Size:<p id="selectedSize">Selected: None</p></h4>
                    <div class="sizes">
                    <?php  
                        $qry_size = $conn->prepare("SELECT S, M, L, XL, XXL FROM size WHERE product_id = ?");
                        $qry_size->execute([$product['product_id']]);
                        $row_size = $qry_size->fetch(PDO::FETCH_ASSOC);

                        if(!empty($row_size['S'])  or !empty($row_size['M']) or !empty($row_size['L']) or !empty($row_size['XL']) or !empty($row_size['XXL'])){
                            foreach($row_size as $sz){
                                if(!empty($sz)){
                                    $safeSz = htmlspecialchars($sz);
                                    echo  "<span class='size' data-size='{$safeSz}'>".$safeSz."</span>";
                                }
                            }
                        } 
                    ?>
                    </div>
                </div>
                </div>
                <!-- quantity selector -->
                    <div class="shop_cart_content-box-inbox1-dbox2-quantity2">
                        <button class="qty-btn" id="minus" type="button">-</button>
                        <span id="quantity">1</span>
                        <button class="qty-btn" id="plus" type="button">+</button>
                    </div>
                <!-- quantity selector -->

                <form method="POST" id="addToCartForm">
                    <input type="hidden" name="product_id" value="<?php echo (int)$product['product_id']; ?>">
                    <input type="hidden" id="selectedColorInput" name="color" value="">
                    <input type="hidden" id="selectedSizeInput" name="size" value="">
                    <input type="hidden" id="quantityInput" name="quantity" value="1">
                    <!-- priceInput present for convenience, server ignores it and uses server-side price -->
                    <input type="hidden" id="priceInput" name="price" value="<?php echo htmlspecialchars(number_format($product_price,2,'.','')); ?>">

                    <button type="submit" name="add_to_cart" id="add_to_cart">ADD TO CART</button>
                </form>

                 <p id="product_categories">categories: <?php echo htmlspecialchars($product['category_name']) ?></p>
                 <p id="product_tag">stock: <?php echo htmlspecialchars($product['total_stock']) ?></p>

                <script>
                    // select proper node lists
                    const colorSwatches = document.querySelectorAll(".color-swatch");
                    const sizes = document.querySelectorAll(".size");
                    const selectedColorText = document.getElementById("selectedColor");
                    const selectedSizeText = document.getElementById("selectedSize");
                    const selectedColorInput = document.getElementById("selectedColorInput");
                    const selectedSizeInput = document.getElementById("selectedSizeInput");
                    const quantityInput = document.getElementById("quantityInput");
                    const priceInput = document.getElementById("priceInput");

                    // Fill priceInput on load (client-side copy only)
                    // value already set by server; this keeps it consistent if you want to display or send it.
                    // Color selection
                    colorSwatches.forEach(swatch => {
                        swatch.addEventListener("click", () => {
                            colorSwatches.forEach(s => s.classList.remove("active"));
                            swatch.classList.add("active");
                            const colorVal = swatch.dataset.color || swatch.getAttribute('title') || '';
                            selectedColorText.textContent = "Selected: " + colorVal;
                            selectedColorInput.value = colorVal;
                        });
                    });

                    // Size selection
                    sizes.forEach(sz => {
                        sz.addEventListener("click", () => {
                            sizes.forEach(s => s.classList.remove("active"));
                            sz.classList.add("active");
                            const sizeVal = sz.dataset.size || sz.textContent;
                            selectedSizeText.textContent = "Selected: " + sizeVal;
                            selectedSizeInput.value = sizeVal;
                        });
                    });

                    // quantity selector
                    let qty = 1;
                    const quantitySpan = document.getElementById("quantity");
                    document.getElementById("plus").addEventListener("click", () => {
                        qty++;
                        quantitySpan.innerText = qty;
                        quantityInput.value = qty;
                    });

                    document.getElementById("minus").addEventListener("click", () => {
                        if(qty > 1){
                            qty--;
                            quantitySpan.innerText = qty;
                            quantityInput.value = qty;
                        }
                    });

                    // Ensure hidden inputs are synced before submit and do simple validation
                    document.getElementById("addToCartForm").addEventListener("submit", function(e){
                        // sync inputs (in case user didn't click but values exist)
                        quantityInput.value = qty;
                        // validate (optional): require color and size if available
                        if (document.querySelectorAll('.color-swatch').length > 0 && selectedColorInput.value === '') {
                            e.preventDefault();
                            alert('Please select a color.');
                            return false;
                        }
                        if (document.querySelectorAll('.size').length > 0 && selectedSizeInput.value === '') {
                            e.preventDefault();
                            alert('Please select a size.');
                            return false;
                        }
                        // allow form to submit (server will use its own price)
                        return true;
                    });
                </script>
                <!-- ////color and size selector ////-->
            </div>
        <!-- product details box2 -->

        <!-- product details box3 -->
            <div class="product_details_box3">
                <div class="tab">
                    <button class="tablinks" onclick="openCity(event,'Description')">Description</button>
                    <button class="tablinks" onclick="openCity(event,'Specifications')">Specifications</button>
                    <button class="tablinks" onclick="openCity(event,'comments')">comments</button>
                    <button class="tablinks" onclick="openCity(event,'customer_reviews')">customer reviews</button>
                </div>

                <div id="Description" class="tabcontent">
                  <section class="product-desc">
                    <div class="desc-header">
                        <p>Bring style and comfort together with our premium edition wear.</p>
                    </div>
                    <div class="desc-content">
                        <div class="desc-text">
                        <p style="font-size: 14px;line-height: 5vh;">
                           <?php
$desc = $product['desc'];
$sentences = explode('.', $desc);
foreach ($sentences as $line) {
    $line = trim($line);
    if ($line !== '') {
        echo "✔ " . htmlspecialchars($line) . ".<br>";
    }
}
?>
                        </p>
                                                </div>
                        <div class="desc-image">
                        <img src="images/<?php echo htmlspecialchars($product['product_image_1']) ?>" alt="Product Description Image">
                        </div>
                    </div>
                    </section>
                </div>

                <div id="Specifications" class="tabcontent">
                    <section class="product-specs">
                        <div class="specs-table">
                            <div><span>Material:</span> 100% Organic Cotton</div>
                            <div><span>Fit Type:</span> Modern Regular Fit</div>
                            <div>
    <span>Sizes:</span>
    <?php  
        $qry_size = $conn->prepare("
            SELECT S, M, L, XL, XXL 
            FROM size 
            WHERE product_id = ?
        ");
        $qry_size->execute([$product['product_id']]);
        $row_size = $qry_size->fetch(PDO::FETCH_ASSOC);

        if (!empty($row_size)) {
            $sizes = array_filter($row_size);
            echo htmlspecialchars(implode(", ", $sizes));
        }
    ?>
</div>

                            <div><span>Color Options:</span> <?php  
                        $qry_color = $conn->prepare("SELECT red, blue, green, white, black, orange FROM color WHERE product_id = ?");
                        $qry_color->execute([$product['product_id']]);
                        $row_color = $qry_color->fetch(PDO::FETCH_ASSOC);
                        if (!empty($row_color)) {
                            $colors = array_filter($row_color);
                            echo htmlspecialchars(implode(", ", $colors));
                        }
                    ?></div>
                            <div><span>Wash Care:</span> Machine Wash Cold, Gentle Cycle</div>
                            <div><span>Origin:</span> Made in India 🇮🇳</div>
                        </div>
                        </section>
                </div>
                 <div id="comments" class="tabcontent">
                    <section class="product-comments">
                        <div class="comment">
                            <img src="images/blog-2.jpg" alt="User">
                            <div class="comment-body">
                            <h4>Rohit Sharma <span>• 2 days ago</span></h4>
                            <p>Super comfy! Love the texture and feel. Totally worth it 😍</p>
                            </div>
                        </div>

                        <div class="comment">
                            <img src="images/blog-3.jpg" alt="User">
                            <div class="comment-body">
                            <h4>Anjali Verma <span>• 1 week ago</span></h4>
                            <p>Nice fabric but the delivery took a bit longer than expected.</p>
                            </div>
                        </div>

                        <div class="add-comment">
                            <textarea placeholder="Write your comment..."></textarea>
                            <button>Post Comment</button>
                        </div>
                        </section>
                </div>

                <div id="customer_reviews" class="tabcontent">
                    <div class="reviews-section">
                        <div class="reviews-wrapper">

    <div class="reviews-left">
        <div class="overall-rating-box">
            <h2>4.7 <span>/ 5</span></h2>
            <div class="stars">★★★★☆</div>
            <p>Based on 128 Reviews</p>
        </div>

        <div class="rating-breakdown">
            <div class="rate-row"><span>5 ★</span><div class="bar"><div style="width: 85%;"></div></div></div>
            <div class="rate-row"><span>4 ★</span><div class="bar"><div style="width: 60%;"></div></div></div>
            <div class="rate-row"><span>3 ★</span><div class="bar"><div style="width: 30%;"></div></div></div>
            <div class="rate-row"><span>2 ★</span><div class="bar"><div style="width: 10%;"></div></div></div>
            <div class="rate-row"><span>1 ★</span><div class="bar"><div style="width: 5%;"></div></div></div>
        </div>

        <div class="review-card">
            <img src="https://i.pravatar.cc/100" alt="">
            <div>
                <h4>John Michael</h4>
                <div class="stars small">★★★★★</div>
                <p>Great quality product! Highly recommended.</p>
            </div>
        </div>

        <div class="review-card">
            <img src="https://i.pravatar.cc/99" alt="">
            <div>
                <h4>Emma Watson</h4>
                <div class="stars small">★★★★☆</div>
                <p>Loved the packaging and fast delivery.</p>
            </div>
        </div>

    </div>

    <div class="reviews-right">
        <h3>Write Your Review</h3>
        <form>
            <input type="text" placeholder="Your Name" required>
            <textarea placeholder="Write your comment..." required></textarea>
            <button type="submit">Post Review</button>
        </form>
    </div>

</div>

                    </div>
                </div>
                <script>
                    function openCity(evt,tabName){
                        var i,tabcontent,tablinks;
                        tabcontent = document.getElementsByClassName("tabcontent");
                        for(i=0;i < tabcontent.length;i++){
                            tabcontent[i].style.display = "none";
                        }
                        tablinks = document.getElementsByClassName("tablinks");
                        for(i=0;i< tablinks.length;i++){
                            tablinks[i].className = tablinks[i].className.replace("active","");
                        }
                        document.getElementById(tabName).style.display = "block";
                        evt.currentTarget.className +="active";
                    }
                    document.addEventListener("DOMContentLoaded",function(){
                        document.getElementById("Description").style.display ="block";
                    });
                </script>
            </div>
        <!-- product details box3 -->

        <!-- product details box4 -->
            <div class="product_details_box4">
                <h3>Related Products</h3>
                    <div class="product_details_box4-inbox1">
                        <?php  
                             $qryr = $conn->prepare("
    SELECT p.*, c.category_name AS category_name 
    FROM product AS p 
    JOIN category AS c 
        ON p.category_id = c.category_id 
    WHERE p.category_id = ? and p.product_id != ? ORDER BY rand() LIMIT 4
");

$qryr->execute([$product['category_id'],$product['product_id']]);

                        while($productr = $qryr->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <div class="product_details_box4-inbox1-dbox1">
                            <img src="images/<?php echo htmlspecialchars($productr['product_image_1']) ?>" style="width: 100%;height: 300px;">
                                <h4><?php echo htmlspecialchars($productr['name']) ?></h4>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <p id="price">$<?php echo htmlspecialchars($productr['price']) ?></p>
                        </div>
                        <?php   
                            }
                        ?>
                    </div>
            </div>
        <!-- product details box4 -->


         <!-- footer -->
<div class="footer">
    <div class="footer-inbox1">

        <!-- Column 1: Logo + Social + About -->
        <div class="footer-inbox-dbox1">
            <div class="footer-social">
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-facebook-f"></i>
                <i class="fa-brands fa-x-twitter"></i>
            </div>

            <h4>Merin <span id="dot"></span></h4>
            <p>The customer is at the heart of our unique business model, which includes design.</p>
            
            <div class="card-img-box">
                <img src="images/payment.png" alt="Payment Methods">
            </div>
        </div>

        <!-- Column 2: Shopping Links -->
        <div class="footer-inbox-dbox1">
            <h3>Shopping</h3>
            <p>Clothing Store</p>
            <p>Men's Fashion</p>
            <p>Accessories</p>
            <p>Sale</p>
        </div>

        <!-- Column 3: Top Products -->
        <div class="footer-inbox-dbox1">
            <h3>Top Products</h3>
            <p>Managed Website</p>
            <p>Power Tools</p>
            <p>Marketing Service</p>
            <p>Best Deals</p>
        </div>

        <!-- Column 4: Newsletter -->
        <div class="footer-inbox-dbox1">
            <h3>NEWSLETTER</h3>
            <p>Be the first to know about new arrivals, look books, sales & promos!</p>
            <div class="newsletter-box">
                <input type="email" placeholder="Your email">
                <i class="fa-solid fa-envelope"></i>
            </div>
        </div>

    </div>

    <p id="copyright">© Merin — All Rights Reserved</p>
</div>
<!-- footer -->

        <!-- main -->
        </div>
        <!-- main -->


           <script>

        // navbar items
        const searchIcon = document.getElementById("searchIcon");
const searchContainer = document.getElementById("searchContainer");
const closeSearch = document.getElementById("closeSearch");
const searchInput = document.getElementById("searchInput");

// Show search box
searchIcon.addEventListener("click", () => {
    searchIcon.style.display = "none";
    searchContainer.style.display = "inline-block";
    searchInput.focus();
});

// Hide search box
closeSearch.addEventListener("click", () => {
    searchContainer.style.display = "none";
    searchIcon.style.display = "inline-block";
});

// Redirect exactly like shop.php search filter
document.getElementById("navbarSearchForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const keyword = searchInput.value.trim();
    if (keyword !== "") {
        window.location.href = "shop.php?search=" + encodeURIComponent(keyword);
    }
});


//========================= SHOPPING CART ICON =========================
// toggle dropdown
document.getElementById("cartIcon").onclick = () => {
    document.getElementById("cartMenu").classList.toggle("show");
};

// add to cart btn
document.querySelectorAll(".addToCartBtn").forEach(btn => {
    btn.onclick = function () {
        let id = this.dataset.id;

        fetch("add_to_cart_ajax.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "product_id=" + id
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById("cart-count").textContent = data.count;
            loadMiniCart();
            document.getElementById("cartMenu").classList.add("show");
        });
    };
});


// load mini cart
function loadMiniCart() {
    fetch("mini_cart.php")
    .then(res => res.json())
    .then(data => {
        let box = document.getElementById("cart-items");
        box.innerHTML = "";
        document.getElementById("cart-total-price").textContent = data.total;

        if (data.items.length === 0) {
            box.innerHTML = "<p>Cart is empty</p>";
            return;
        }

        data.items.forEach(item => {
            box.innerHTML += `
                <div class="cart-item">
                    <img src="images/${item.image}">
                    <div>
                        <p>${item.name}</p>
                        <p>₹${item.price}</p>
                        <div class="qty-box">
                            <button onclick="updateQty(${item.index}, -1)">-</button>
                            <span>${item.quantity}</span>
                            <button onclick="updateQty(${item.index}, 1)">+</button>
                        </div>
                    </div>
                </div>
            `;
        });
    });
}

function updateQty(index, change) {
    fetch("update_cart_qty.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `index=${index}&change=${change}`
    }).then(() => loadMiniCart());
}

function removeItem(index) {
    fetch("remove_cart_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `index=${index}`
    })
    .then(res => res.json())
    .then(data => {
        loadMiniCart(); // refresh mini cart
        document.getElementById("cart-count").textContent = data.count; // update badge
    });
}


    // Refresh mini cart
    loadMiniCart();
        // navbar items end
document.querySelectorAll('.dropdown-header').forEach(header => {
    header.addEventListener('click', () => {
        const parent = header.parentElement;
        parent.classList.toggle('active');
    });
});
</script>
</body>
</html>
