<?php  
session_start();
    require 'config.php';
    $qry=$conn->prepare("SELECT * FROM product ORDER BY rand() LIMIT 8");
    $qry->execute();
    unset($_SESSION['cart']);  



if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'customer') {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_image']);
}

    // session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

    <!-- main div -->
    <div class="main">

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

                           <!-- Search Icon -->
                        <i id="searchIcon" class="fa fa-search" style="cursor:pointer;"></i>

                        <!-- Search Box + Close Button -->
                        <div id="searchContainer" style="display:none;" class="search-box">
                            <form id="navbarSearchForm" method="GET" action="shop.php">
                                <input type="text" id="searchInput" name="search" placeholder="Search..." required>
                                <span id="closeSearch" class="close-btn">&times;</span>
                            </form>
                        </div>

                        <!-- search icon end -->
                         
                            <!-- Shopping Cart -->
                            <div class="cart-wrapper">
                                <i class="fa-solid fa-cart-shopping" id="cartIcon"></i>
                                <span id="cart-count"><?php echo $cartCount ?? 0; ?></span>

                                <div class="cart-menu" id="cartMenu">
                                    <span class="cart-close" id="closeCart">&times;</span>

                                    <h3>Your Cart</h3>
                                    <div id="cart-items"></div>

                                    <div class="cart-total">
                                        <p>Total: $<span id="cart-total-price">0</span></p>
                                    </div>

                                    <a href="shopping_cart.php" class="checkout-btn">View Cart</a>
                                </div>
                            </div>

                            <!-- Shopping cart end -->

                            <!-- navbar-user -->
                            <?php include 'navbar_user.php'; ?>
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
               

        <!-- navbar -->

        <!-- box3 -->
            <!-- HERO SECTION -->
                <section class="hero-slider">
                    <!-- Slide 1 -->
                    <div class="hero-slide active" style="background-image: url('images/slide-02.jpg');">
                        <div class="hero-content">
                            <h4>MEN COLLECTION</h4>
                            <h1><span class="green">Show</span> Your <span class="green">Style</span></h1>
                            <p>Pick your desire</p>
                            <button class="hero-btn">VIEW COLLECTION</button>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="hero-slide" style="background-image: url('images/slide-03.jpg');">
                        <div class="hero-content">
                            <h4>MEN Fashion</h4>
                            <h1><span class="green">Define</span> Your <span class="green">Elegance</span></h1>
                            <p>Discover your fashion</p>
                            <button class="hero-btn">SHOP NOW</button>
                        </div>
                    </div>

                    <!-- Slide 3 -->
                    <div class="hero-slide" style="background-image: url('images/slide-07.jpg');">
                        <div class="hero-content">
                            <h4>NEW ARRIVALS</h4>
                            <h1><span class="green">Upgrade</span> Your <span class="green">Wardrobe</span></h1>
                            <p>Fresh and trendy styles</p>
                            <button class="hero-btn">EXPLORE</button>
                        </div>
                    </div>

                    <!-- Arrows -->
                    <div class="hero-arrow left" onclick="prevSlide()">&#10094;</div>
                    <div class="hero-arrow right" onclick="nextSlide()">&#10095;</div>

                    <!-- Dots -->
                    <div class="hero-dots"></div>

                </section>

               
        <!-- box3 -->

       <!-- box4 -->
            <section class="collections-section">
                <div class="collection-grid">

                    <!-- Card 1 -->
                    <div class="collection-card large">
                        <img src="images/mens shirt-1 black.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="collection-content">
                            <h3>Clothing</h3>
                            <p>Collection 2024</p>
                            <a href="shop.php">SHOP NOW</a>
                        </div>

                    </div>

                    <!-- Card 2 -->
                    <div class="collection-card small">
                        <img src="images/mens watch-1 blue.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="collection-content">
                            <h3>Accessories</h3>
                            <a href="shop.php">SHOP NOW</a>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="collection-card small">
                        <img src="images/mens bag-1 black.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="collection-content">
                            <h3>New Arrivals</h3>
                            <a href="shop.php">EXPLORE</a>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="collection-card large">
                        <img src="images/banner-3.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="collection-content">
                            <h3>Shoe Spring</h3>
                            <p>2024</p>
                            <a href="shop.php">SHOP NOW</a>
                        </div>
                    </div>

                </div>
            </section>
<!-- box4 -->



