<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Bir öğeyi silmek için giriş yapmış olmanız gerekiyor.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];

    if (!empty($order_id)) {
        $sql = "DELETE FROM orders WHERE id = $order_id";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success_message'] = "Sipariş başarıyla silindi.";
        } else {
            $_SESSION['error_message'] = "Hata: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = "Sipariş kimliği mevcut değil.";
    }

    header("Location: orders.php");
    exit();
}

$conn->close();
?>
