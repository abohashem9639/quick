<?php
session_start();
unset($_SESSION['cart']);
if (isset($_SESSION['pending_cart_item'])) {
    $_SESSION['cart'] = [$_SESSION['pending_cart_item']];
    unset($_SESSION['pending_cart_item']);
}
echo "success";
?>
