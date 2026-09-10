<?php
session_start();

$index = intval($_POST['index'] ?? -1);

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($index >= 0 && isset($_SESSION['cart'][$index])) {
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
}

$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity') ?? [0]);

echo json_encode([
    "status" => "success",
    "count" => $cartCount
]);
