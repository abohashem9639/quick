<!-- restaurants.php -->
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

        .favorite-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
    background: transparent;
    border: none;
    padding: 0;
    cursor: pointer;
}

.favorite-btn i {
    font-size: 1.5rem;
    transition: color 0.3s ease;
    color: grey;
}

.favorite-btn i.gold {
    color: gold;
}

.favorite-btn:hover i {
    color: #ccc;
}

form.d-flex {
    margin-bottom: 20px;
}

form.d-flex input {
    border: 1px solid var(--border-color);
    border-radius: 5px;
    padding: 10px;
    color: var(--text-color);
    background-color: var(--bg-color);
}

form.d-flex input:focus {
    border-color: var(--primary-color);
    outline: none;
}

form.d-flex button {
    border-radius: 5px;
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
                            <a class="nav-link active" href="restaurants.php">Restoranlar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">Sipariş Sepeti</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="favorites.php">Favorilerim</a>
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

<?php
include 'includes/db.php';


try {
    // Arama terimini kontrol et
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

    $sql = "SELECT restaurants.id, restaurants.name, restaurants.image, 
    COALESCE(rr.avg_rating, 0) AS avg_rating
    FROM restaurants
    LEFT JOIN restaurant_ratings rr ON restaurants.id = rr.restaurant_id";

if (!empty($searchTerm)) {
$sql .= " WHERE restaurants.name LIKE :search";
}

$stmt = $pdo->prepare($sql);

if (!empty($searchTerm)) {
$stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
}

$stmt->execute();
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);



} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
    die();
}
?>

<div class="container mt-5">
    <h2>Restoran Listesi</h2>
    
    <div class="container mt-4">
    <div class="d-flex">
        <input 
            type="text" 
            id="searchInput" 
            class="form-control me-2" 
            placeholder="Restoran ara..." 
        >
    </div>
</div>
<div id="restaurantList" class="row mt-4">
    <!-- Restoranlar buraya yüklenecek -->
</div>

    <div class="row">
    <?php foreach ($restaurants as $row): ?>
    <?php
        // Her restoran için favori durumu kontrol edilir
        $isFavoriteStmt = $pdo->prepare("SELECT COUNT(*) FROM favorite_restaurants WHERE user_id = :user_id AND restaurant_id = :restaurant_id");
        $isFavoriteStmt->execute(['user_id' => $_SESSION['user_id'], 'restaurant_id' => $row['id']]);
        $isFavorite = $isFavoriteStmt->fetchColumn() > 0;
    ?>
    <?php endforeach; ?>
</div>

</div>
<?php
$pdo = null;
include 'includes/footer.php';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).on('click', '.favorite-btn', function() {
    const restaurantId = $(this).data('restaurant-id');
    const button = $(this);
    const isFavorite = button.find('i').hasClass('gold');

    $.ajax({
        url: isFavorite ? 'remove_favorite.php' : 'add_favorite.php',
        type: 'POST',
        data: { restaurant_id: restaurantId },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                if (isFavorite) {
                    button.find('i').removeClass('gold').css('color', 'grey'); // Favoriden çıkarıldı
                } else {
                    button.find('i').addClass('gold').css('color', 'gold'); // Favorilere eklendi
                }
            } else {
                console.error('Hata:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Hatası:', xhr.responseText || error);
        }
    });
});

</script>

<script>
    $(document).ready(function() {
    // Sayfa yüklendiğinde tüm restoranları getir
    fetchRestaurants('');

    // Arama kutusunda yazıldıkça sonuçları getir
    $('#searchInput').on('keyup', function() {
        const query = $(this).val();
        fetchRestaurants(query);
    });

    // Restoranları AJAX ile getir
    function fetchRestaurants(query) {
        $.ajax({
            url: 'fetch_restaurants.php',
            type: 'GET',
            data: { search: query },
            success: function(data) {
                $('#restaurantList').html(data); // Sonuçları ekle
            },
            error: function(xhr, status, error) {
                console.error('Hata:', error);
            }
        });
    }
});
</script>

</body>
</html>