<section class="featured-section">

    <h3>FEATURED PRODUCTS</h3>
    <div class="hz-line"></div>
    <p>Discover the best picks curated just for you.</p>

    <!-- FILTER LINKS -->
    <div class="filter-links">
        <span data-filter="best" class="active">Best Sellers</span>
        <span data-filter="new">New Arrivals</span>
        <span data-filter="hot">Hot Sales</span>
    </div>

    <!-- PRODUCT GRID -->
    <div class="featured-grid" id="productGrid">

        <?php while($row = $qry->fetch(PDO::FETCH_ASSOC)){ ?>
        <div class="featured-card" data-type="best new hot"
             data-product-id="<?= $row['product_id'] ?>">

            <!-- ❤️ Wishlist -->
            <span class="wish" data-id="<?= $row['product_id'] ?>">
                <!-- <i class="fa-solid fa-plus"></i> -->

                <i class="fa-regular fa-heart"></i>
            </span>

<!-- <span class="badge"><?= $row1['product_type'] ?></span> -->
            <!-- Clickable area -->
            <a href="product_details.php?id=<?= $row['product_id'] ?>"
               class="card-link">

                <img src="images/<?= $row['product_image_1']; ?>" alt="Product">

                <h2><?= $row['name']; ?></h2>

                <div class="featured-section-price">
                    $<?= $row['price']; ?>
                    <del>$<?= $row['price'] + 200; ?></del>
                </div>

            </a>

            <!-- Add to cart -->
            <button class="addToCartBtn"
                data-id="<?= $row['product_id']; ?>">
                Add to Cart
            </button>

        </div>
        <?php } ?>

    </div>
</section>



    

<!-- </section> -->

        
        <!-- box 6 -->
       <!-- BOX 6 -->
<div class="main-box6">
    <div class="main-box6-inbox1">
        <h4>Deal Of The Week</h4>
        <h1>Multi-pocket Chest Bag Black</h1>
    <!-- </div> -->
        <!-- COUNTDOWN TIMER -->
        <!-- <div class="countdown">
            <div class="time-box"><span id="days">00</span><p>Days</p></div>
            <div class="time-box"><span id="hours">00</span><p>Hours</p></div>
            <div class="time-box"><span id="minutes">00</span><p>Min</p></div>
            <div class="time-box"><span id="seconds">00</span><p>Sec</p></div>
        </div> -->

        <button>DISCOVER NOW</button>
        <p>Limited Time Offer</p>
    </div>
</div>




<!-- BOX 6 -->

        <!-- box6 -->

        <!-- box7 -->
        <div class="main-box7">
            <h2>NEW PRODUCTS</h2>
            <div class="hz-line1"></div>
            <p>Products picked according to latest vibes</p>
            <!-- inbox -->
            <div class="main-box7-inbox1">
                <!-- dbox -->
                <div class="main-box7-inbox1-dbox1">
                    <!-- upperbox -->
                    <div class="dbox1-upperbox">
                        <h4 style="color: white;">COLLECTION OF 2019</h4>
                        <h2 style="color: white;">MEN'S SUMMER T-SHIRT</h2>
                    </div>
                    <!-- lowerbox -->
                    <div class="dbox1-lowerbox">
                        <p style="color: white;">$20.00</p>
                        <a href="shop.php"><button>DISCOVER NOW</button></a>
                    </div>
                </div>

                <!-- dbox2 -->
                <div class="main-box7-inbox1-dbox2">
                    <?php 
                // Fetch 4 random products
                $q = $conn->prepare("SELECT * FROM product ORDER BY RAND() LIMIT 4");
                $q->execute();
                $items = $q->fetchAll(PDO::FETCH_ASSOC);

                // Split 4 items: first 2 -> upper box, next 2 -> lower box
                $upper = array_slice($items, 0, 2);
                $lower = array_slice($items, 2, 2);
            ?>

           <!-- UPPER BOX -->
<div class="dbox2-upperbox">
    <?php foreach($upper as $p): ?>

    <div class="upperbox-inbox1">

        <!-- ❤️ Wishlist -->
        <span class="wish" data-id="<?= $p['product_id']; ?>">
            <i class="fa-regular fa-heart"></i>
        </span>

        <!-- Clickable product area -->
        <a href="product_details.php?id=<?php echo $p['product_id']; ?>">
            <img src="images/<?php echo $p['product_image_1']; ?>">
            <h5><?php echo $p['name']; ?></h5>
            <p id="dollar">$<?php echo $p['price']; ?></p>
            <p id="del-text"><del>$<?php echo $p['price'] + 20; ?></del></p>
        </a>

        <!-- Add to cart -->
        <button class="addToCartBtn"
            data-id="<?= $p['product_id']; ?>">
            Add to Cart
        </button>

    </div>

    <?php endforeach; ?>
</div>


                   <!-- LOWER BOX -->
<div class="dbox2-lowerbox">
    <?php foreach($lower as $p): ?>

    <div class="upperbox-inbox1">

        <!-- ❤️ Wishlist -->
        <span class="wish" data-id="<?= $p['product_id']; ?>">
            <i class="fa-regular fa-heart"></i>
        </span>

        <!-- Clickable product area -->
        <a href="product_details.php?id=<?php echo $p['product_id']; ?>">
            <img src="images/<?php echo $p['product_image_1']; ?>">
            <h5><?php echo $p['name']; ?></h5>
            <p id="dollar">$<?php echo $p['price']; ?></p>
            <p id="del-text">
                <del>$<?php echo $p['price'] + 20; ?></del>
            </p>
        </a>

        <!-- Add to cart -->
        <button class="addToCartBtn"
            data-id="<?= $p['product_id']; ?>">
            Add to Cart
        </button>

    </div>

    <?php endforeach; ?>
