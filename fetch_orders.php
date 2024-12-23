<!-- fetch_orders.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Sepeti görüntülemek için giriş yapmanız gerekiyor.");
}

$user_id = $_SESSION['user_id'];
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Mevcut siparişleri restoranlara göre gruplama
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

// Restoran isimlerini getirme
$restaurant_names = [];
if (!empty($grouped_cart)) {
    $restaurant_ids = implode(',', array_keys($grouped_cart));
    $sql = "SELECT id, name FROM restaurants WHERE id IN ($restaurant_ids)";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $restaurant_names[$row['id']] = $row['name'];
    }
}

$sql = "SELECT orders.id as order_id, orders.restaurant_id, restaurants.name as restaurant_name, orders.total_price, orders.status, orders.created_at, orders.rejection_message, orders.rated 
        FROM orders 
        JOIN restaurants ON orders.restaurant_id = restaurants.id 
        WHERE orders.user_id = $user_id
        ORDER BY orders.created_at DESC";

$result = $conn->query($sql);
$orders = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

// Veritabanından değerlendirmeleri getirme
$ratings = [];
$sql_ratings = "SELECT order_id, rating, comment FROM ratings WHERE user_id = $user_id";
$result_ratings = $conn->query($sql_ratings);
if ($result_ratings->num_rows > 0) {
    while ($row = $result_ratings->fetch_assoc()) {
        $ratings[$row['order_id']] = $row;
    }
}

// Mevcut ve önceki siparişleri gruplama
$grouped_orders_current = [];
$grouped_orders_previous = [];
foreach ($orders as $order) {
    $restaurant_id = $order['restaurant_id'];
    $created_at = date('Y-m-d H:i', strtotime($order['created_at']));
    if ($order['status'] == 'pending' || $order['status'] == 'not_approved') {
        if (!isset($grouped_orders_current[$restaurant_id])) {
            $grouped_orders_current[$restaurant_id] = [];
        }
        $grouped_orders_current[$restaurant_id][] = $order;
    } else {
        if (!isset($grouped_orders_previous[$created_at])) {
            $grouped_orders_previous[$created_at] = [];
        }
        $grouped_orders_previous[$created_at][] = $order;
    }
}
?>

<?php if (empty($cart) && empty($orders)): ?>
    <div class="alert alert-info">Sepetiniz boş</div>
