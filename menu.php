<!-- menu.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Europe/Istanbul'); // Türkiye saatine göre ayarla

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

    .container .restaurantImg{
        margin-top: 40px;
        margin-bottom: 40px;
        width: 100%;
        height: 100%;
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
    @media (max-width: 576px) {
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

    .modal-custom {
        display: none;
        justify-content: center;
        align-items: center;
        position: fixed;
        z-index: 1050;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content-custom {
        background-color: var(--modal-content-bg-color);
        color: var(--text-color);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        max-width: 500px;
        width: 80%;
        margin: auto;
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
            text-align: left; /* تأكد من أن النص يظهر من اليسار */
            margin-bottom: 15px;
        }
    .restaurant-info-details p {
        color: var(--muted-text-color);
        margin: 5px 0;
        font-size: 1.1rem;
    }

    .btn-info{
        background-color: var(--primary-color);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 15px;
        padding: 5px;
        margin-bottom: 10px;
        color: black;
    }

    .quantity-container {
        display: flex;
        align-items: center;
        justify-content: center;
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

    #itemModal{
        overflow-y: scroll; 
        display: none; /* Chrome, Edge ve Safari için kaydırma çubuğunu gizler */

        -ms-overflow-style: none;  /* Internet Explorer ve Edge için gizle */
    scrollbar-width: none; /* Firefox için gizle */
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

$restaurant_id = $_GET['restaurant_id'];

// Restoran bilgilerini çek
$sql = "SELECT restaurants.*, 
        COALESCE(rr.avg_rating, 0) AS avg_rating 
        FROM restaurants 
        LEFT JOIN restaurant_ratings rr ON restaurants.id = rr.restaurant_id
        WHERE restaurants.id = :restaurant_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['restaurant_id' => $restaurant_id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
    die("Restoran bulunamadı.");
}

// Kampanyalı ürünleri ve kampanya bilgilerini çek (sadece bitmemiş kampanyalar)
$sql = "SELECT promotions.id AS promotion_id, 
               promotions.name AS promotion_name, 
               promotions.description, 
               promotions.discount, 
               promotions.start_date, 
               promotions.end_date, 
               menu_items.*
        FROM promotions
        JOIN promotion_products ON promotions.id = promotion_products.promotion_id
        JOIN menu_items ON promotion_products.product_id = menu_items.id
        WHERE menu_items.restaurant_id = :restaurant_id
        AND promotions.end_date > NOW() -- Bitmemiş kampanyaları filtrele
        ORDER BY promotions.id DESC, menu_items.name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['restaurant_id' => $restaurant_id]);
$campaign_items = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);



// Normal menü ürünlerini çek (kampanyada olmayan veya kampanyası bitmiş ürünler)
$sql = "
    SELECT menu_items.*
    FROM menu_items
    WHERE menu_items.restaurant_id = :restaurant_id
    AND (
        menu_items.id NOT IN (
            SELECT product_id
            FROM promotion_products
            JOIN promotions ON promotion_products.promotion_id = promotions.id
            WHERE promotions.restaurant_id = :restaurant_id
            AND promotions.end_date > NOW() -- Aktif kampanyalar
        )
        OR menu_items.id IN (
            SELECT product_id
            FROM promotion_products
            JOIN promotions ON promotion_products.promotion_id = promotions.id
            WHERE promotions.restaurant_id = :restaurant_id
            AND promotions.end_date <= NOW() -- Kampanyası bitmiş ürünler
        )
    )
";
$stmt = $pdo->prepare($sql);
$stmt->execute(['restaurant_id' => $restaurant_id]);
$normal_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container">
    <div class="restaurant-info">
        <img class="restaurantImg" src="uploads/<?php echo htmlspecialchars($restaurant['image'] ?? ''); ?>" alt="Restoran Görseli">
        <div class="restaurant-info-details">
            <h3><?php echo htmlspecialchars($restaurant['name'] ?? ''); ?></h3>
            <p>Telefon: <?php echo htmlspecialchars($restaurant['phone'] ?? ''); ?></p>
            <p>Adres: <?php echo htmlspecialchars($restaurant['address'] ?? ''); ?>
                <?php if (!empty($google_map_link)): ?>
                    <br><a href="<?php echo htmlspecialchars($google_map_link ?? ''); ?>" target="_blank" class="btn btn-info">Haritada Gör</a>
                <?php endif; ?>
            </p>
            <p>Çalışma Saatleri: <?php echo htmlspecialchars($restaurant['working_hours'] ?? ''); ?></p>
        </div>
        <?php if ($restaurant['avg_rating'] > 0): ?>
            <span class="average-rating">Ortalama Puan: 10 / <?php echo round($restaurant['avg_rating'], 1); ?></span>
        <?php else: ?>
            <span class="average-rating">Henüz restoran puanlanmadı</span>
        <?php endif; ?>
        <br><a href="restaurant_ratings.php?restaurant_id=<?php echo $restaurant_id; ?>" class="btn btn-info mb-3">Puanları Görüntüle</a>
    </div>


    <!-- Kampanya Ürünleri -->
<?php foreach ($campaign_items as $campaign_id => $products): ?>
    <?php 
        // Kampanya bilgilerini al
        $campaign_name = $products[0]['promotion_name'];
        $campaign_description = $products[0]['description'];
        $campaign_discount = $products[0]['discount'];
        $campaign_start_date = $products[0]['start_date']; // Kampanya başlangıç tarihi
        $campaign_end_date = $products[0]['end_date']; // Kampanya bitiş tarihi

        // Tarih farkını hesapla
        $now = new DateTime();
        $end_date = new DateTime($campaign_end_date);
        $interval = $now->diff($end_date);
        
        // Zamanı timestamp olarak aktar
        $end_date_timestamp = $end_date->getTimestamp() * 1000; // JS için milisaniyeye çevir
    ?>
    <div class="campaign-section">
        <h2 class="campaign-title">
            <?php echo htmlspecialchars($campaign_name); ?> - 
            İndirim: <?php echo $campaign_discount; ?>%
        </h2>
        <p><?php echo htmlspecialchars($campaign_description); ?></p>
        <p><strong>Başlangıç Tarihi:</strong> <?php echo date('d-m-Y H:i:s', strtotime($campaign_start_date)); ?></p>
        <p><strong>Bitiş Tarihi:</strong> <?php echo date('d-m-Y H:i:s', strtotime($campaign_end_date)); ?></p>

        <!-- Kalan süre -->
        <?php if ($now < $end_date): ?>
            <?php if ($interval->days < 1): ?>
                <p style="color: green;">
                    <strong>
                        Kampanya bitimine kalan süre: 
                        <span class="countdown-timer" 
                              data-end-time="<?php echo $end_date_timestamp; ?>"></span>
                    </strong>
                </p>
            <?php else: ?>
                <p style="color: green;">
                    <strong>Kampanya bitimine <?php echo $interval->days; ?> gün kaldı!</strong>
                </p>
            <?php endif; ?>
        <?php else: ?>
            <p style="color: red;"><strong>Kampanya sona erdi.</strong></p>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($products as $item): ?>
                <?php $discounted_price = $item['price'] * (1 - $item['discount'] / 100); ?>
                <div class="col-md-4 col-6">
                    <div class="card">
                        <?php if ($item['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="card-img-top">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <p class="card-text">
                                <span style="text-decoration: line-through;"><?php echo number_format($item['price'], 2); ?> TL</span>
                                <span style="color: var(--primary-color); font-weight: bold;"><?php echo number_format($discounted_price, 2); ?> TL</span>
                            </p>
                            <button class="btn btn-primary open-modal" 
        data-item-id="<?php echo $item['id']; ?>" 
        data-restaurant-id="<?php echo $restaurant_id; ?>">
    Sepete Ekle
</button>


                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>



    <!-- Normal Menü -->
    <h2 class="normal-menu-title">Normal Menü</h2>
    <div class="row">
        <?php foreach ($normal_items as $item): ?>
            <div class="col-md-4 col-6">
                <div class="card">
                    <?php if ($item['image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="card-img-top">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p class="card-text"><?php echo number_format($item['price'], 2); ?> TL</p>
                        <button class="btn btn-primary open-modal" 
        data-item-id="<?php echo $item['id']; ?>" 
        data-restaurant-id="<?php echo $restaurant_id; ?>">
    Sepete Ekle
</button>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

  <!-- Modal for messages -->
  <div id="messageModal" class="modal-custom">
        <div class="modal-content-custom">
            <p id="messageText"></p>
            <button class="close-btn" onclick="closeModal()">Tamam</button>
        </div>
    </div>
<!-- Modal -->
<div id="itemModal" class="modal">
    <div class="modal-content">
        <form id="modalForm">
            <div id="modalContent"></div>
            <button id="addItemButton" type="submit" class="btn btn-primary">Sepete Ekle</button>
        </form>
        <button class="btn btn-secondary" onclick="closeModal()">Kapat</button>
    </div>
</div>


<script>
            $(document).ready(function() {
                $('.open-modal').on('click', function() {
                    var item_id = $(this).data('item-id');
                    var restaurant_id = $(this).data('restaurant-id');
                    
                    $.ajax({
                        type: 'GET',
                        url: 'get_item_details.php',
                        data: { item_id: item_id, restaurant_id: restaurant_id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#modalContent').html(response.modalContent);
                                $('#itemModal').show();

                                $('.btn-increase').on('click', function() {
                                    var quantityInput = $(this).siblings('.quantity-input');
                                    var currentValue = parseInt(quantityInput.val());
                                    quantityInput.val(currentValue + 1);
                                });

                                $('.btn-decrease').on('click', function() {
                                    var quantityInput = $(this).siblings('.quantity-input');
                                    var currentValue = parseInt(quantityInput.val());
                                    if (currentValue > 1) {
                                        quantityInput.val(currentValue - 1);
                                    }
                                });
                            } else {
                                $('#messageText').text(response.message || 'Ürün detayları getirilirken hata oluştu.');
                                $('#messageModal').show();
                            }
                        },
                        error: function() {
                            $('#messageText').text('Ürün detayları getirilirken hata oluştu.');
                            $('#messageModal').show();
                        }
                    });
                });

                $('#addItemButton').on('click', function(e) {
                    e.preventDefault();
                    var form = $('#modalForm');
                    var quantityValue = $('#quantity').val();
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'quantity',
                        value: quantityValue
                    }).appendTo(form);
                    $.ajax({
                        type: 'POST',
                        url: 'add_to_cart_final.php',
                        data: form.serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#itemModal').hide();
                                $('#messageText').text(response.message);
                                $('#messageModal').show();
                            } else if (response.status === 'conflict') {
                                $('#itemModal').hide();
                                $('#conflictModal').show();
                            } else {
                                $('#messageText').text(response.message || 'Ürün sepete eklenirken hata oluştu.');
                                $('#messageModal').show();
                            }
                        },
                        error: function() {
                            $('#messageText').text('Ürün sepete eklenirken hata oluştu.');
                            $('#messageModal').show();
                        }
                    });
                });

                $('.close-btn').on('click', function() {
                    $(this).closest('.modal').hide();
                });
            });

            // Mesaj modalını gösterme fonksiyonu
function showMessageModal(message) {
    $('#messageText').text(message); // Mesajı modalda göster
    $('#messageModal').css('display', 'flex'); // Modalı aç
}

            function closeModal() {
                $('#messageModal').hide();
            }
        </script>
    
    <?php if (isset($_SESSION['conflict'])): ?>
            <div id="conflictModal" class="modal" style="display: block;">
                <div class="modal-content">
                    <p>Başka bir restorandan gönderilmemiş bir siparişiniz var. Ne yapmak istiyorsunuz?</p>
                    <form method="post" action="resolve_conflict.php">
                        <button class="btn btn-secondary" name="action" value="keep_previous">Önceki Siparişe Devam Et</button>
                        <button class="btn btn-danger" name="action" value="replace_with_current">Güncel Siparişe Devam Et</button>
                    </form>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    $('#conflictModal').show();
                });
            </script>
        <?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const timers = document.querySelectorAll(".countdown-timer");

        timers.forEach(timer => {
            const endTime = parseInt(timer.dataset.endTime);

            function updateTimer() {
                const now = new Date().getTime();
                const timeLeft = endTime - now;

                if (timeLeft <= 0) {
                    timer.innerHTML = "Kampanya sona erdi!";
                    return;
                }

                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                // HH:MM:SS formatında gösterim
                const formattedTime = 
                    String(hours).padStart(2, '0') + ":" + 
                    String(minutes).padStart(2, '0') + ":" + 
                    String(seconds).padStart(2, '0');

                timer.innerHTML = formattedTime;
            }

            updateTimer();
            setInterval(updateTimer, 1000); // Her saniyede bir güncelle
        });
    });
</script>
    </div>
</body>
</html>

<?php
include 'includes/footer.php';
?>
