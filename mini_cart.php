<?php
session_start();
require "config.php";

$items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $index => $item) {

        // fetch product image + name
        $stmt = $conn->prepare("SELECT name, product_image_1, price FROM product WHERE product_id=?");
        $stmt->execute([$item['product_id']]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$p) continue;

        $lineTotal = $p['price'] * $item['quantity'];
        $total += $lineTotal;

        $items[] = [
            "index" => $index,
            "name" => $p['name'],
            "image" => $p['product_image_1'],
            "price" => $p['price'],
            "quantity" => $item['quantity']
        ];
    }
}

echo json_encode([
    "items" => $items,
    "total" => $total
]);
