<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT p.*
    FROM wishlist w
    JOIN product p ON w.product_id = p.product_id
    WHERE w.user_id = ?
");
$stmt->execute([$user_id]);
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Wishlist</title>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f6f7fb;
    margin: 0;
    padding: 0;
}

/* ===== HEADER ===== */
.wishlist-header {
    background: #fff;
    padding: 15px 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    position: sticky;
    top: 0;
    z-index: 100;
}

.back-btn {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #333;
    display: flex;
    align-items: center;
    gap: 6px;
}

.back-btn:hover {
    color: #000;
}

.wishlist-title {
    font-size: 20px;
    font-weight: 600;
    color: #222;
}

/* ===== MAIN ===== */
.main-content {
    padding: 30px;
}

/* GRID */
.wishlist-grid {
    max-width: 1200px;
    margin: auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
}

/* CARD */
.wishlist-card {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
}

.wishlist-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(0,0,0,0.12);
}

/* IMAGE */
.wishlist-card img {
    width: 100%;
    height: 230px;
    object-fit: cover;
}

/* CONTENT */
.wishlist-card-content {
    padding: 15px;
    text-align: center;
}

.wishlist-card-content h4 {
    font-size: 16px;
    margin: 10px 0;
    color: #333;
}

.wishlist-card-content p {
    font-size: 18px;
    color: black;
    font-weight: 600;
    margin: 8px 0;
}

/* VIEW BUTTON */
.wishlist-card-content a {
    display: inline-block;
    margin-top: 10px;
    padding: 8px 18px;
    background: #111;
    color: #fff;
    border-radius: 20px;
    font-size: 14px;
    text-decoration: none;
    transition: background 0.3s ease;
}

.wishlist-card-content a:hover {
    background: #444;
}

/* REMOVE ICON */
.remove-wish {
    position: absolute;
    top: 12px;
    right: 12px;
    background: #fff;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #e74c3c;
    font-size: 17px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: transform 0.2s ease;
}

.remove-wish:hover {
    transform: scale(1.2);
}

/* EMPTY */
.empty-wishlist {
    text-align: center;
    font-size: 18px;
    color: #777;
    margin-top: 100px;
}
</style>
</head>

<body>

<!-- ===== HEADER ===== -->
<div class="wishlist-header">
    <button class="back-btn" onclick="history.back()">
        <!-- <i class="fa-solid fa-arrow-left"></i> -->
        Back
    </button>
    <div class="wishlist-title">My Wishlist</div>
</div>

<!-- ===== CONTENT ===== -->
<div class="main-content">

<?php if (empty($wishlist_items)): ?>
    <div class="empty-wishlist">
        <!-- <i class="fa-regular fa-heart" style="font-size:60px; color:#ccc;"></i> -->
        <p>Your wishlist is empty</p>
    </div>
<?php else: ?>

<div class="wishlist-grid">
<?php foreach ($wishlist_items as $item): ?>
    <div class="wishlist-card">

        <i class="fa-solid fa-xmark remove-wish"
           onclick="removeWish(<?= $item['product_id'] ?>)"></i>

        <img src="images/<?= htmlspecialchars($item['product_image_1']) ?>" alt="Product">

        <div class="wishlist-card-content">
            <h4><?= htmlspecialchars($item['name']) ?></h4>
            <p>$<?= number_format($item['price'], 2) ?></p>
            <a href="product_details.php?id=<?= $item['product_id'] ?>">
                View Product
            </a>
        </div>

    </div>
<?php endforeach; ?>
</div>

<?php endif; ?>

</div>

<script>
function removeWish(id) {
    fetch('wishlist_toggle.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + id
    }).then(() => location.reload());
}
</script>

</body>
</html>
