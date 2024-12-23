<!-- manage_promotions.php -->
<?php
include '../includes/db.php'; // Veritabanı bağlantısını içe aktar

session_start(); // Oturumu başlat

// Restoran ID oturumdan alınır
if (!isset($_SESSION['restaurant_id'])) {
    echo json_encode(["error" => "Yetkilendirme hatası: Restoran ID bulunamadı."]);
    exit;
}

$restaurant_id = $_SESSION['restaurant_id'];

// Kampanyaları listeleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $sql = "
    SELECT p.*, 
           ARRAY_AGG(mi.name) AS product_names
    FROM promotions p
    LEFT JOIN promotion_products pp ON p.id = pp.promotion_id
    LEFT JOIN menu_items mi ON pp.product_id = mi.id
    WHERE p.restaurant_id = :restaurant_id
    GROUP BY p.id
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':restaurant_id' => $restaurant_id]);
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Kampanya ürünlerini formatlama
        foreach ($promotions as &$promotion) {
            $promotion['product_names'] = !empty($promotion['product_names']) 
                ? array_filter(explode(',', str_replace(['{', '}'], '', $promotion['product_names']))) 
                : [];
        }

        echo json_encode($promotions); // JSON formatında döndür
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

// Kampanya ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $discount = $_POST['discount'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $product_ids = isset($_POST['product_ids']) ? $_POST['product_ids'] : [];

    try {
        // Yeni kampanya ekle
        $sql = "INSERT INTO promotions (restaurant_id, name, description, discount, start_date, end_date)
                VALUES (:restaurant_id, :name, :description, :discount, :start_date, :end_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':restaurant_id' => $restaurant_id,
            ':name' => $name,
            ':description' => $description,
            ':discount' => $discount,
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);

        $promotion_id = $pdo->lastInsertId();

        // Kampanya ürünlerini ilişkilendir
        if (!empty($product_ids)) {
            $sql = "INSERT INTO promotion_products (promotion_id, product_id) VALUES (:promotion_id, :product_id)";
            $stmt = $pdo->prepare($sql);
            foreach ($product_ids as $product_id) {
                $stmt->execute([':promotion_id' => $promotion_id, ':product_id' => $product_id]);
            }
        }

        echo json_encode(["success" => "Promosyon başarıyla eklendi."]);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

// Kampanya silme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    $promotion_id = $_DELETE['id'];

    try {
        // Kampanyayı ve ilişkili ürünleri sil
        $pdo->beginTransaction();
        $sql = "DELETE FROM promotion_products WHERE promotion_id = :promotion_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':promotion_id' => $promotion_id]);

        $sql = "DELETE FROM promotions WHERE id = :id AND restaurant_id = :restaurant_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $promotion_id, ':restaurant_id' => $restaurant_id]);
        $pdo->commit();

        echo json_encode(["success" => "Promosyon başarıyla silindi."]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

// Kampanya güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    parse_str(file_get_contents("php://input"), $_PUT);
    $promotion_id = $_PUT['id'];
    $name = $_PUT['name'];
    $description = $_PUT['description'];
    $discount = $_PUT['discount'];
    $start_date = $_PUT['start_date'];
    $end_date = $_PUT['end_date'];

    try {
        // Kampanya güncelle
        $sql = "UPDATE promotions 
                SET name = :name, description = :description, discount = :discount, 
                    start_date = :start_date, end_date = :end_date 
                WHERE id = :id AND restaurant_id = :restaurant_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id' => $promotion_id,
            ':restaurant_id' => $restaurant_id,
            ':name' => $name,
            ':description' => $description,
            ':discount' => $discount,
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);

        echo json_encode(["success" => "Promosyon başarıyla güncellendi."]);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // جلب العروض الخاصة بالمطعم
    $restaurant_id = $_GET['restaurant_id'];

    try {
        $sql = "SELECT p.*, ARRAY_AGG(pp.product_id) AS product_ids 
                FROM promotions p
                LEFT JOIN promotion_products pp ON p.id = pp.promotion_id
                WHERE p.restaurant_id = :restaurant_id
                GROUP BY p.id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':restaurant_id' => $restaurant_id]);
        $promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($promotions);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $restaurant_id = $_SESSION['restaurant_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $discount = $_POST['discount'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $product_ids = isset($_POST['product_ids']) ? $_POST['product_ids'] : [];

    try {
        // إضافة العرض
        $sql = "INSERT INTO promotions (restaurant_id, name, description, discount, start_date, end_date)
                VALUES (:restaurant_id, :name, :description, :discount, :start_date, :end_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':restaurant_id' => $restaurant_id,
            ':name' => $name,
            ':description' => $description,
            ':discount' => $discount,
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);

        $promotion_id = $pdo->lastInsertId();

        // ربط المنتجات بالعرض
        if (!empty($product_ids)) {
            $sql = "INSERT INTO promotion_products (promotion_id, product_id) VALUES (:promotion_id, :product_id)";
            $stmt = $pdo->prepare($sql);
            foreach ($product_ids as $product_id) {
                $stmt->execute([':promotion_id' => $promotion_id, ':product_id' => $product_id]);
            }
        }

        echo "تم إضافة العرض بنجاح.";
    } catch (PDOException $e) {
        echo "خطأ: " . $e->getMessage();
    }
    exit;
}
?>
