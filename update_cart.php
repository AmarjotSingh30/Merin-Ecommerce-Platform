<?php
session_start();

$idx = intval($_POST['index']);
$change = intval($_POST['change']);

if (isset($_SESSION['cart'][$idx])) {
    $_SESSION['cart'][$idx]['quantity'] += $change;

    if ($_SESSION['cart'][$idx]['quantity'] < 1) {
        $_SESSION['cart'][$idx]['quantity'] = 1;
    }
}

echo "updated";
?>
