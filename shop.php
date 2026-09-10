<?php
$connection = new mysqli("localhost", "root", "", "merin");
if($connection->connect_error){
    die("DB Connection Failed: " . $connection->connect_error);
}

// ---------- PAGINATION SETUP ----------
$limit = 9; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// SEARCH PARAM
$search = isset($_GET['search']) ? $_GET['search'] : '';

// CATEGORY
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// BRAND
$brand = isset($_GET['brand']) ? $_GET['brand'] : [];

// PRICE
$minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 0;

// SIZE
$size = isset($_GET['size']) ? $_GET['size'] : [];

// COLOR
$color = isset($_GET['color']) ? $_GET['color'] : [];

// TAGS
$tags = isset($_GET['tags']) ? $_GET['tags'] : [];

// SORT
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$orderBy = "p.created_at DESC";
switch($sort){
    case 'low': $orderBy = "CAST(p.price AS UNSIGNED) ASC"; break;
    case 'high': $orderBy = "CAST(p.price AS UNSIGNED) DESC"; break;
    case 'newest': $orderBy = "p.created_at DESC"; break;
}

// ---------- FETCH MIN/MAX PRICE ----------
$priceQuery = $connection->query("SELECT MIN(price) AS min_price, MAX(price) AS max_price FROM product WHERE is_deleted=0");
$priceData = $priceQuery->fetch_assoc();
if($minPrice==0) $minPrice = (int)$priceData['min_price'];
if($maxPrice==0) $maxPrice = (int)$priceData['max_price'];

// ---------- BUILD FILTER SQL ----------
$filterSQL = "";

if(!empty($search)){
    $s = $connection->real_escape_string($search);
    $filterSQL .= " AND (p.name LIKE '%$s%' OR p.`desc` LIKE '%$s%')";
}

if($category > 0){
    $filterSQL .= " AND p.category_id = $category";
}

if(!empty($brand)){
    $brandIDs = implode(",", array_map('intval',$brand));
    $filterSQL .= " AND p.brand_id IN ($brandIDs)";
}

if(!empty($size)){
    $sizesSafe = implode("','", array_map(fn($s)=>$connection->real_escape_string($s), $size));
    $filterSQL .= " AND p.product_id IN (
        SELECT product_id FROM (
            SELECT product_id, 'S' AS sz FROM size WHERE S!='0'
            UNION ALL
            SELECT product_id, 'M' AS sz FROM size WHERE M!='0'
            UNION ALL
            SELECT product_id, 'L' AS sz FROM size WHERE L!='0'
            UNION ALL
            SELECT product_id, 'XL' AS sz FROM size WHERE XL!='0'
            UNION ALL
            SELECT product_id, 'XXL' AS sz FROM size WHERE XXL!='0'
        ) sizeData WHERE sz IN ('$sizesSafe')
    )";
}

if(!empty($color)){
    $colorConditions = [];
    foreach($color as $c){
        $cSafe = $connection->real_escape_string($c);
        $colorConditions[] = "TRIM(LOWER(color.$cSafe)) != '' AND TRIM(LOWER(color.$cSafe)) != '0'";
    }
    $filterSQL .= " AND p.product_id IN (SELECT product_id FROM color WHERE ".implode(" OR ",$colorConditions).")";
}

if(!empty($tags)){
    $tagIDs = implode(",", array_map('intval',$tags));
    $filterSQL .= " AND p.product_id IN (SELECT DISTINCT product_id FROM product_tags WHERE tag_id IN ($tagIDs))";
}

// PRICE RANGE
$filterSQL .= " AND p.price BETWEEN $minPrice AND $maxPrice";

// ---------- COUNT TOTAL PRODUCTS ----------
$countSQL = "SELECT COUNT(*) AS total FROM product p WHERE p.is_deleted=0 $filterSQL";
$countResult = $connection->query($countSQL);
$total = (int)$countResult->fetch_assoc()['total'];

// ---------- CALCULATE PAGINATION ----------
$pages = max(1, ceil($total / $limit));
if($page < 1) $page = 1;
if($page > $pages) $page = $pages;
$start = ($page - 1) * $limit;

