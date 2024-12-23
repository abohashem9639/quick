<!-- m_o_2.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light w-100">
    <div class="container">
        <a class="navbar-brand" href="index.php">Quick</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_role'] == 'superadmin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">لوحة تحكم المدير العام</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_restaurants.php">إدارة المطاعم</a>
                        </li>
                    <?php elseif ($_SESSION['user_role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_orders.php">إدارة الطلبات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_product.php">إضافة منتج جديد</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="restaurants.php">المطاعم</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php">سلة الطلبات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">الملف الشخصي</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">تسجيل الخروج</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">تسجيل الدخول</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">إنشاء حساب</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
<?php
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

$restaurant_id = $_SESSION['restaurant_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['order_action']) && isset($_POST['datetime']) && isset($_POST['user_id'])) {
        $datetime = $_POST['datetime'];
        $user_id = $_POST['user_id'];
        $status = $_POST['order_action'];
        $rejection_message = $_POST['rejection_message'] ?? '';

        $sql = "UPDATE orders 
                SET status = '$status', rejection_message = '$rejection_message' 
                WHERE restaurant_id = $restaurant_id 
                  AND user_id = $user_id 
                  AND DATE_FORMAT(created_at, '%Y-%m-%d %H:%i') = '$datetime'";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>تم تحديث حالة الطلبات</div>";
        } else {
            echo "<div class='alert alert-danger'>خطأ: " . $sql . "<br>" . $conn->error . "</div>";
        }
    }

    if (isset($_POST['delete_order_item'])) {
        $order_item_id = $_POST['order_item_id'];

        $sql = "DELETE FROM order_items WHERE id = $order_item_id";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>تم حذف الطلب</div>";
        } else {
            echo "<div class='alert alert-danger'>خطأ: " . $sql . "<br>" . $conn->error . "</div>";
        }
    }
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

    $sql_items = "SELECT item_name, SUM(quantity) as quantity, price, note, unavailable_action 
                  FROM order_items 
                  WHERE order_id = $order_id 
                  GROUP BY item_name, price, note, unavailable_action";
    $result_items = $conn->query($sql_items);

    while ($item = $result_items->fetch_assoc()) {
        $orders[$user_id]['items'][] = $item;
    }
}

$conn->close();
?>

<div class="container" dir="rtl">
    <h2>إدارة الطلبات</h2>
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
                foreach ($items as $item): ?>
                <li class="list-group-item">
                    <?php echo $item['item_name']; ?> - الكمية: <?php echo $item['quantity']; ?> - السعر لكل قطعة: <?php echo $item['price']; ?> TL - السعر الإجمالي: <?php echo $item['price'] * $item['quantity']; ?> TL
                    <?php if (!empty($item['note'])): ?>
                        <br>ملاحظة: <?php echo $item['note']; ?>
                    <?php endif; ?>
                    <br>
                    <small>التصرف في حال عدم توفر المنتج: <?php echo $item['unavailable_action'] == 'remove' ? 'احذفه من الطلب' : 'إلغاء الطلب بالكامل'; ?></small>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="card-footer">
                السعر الإجمالي: <?php echo array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity'];
                }, $items)); ?> TL
                <form method="post" class="d-inline">
                    <input type="hidden" name="datetime" value="<?php echo $order['last_order_date']; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <textarea name="rejection_message" class="form-control mb-2" placeholder="رسالة الرفض (اختياري)"></textarea>
                    <button type="submit" name="order_action" value="approved" class="btn btn-success">الموافقة على الطلبات</button>
                    <button type="submit" name="order_action" value="rejected" class="btn btn-danger">رفض الطلبات</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../includes/footer.php'; ?>
</div>
</body>
</html>