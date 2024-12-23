<?php
include 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Bu işlemi gerçekleştirmek için giriş yapmış olmanız gerekiyor.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel_all'])) {
        // Sepetten tüm siparişleri sil
        unset($_SESSION['cart']);
        $_SESSION['success_message'] = "Siparişler başarıyla iptal edildi.";
    } elseif (isset($_POST['cancel_restaurant'])) {
        $restaurant_id = $_POST['cancel_restaurant'];
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['restaurant_id'] == $restaurant_id) {
                    unset($_SESSION['cart'][$key]);
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Sepeti yeniden dizinle
            $_SESSION['success_message'] = "Restorandan gelen siparişler başarıyla iptal edildi.";
        }
    } else {
        $_SESSION['error_message'] = "Herhangi bir işlem seçilmedi.";
    }
}

// Başarı mesajıyla yönlendirme
header("Location: orders.php");
exit;
?>
