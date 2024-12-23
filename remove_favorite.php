<?php
session_start();
include 'includes/db.php';

// JSON yanıt için içerik türünü ayarlayın
header('Content-Type: application/json');

// Eksik parametre kontrolü
if (!isset($_POST['restaurant_id']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Eksik parametreler.']);
    exit;
}

$restaurant_id = intval($_POST['restaurant_id']); // Güvenlik için tip dönüşümü
$user_id = intval($_SESSION['user_id']); // Güvenlik için tip dönüşümü

try {
    // Kullanıcının favorilerinden restoranı sil
    $stmt = $pdo->prepare("DELETE FROM favorite_restaurants WHERE user_id = :user_id AND restaurant_id = :restaurant_id");
    $stmt->execute(['user_id' => $user_id, 'restaurant_id' => $restaurant_id]);

    // Silme işleminin başarılı olduğunu kontrol et
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Favorilerden çıkarıldı.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Favorilerden çıkarılacak kayıt bulunamadı.']);
    }
} catch (PDOException $e) {
    // Veritabanı hatalarını yakalayın ve kullanıcıya bilgi verin
    echo json_encode(['status' => 'error', 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
    exit;
}
?>
