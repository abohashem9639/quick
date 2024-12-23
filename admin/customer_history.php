<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../includes/db.php'; // PDO bağlantısını içerir

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Erişim reddedildi");
}

$restaurant_id = $_SESSION['restaurant_id'];

// Müşteri geçmişi verilerini çekiyoruz
$sql = "SELECT users.id AS user_id, users.first_name, users.last_name, users.phone, COUNT(orders.id) AS order_count, 
               SUM(orders.total_price) AS total_spent
        FROM users
        JOIN orders ON users.id = orders.user_id
        WHERE orders.restaurant_id = :restaurant_id
        GROUP BY users.id, users.first_name, users.last_name, users.phone
        ORDER BY total_spent DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['restaurant_id' => $restaurant_id]);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Müşteri Geçmişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .table {
            background-color: var(--card-bg-color);
            color: var(--text-color);
        }
        .table th, .table td {
            border-color: var(--border-color);
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
                <li class="nav-item">
                    <a class="nav-link" href="manage_orders.php">Siparişleri Yönet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_products.php">Ürünleri Yönet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="promotions.html">Promosyon Yönetimi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="customer_history.php">Müşteri Geçmişi</a>
                </li>
                <li class="nav-item">
                        <a class="nav-link" href="sales_analytics.php">Satış Analitikleri</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Çıkış Yap</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container" style="margin-top: 50px;">
    <h2>Müşteri Geçmişi</h2>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Ad Soyad</th>
                <th>Telefon</th>
                <th>Sipariş Sayısı</th>
                <th>Toplam Harcama</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                    <td><?php echo $customer['order_count']; ?></td>
                    <td><?php echo $customer['total_spent']; ?> TL</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
