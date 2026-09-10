<?php
require 'config.php';

$order_id = $_GET['order_id'] ?? '';

if (!$order_id) {
    die("Invalid order.");
}

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

// Fetch items
$stmt2 = $conn->prepare("
    SELECT od.*, p.name 
    FROM order_details od
    JOIN product p ON p.product_id = od.product_id
    WHERE od.order_id = ?
");
$stmt2->execute([$order_id]);
$items = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Details</title>

<!-- Include jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f9f9f9;
    margin: 0;
    padding: 0;
}

.order-success-container {
    width: 400px;
    max-width: 90%;
    margin: 50px auto;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.checkmark-wrapper {
    width: 100px;
    height: 100px;
    margin: 0 auto 20px;
}

.checkmark-circle {
    fill: none;
    stroke: #4caf50;
    stroke-width: 4;
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    animation: draw-circle 0.6s ease-out forwards;
}

.checkmark-check {
    fill: none;
    stroke: #4caf50;
    stroke-width: 4;
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: draw-check 0.3s 0.6s ease-out forwards;
}

@keyframes draw-circle {
    to { stroke-dashoffset: 0; }
}
@keyframes draw-check {
    to { stroke-dashoffset: 0; }
}

.highlight {
    font-weight: bold;
    margin: 10px 0;
}

.home-btn, #downloadBill {
    display: inline-block;
    margin: 10px 5px;
    padding: 10px 20px;
    background: #6c7ae0;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}
.home-btn:hover, #downloadBill:hover {
    background: #5a68c7;
}
</style>
</head>
<body>

<div class="order-success-container">

    <div class="checkmark-wrapper">
        <svg class="checkmark" viewBox="0 0 52 52">
            <path class="checkmark-circle" d="M26 2 C12.8 2 2 12.8 2 26 s10.8 24 24 24 s24 -10.8 24 -24 S39.2 2 26 2" />
            <path class="checkmark-check" d="M14 27 l8 8 l16 -16" />
        </svg>
    </div>

    <h2>Thank You for Your Order!</h2>

    <p class="highlight">Order ID: <?= htmlspecialchars($order_id) ?></p>
    <p class="highlight">Total Amount: $<?= htmlspecialchars(number_format($order['order_amount'], 2)) ?></p>

    <p>You will receive a confirmation email shortly.</p>

    <a href="order_details.php?order_id=<?= $order_id ?>" class="home-btn">View Order Details</a>
    <br>
    <a href="index.php" class="home-btn">Back to Home</a>
    <br>
    <button id="downloadBill">Download Your Bill (PDF)</button>
</div>

<script>
document.getElementById("downloadBill").addEventListener("click", () => {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    let y = 20;

    /* ---------- HEADER ---------- */
    doc.setFont("helvetica", "bold");
    doc.setFontSize(18);
    doc.text("INVOICE", 105, y, { align: "center" });

    y += 6;
    doc.setLineWidth(0.5);
    doc.line(20, y, 190, y);

    y += 10;

    /* ---------- ORDER INFO ---------- */
    doc.setFontSize(11);
    doc.setFont("helvetica", "normal");
    doc.text(`Order ID: <?= $order_id ?>`, 20, y);
    doc.text(`Date: <?= date("d M Y") ?>`, 140, y);
    y += 8;

    doc.text(`Total Amount: $<?= number_format($order['order_amount'],2) ?>`, 20, y);
    doc.text(`Payment Mode: <?= htmlspecialchars($order['payment_mode'] ?? 'Online') ?>`, 140, y);
    y += 10;

    doc.line(20, y, 190, y);
    y += 8;

    /* ---------- ITEMS HEADER ---------- */
    doc.setFont("helvetica", "bold");
    doc.text("Item", 20, y);
    doc.text("Qty", 120, y);
    doc.text("Price", 160, y);

    y += 5;
    doc.setLineWidth(0.3);
    doc.line(20, y, 190, y);
    y += 6;

    /* ---------- ITEMS LIST ---------- */
    doc.setFont("helvetica", "normal");

    <?php foreach($items as $item): ?>
        doc.text("<?= addslashes($item['name']) ?>", 20, y);
        doc.text("<?= $item['quantity'] ?>", 125, y);
        doc.text("$<?= number_format($item['product_amount'],2) ?>", 160, y);
        y += 8;
    <?php endforeach; ?>

    y += 4;
    doc.line(20, y, 190, y);
    y += 10;

    /* ---------- TOTAL BOX ---------- */
    doc.setFont("helvetica", "bold");
    doc.setFontSize(13);
    doc.text("Grand Total:", 120, y);
    doc.text("$<?= number_format($order['order_amount'],2) ?>", 160, y);

    y += 20;

    /* ---------- FOOTER ---------- */
    doc.setFontSize(10);
    doc.setFont("helvetica", "italic");
    doc.text("Thank you for shopping with us!", 105, y, { align: "center" });
    y += 6;
    doc.text("This is a system generated invoice.", 105, y, { align: "center" });

    doc.save("Invoice_Order_<?= $order_id ?>.pdf");
});
</script>


</body>
</html>