// ---------- SHOWING RANGE ----------
$start_show = ($total==0)?0:$start+1;
$end_show = min($start+$limit, $total);

// ---------- FETCH PRODUCTS ----------
$productSQL = "
SELECT p.* 
FROM product p 
LEFT JOIN category c ON p.category_id=c.category_id
WHERE p.is_deleted=0
$filterSQL
ORDER BY $orderBy
LIMIT $start, $limit
";
$products = $connection->query($productSQL);

// ---------- FETCH CATEGORIES & BRANDS ----------
$categories = $connection->query("
SELECT c.category_id, c.category_name, 
(SELECT COUNT(*) FROM product p WHERE p.category_id = c.category_id AND p.is_deleted=0) AS total_products
FROM category c
");

$brands = $connection->query("SELECT * FROM brands ORDER BY brand_name ASC");

// ---------- FETCH SIZE SAMPLE ----------
$sizeQuery = $connection->query("SELECT S, M, L, XL, XXL FROM size LIMIT 1");
$sizes = $sizeQuery->fetch_assoc();

// ---------- FETCH COLORS SAMPLE ----------
$colorQuery = $connection->query("SELECT red, blue, green, white, black, orange FROM color");
$colorData = $colorQuery->fetch_assoc();
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>shop_page</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                            
                            <!-- Search Icon -->
<i id="searchIcon" class="fa fa-search" style="cursor:pointer;"></i>

<!-- Search Box + Close Button -->
<div id="searchContainer" style="display:none;" class="search-box">
    <form id="navbarSearchForm" method="GET" action="shop.php">
        <input type="text" id="searchInput" name="search" placeholder="Search..." required>
        <span id="closeSearch" class="close-btn">&times;</span>
    </form>
</div>


                            <!-- Shopping Cart -->
                              <!-- MINI CART HEADER ICON -->
                            <div class="cart-wrapper">
                                <i class="fa-solid fa-cart-shopping" id="cartIcon"></i>
                                <span id="cart-count"><?php echo $cartCount ?? 0; ?></span>

                                <div class="cart-menu" id="cartMenu">
                                    <span class="cart-close" id="closeCart">&times;</span>

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
                <!-- javascript -->
                <script>
                    const hamburger = document.getElementById("hamburger");
                    const navLinks = document.getElementById("navLinks");

                    hamburger.addEventListener("click", () => {
                        navLinks.classList.toggle("active");
                    });
                </script>
                <!-- javascript -->

        <!-- navbar -->


<!-- shop box1 -->
      <div class="shop-box1">
    
    <!-- Category Tag -->
    <span class="shop-tag">Category: All Products</span>

    <!-- Main Title -->
    <h4 class="shop-title">Shop</h4>

    <!-- Trust Indicator -->
    <div class="shop-trust">
        <i class="fa-solid fa-shield-check"></i> 100% Genuine Products
    </div>

    <!-- Breadcrumb -->
    <p class="shop-breadcrumb">
        <a href="index.php">Home</a> 
        <i class="fa-solid fa-angle-right"></i> 
        <span>Shop</span>
    </p>

    <!-- Offer Banner -->
    <div class="shop-offer-banner">
        🔥 This Week: Extra 15% OFF on selected items
    </div>

</div>
<!-- shop box1 -->




  <!-- SHOP SECTION -->
<div class="shop-wrapper">

   <div class="sidebar">

    <div class="sidebar-box">

    <!-- Search -->
        <form method="GET" id="searchForm">
            <div class="search-box">
                <input type="text" 
                    name="search" 
                    placeholder="Search product..." 
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                <input type="hidden" name="sort" value="<?= $sort ?>">
            </div>
        </form>
    </div>
    <!-- Search end -->

    <!-- CATEGORY DROPDOWN START  -->
    <div class="dropdown-item">
        <div class="dropdown-header">
            <h4>Categories</h4>
            <i class="fa-solid fa-chevron-down"></i>
        </div>
        <div class="dropdown-content">
            <ul>
                <?php while($cat = $categories->fetch_assoc()){ ?>
    <li>
         <a href="shop.php?category=<?= $cat['category_id'] ?>">
            <?= $cat['category_name'] ?> (<?= $cat['total_products'] ?>)
        </a>
    </li>
<?php } ?>
                
            </ul>
        </div>
    </div>
    <!-- CATEGORY DROPDOWN END  -->


    <!-- BRAND FILTER -->
<div class="dropdown-item">
    <div class="dropdown-header">
        <h4>Brand</h4>
        <i class="fa-solid fa-chevron-down"></i>
    </div>

    <div class="dropdown-content">
        <ul>
            <?php while($b = $brands->fetch_assoc()){ ?>
                <li>
                    <label>
                        <input 
                            type="checkbox" 
                            name="brand[]" 
                            value="<?= $b['brand_id'] ?>"
                            <?= (isset($_GET['brand']) && in_array($b['brand_id'], $_GET['brand'])) ? 'checked' : '' ?>
                            class="auto-brand"
                        >
                        <?= $b['brand_name'] ?>
                    </label>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>

<script>
document.querySelectorAll(".auto-brand").forEach(box => {
    box.addEventListener("change", () => {

        let params = new URLSearchParams(window.location.search);

        // BRAND UPDATE
        let currentBrands = params.getAll("brand[]");
        let value = box.value;

        if (box.checked) {
            params.append("brand[]", value);
        } else {
            // Remove unchecked brand
            params.delete("brand[]");
            currentBrands
                .filter(b => b != value)
                .forEach(b => params.append("brand[]", b));
        }

        // RESET PAGE TO 1
        params.set("page", 1);

        // Reload with full params
        window.location.href = "shop.php?" + params.toString();
    });
});
</script>




<div class="dropdown-item">
    <div class="dropdown-header">
        <h4>Filter Price</h4>
        <i class="fa-solid fa-chevron-down"></i>
    </div>

    <div class="dropdown-content">
        <form method="GET" id="priceFilterForm">

            <input 
                type="range" 
                name="min_price"
                min="<?= $priceData['min_price'] ?>" 
                max="<?= $priceData['max_price'] ?>" 
                value="<?= $minPrice ?>" 
                oninput="document.getElementById('minPrice').innerText = this.value;"
                class="auto-price"
            >

            <input 
                type="range" 
                name="max_price"
                min="<?= $priceData['min_price'] ?>" 
                max="<?= $priceData['max_price'] ?>" 
                value="<?= $maxPrice ?>" 
                oninput="document.getElementById('maxPrice').innerText = this.value;"
                class="auto-price"
            >

            <p class="price-values">
                Price: ₹<span id="minPrice"><?= $minPrice ?></span> — ₹<span id="maxPrice"><?= $maxPrice ?></span>
            </p>

            <?php if (!empty($search)) { ?>
                <input type="hidden" name="search" value="<?= $search ?>">
            <?php } ?>

            <?php if ($category > 0) { ?>
                <input type="hidden" name="category" value="<?= $category ?>">
            <?php } ?>

            <?php if (!empty($brand)) { 
                foreach ($brand as $b) { ?>
                    <input type="hidden" name="brand[]" value="<?= intval($b) ?>">
            <?php } } ?>
        </form>
    </div>
</div>

<script>
let priceTimer;

document.querySelectorAll(".auto-price").forEach(slider => {
    slider.addEventListener("input", () => {
        clearTimeout(priceTimer);
        priceTimer = setTimeout(() => {
            document.getElementById("priceFilterForm").submit();
        }, 400);
    });
});
</script>

 
<!-- size dropdown -->
<div class="dropdown-item">
    <div class="dropdown-header">
        <h4>Size</h4>
        <i class="fa-solid fa-chevron-down"></i>
    </div>

    <?php
    // Get one row sample to display size labels
    $sizeQuery = $connection->query("SELECT S, M, L, XL, XXL FROM size LIMIT 1");
    $sizes = $sizeQuery->fetch_assoc();
    $sizeFilter = isset($_GET['size']) ? $_GET['size'] : [];
    ?>

    <div class="dropdown-content size-options">
        <?php foreach($sizes as $sizeLabel => $val){ ?>
            <label style="margin-right:10px; display:inline-block;">
                <input 
                    type="checkbox" 
                    name="size[]" 
                    value="<?= $sizeLabel ?>"
                    <?= (in_array($sizeLabel, $sizeFilter)) ? 'checked' : '' ?>
                >
                <span><?= $sizeLabel ?></span>
            </label>
        <?php } ?>
    </div>
</div>

<script>
document.querySelectorAll("input[name='size[]']").forEach(box => {
    box.addEventListener("change", () => {
        const params = new URLSearchParams(window.location.search);

        // Clear old sizes
        params.delete('size[]');

        // Add selected sizes
        document.querySelectorAll("input[name='size[]']:checked").forEach(cb => {
            params.append('size[]', cb.value);
        });

        window.location.search = params.toString(); // reload page
    });
});
</script>





<!-- COLOR CATEGORY -->
<div class="dropdown-item">
    <div class="dropdown-header">
        <h4>Colors</h4>
        <i class="fa-solid fa-chevron-down"></i>
    </div>

    <div class="dropdown-content color-options">

        <?php
        $colorQuery = $connection->query("SELECT red, blue, green, white, black, orange FROM color");
        $colorData = $colorQuery->fetch_assoc();
        ?>

        <ul class="color-swatch-list">
            <?php foreach($colorData as $colorName => $value){ ?>
                <?php if($value != ""){ ?>

                    <li class="color-swatch-item">
                        <label class="color-swatch">
                            <input type="checkbox" name="color[]" value="<?= $colorName ?>">
                            <span class="swatch-circle" style="background: <?= $colorName ?>;"></span>
                        </label>
                    </li>

                <?php } ?>
            <?php } ?>
        </ul>
    </div>
</div>


<script>
// When user clicks color checkbox → update URL filters
document.querySelectorAll("input[name='color[]']").forEach(box => {
    box.addEventListener("change", () => {
        const params = new URLSearchParams(window.location.search);

        // Remove old color values
        params.delete('color[]');

        // Add new selected colors
        document.querySelectorAll("input[name='color[]']:checked").forEach(cb => {
            params.append('color[]', cb.value);
        });

        window.location.search = params.toString();
    });
});
</script>
<!-- color category end -->



<form id="tagFilterForm" method="GET">
    <div class="dropdown-item">
        <div class="dropdown-header">
            <h4>Tags</h4>
            <i class="fa-solid fa-chevron-down"></i>
        </div>

        <div class="dropdown-content tags">
            <label>
                <input type="checkbox" name="tags[]" value="1" 
                <?php if (!empty($_GET['tags']) && in_array(1, $_GET['tags'])) echo "checked"; ?>>
                Fashion
            </label>

            <label>
                <input type="checkbox" name="tags[]" value="2"
                <?php if (!empty($_GET['tags']) && in_array(2, $_GET['tags'])) echo "checked"; ?>>
                New
            </label>

            <label>
                <input type="checkbox" name="tags[]" value="3"
                <?php if (!empty($_GET['tags']) && in_array(3, $_GET['tags'])) echo "checked"; ?>>
                Popular
            </label>

            <label>
                <input type="checkbox" name="tags[]" value="4"
                <?php if (!empty($_GET['tags']) && in_array(4, $_GET['tags'])) echo "checked"; ?>>
                Sale
            </label>

            <label>
                <input type="checkbox" name="tags[]" value="5"
                <?php if (!empty($_GET['tags']) && in_array(5, $_GET['tags'])) echo "checked"; ?>>
                Trending
            </label>
        </div>
    </div>
</form>
<script>
document.querySelectorAll('#tagFilterForm input[name="tags[]"]').forEach(tag => {
    tag.addEventListener('change', () => {
        document.getElementById("tagFilterForm").submit();
    });
});
</script>



</div>

<!-- product grid right section -->
<div class="shop-content">

    <!-- Top Bar -->
    <div class="shop-top">
        <p>Showing <?= $start_show ?>–<?= $end_show ?> of <?= $total ?> results</p>

        <form method="GET" id="sortForm">
            <select class="sort-select" name="sort" onchange="document.getElementById('sortForm').submit()">
                <option value="">Sort by Price</option>
                <option value="low" <?= (isset($_GET['sort']) && $_GET['sort']=='low') ? 'selected' : '' ?>>Low to High</option>
                <option value="high" <?= (isset($_GET['sort']) && $_GET['sort']=='high') ? 'selected' : '' ?>>High to Low</option>
                <option value="newest" <?= (isset($_GET['sort']) && $_GET['sort']=='newest') ? 'selected' : '' ?>>Newest</option>
            </select>
        </form>
    </div>

    <!-- //////////////product-grid////////////////// -->
    <div class="product-grid">

    <?php 
    // Ensure $orderBy is defined
    if(!isset($orderBy)) $orderBy = "p.created_at DESC";

    $productQuery = "
        SELECT p.*
        FROM product p
        LEFT JOIN category c ON p.category_id = c.category_id
        WHERE p.is_deleted = 0
        $filterSQL
        ORDER BY $orderBy
        LIMIT $start, $limit
    ";
    $products = $connection->query($productQuery);

    if($products->num_rows > 0){
        while($p = $products->fetch_assoc()){ ?>
            <div class="product-card">
                <img src="images/<?= $p['product_image_1'] ?>" alt="">
                <h5><?= $p['name'] ?></h5>
                <p class="price">$<?= $p['price'] ?></p>
                <a href="product_details.php?id=<?= $p['product_id'] ?>">
                    <button class="add-cart">View Product</button>
                </a>
            </div>
    <?php 
        }
    } else {
        echo "<p style='padding:20px;'>No products found</p>";
    }
    ?>

    </div>

    <!-- PAGINATION -->
    <div class="pagination-container">
        <ul class="pagination">
            <?php 
                $sizeQueryString = ""; if (!empty($size)) foreach ($size as $s) $sizeQueryString .= "&size[]=" . $s;
                $brandQueryString = ""; if (!empty($brand)) foreach ($brand as $b) $brandQueryString .= "&brand[]=" . intval($b);
                $colorQueryString = ""; if (!empty($color)) foreach ($color as $c) $colorQueryString .= "&color[]=" . $c;
                $tagsQueryString = ""; if (!empty($tags)) foreach ($tags as $t) $tagsQueryString .= "&tags[]=" . intval($t);
            ?>

            <?php if ($page > 1): ?>
                <li>
                    <a href="?page=<?= $page - 1 ?>&sort=<?= $sort ?>&search=<?= $search ?>&category=<?= $category ?><?= $brandQueryString ?><?= $sizeQueryString ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?><?= $colorQueryString ?><?= $tagsQueryString ?>">Prev</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li>
                    <a href="?page=<?= $i ?>&sort=<?= $sort ?>&search=<?= $search ?>&category=<?= $category ?><?= $brandQueryString ?><?= $sizeQueryString ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?><?= $colorQueryString ?><?= $tagsQueryString ?>" class="<?= ($i == $page) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $pages): ?>
                <li>
                    <a href="?page=<?= $page + 1 ?>&sort=<?= $sort ?>&search=<?= $search ?>&category=<?= $category ?><?= $brandQueryString ?><?= $sizeQueryString ?>&min_price=<?= $minPrice ?>&max_price=<?= $maxPrice ?><?= $colorQueryString ?><?= $tagsQueryString ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- //////////////product-grid end///////////////////////// -->

</div>




            </ul>
        </div>

    </div>

</div>







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


function loadMiniCart() {
    fetch("mini_cart.php")
    .then(res => res.json())
    .then(data => {
        let box = document.getElementById("cart-items");
        box.innerHTML = "";
        document.getElementById("cart-total-price").textContent = data.total;

        // Update cart badge
        let totalQty = data.items.reduce((sum, item) => sum + item.quantity, 0);
        document.getElementById("cart-count").textContent = totalQty;

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
                    <button class="delete-btn" onclick="removeItem(${item.index})">&times;</button>
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

// close cart button
document.getElementById("closeCart").onclick = function (e) {
    e.stopPropagation();
    document.getElementById("cartMenu").classList.remove("show");
};


// ======================== SHOPPING CART END ==========================
</script>




</body>
</html>