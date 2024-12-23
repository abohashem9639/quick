<!-- restaurant_ratings.php -->
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

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: Arial, sans-serif;
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

        .btn-info:hover {
            background-color: var(--primary-hover-color);
        }
        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .col-md-4 {
            flex: 0 0 23%;
            max-width: 23%;
            margin: 10px;
        }
        @media (max-width: 768px) {
            .col-md-4 {
                flex: 0 0 48%;
                max-width: 48%;
            }
        }
        .card {
            background-color: var(--card-bg-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
        .card-img-top {
            width: 100%;
            height: 200px;
            border-radius: 12px;
            object-fit: cover;
        }
        .card-title {
            color: var(--primary-color);
            font-size: 1.2rem;
            margin-top: 10px;
        }
        .card-text {
            color: var(--muted-text-color);
            margin-bottom: 15px;
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 500px;
            background-color: var(--modal-bg-color);
            color: var(--text-color);
            padding: 1.5rem;
            border-radius: 0.3rem;
            box-shadow: 0 3px 7px rgba(0, 0, 0, 0.25);
            align-items: center;
        }
        .modal-content {
            color: var(--text-color);
            background-color: var(--modal-content-bg-color);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        .close-btn {
            display: block;
            margin: 1rem auto 0 auto;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.25rem;
            background-color: var(--danger-color);
            color: var(--text-color);
            cursor: pointer;
        }
        .close-btn:hover {
            background-color: var(--danger-hover-color);
        }
        .btn-secondary, .btn-danger {
            margin-top: 10px;
            width: 100%;
        }
        .btn-secondary {
            background-color: var(--secondary-color);
            border: none;
        }
        .btn-secondary:hover {
            background-color: var(--secondary-hover-color);
        }
        .btn-danger {
            background-color: var(--danger-color);
            border: none;
        }
        .btn-danger:hover {
            background-color: var(--danger-hover-color);
        }
        .modal h5 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .modal p {
            color: var(--muted-text-color);
            margin-bottom: 1rem;
        }
        .modal ul {
            list-style-type: none;
            padding: 0;
        }
        .modal ul li {
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }
        .modal ul li label {
            color: var(--primary-color);
        }
        .restaurant-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .restaurant-info img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 12px;
        }
        .restaurant-info-details {
            text-align: right;
            margin-bottom: 15px;
        }
        .restaurant-info-details p {
            color: var(--muted-text-color);
            margin: 5px 0;
            font-size: 1.1rem;
        }
        .btn-info {
            background-color: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            padding: 5px;
            margin-bottom: 10px;
            color: black;
        }
        .quantity-input {
            text-align: center;
            width: 50px;
            margin: 0 10px;
            -moz-appearance: textfield;
        }
        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .quantity-input {
            -moz-appearance: textfield;
        }
        .rating-box {
            background-color: var(--card-bg-color);
            border: 1px solid var(--border-color);
            border-radius: 25px;
            padding: 15px;
            margin-bottom: 20px;
            color: var(--text-color);
        }
        .rating-box p {
            margin: 0;
            margin-bottom: 10px;
        }

        
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light w-100">
    <div class="container">
        <a class="navbar-brand" href="index.php">Quick</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_role'] == 'superadmin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">Genel Yönetici Paneli</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_restaurants.php">Restoranları Yönet</a>
                        </li>
                    <?php elseif ($_SESSION['user_role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_orders.php">Siparişleri Yönet</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_product.php">Yeni Ürün Ekle</a>
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
                        <li class="nav-item">
                            <a class="nav-link active" href="favorites.php">Favorilerim</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Çıkış Yap</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Giriş Yap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Hesap Oluştur</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container" style="margin-top: 50px;" dir="ltr">
<?php
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Değerlendirmeleri görmek için giriş yapmanız gerekiyor.");
}

$restaurant_id = $_GET['restaurant_id'] ?? null;

if (!$restaurant_id) {
    die("Restoran kimliği mevcut değil.");
}

// Restoran bilgilerini çek
$sql_restaurant = "SELECT name, image FROM restaurants WHERE id = :restaurant_id";
$stmt_restaurant = $pdo->prepare($sql_restaurant);
$stmt_restaurant->execute(['restaurant_id' => $restaurant_id]);
$restaurant = $stmt_restaurant->fetch(PDO::FETCH_ASSOC);


if (!$restaurant) {
    die("Restoran bulunamadı.");
}

// Ortalama puan ve toplam yorum sayısını çek
$sql_summary = "SELECT avg_rating, total_reviews FROM restaurant_ratings WHERE restaurant_id = :restaurant_id";
$stmt_summary = $pdo->prepare($sql_summary);
$stmt_summary->execute(['restaurant_id' => $restaurant_id]);
$summary = $stmt_summary->fetch(PDO::FETCH_ASSOC);

// Veritabanından değerlendirmeleri çek
$sql_ratings = "SELECT ratings.rating, ratings.comment, ratings.created_at, users.first_name, users.last_name
                FROM ratings 
                JOIN users ON ratings.user_id = users.id 
                WHERE ratings.restaurant_id = :restaurant_id
                ORDER BY ratings.created_at DESC";
$stmt_ratings = $pdo->prepare($sql_ratings);
$stmt_ratings->execute(['restaurant_id' => $restaurant_id]);
$ratings = $stmt_ratings->fetchAll(PDO::FETCH_ASSOC);

// Başarı veya hata mesajını çek
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<div class="container" dir="ltr">
<div class="restaurant-info text-center">
    <!-- Restoran Fotoğrafı -->
    <?php if (!empty($restaurant['image'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($restaurant['image']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" class="img-fluid mb-4">
    <?php else: ?>
        <img src="uploads/default.jpg" alt="Varsayılan Resim" class="img-fluid mb-4">
    <?php endif; ?>
    
    <!-- Restoran Adı ve Bilgileri -->
    <h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
    <p><strong>Ortalama Puan:</strong> <?php echo htmlspecialchars($summary['avg_rating'] ?? '0'); ?> / 10</p>
    <p><strong>Toplam Yorum:</strong> <?php echo htmlspecialchars($summary['total_reviews'] ?? 0); ?></p>
</div>


    <h2>Müşteri Değerlendirmeleri</h2>
    <br>
    <?php if ($success_message || $error_message): ?>
        <div class="alert <?php echo $success_message ? 'alert-success' : 'alert-danger'; ?>">
            <?php echo htmlspecialchars($success_message ? $success_message : $error_message); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($ratings)): ?>
        <div class="alert alert-info">Henüz değerlendirme yok.</div>
    <?php else: ?>
        <?php foreach ($ratings as $rating): ?>
            <div class="rating-box">
                <p><strong>Değerlendiren: </strong><bdi><?php echo htmlspecialchars(substr($rating['first_name'], 0, 3) . '*** ' . substr($rating['last_name'], 0, 3) . '***'); ?></bdi></p>
                <p><strong>Değerlendirme: </strong><?php echo htmlspecialchars($rating['rating']); ?>/10</p>
                <?php if (!empty($rating['comment'])): ?>
                    <p><strong>Yorum: </strong><?php echo htmlspecialchars($rating['comment']); ?></p>
                <?php endif; ?>
                <p><strong>Değerlendirme Tarihi: </strong><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($rating['created_at']))); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


<?php
$pdo = null;
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
