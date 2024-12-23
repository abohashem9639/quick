<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kullanıcının oturum açıp açmadığını kontrol ediyoruz
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

try {
    // Kullanıcının favori restoranlarını getiriyoruz
    $sql = "SELECT r.id, r.name, r.image 
            FROM favorite_restaurants fr
            JOIN restaurants r ON fr.restaurant_id = r.id
            WHERE fr.user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favori Restoranlarım</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
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
            margin-top: auto;
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

          .favorite-btn i {
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.3s ease;
            color: gold;
        }

        .favorite-btn i:hover {
            color: #ccc;
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


<div class="container mt-5">
    <h2>Favori Restoranlarım</h2>
    <div class="row">
        <?php if (!empty($favorites)): ?>
            <?php foreach ($favorites as $row): ?>
                <div class="col-md-4 mb-4 restaurant-card" data-restaurant-id="<?php echo $row['id']; ?>">
                    <div class="card">
                        <button class="btn btn-link favorite-btn p-0 position-absolute top-0 end-0 mt-2 me-2">
                            <i class="fas fa-star"></i>
                        </button>
                        <?php if ($row['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <a href="menu.php?restaurant_id=<?php echo $row['id']; ?>" class="btn btn-primary">Menüyü Görüntüle</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Henüz favori restoran eklemediniz.</p>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.favorite-btn').on('click', function() {
        const restaurantId = $(this).closest('.restaurant-card').data('restaurant-id');
        const card = $(this).closest('.restaurant-card');

        $.ajax({
            url: 'remove_favorite.php',
            type: 'POST',
            data: { restaurant_id: restaurantId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    card.remove(); // Restoran kartını sayfadan kaldır
                } else {
                    console.error('Hata:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Hatası:', error);
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>