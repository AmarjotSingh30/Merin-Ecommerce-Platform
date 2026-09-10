<?php
session_start();
$order_id = $_GET['order_id'] ?? 0;
if (!$order_id) {
    die("Invalid order.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmed</title>
<link rel="stylesheet" href="style.css">
<style>
body {font-family: Arial, sans-serif; background:#f9f9f9; text-align:center; padding:50px;}
.confirm-box {background:#fff; padding:40px; border-radius:8px; display:inline-block; box-shadow:0 0 20px rgba(0,0,0,0.1);}
.checkmark {font-size:60px; color:green; animation: pop 0.6s ease;}
@keyframes pop {0%{transform: scale(0);}50%{transform: scale(1.2);}100%{transform: scale(1);}}
h2 {margin-top:20px;}
p {margin-top:10px; font-size:16px; color:#555;}
</style>
</head>
<body>

<div class="confirm-box">
    <div class="checkmark">✔</div>
    <h2>Thank You!</h2>
    <p>Your order <strong>#<?= htmlspecialchars($order_id) ?></strong> has been placed successfully.</p>
    <p>Redirecting to your order details...</p>
</div>

<script>
// Redirect after 3 seconds
setTimeout(() => {
    window.location.href = "order_details.php?order_id=<?= $order_id ?>";
}, 3000);
</script>

</body>
</html>
