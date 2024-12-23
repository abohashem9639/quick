<!-- manage_orders.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --bg-color: #121212;
            --text-color: #ffffff;
            --navbar-bg-color: #1c1c1c;
            --border-color: #444;
            --primary-color: red;
            --primary-hover-color: #e65c00;
            --secondary-color: #888;
            --secondary-hover-color: #777;
            --danger-color: #ff0000;
            --danger-hover-color: #cc0000;
            --card-bg-color: #1c1c1c;
            --modal-bg-color: #333;
            --modal-content-bg-color: #2c2c2c;
            --muted-text-color: #bbb;
        }

        .footer {
        background-color: var(--navbar-bg-color);
        color: white;
        text-align: center;
        padding: 20px 0;
        margin-top: 300px;
    }

    .footer p {
        margin: 0;
        font-size: 14px;
    }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: var(--navbar-bg-color);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }
        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
            font-size: 1.5rem;
            text-align: left;
        }
        .navbar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            align-items: center;
        }
        .nav-item {
            margin-left: 15px;
        }
        .nav-link {
            color: var(--text-color) !important; 
            font-size: 1.1rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color) !important;
            
        }
        .container {
            padding-left: 10px;
            padding-right: 10px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            padding: 5px;
            margin-bottom: 10px;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover-color);
        }
        .btn-success {
            background-color: green;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            padding: 5px;
            margin-bottom: 10px;
        }
        .btn-success:hover {
            background-color: #006400;
        }
        .btn-danger {
            background-color: var(--danger-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            padding: 5px;
            margin-bottom: 10px;
        }
        .btn-danger:hover {
            background-color: var(--danger-hover-color);
        }
        .card {
            background-color: var(--card-bg-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .card-header, .card-footer {
            background-color: var(--card-bg-color);
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
        }
        .card-body {
            color: var(--text-color);
        }
        .list-group-item {
            background-color: var(--card-bg-color);
            color: var(--text-color);
            border: none;
            border-bottom: 1px solid var(--border-color);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light w-100">
    <div class="container">
        <a class="navbar-brand" href="#">Quick</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_role'] == 'superadmin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Genel Yönetim Paneli</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_restaurants.php">Restoranları Yönet</a>
                        </li>
                    <?php elseif ($_SESSION['user_role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="manage_orders.php">Siparişleri Yönet</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_product.php">Ürünleri Yönet</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="promotions.html">Promosyon Yönetimi</a>
                        </li> 
                        <li class="nav-item">
                            <a class="nav-link" href="customer_history.php">Müşteri Geçmişi</a>
                        </li> 
                        <li class="nav-item">
                        <a class="nav-link" href="sales_analytics.php">Satış Analitikleri</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="restaurants.php">Restoranlar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">Sipariş Sepeti</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profil</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Çıkış Yap</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../login.php">Giriş Yap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../register.php">Hesap Oluştur</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container" style="margin-top: 50px;">
<?php
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Erişim Reddedildi");
}

$restaurant_id = $_SESSION['restaurant_id'];

// İstekleri İşleme
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['order_action']) && isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        $status = $_POST['order_action'];
        $rejection_message = $_POST['rejection_message'] ?? '';

        $sql = "UPDATE orders 
                SET status = :status, rejection_message = :rejection_message 
                WHERE restaurant_id = :restaurant_id 
                  AND user_id = :user_id 
                  AND status = 'pending'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'status' => $status,
            'rejection_message' => $rejection_message,
            'restaurant_id' => $restaurant_id,
            'user_id' => $user_id,
        ]);

        echo "<div class='alert alert-success'>Sipariş Durumu Güncellendi</div>";
    }

    if (isset($_POST['delete_order_item'])) {
        $order_item_id = $_POST['order_item_id'];

        $sql = "DELETE FROM order_items WHERE id = :order_item_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['order_item_id' => $order_item_id]);

        echo "<div class='alert alert-success'>Sipariş Silindi</div>";
    }
}

// Ürün adlarını alma işlevi
function getProductNames($pdo, $identifiers) {
    if (empty($identifiers)) {
        return [];
    }
    $placeholders = implode(",", array_fill(0, count($identifiers), '?'));
    $sql = "SELECT id, name FROM optional_ingredients WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($identifiers);

    $names = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $names[$row['id']] = $row['name'];
    }
    return $names;
}

