<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "login_required"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'] ?? 0;

if (!$product_id) {
    echo json_encode(["status" => "error"]);
    exit;
}

// check if exists
$stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
$stmt->execute([$user_id, $product_id]);

if ($stmt->rowCount() > 0) {
    $del = $conn->prepare("DELETE FROM wishlist WHERE user_id=? AND product_id=?");
    $del->execute([$user_id, $product_id]);
    echo json_encode(["status" => "removed"]);
} else {
    $ins = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $ins->execute([$user_id, $product_id]);
    echo json_encode(["status" => "added"]);
}
