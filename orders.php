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
        margin-top: auto;
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

.card {
    background-color: var(--card-bg-color);
    border: 1px solid var(--border-color);
    border-radius: 4px;
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

.modal-custom {
    display: flex;
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

.rating-form {
    display: none;
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
                            <a class="nav-link active" href="orders.php">Sipariş Sepeti</a>
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



<div class="container">
<?php
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Sepeti görüntülemek için giriş yapmalısınız.");
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'] ?? [];

// Siparişleri restoranlara göre grupla
$grouped_cart = [];
foreach ($cart as $item) {
    $restaurant_id = $item['restaurant_id'];
    $item_id = $item['item_id'];
    if (!isset($grouped_cart[$restaurant_id])) {
        $grouped_cart[$restaurant_id] = [];
    }
    if (!isset($grouped_cart[$restaurant_id][$item_id])) {
        $grouped_cart[$restaurant_id][$item_id] = $item;
    } else {
        $grouped_cart[$restaurant_id][$item_id]['quantity'] += $item['quantity'];
        $grouped_cart[$restaurant_id][$item_id]['total_price'] += $item['total_price'];
    }
}

// Restoran isimlerini getir
$restaurant_names = [];
if (!empty($grouped_cart)) {
    $restaurant_ids = implode(',', array_map('intval', array_keys($grouped_cart)));
    $sql = "SELECT id, name FROM restaurants WHERE id IN ($restaurant_ids)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $restaurant_names[$row['id']] = $row['name'];
    }
}

// Kullanıcı siparişlerini getir
$sql = "SELECT orders.id as order_id, orders.restaurant_id, restaurants.name as restaurant_name, orders.total_price, orders.status, orders.created_at, orders.rejection_message, orders.rated 
        FROM orders 
        JOIN restaurants ON orders.restaurant_id = restaurants.id 
        WHERE orders.user_id = :user_id
        ORDER BY orders.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Veritabanından değerlendirmeleri getir
$ratings = [];
$sql_ratings = "SELECT order_id, rating, comment FROM ratings WHERE user_id = :user_id";
$stmt_ratings = $pdo->prepare($sql_ratings);
$stmt_ratings->execute(['user_id' => $user_id]);
while ($row = $stmt_ratings->fetch(PDO::FETCH_ASSOC)) {
    $ratings[$row['order_id']] = $row;
}

// Önceki ve mevcut siparişleri gruplandır
$grouped_orders_current = [];
$grouped_orders_previous = [];
foreach ($orders as $order) {
    $restaurant_id = $order['restaurant_id'];
    $created_at = date('Y-m-d H:i', strtotime($order['created_at']));
    if ($order['status'] == 'pending' || $order['status'] == 'not_approved') {
        $grouped_orders_current[$restaurant_id][] = $order;
    } else {
        $grouped_orders_previous[$created_at][] = $order;
    }
}

// Başarı veya hata mesajı getir
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>


<div class="container" style='margin-top: 50px;' dir="ltr">
    <h2 style='text-align:center;'>Sipariş Sepeti</h2>
    <br>
    <br>
    <?php if ($success_message || $error_message): ?>
        <div id="messageModal" class="modal-custom">
            <div class="modal-content-custom">
                <p><?php echo $success_message ? $success_message : $error_message; ?></p>
                <button class="close-btn" onclick="closeModal()">Tamam</button>
            </div>
        </div>
        <script>
            function closeModal() {
                document.getElementById('messageModal').style.display = 'none';
            }
            window.onload = function() {
                document.getElementById('messageModal').style.display = 'block';
            }
        </script>
    <?php endif; ?>

    <?php if (empty($cart) && empty($orders)): ?>
        <div class="alert alert-info">Sepetiniz boş</div>
    <?php else: ?>
        <!-- Sepetteki mevcut siparişleri göster -->
        <?php if (!empty($cart)): ?>
            <form method="post" action="send_orders.php">
                <?php foreach ($grouped_cart as $restaurant_id => $items): ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            <?php echo "Restoran: " . (isset($restaurant_names[$restaurant_id]) ? $restaurant_names[$restaurant_id] : 'Bilinmeyen'); ?>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php
                            $grouped_items = [];
                            foreach ($items as $item) {
                                $key = $item['name'] . '_' . $item['price'];
                                if (!isset($grouped_items[$key])) {
                                    $grouped_items[$key] = $item;
                                } else {
                                    $grouped_items[$key]['quantity'] += $item['quantity'];
                                    $grouped_items[$key]['total_price'] += $item['total_price'];
                                }
                            }
                            ?>
                            <?php foreach ($grouped_items as $item): ?>
    <li class="list-group-item">
        <?php echo htmlspecialchars($item['name']); ?> - Adet: <?php echo htmlspecialchars($item['quantity']); ?> - Parça Fiyatı: <?php echo htmlspecialchars($item['price']); ?> TL - Toplam Fiyat: <?php echo htmlspecialchars($item['total_price']); ?> TL
        <?php if (!empty($item['note'])): ?>
            <br>Not: <?php echo htmlspecialchars($item['note']); ?>
        <?php endif; ?>
        <?php if (!empty($item['ingredients'])): ?>
            <br>İstemediğiniz Malzemeler: 
            <ul>
                <?php
                $ingredient_names = [];
                foreach ($item['ingredients'] as $ingredient_id) {
                    $ingredient_sql = "SELECT name FROM optional_ingredients WHERE id = :ingredient_id";
                    $ingredient_stmt = $pdo->prepare($ingredient_sql);
                    $ingredient_stmt->execute(['ingredient_id' => $ingredient_id]);
                    
                    $ingredient = $ingredient_stmt->fetch(PDO::FETCH_ASSOC);
                    if ($ingredient) {
                        $ingredient_names[] = $ingredient['name'];
                    }
                }
                foreach ($ingredient_names as $name) {
                    echo "<li>" . htmlspecialchars($name) . "</li>";
                }
                ?>
            </ul>
        <?php endif; ?>
        <br>
        <small>Ürün mevcut değilse yapılacak işlem: <?php echo $item['unavailable_action'] == 'remove' ? 'Sepetten çıkar' : 'Tüm siparişi iptal et'; ?></small>
    </li>
<?php endforeach; ?>

                        </ul>
                        <div class="card-footer">
                            Restoran Toplam Fiyatı: <?php echo array_sum(array_column($grouped_items, 'total_price')); ?> TL
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="text-center">
                    <button type="submit" name="send_all" class="btn btn-primary">Siparişi Gönder</button>
                </div>
            </form>
            <form method="post" action="cancel_orders.php">
                <div class="text-center mt-3">
                    <button type="submit" name="cancel_all" class="btn btn-danger">Siparişi İptal Et</button>
                </div>
            </form>
        <?php endif; ?>

        <!-- Onaylanmamış mevcut siparişleri göster -->
        <?php if (!empty($grouped_orders_current)): ?>
            <h3>Mevcut Siparişler</h3>
            <?php foreach ($grouped_orders_current as $restaurant_id => $orders_group): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <?php echo "Restoran: " . $orders_group[0]['restaurant_name']; ?>
                    </div>
                    <ul class="list-group list-group-flush">
                    <?php
$grouped_items = [];
foreach ($orders_group as $order) {
    $order_id = $order['order_id'];
    $sql_items = "SELECT item_name, ingredients, SUM(quantity) AS quantity, price, note, unavailable_action 
                  FROM order_items 
                  WHERE order_id = :order_id 
                  GROUP BY item_name, ingredients, price, note, unavailable_action";
    
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->execute(['order_id' => $order_id]);
    
    while ($item = $stmt_items->fetch(PDO::FETCH_ASSOC)) {
        $item['ingredients'] = json_decode($item['ingredients'], true);
        $key = $item['item_name'] . '_' . $item['price'];
        
        if (!isset($grouped_items[$key])) {
            $grouped_items[$key] = $item;
        } else {
            $grouped_items[$key]['quantity'] += $item['quantity'];
        }
    }
}
?>

<?php foreach ($grouped_items as $item): ?>
    <li class="list-group-item">
        <?php echo htmlspecialchars($item['item_name']); ?> - Adet: <?php echo $item['quantity']; ?> - Parça Fiyatı: <?php echo $item['price']; ?> TL - Toplam Fiyat: <?php echo $item['price'] * $item['quantity']; ?> TL
        <?php if (!empty($item['note'])): ?>
            <br>Not: <?php echo htmlspecialchars($item['note']); ?>
        <?php endif; ?>
        <?php if (!empty($item['ingredients'])): ?>
            <br>İstemediğiniz Malzemeler: 
            <ul>
                <?php
                $ingredient_names = [];
                foreach ($item['ingredients'] as $ingredient_id) {
                    $ingredient_sql = "SELECT name FROM optional_ingredients WHERE id = :ingredient_id";
                    $stmt = $pdo->prepare($ingredient_sql);
                    $stmt->execute(['ingredient_id' => $ingredient_id]);
                    if ($ingredient_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $ingredient_names[] = $ingredient_row['name'];
                    }
                }
                foreach ($ingredient_names as $name) {
                    echo "<li>" . htmlspecialchars($name) . "</li>";
                }
                ?>
            </ul>
        <?php endif; ?>
        <br>
        <small>Ürün mevcut değilse yapılacak işlem: <?php echo $item['unavailable_action'] == 'remove' ? 'Sepetten çıkar' : 'Tüm siparişi iptal et'; ?></small>
    </li>
<?php endforeach; ?>

                    </ul>
                    <div class="card-footer">
                        Toplam Fiyat: <?php echo array_sum(array_map(function($item) {
                            return $item['price'] * $item['quantity'];
                        }, $grouped_items)); ?> TL
                        <span class="badge bg-warning">
                            <?php echo 'Henüz onaylanmadı'; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Önceki siparişleri göster -->
        <?php if (!empty($grouped_orders_previous)): ?>
            <h3>Önceki Siparişler</h3>
            <?php foreach ($grouped_orders_previous as $datetime => $orders_group): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <?php echo "Gönderim Tarihi: " . $datetime; ?>
                    </div>
                    <ul class="list-group list-group-flush">
                    <?php
$grouped_items = [];
foreach ($orders_group as $order) {
    $order_id = $order['order_id'];

    $sql_items = "SELECT item_name, ingredients, SUM(quantity) as quantity, price, note, unavailable_action 
                  FROM order_items 
                  WHERE order_id = :order_id 
                  GROUP BY item_name, ingredients, price, note, unavailable_action";
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->execute(['order_id' => $order_id]);

    while ($item = $stmt_items->fetch(PDO::FETCH_ASSOC)) {
        $item['ingredients'] = json_decode($item['ingredients'], true);
        $key = $item['item_name'] . '_' . $item['price'];
        if (!isset($grouped_items[$key])) {
            $grouped_items[$key] = $item;
        } else {
            $grouped_items[$key]['quantity'] += $item['quantity'];
        }
    }
}
?>

                            <?php foreach ($grouped_items as $item): ?>
                            <li class="list-group-item">
                                <?php echo $item['item_name']; ?> - Adet: <?php echo $item['quantity']; ?> - Parça Fiyatı: <?php echo $item['price']; ?> TL - Toplam Fiyat: <?php echo $item['price'] * $item['quantity']; ?> TL
                                <?php if (!empty($item['note'])): ?>
                                    <br>Not: <?php echo $item['note']; ?>
                                <?php endif; ?>
                                <?php if (!empty($item['ingredients'])): ?>
    <br>İstemediğiniz Malzemeler: 
    <ul>
        <?php
        $ingredient_names = [];
        
        // Malzemeleri bir SQL sorgusuyla topluca getir
        if (!empty($item['ingredients'])) {
            $ingredient_ids = implode(',', array_map('intval', $item['ingredients']));
            $ingredient_sql = "SELECT name FROM optional_ingredients WHERE id IN ($ingredient_ids)";
            $stmt = $pdo->prepare($ingredient_sql);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $ingredient_names[] = $row['name'];
            }
        }

        foreach ($ingredient_names as $name) {
            echo "<li>" . htmlspecialchars($name) . "</li>";
        }
        ?>
    </ul>
<?php endif; ?>

                                <br>
                                <small>Ürün mevcut değilse yapılacak işlem: <?php echo $item['unavailable_action'] == 'remove' ? 'Sepetten çıkar' : 'Tüm siparişi iptal et'; ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="card-footer">
                        <?php echo "Restoran: " . $orders_group[0]['restaurant_name']; ?> - Toplam Fiyat: <?php echo array_sum(array_map(function($item) {
                            return $item['price'] * $item['quantity'];
                        }, $grouped_items)); ?> TL
                        <span class="badge bg-<?php echo $orders_group[0]['status'] == 'approved' ? 'success' : 'danger'; ?>">
                            <?php
                            if ($orders_group[0]['status'] == 'approved') {
                                echo 'Sipariş onaylandı';
                            } elseif ($orders_group[0]['status'] == 'rejected') {
                                echo 'Sipariş reddedildi';
                                if ($orders_group[0]['rejection_message']) {
                                    echo ': ' . $orders_group[0]['rejection_message'];
                                }
                            }
                            ?>
                        </span>
                        <?php
                        $has_unrated_orders = false;
                        foreach ($orders_group as $order) {
                            if ($order['rated'] == 0) {
                                $has_unrated_orders = true;
                                break;
                            }
                        }
                        ?>
<?php if ($orders_group[0]['status'] == 'approved' && $has_unrated_orders): ?>
    <button class="btn btn-primary" onclick="toggleRatingForm(this)">Siparişi Değerlendir</button>
    <div class="rating-form">
        <form method="post" action="rate_order.php">
            <div class="mb-3 mt-3">
                <label for="rating" class="form-label">Sipariş Değerlendirme</label>
                <select class="form-select" id="rating" name="rating" required>
                    <option value="" disabled selected>Değerlendirme seçin</option>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Yorum</label>
                <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
            </div>
            <input type="hidden" name="order_id" value="<?php echo $orders_group[0]['order_id']; ?>">
            <input type="hidden" name="restaurant_id" value="<?php echo $orders_group[0]['restaurant_id']; ?>">
            <button type="submit" class="btn btn-primary">Değerlendirmeyi Gönder</button>
        </form>
    </div>
<?php elseif (isset($ratings[$orders_group[0]['order_id']])): ?>
    <p>Değerlendirildi: <?php echo $ratings[$orders_group[0]['order_id']]['rating']; ?>/10</p>
    <?php if (!empty($ratings[$orders_group[0]['order_id']]['comment'])): ?>
        <p>Yorum: <?php echo htmlspecialchars($ratings[$orders_group[0]['order_id']]['comment']); ?></p>
    <?php endif; ?>
<?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
include 'includes/footer.php';
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleRatingForm(button) {
        var form = button.nextElementSibling;
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }
</script>
</body>
</html>
