<!-- add_products.php file -->

<?php
include '../includes/headerAdmin.php';
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $restaurant_id = $_SESSION['restaurant_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image'];

    // معالجة تحميل الصورة
    if ($image['error'] == 0) {
        $imageName = time() . '-' . $image['name'];
        $imagePath = '../uploads/' . $imageName;
        move_uploaded_file($image['tmp_name'], $imagePath);
    } else {
        $imageName = null;
    }

    // استخدام PDO لإضافة المنتج إلى قاعدة البيانات
    $sql = "INSERT INTO menu_items (restaurant_id, name, price, image) VALUES (:restaurant_id, :name, :price, :image)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':restaurant_id' => $restaurant_id,
        ':name' => $name,
        ':price' => $price,
        ':image' => $imageName
    ]);

    if ($result) {
        echo "<div class='alert alert-success'>تم إضافة المنتج بنجاح</div>";
    } else {
        echo "<div class='alert alert-danger'>خطأ: " . implode(" ", $stmt->errorInfo()) . "</div>";
    }
}

// إعادة التوجيه إلى صفحة إدارة المنتجات
header('Location: manage_products.php');
exit();
?>
