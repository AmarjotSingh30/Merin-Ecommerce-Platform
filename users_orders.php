<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /6_MONTHS_Training/e_commerce_project/experiments_for_loginsignup/login.php");
    exit;
}




$user_email = $_SESSION['email']; // make sure email is stored at login

$stmt = $conn->prepare("
    SELECT order_id, order_amount, payment_mode, order_status, payment_status, created_at
    FROM orders
    WHERE email = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_email]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: "Segoe UI", sans-serif;
}

body {
    background: #f4f6fb;
}

/* Page container */
.orders-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

/* Header */
.orders-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.orders-header h2 {
    font-size: 26px;
    color: #333;
}

.orders-header a {
    text-decoration: none;
    background: #6c7ae0;
    color: #fff;
    padding: 10px 18px;
    border-radius: 6px;
    font-size: 14px;
}

.orders-header a:hover {
    background: #5a68c7;
}

/* Table */
.orders-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

.orders-table th,
.orders-table td {
    padding: 15px;
    text-align: center;
}

.orders-table th {
    background: #6c7ae0;
    color: #fff;
    font-size: 14px;
}

.orders-table tr:nth-child(even) {
    background: #f9f9f9;
}

/* Status badges */
.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    display: inline-block;
}

.badge.pending {
    background: #fff3cd;
    color: #856404;
}

.badge.confirmed {
    background: #d4edda;
    color: #155724;
}

.badge.cancelled {
    background: #f8d7da;
    color: #721c24;
}

/* Action buttons */
.view-btn {
    text-decoration: none;
    padding: 8px 14px;
    background: #6c7ae0;
    color: #fff;
    border-radius: 6px;
    font-size: 13px;
}

.view-btn:hover {
    background: #5a68c7;
}

/* Empty orders */
.no-orders {
    text-align: center;
    padding: 50px;
    background: #fff;
    border-radius: 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .orders-table thead {
        display: none;
    }

    .orders-table tr {
        display: block;
        margin-bottom: 15px;
        border-bottom: 2px solid #eee;
    }

    .orders-table td {
        display: flex;
        justify-content: space-between;
        padding: 12px;
        text-align: left;
    }

    .orders-table td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #555;
    }
}
</style>
</head>
<body>

<div class="orders-container">

    <div class="orders-header">
        <h2>My Orders</h2>
        <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Back to Shop</a>
    </div>

    <?php if (count($orders) > 0): ?>
    <table class="orders-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td data-label="Order ID">#<?= htmlspecialchars($order['order_id']) ?></td>
                <td data-label="Date"><?= date("d M Y", strtotime($order['created_at'])) ?></td>
                <td data-label="Amount">$<?= number_format($order['order_amount'], 2) ?></td>
                <td data-label="Payment"><?= htmlspecialchars($order['payment_mode']) ?></td>
                <td data-label="Status">
                    <span class="badge <?= strtolower($order['order_status']) ?>">
                        <?= htmlspecialchars($order['order_status']) ?>
                    </span>
                </td>
                <td data-label="Action">
                    <a href="order_details.php?order_id=<?= $order['order_id'] ?>" class="view-btn">
                        View
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="no-orders">
            <h3>No orders found</h3>
            <p>You haven't placed any orders yet.</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
