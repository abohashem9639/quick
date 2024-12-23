<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Ürün ayrıntılarını görüntülemek için giriş yapmalısınız.']);
    exit;
}

$item_id = $_GET['item_id'];
$restaurant_id = $_GET['restaurant_id'];

// Ürünün ayrıntılarını veritabanından almak için sorgu
$sql = "SELECT * FROM menu_items WHERE id = :item_id AND restaurant_id = :restaurant_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['item_id' => $item_id, 'restaurant_id' => $restaurant_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    echo json_encode(['status' => 'error', 'message' => 'Ürün bulunamadı.']);
    exit;
}

// Opsiyonel bileşenleri almak için sorgu
$sql = "SELECT optional_ingredients.id, optional_ingredients.name 
        FROM product_ingredients 
        JOIN optional_ingredients ON product_ingredients.ingredient_id = optional_ingredients.id 
        WHERE product_ingredients.product_id = :item_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['item_id' => $item_id]);
$ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Popup içeriği oluşturma
$modalContent = '<div dir="ltr">
    <label for="note" class="form-label">İstemediğiniz bileşenleri seçin:</label>
    <br><br>';
foreach ($ingredients as $ingredient) {
    $modalContent .= '<div class="form-check">
        <input class="form-check-input" type="checkbox" name="ingredients[]" value="' . htmlspecialchars($ingredient['id']) . '">
        <label class="form-check-label">' . htmlspecialchars($ingredient['name']) . '</label>
    </div>';
}
$modalContent .= '</div><hr>';
$modalContent .= '<div class="mb-3">
    <label for="note" class="form-label">Not (Opsiyonel)</label>
    <br><br>
    <input class="form-control" id="note" name="note"></input>
    <br><br>
</div>';
$modalContent .= '<div class="mb-3">
    <label for="unavailable_action" class="form-label">Ürün mevcut değilse ne yapmak istersiniz?</label>
    <br><br>
    <select class="form-select" id="unavailable_action" name="unavailable_action">
        <option value="remove" selected>Ürünü siparişten çıkar</option>
        <option value="cancel">Siparişi tamamen iptal et</option>
    </select>
</div><br>';

$modalContent .= '<div class="mb-3">
    <label for="quantity" class="form-label">Miktar</label>
    <br>
    <div class="quantity-container">
        <button type="button" class="btn btn-light btn-decrease">-</button>
        <input type="number" class="form-control quantity-input" id="quantity" name="quantity" value="1" min="1" readonly>
        <button type="button" class="btn btn-light btn-increase">+</button>
    </div>
    <br>
</div>';

$modalContent .= '<input type="hidden" name="item_id" value="' . htmlspecialchars($item_id) . '">
                  <input type="hidden" name="restaurant_id" value="' . htmlspecialchars($restaurant_id) . '">
                  <input type="hidden" name="quantity" value="1">
                  <input type="hidden" name="price" value="' . htmlspecialchars($item['price']) . '">';

echo json_encode(['status' => 'success', 'modalContent' => $modalContent]);
exit;
