<?php
session_start();
header('Content-Type: application/json');

include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['restaurant_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Eksik parametreler.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$restaurant_id = intval($_POST['restaurant_id']);

try {
    $stmt = $pdo->prepare("INSERT INTO favorite_restaurants (user_id, restaurant_id) VALUES (:user_id, :restaurant_id)");
    $stmt->execute(['user_id' => $user_id, 'restaurant_id' => $restaurant_id]);
    echo json_encode(['status' => 'success', 'message' => 'Favorilere eklendi.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?>
