<!-- add_menu_item.php -->
<?php
include '../includes/header.php';
include '../includes/db.php';

// تحقق من أن المستخدم هو صاحب المطعم
// هذا مجرد مثال، تحتاج إلى إضافة منطق التحقق الخاص بك
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $restaurant_id = $_POST['restaurant_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    $sql = "INSERT INTO menu_items (restaurant_id, name, price) VALUES ($restaurant_id, '$name', $price)";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>تم إضافة الطلب بنجاح</div>";
    } else {
        echo "<div class='alert alert-danger'>خطأ: " . $sql . "<br>" . $conn->error . "</div>";
    }
}

$restaurants = $conn->query("SELECT * FROM restaurants");

$conn->close();
?>

<div class="container">
    <h2>إضافة طلب جديد</h2>
    <form method="post">
        <div class="mb-3">
            <label for="restaurant_id" class="form-label">اختر المطعم</label>
            <select class="form-select" id="restaurant_id" name="restaurant_id" required>
                <?php while ($row = $restaurants->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">اسم الطلب</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">السعر</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-primary">إضافة</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
