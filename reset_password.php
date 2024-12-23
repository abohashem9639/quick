<!-- reset_password.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php';

if (!isset($_SESSION['reset_phone']) || !isset($_SESSION['reset_verified'])) {
    header("Location: forgot_password.php");
    exit();
}

$phone = $_SESSION['reset_phone'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Şifrelerin eşleştiğini ve gereksinimleri karşıladığını kontrol et
        if ($password !== $confirm_password) {
            $error_message = "Şifreler eşleşmiyor. Lütfen tekrar deneyin.";
        } elseif (strlen($password) < 8 || strlen($password) > 16) {
            $error_message = "Şifre 8 ile 16 karakter arasında olmalıdır.";
        } else {
            $password_hashed = password_hash($password, PASSWORD_BCRYPT);

            $update_sql = "UPDATE users SET password = '$password_hashed', reset_code = NULL, reset_expiry = NULL WHERE phone = '$phone'";
            if ($conn->query($update_sql) === TRUE) {
                unset($_SESSION['reset_phone']);
                unset($_SESSION['reset_verified']);
                header("Location: login.php?message=password_reset");
                exit();
            } else {
                $error_message = "Şifre güncellenirken bir hata oluştu.";
            }
        }
    } else {
        $error_message = "Lütfen tüm alanları doldurun.";
    }
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifreyi Sıfırla</title>
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
            color: var(--text-color) !important;
            font-size: 1.1rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: var(--primary-color) !important;
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
<div class="container" style="margin-top: 50px;" dir="ltr">
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <h2>Şifreyi Sıfırla</h2>
    <br>
    <br>
    <form method="post">
    <div class="mb-3">
        <label for="password" class="form-label">Yeni Şifre</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
        <label for="confirm_password" class="form-label">Şifreyi Onayla</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
    </div>
    <button type="submit" class="btn btn-primary">Şifreyi Sıfırla</button>
</form>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function validatePassword() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm_password").value;
        if (password !== confirmPassword) {
            alert("Şifreler eşleşmiyor. Lütfen tekrar deneyin.");
            return false;
        }
        if (password.length < 8 || password.length > 16) {
            alert("Şifre 8 ile 16 karakter arasında olmalıdır.");
            return false;
        }
        return true;
    }
</script>
</body>
</html>
