<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $row = null;
    $role = null;

    try {
        // Kullanıcı bilgilerini `users` tablosundan al
        $userStmt = $pdo->prepare("
            SELECT id, password, user_role
            FROM users
            WHERE phone = :phone
        ");
        $userStmt->bindParam(':phone', $phone);
        $userStmt->execute();

        if ($userStmt->rowCount() > 0) {
            $row = $userStmt->fetch(PDO::FETCH_ASSOC);

            // Şifre doğrulama
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];

                // Rol kontrolü
                if ($row['user_role'] === 'user') {
                    // Genel kullanıcı (general_users)
                    $generalUserStmt = $pdo->prepare("
                        SELECT verified
                        FROM general_users
                        WHERE user_id = :user_id
                    ");
                    $generalUserStmt->bindParam(':user_id', $row['id']);
                    $generalUserStmt->execute();

                    $generalUser = $generalUserStmt->fetch(PDO::FETCH_ASSOC);
                    if ($generalUser && $generalUser['verified']) {
                        $_SESSION['user_role'] = 'user';
                        header("Location: restaurants.php");
                        exit();
                    } else {
                        $error_message = "Hesap doğrulanmamış. Lütfen e-postanızı kontrol edin.";
                    }
                } elseif ($row['user_role'] === 'admin') {
                    // Yönetici (managers)
                    $managerStmt = $pdo->prepare("
                        SELECT restaurant_id
                        FROM managers
                        WHERE user_id = :user_id
                    ");
                    $managerStmt->bindParam(':user_id', $row['id']);
                    $managerStmt->execute();

                    $manager = $managerStmt->fetch(PDO::FETCH_ASSOC);
                    if ($manager) {
                        $_SESSION['user_role'] = 'admin';
                        $_SESSION['restaurant_id'] = $manager['restaurant_id'];
                        header("Location: admin/manage_orders.php");
                        exit();
                    } else {
                        $error_message = "Yönetici bilgileri bulunamadı.";
                    }
                } elseif ($row['user_role'] === 'superadmin') {
                    // Süper Yönetici (superadmins)
                    $superAdminStmt = $pdo->prepare("
                        SELECT user_id
                        FROM superadmins
                        WHERE user_id = :user_id
                    ");
                    $superAdminStmt->bindParam(':user_id', $row['id']);
                    $superAdminStmt->execute();

                    if ($superAdminStmt->rowCount() > 0) {
                        $_SESSION['user_role'] = 'superadmin';
                        header("Location: admin/dashboard.php");
                        exit();
                    } else {
                        $error_message = "Süper yönetici bilgileri bulunamadı.";
                    }
                } else {
                    $error_message = "Geçersiz kullanıcı rolü.";
                }
            } else {
                $error_message = "Telefon numarası veya şifre hatalı.";
            }
        } else {
            $error_message = "Telefon numarası veya şifre hatalı.";
        }
    } catch (PDOException $e) {
        $error_message = "Bir hata oluştu: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-color: #121212;
            --text-color: #ffffff;
            --navbar-bg-color: #1c1c1c;
            --border-color: #444;
            --primary-color: red;
            --primary-hover-color: #e65c00;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: var(--navbar-bg-color);
            padding: 10px 20px;
        }

        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
        }

        .container {
            margin-top: 50px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: 1px solid var(--border-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover-color);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Quick</a>
    </div>
</nav>
<div class="container">
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <h2>Giriş Yap</h2>
    <form method="post">
        <div class="mb-3">
            <label for="phone" class="form-label">Telefon Numarası</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Şifre</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Giriş Yap</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
