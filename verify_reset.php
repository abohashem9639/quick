<!-- verify_reset.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php';

if (!isset($_SESSION['reset_phone'])) {
    header("Location: forgot_password.php");
    exit();
}

$phone = $_SESSION['reset_phone'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_code = $_POST['reset_code'];

    $sql = "SELECT reset_code, reset_expiry FROM users WHERE phone = '$phone'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($entered_code == $row['reset_code'] && strtotime($row['reset_expiry']) > time()) {
            $_SESSION['reset_verified'] = true;
            header("Location: reset_password.php");
            exit();
        } else {
            $error_message = "رمز إعادة التعيين غير صحيح أو منتهي الصلاحية.";
        }
    } else {
        $error_message = "رقم الهاتف غير موجود.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من إعادة تعيين كلمة المرور</title>
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
            color: var(--text-color) !important; /* التأكد من أن الروابط تظهر باللون الأبيض */
            font-size: 1.1rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: var(--primary-color) !important; /* التأكد من أن الروابط تتغير إلى اللون الأحمر عند التمرير */
        }
        .container {
            padding-left: 10px;
            padding-right: 10px;
            margin-left: auto;
            margin-right: auto;
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
        .form-check {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .form-check input {
            margin-left: 10px;
        }

        .form-check label {
            margin-right: 30px;
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
<div class="container" style="margin-top: 50px;" dir="rtl">
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <h2>التحقق من إعادة تعيين كلمة المرور</h2>
    <br>
    <br>
    <form method="post">
        <div class="mb-3">
            <label for="reset_code" class="form-label">رمز إعادة التعيين</label>
            <input type="text" class="form-control" id="reset_code" name="reset_code" required>
        </div>
        <button type="submit" class="btn btn-primary">تحقق</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
