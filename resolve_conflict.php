<!-- resolve_conflict.php -->
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Çakışmaları çözmek için giriş yapmanız gerekiyor.");
}

if (!isset($_POST['action']) || !isset($_SESSION['conflict'])) {
    die("Bir işlem seçilmedi veya bir çakışma bulunamadı.");
}

$action = $_POST['action'];
$new_item = $_SESSION['conflict']['new_item'];

if ($action == 'keep_previous') {
    // Önceki siparişe devam et, sadece kullanıcıyı yönlendir
    unset($_SESSION['conflict']);
    header('Location: orders.php');
    exit;
} elseif ($action == 'replace_with_current') {
    // Mevcut siparişle devam et: Geçerli sepeti boşalt ve yeni öğeyi ekle
    $_SESSION['cart'] = [$new_item];
    unset($_SESSION['conflict']);

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // AJAX türü istek için JSON yanıtı gönder
        echo json_encode(['status' => 'success', 'message' => 'Önceki sipariş mevcut siparişle değiştirildi.']);
    } else {
        // POST türü istek için kullanıcıyı yönlendir
        $_SESSION['success_message'] = "Önceki sipariş mevcut siparişle değiştirildi.";
        header('Location: orders.php');
    }
    exit;
} else {
    die("Geçersiz işlem.");
}
?>