$sql = "SELECT orders.id as order_id, orders.user_id, users.first_name, users.last_name, users.phone, orders.user_address, orders.total_price, orders.status, orders.created_at
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        WHERE orders.restaurant_id = :restaurant_id AND orders.status = 'pending'
        ORDER BY orders.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['restaurant_id' => $restaurant_id]);

$orders = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $order_id = $row['order_id'];
    $user_id = $row['user_id'];
    $datetime = date('Y-m-d H:i', strtotime($row['created_at']));

    if (!isset($orders[$user_id])) {
        $orders[$user_id] = [
            'user' => $row['first_name'] . ' ' . $row['last_name'],
            'phone' => $row['phone'],
            'address' => $row['user_address'],
            'items' => [],
            'total_price' => 0,
            'status' => $row['status'],
            'last_order_date' => $datetime
        ];
    }

    $orders[$user_id]['total_price'] += $row['total_price'];

    $sql_items = "SELECT item_name, ingredients, SUM(quantity) as quantity, price, note, unavailable_action 
                  FROM order_items 
                  WHERE order_id = :order_id 
                  GROUP BY item_name, ingredients, price, note, unavailable_action";
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->execute(['order_id' => $order_id]);

    while ($item = $stmt_items->fetch(PDO::FETCH_ASSOC)) {
        $item['ingredients'] = json_decode($item['ingredients']);
        $orders[$user_id]['items'][] = $item;
    }
}

?>

<div class="container" dir="ltr" id="orders-container">
    <h2>Sipariş Yönetimi</h2>
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">Şu anda sipariş bulunmamaktadır.</div>
    <?php else: ?>
        <?php foreach ($orders as $user_id => $order): ?>
            <div class="card mb-3">
                <div class="card-header">
                    Müşteri: <?php echo $order['user']; ?><br>
                    Telefon: <?php echo $order['phone']; ?><br>
                    Adres: <?php echo $order['address']; ?><br>
                    Son Sipariş Tarihi: <?php echo $order['last_order_date']; ?>
                </div>
                <ul class="list-group list-group-flush">
                    <?php 
                    $items = [];
                    foreach ($order['items'] as $item) {
                        $key = $item['item_name'] . '_' . $item['price'];
                        if (!isset($items[$key])) {
                            $items[$key] = $item;
                        } else {
                            $items[$key]['quantity'] += $item['quantity'];
                        }
                    }
                    foreach ($items as $item): 
                        $ingredient_names = getProductNames($pdo, $item['ingredients']);
                    ?>
                    <li class="list-group-item">
                        <?php echo htmlspecialchars($item['item_name']); ?> - Adet: <?php echo $item['quantity']; ?> - Birim Fiyat: <?php echo $item['price']; ?> TL - Toplam Fiyat: <?php echo $item['price'] * $item['quantity']; ?> TL
                        <?php if (!empty($item['note'])): ?>
                            <br>Not: <?php echo htmlspecialchars($item['note']); ?>
                        <?php endif; ?>
                        <br>İstemediği Malzemeler:
                        <ul>
                            <?php foreach ($ingredient_names as $name): ?>
                                <li><?php echo htmlspecialchars($name); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <small>Ürün Bulunmazsa: <?php echo $item['unavailable_action'] == 'remove' ? 'Siparişten Kaldır' : 'Tüm Siparişi İptal Et'; ?></small>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="card-footer">
                    Toplam Fiyat: <?php echo array_sum(array_map(function($item) {
                        return $item['price'] * $item['quantity'];
                    }, $items)); ?> TL
                    <form method="post" class="d-inline">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        <textarea name="rejection_message" class="form-control mb-2" placeholder="Reddetme Mesajı (İsteğe Bağlı)"></textarea>
                        <button type="submit" name="order_action" value="approved" class="btn btn-success">Siparişleri Onayla</button>
                        <button type="submit" name="order_action" value="rejected" class="btn btn-danger">Siparişleri Reddet</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
include '../includes/footer.php'; 
?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
