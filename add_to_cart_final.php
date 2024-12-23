<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sipariş vermek için giriş yapmanız gerekiyor']);
    exit;
}

$item_id = (int)$_POST['item_id'];
$restaurant_id = (int)$_POST['restaurant_id'];
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$unavailable_action = $_POST['unavailable_action'];
$note = $_POST['note'];
$ingredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : [];
$user_id = $_SESSION['user_id'];

// Ürünü ve varsa kampanyasını kontrol et
$sql = "SELECT menu_items.*, 
               promotions.discount 
        FROM menu_items 
        LEFT JOIN promotion_products ON menu_items.id = promotion_products.product_id
        LEFT JOIN promotions ON promotion_products.promotion_id = promotions.id 
                             AND promotions.start_date <= NOW() 
                             AND promotions.end_date >= NOW()
        WHERE menu_items.id = :item_id AND menu_items.restaurant_id = :restaurant_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['item_id' => $item_id, 'restaurant_id' => $restaurant_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz ürün.']);
    exit;
}

// İndirimli fiyatı hesapla (eğer bir indirim varsa)
$discount = isset($item['discount']) ? $item['discount'] : 0;
$discounted_price = $item['price'] * (1 - $discount / 100);

// Toplam fiyatı hesapla
$total_price = $discounted_price * $quantity;

// Sepet oturumunu kontrol et
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
if (!empty($cart)) {
    $existing_restaurant_id = $cart[0]['restaurant_id'];
    if ($existing_restaurant_id != $restaurant_id) {
        $_SESSION['conflict'] = [
            'new_item' => [
                'item_id' => $item_id,
                'restaurant_id' => $restaurant_id,
                'quantity' => $quantity,
                'price' => $discounted_price, // İndirimli fiyatı sepete ekle
                'total_price' => $total_price,
                'name' => $item['name'],
                'unavailable_action' => $unavailable_action,
                'note' => $note,
                'ingredients' => $ingredients
            ]
        ];
        echo json_encode(['status' => 'conflict']);
        exit;
    }
}

// Ürünü sepete ekle
$_SESSION['cart'][] = [
    'item_id' => $item_id,
    'restaurant_id' => $restaurant_id,
    'quantity' => $quantity,
    'price' => $discounted_price, // İndirimli fiyatı kaydet
    'total_price' => $total_price,
    'name' => $item['name'],
    'unavailable_action' => $unavailable_action,
    'note' => $note,
    'ingredients' => $ingredients
];

echo json_encode(['status' => 'success', 'message' => 'Ürün başarıyla sepete eklendi.']);
exit;
?>
