<?php
include '../includes/db.php'; // Veritabanı bağlantısı

session_start(); // Oturum başlat

// Restoran ID oturumdan alınır
if (!isset($_SESSION['restaurant_id'])) {
    echo json_encode(["error" => "Yetkilendirme hatası: Restoran ID bulunamadı."]);
    exit;
}

$restaurant_id = $_SESSION['restaurant_id'];

try {
    // Sadece oturumdaki restoranın kampanyalarını getiren sorgu
    $sql = "
        SELECT promotions.*, 
               string_agg(menu_items.name, ', ') AS product_names
        FROM promotions
        LEFT JOIN promotion_products ON promotions.id = promotion_products.promotion_id
        LEFT JOIN menu_items ON promotion_products.product_id = menu_items.id
        WHERE promotions.restaurant_id = :restaurant_id
        GROUP BY promotions.id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':restaurant_id' => $restaurant_id]);
    $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kampanya ürünlerini detaylı eklemek için
    foreach ($promotions as &$promotion) {
        $promotionId = $promotion['id'];
        $productsQuery = $pdo->prepare("
            SELECT name FROM menu_items 
            JOIN promotion_products 
            ON menu_items.id = promotion_products.product_id 
            WHERE promotion_products.promotion_id = :promotionId
        ");
        $productsQuery->execute([':promotionId' => $promotionId]);
        $promotion['products'] = $productsQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($promotions);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
