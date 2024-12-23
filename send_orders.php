<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to send orders.");
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart)) {
    $_SESSION['error_message'] = "No items in the cart.";
    header('Location: orders.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Kullanıcı adresini `general_users` tablosundan getirme
$sql = "SELECT address FROM general_users WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && !empty($row['address'])) {
    $user_address = $row['address'];
} else {
    $_SESSION['error_message'] = "User address not found. Please update your profile.";
    header('Location: orders.php');
    exit;
}

// Sepetteki siparişleri işleme
if (isset($_POST['send_all'])) {
    foreach ($cart as $item) {
        send_order($item, $pdo, $user_id, $user_address);
    }
    unset($_SESSION['cart']);
    $_SESSION['success_message'] = "Sipariş başarıyla gönderildi, restoranın onayını bekleyin.";
} elseif (isset($_POST['send_restaurant'])) {
    $restaurant_id = $_POST['send_restaurant'];
    foreach ($cart as $key => $item) {
        if ($item['restaurant_id'] == $restaurant_id) {
            send_order($item, $pdo, $user_id, $user_address);
            unset($cart[$key]);
        }
    }
    $_SESSION['cart'] = $cart;
    $_SESSION['success_message'] = "Restaurant orders have been successfully sent.";
} else {
    $_SESSION['error_message'] = "No action selected.";
}

header('Location: orders.php');
exit();

// Sipariş gönderme fonksiyonu
function send_order($item, $pdo, $user_id, $user_address) {
    $restaurant_id = $item['restaurant_id'];
    $note = isset($item['note']) ? $item['note'] : '';
    $ingredients = isset($item['ingredients']) ? json_encode($item['ingredients']) : '[]';
    $current_time = date('Y-m-d H:i:s'); // Sipariş gönderim zamanı
    
    // İndirimli fiyatı hesapla
    $price = isset($item['discounted_price']) ? $item['discounted_price'] : $item['price'];
    $total_price = $price * $item['quantity'];
    
    // Siparişi `orders` tablosuna ekle
    $sql = "INSERT INTO orders (user_id, restaurant_id, total_price, user_address, status, created_at) 
            VALUES (:user_id, :restaurant_id, :total_price, :user_address, 'pending', :created_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_id' => $user_id,
        'restaurant_id' => $restaurant_id,
        'total_price' => $total_price,
        'user_address' => $user_address,
        'created_at' => $current_time
    ]);

    // Sipariş ID'sini alın
    $order_id = $pdo->lastInsertId();

    // Sipariş öğelerini `order_items` tablosuna ekle
    $sql = "INSERT INTO order_items (order_id, item_name, quantity, price, note, unavailable_action, ingredients) 
            VALUES (:order_id, :item_name, :quantity, :price, :note, :unavailable_action, :ingredients)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'order_id' => $order_id,
        'item_name' => $item['name'],
        'quantity' => $item['quantity'],
        'price' => $price, // İndirimli fiyatı kullanıyoruz
        'note' => $note,
        'unavailable_action' => $item['unavailable_action'],
        'ingredients' => $ingredients
    ]);
}
?>