<?php else: ?>
    <!-- Sepetteki mevcut siparişleri görüntüle -->
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
                                <?php echo $item['name']; ?> - Adet: <?php echo $item['quantity']; ?> - Parça başına fiyat: <?php echo $item['price']; ?> TL - Toplam fiyat: <?php echo $item['total_price']; ?> TL
                                <?php if (!empty($item['note'])): ?>
                                    <br>Not: <?php echo $item['note']; ?>
                                <?php endif; ?>
                                <?php if (!empty($item['ingredients'])): ?>
                                    <br>İstemediğiniz malzemeler: 
                                    <ul>
                                        <?php
                                        $ingredient_names = [];
                                        foreach ($item['ingredients'] as $ingredient_id) {
                                            $ingredient_sql = "SELECT name FROM optional_ingredients WHERE id = $ingredient_id";
                                            $ingredient_result = $conn->query($ingredient_sql);
                                            if ($ingredient_result->num_rows > 0) {
                                                $ingredient_names[] = $ingredient_result->fetch_assoc()['name'];
                                            }
                                        }
                                        foreach ($ingredient_names as $name) {
                                            echo "<li>" . htmlspecialchars($name) . "</li>";
                                        }
                                        ?>
                                    </ul>
                                <?php endif; ?>
                                <br>
                                <small>Ürün mevcut değilse yapılacak işlem: <?php echo $item['unavailable_action'] == 'remove' ? 'Siparişten çıkar' : 'Tüm siparişi iptal et'; ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="card-footer">
                        Restoran için toplam fiyat: <?php echo array_sum(array_column($grouped_items, 'total_price')); ?> TL
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
                        $sql_items = "SELECT item_name, ingredients, SUM(quantity) as quantity, price, note, unavailable_action 
                                      FROM order_items 
                                      WHERE order_id = $order_id 
                                      GROUP BY item_name, ingredients, price, note, unavailable_action";
                        $result_items = $conn->query($sql_items);
                        while ($item = $result_items->fetch_assoc()) {
                            $item['ingredients'] = json_decode($item['ingredients'], true); // Ensure ingredients is an array
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
                            <?php echo $item['item_name']; ?> - Adet: <?php echo $item['quantity']; ?> - Parça başına fiyat: <?php echo $item['price']; ?> TL - Toplam fiyat: <?php echo $item['price'] * $item['quantity']; ?> TL
                            <?php if (!empty($item['note'])): ?>
                                <br>Not: <?php echo $item['note']; ?>
                            <?php endif; ?>
                            <?php if (!empty($item['ingredients'])): ?>
                                <br>İstemediğiniz malzemeler: 
                                <ul>
                                    <?php
                                    $ingredient_names = [];
                                    foreach ($item['ingredients'] as $ingredient_id) {
                                        $ingredient_sql = "SELECT name FROM optional_ingredients WHERE id = $ingredient_id";
                                        $ingredient_result = $conn->query($ingredient_sql);
                                        if ($ingredient_result->num_rows > 0) {
                                            $ingredient_names[] = $ingredient_result->fetch_assoc()['name'];
                                        }
                                    }
                                    foreach ($ingredient_names as $name) {
                                        echo "<li>" . htmlspecialchars($name) . "</li>";
                                    }
                                    ?>
                                </ul>
                            <?php endif; ?>
                            <br>
                            <small>Ürün mevcut değilse yapılacak işlem: <?php echo $item['unavailable_action'] == 'remove' ? 'Siparişten çıkar' : 'Tüm siparişi iptal et'; ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="card-footer">
                    Toplam fiyat: <?php echo array_sum(array_map(function($item) {
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
                                      WHERE order_id = $order_id 
                                      GROUP BY item_name, ingredients, price, note, unavailable_action";
                        $result_items = $conn->query($sql_items);
                        while ($item = $result_items->fetch_assoc()) {
                            $item['ingredients'] = json_decode($item['ingredients'], true); // Ensure ingredients is an array
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
                            <?php echo $item['item_name']; ?> - Adet: <?php echo $item['quantity']; ?> - Parça başına fiyat: <?php echo $item['price']; ?> TL - Toplam fiyat: <?php echo $item['price'] * $item['quantity']; ?> TL
                            <?php if (!empty($item['note'])): ?>
                                <br>Not: <?php echo $item['note']; ?>
                            <?php endif; ?>
                            <?php if (!empty($item['ingredients'])): ?>
                                <br>İstemediğiniz malzemeler: 
                                <ul>
                                    <?php
                                    $ingredient_names = [];
                                    foreach ($item['ingredients'] as $ingredient_id) {
                                        $ingredient_sql = "SELECT name FROM optional_ingredients WHERE id = $ingredient_id";
                                        $ingredient_result = $conn->query($ingredient_sql);
                                        if ($ingredient_result->num_rows > 0) {
                                            $ingredient_names[] = $ingredient_result->fetch_assoc()['name'];
                                        }
                                    }
                                    foreach ($ingredient_names as $name) {
                                        echo "<li>" . htmlspecialchars($name) . "</li>";
                                    }
                                    ?>
                                </ul>
                            <?php endif; ?>
                            <br>
                            <small>Ürün mevcut değilse yapılacak işlem: <?php echo $item['unavailable_action'] == 'remove' ? 'Siparişten çıkar' : 'Tüm siparişi iptal et'; ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="card-footer">
                    <?php echo "Restoran: " . $orders_group[0]['restaurant_name']; ?> - Toplam fiyat: <?php echo array_sum(array_map(function($item) {
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
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endif; ?>

<?php
$conn->close();
?>
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
