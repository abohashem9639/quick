<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../includes/db.php'; // PDO bağlantısını içerir

// Kullanıcı erişim kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Erişim reddedildi");
}

$restaurant_id = $_SESSION['restaurant_id'];

// Toplam gelir, toplam satış ve en çok satılan ürünler sorguları
try {
    // Toplam gelir
    $sql = "SELECT SUM(total_price) AS total_revenue FROM sales_analytics WHERE restaurant_id = :restaurant_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['restaurant_id' => $restaurant_id]);
    $total_revenue = $stmt->fetchColumn();

    // Toplam satış adedi
    $sql = "SELECT COUNT(*) AS total_sales FROM sales_analytics WHERE restaurant_id = :restaurant_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['restaurant_id' => $restaurant_id]);
    $total_sales = $stmt->fetchColumn();

    // En çok satılan ürünler
    $sql = "SELECT product_id, SUM(quantity) AS total_quantity, SUM(total_price) AS total_revenue
            FROM sales_analytics 
            WHERE restaurant_id = :restaurant_id
            GROUP BY product_id
            ORDER BY total_quantity DESC
            LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['restaurant_id' => $restaurant_id]);
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Hata: " . $e->getMessage());
}

// Ürün isimlerini almak için fonksiyon
function getProductName($pdo, $product_id) {
    $sql = "SELECT name FROM menu_items WHERE id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['product_id' => $product_id]);
    return $stmt->fetchColumn();
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satış Analitikleri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-color: #121212;
            --text-color: #ffffff;
            --navbar-bg-color: #1c1c1c;
            --border-color: #444;
            --primary-color: red;
            --primary-hover-color: #e65c00;
            --card-bg-color: #1c1c1c;
            --muted-text-color: #bbb;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: Arial, sans-serif;
        }
        .container {
            padding-left: 10px;
            padding-right: 10px;
            margin-left: auto;
            margin-right: auto;
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

        .card {
            background-color: var(--card-bg-color);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .card-header, .card-footer {
            background-color: var(--card-bg-color);
            color: var(--text-color);
        }
        .table {
            color: var(--text-color);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Quick</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav" dir="ltr">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="manage_orders.php">Siparişleri Yönet</a>
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
                        <a class="nav-link active" href="sales_analytics.php">Satış Analitikleri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Çıkış Yap</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<div class="container">
    <h2 style="margin-top: 50px;">Satış Analitikleri</h2>
    <hr>

    <!-- Genel Veriler -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Toplam Gelir</div>
                <div class="card-body">
                    <h3><?php echo number_format($total_revenue ?? 0, 2); ?> TL</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Toplam Sipariş</div>
                <div class="card-body">
                    <h3><?php echo $total_sales; ?> Sipariş</h3>
                </div>
            </div>
        </div>
    </div>

<!-- En Çok Satılan Ürünler -->
<h3>En Çok Satılan Ürünler</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Ürün Adı</th>
            <th>Toplam Satış Adedi</th>
            <th>Toplam Gelir</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($top_products)): ?>
            <?php foreach ($top_products as $product): ?>
                <tr>
                    <td><?php echo getProductName($pdo, $product['product_id']); ?></td>
                    <td><?php echo $product['total_quantity']; ?></td>
                    <td><?php echo number_format($product['total_revenue'] ?? 0, 2); ?> TL</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">Satılan ürün yok</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
