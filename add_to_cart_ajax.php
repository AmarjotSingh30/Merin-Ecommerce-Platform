<?php
session_start();
require "config.php";

$pid = intval($_POST['product_id']);
$qty = 1;

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$found = false;

foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $pid) {
        $item['quantity']++;
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = [
        "product_id" => $pid,
        "quantity" => 1
    ];
}

$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

echo json_encode([
    "status" => "success",
    "count" => $cartCount
]);
