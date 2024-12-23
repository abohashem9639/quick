<?php
include '../includes/db.php'; // Veritabanı bağlantısını içe aktar

try {
    // Restoranları çek
    $query = "SELECT id, name FROM restaurants";
    $stmt = $pdo->query($query);
    $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON formatında döndür
    header('Content-Type: application/json');
    echo json_encode($restaurants);
} catch (PDOException $e) {
    // Hata mesajı döndür
    header('Content-Type: application/json', true, 500);
    echo json_encode(["error" => "Veritabanı hatası: " . $e->getMessage()]);
}
?>