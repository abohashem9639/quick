<!-- rate_order.php -->
<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Siparişi değerlendirmek için giriş yapmalısınız.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $order_id = $_POST['order_id'];
    $restaurant_id = $_POST['restaurant_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Aynı sipariş için önceki bir değerlendirmeyi kontrol et
    $sql_check = "SELECT * FROM ratings WHERE user_id = :user_id AND order_id = :order_id";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute(['user_id' => $user_id, 'order_id' => $order_id]);

    if ($stmt_check->rowCount() > 0) {
        $_SESSION['error_message'] = "Bu siparişi daha önce değerlendirdiniz.";
    } else {
        // Yeni değerlendirmeyi veritabanına ekle
        $sql = "INSERT INTO ratings (user_id, order_id, restaurant_id, rating, comment) VALUES (:user_id, :order_id, :restaurant_id, :rating, :comment)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'user_id' => $user_id,
            'order_id' => $order_id,
            'restaurant_id' => $restaurant_id,
            'rating' => $rating,
            'comment' => $comment
        ]);

        if ($result) {
            // Tüm ilgili siparişlerin değerlendirildi olarak işaretlenmesini güncelle
            $sql_update_order = "UPDATE orders SET rated = TRUE WHERE restaurant_id = :restaurant_id AND user_id = :user_id";
            $stmt_update_order = $pdo->prepare($sql_update_order);
            $stmt_update_order->execute(['restaurant_id' => $restaurant_id, 'user_id' => $user_id]);

            $_SESSION['success_message'] = "Değerlendirme başarıyla gönderildi";
        } else {
            $_SESSION['error_message'] = "Değerlendirme ekleme hatası.";
        }
    }

    header("Location: orders.php");
    exit();
}

$pdo = null;
?>
