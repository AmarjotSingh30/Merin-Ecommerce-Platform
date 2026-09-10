<?php
// order_success.php
$order_id = $_GET['order_id'] ?? '';
$amount = $_GET['amount'] ?? '';

if (!$order_id || !$amount) {
    die("Invalid access.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Success</title>
<link rel="stylesheet" href="responsive.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
<style>
	/* ORDER SUCCESS PAGE */

.order-success-container {
    background: #fff;
    max-width: 700px;
    margin: 60px auto;
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 25px rgba(0,0,0,0.08);
    text-align: center;
}

.order-success-container h2 {
    font-size: 32px;
    color: #28a745;
    margin-bottom: 10px;
}

.order-success-container p {
    font-size: 18px;
    margin: 8px 0;
}

.order-success-container .highlight {
    font-weight: bold;
    color: #333;
    font-size: 20px;
}

.order-success-container .home-btn {
    background: #333;
    color: white;
    padding: 12px 28px;
    display: inline-block;
    margin-top: 25px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 18px;
    letter-spacing: 0.5px;
    transition: 0.3s ease;
}

.order-success-container .home-btn:hover {
    background: #111;
    transform: translateY(-3px);
}

/* Make it responsive */
@media (max-width: 768px) {
    .order-success-container {
        margin: 30px 15px;
        padding: 30px 20px;
    }
    .order-success-container h2 {
        font-size: 26px;
    }
    .order-success-container p {
        font-size: 16px;
    }
    .order-success-container .home-btn {
        font-size: 16px;
        padding: 10px 22px;
    }
}
/* Animated checkmark */

.checkmark-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.checkmark {
    width: 90px;
    height: 90px;
    stroke-width: 3;
    stroke: #28a745;
    stroke-miterlimit: 10;
    fill: none;
    animation: scaleUp 0.4s ease-out forwards;
}

.checkmark-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    animation: circleAnim 0.6s ease-out forwards;
}

.checkmark-check {
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: checkAnim 0.3s 0.6s ease-out forwards;
}

@keyframes circleAnim {
    to { stroke-dashoffset: 0; }
}

@keyframes checkAnim {
    to { stroke-dashoffset: 0; }
}

@keyframes scaleUp {
    from { transform: scale(0.8); opacity: 0; }
    to { transform: scale(1); opacity:1; }
}

</style>
</head>
<body>

<div class="checkmark-wrapper">
    <svg class="checkmark" viewBox="0 0 52 52">
        <path class="checkmark-circle" d="M26 2 C12.8 2 2 12.8 2 26 s10.8 24 24 24 s24 -10.8 24 -24 S39.2 2 26 2" />
        <path class="checkmark-check" d="M14 27 l8 8 l16 -16" />
    </svg>
</div>
<div class="order-success-container">
    <h2>✔ Thank You for Your Order!</h2>
    <p>Your order has been placed successfully.</p>

    <p class="highlight">Order ID: <?= htmlspecialchars($order_id) ?></p>
    <p class="highlight">Total Amount: $<?= htmlspecialchars(number_format($amount,2)) ?></p>

    <p>You will receive a confirmation email shortly.</p>

    <a href="index.php" class="home-btn">Back to Home</a>
</div>


</body>
</html>