</div>


                </div>
                    
                </div>
            </div>
        </div>
        <!-- box7 -->

        <!-- WHY CHOOSE US -->
<section class="why-us">
    <h2>Why Choose Us</h2>

    <div class="why-grid">
        <div class="why-card">
            <i class="fa-solid fa-truck-fast"></i>
            <h3>Fast Delivery</h3>
            <p>Quick & reliable shipping across India.</p>
        </div>

        <div class="why-card">
            <i class="fa-solid fa-shield-halved"></i>
            <h3>Secure Payments</h3>
            <p>100% secure checkout experience.</p>
        </div>

        <div class="why-card">
            <i class="fa-solid fa-rotate-left"></i>
            <h3>Easy Returns</h3>
            <p>Hassle-free returns within 7 days.</p>
        </div>

        <div class="why-card">
            <i class="fa-solid fa-headset"></i>
            <h3>24/7 Support</h3>
            <p>We’re here whenever you need us.</p>
        </div>
    </div>
</section>
<!-- WHY CHOOSE US END -->

        <!-- box8 -->
<div class="inspired-section">
    <h3>INSPIRED PRODUCTS</h3>
    <div class="hz-line1"></div>
    <p>Your daily dose of premium inspired styles.</p>

    <div class="inspired-grid">

        <!-- CARD -->
<?php 
$qry1 = $conn->prepare("SELECT * FROM product ORDER BY rand() LIMIT 8");
$qry1->execute();
while($row1 = $qry1->fetch(PDO::FETCH_ASSOC)){
?>
<div class="inspired-card" data-product-id="<?= $row1['product_id'] ?>">

    <span class="badge"><?= $row1['product_type'] ?></span>

    <!-- ❤️ Wishlist -->
    <span class="wish" data-id="<?= $row1['product_id']; ?>">
        <i class="fa-regular fa-heart"></i>
    </span>

    <!-- Clickable product area -->
    <a href="product_details.php?id=<?= $row1['product_id'] ?>" class="card-link">
        <img src="images/<?= $row1['product_image_1'] ?>">
        <h4><?= $row1['name'] ?></h4>

        <div class="price-box">
            <span class="price">$<?= $row1['price'] ?></span>
            <span class="old-price">$<?= $row1['price'] + 20 ?></span>
        </div>
    </a>

    <!-- 🛒 Add to cart -->
    <button class="addToCartBtn" data-id="<?= $row1['product_id']; ?>">
        Add to Cart
    </button>

</div>
<?php } ?>

</div>
<!-- box8 -->

<!-- Brands Section -->
<div class="brands-section">
    <div class="brands-icon">

        <div class="brands-icon-img-box1">
            <img src="images/brand2.png">
        </div>
        <div class="brands-icon-img-box1">
            <img src="images/brand3.png">
        </div>
        <div class="brands-icon-img-box1">
            <img src="images/brand4.png">
        </div>
        <div class="brands-icon-img-box1">
            <img src="images/brand5.png">
        </div>
    </div>
</div>
<!-- Brands Section End -->


        <!-- BLOG SECTION -->
<section class="blog-section">
    <div class="blog-header">
        <h2> Journal</h2>
        <p>Stories, trends & inspiration</p>
    </div>

    <div class="blog-grid">

        <article class="blog-card">
            <div class="blog-img">
                <img src="images/blog-1.jpg" alt="">
            </div>
            <div class="blog-content">
                <span>By Admin · 2 Comments</span>
                <h3>Minimal fashion trends you’ll love</h3>
                <a href="#">Read Article →</a>
            </div>
        </article>

        <article class="blog-card">
            <div class="blog-img">
                <img src="images/blog-2.jpg" alt="">
            </div>
            <div class="blog-content">
                <span>By Admin · 2 Comments</span>
                <h3>Why clean design sells more</h3>
                <a href="#">Read Article →</a>
            </div>
        </article>

        <article class="blog-card">
            <div class="blog-img">
                <img src="images/blog-3.jpg" alt="">
            </div>
            <div class="blog-content">
                <span>By Admin · 2 Comments</span>
                <h3>Modern wardrobe essentials</h3>
                <a href="#">Read Article →</a>
            </div>
        </article>

    </div>
</section>









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

            <h4>Merin<span id="dot"></span></h4>
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
            <h3>Newsletter</h3>
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

     <!-- js -->
      
     <script src="script.js"></script>
     <!-- js -->

</body>
</html>