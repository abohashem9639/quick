<!-- headerIndex.php -->
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
    <link rel="stylesheet" href="css/menu.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light w-100" style="background-color: var(--header-bg-color);">
    <div class="container">
        <a class="navbar-brand" href="index.php" style="color: var(--header-text-color);">Quick</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['user_role'] == 'superadmin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php" style="color: var(--header-text-color);">لوحة تحكم المدير العام</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_restaurants.php" style="color: var(--header-text-color);">إدارة المطاعم</a>
                        </li>
                    <?php elseif ($_SESSION['user_role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_orders.php" style="color: var(--header-text-color);">إدارة الطلبات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_product.php" style="color: var(--header-text-color);">إضافة منتج جديد</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="restaurants.php" style="color: var(--header-text-color);">المطاعم</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders.php" style="color: var(--header-text-color);">سلة الطلبات</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php" style="color: var(--header-text-color);">الملف الشخصي</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php" style="color: var(--header-text-color);">تسجيل الخروج</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php" style="color: var(--header-text-color);">تسجيل الدخول</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php" style="color: var(--header-text-color);">إنشاء حساب</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
