<?php
include '../includes/db.php'; // Veritabanı bağlantısı

session_start();

if (!isset($_SESSION['restaurant_id'])) {
    echo json_encode(["error" => "Yetkilendirme hatası: Restoran ID bulunamadı."]);
    exit;
}

$restaurant_id = $_SESSION['restaurant_id'];

try {
    // Ürünleri getir
    $sql = "SELECT id, name, price, image FROM menu_items WHERE restaurant_id = :restaurant_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['restaurant_id' => $restaurant_id]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ürün bilgilerini JSON formatında döndür
    echo json_encode($products);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
