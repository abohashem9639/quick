<!-- fetch_orders.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

$restaurant_id = $_SESSION['restaurant_id'];

// Function to get product names from optional ingredients
function getProductNames($conn, $identifiers) {
    if (empty($identifiers)) {
        return [];
    }
    $ids = implode(",", $identifiers);
    $sql = "SELECT id, name FROM optional_ingredients WHERE id IN ($ids)";
    $result = $conn->query($sql);

    $names = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $names[$row['id']] = $row['name'];
        }
    }
    return $names;
}

$sql = "SELECT orders.id as order_id, orders.user_id, users.first_name, users.last_name, users.phone, orders.user_address, orders.total_price, orders.status, orders.created_at
        FROM orders 
        JOIN users ON orders.user_id = users.id 
        WHERE orders.restaurant_id = $restaurant_id AND orders.status = 'pending'
        ORDER BY orders.created_at DESC";
$result = $conn->query($sql);

$orders = [];
while ($row = $result->fetch_assoc()) {
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
                  WHERE order_id = $order_id 
                  GROUP BY item_name, ingredients, price, note, unavailable_action";
    $result_items = $conn->query($sql_items);

    while ($item = $result_items->fetch_assoc()) {
        $item['ingredients'] = json_decode($item['ingredients']);
        $orders[$user_id]['items'][] = $item;
    }
}

?>
<div class="container" dir="rtl" id="orders-container">
    <h2>إدارة الطلبات</h2>
    <?php if (empty($orders)): ?>
        <div class="alert alert-info">لا يوجد طلبات بالوقت الحالي.</div>
    <?php else: ?>
        <?php foreach ($orders as $user_id => $order): ?>
            <div class="card mb-3">
                <div class="card-header">
                    الزبون: <?php echo $order['user']; ?><br>
                    الهاتف: <?php echo $order['phone']; ?><br>
                    العنوان: <?php echo $order['address']; ?><br>
                    تاريخ آخر طلب: <?php echo $order['last_order_date']; ?>
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
                        $ingredient_names = getProductNames($conn, $item['ingredients']);
                    ?>
                    <li class="list-group-item">
                        <?php echo $item['item_name']; ?> - الكمية: <?php echo $item['quantity']; ?> - السعر لكل قطعة: <?php echo $item['price']; ?> TL - السعر الإجمالي: <?php echo $item['price'] * $item['quantity']; ?> TL
                        <?php if (!empty($item['note'])): ?>
                            <br>ملاحظة: <?php echo $item['note']; ?>
                        <?php endif; ?>
                        <br>المكونات التي لا يريدها:
                        <ul>
                            <?php foreach ($ingredient_names as $name): ?>
                                <li><?php echo htmlspecialchars($name); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <small>التصرف في حال عدم توفر المنتج: <?php echo $item['unavailable_action'] == 'remove' ? 'احذفه من الطلب' : 'إلغاء الطلب بالكامل'; ?></small>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="card-footer">
                    السعر الإجمالي: <?php echo array_sum(array_map(function($item) {
                        return $item['price'] * $item['quantity'];
                    }, $items)); ?> TL
                    <form method="post" class="d-inline">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        <textarea name="rejection_message" class="form-control mb-2" placeholder="رسالة الرفض (اختياري)"></textarea>
                        <button type="submit" name="order_action" value="approved" class="btn btn-success">الموافقة على الطلبات</button>
                        <button type="submit" name="order_action" value="rejected" class="btn btn-danger">رفض الطلبات</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$conn->close();
?>
