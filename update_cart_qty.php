<?php
session_start();

$index = intval($_POST['index'] ?? -1);
$change = intval($_POST['change'] ?? 0);

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($index >= 0 && isset($_SESSION['cart'][$index])) {
    $_SESSION['cart'][$index]['quantity'] += $change;
    if ($_SESSION['cart'][$index]['quantity'] < 1) {
        $_SESSION['cart'][$index]['quantity'] = 1; // min quantity 1
    }
}

$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity') ?? [0]);

echo json_encode([
    "status" => "success",
    "count" => $cartCount
]);
