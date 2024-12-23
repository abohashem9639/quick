<!-- register.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php';
include 'includes/smtp_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendVerificationEmail($email, $verification_code) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Ayarları
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wasseli.info@gmail.com';
        $mail->Password = 'zvxw ndrh chdf afdk'; // Bu bilgileri gizli tutmak önemlidir
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // E-posta içeriği
        $mail->setFrom('wasseli.info@gmail.com', 'Quick');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Doğrulama Kodu';
        $mail->Body    = 'Doğrulama kodunuz: ' . $verification_code;

        $mail->send();
    } catch (Exception $e) {
        echo "Mesaj gönderilemedi. Hata: {$mail->ErrorInfo}";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    try {
        // PDO kullanarak veritabanına bağlanma
        $pdo = new PDO("pgsql:host=localhost;port=5432;dbname=restaurant_management", "postgres", "Salah.963");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Telefon numarası veya e-posta zaten kullanılıyor mu kontrol et
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = :phone OR email = :email");
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<div class='alert alert-danger'>Telefon numarası veya e-posta zaten kullanılıyor. Lütfen tekrar deneyin.</div>";
        } elseif ($password !== $confirm_password) {
            echo "<div class='alert alert-danger'>Şifreler eşleşmiyor. Lütfen tekrar deneyin.</div>";
        } elseif (strlen($password) < 8 || strlen($password) > 16) {
            echo "<div class='alert alert-danger'>Şifre 8 ila 16 karakter arasında olmalıdır.</div>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $verification_code = rand(100000, 999999);

            // Kullanıcı rolü ve diğer bilgileri veritabanına ekle
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, phone, email, address, password, verification_code, verified, user_role) VALUES (:first_name, :last_name, :phone, :email, :address, :password, :verification_code, :verified, :user_role)");
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':verification_code', $verification_code);
            $verified = false;
            $stmt->bindParam(':verified', $verified, PDO::PARAM_BOOL);

            $user_role = 'user';
            $stmt->bindParam(':user_role', $user_role);

            if ($stmt->execute()) {
                sendVerificationEmail($email, $verification_code);
                $_SESSION['email'] = $email;
                header("Location: verify_code.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Hesap oluşturulurken bir hata oluştu. Lütfen daha sonra tekrar deneyin.</div>";
            }
        }

        // Veritabanı bağlantısını kapat
        $pdo = null;
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Veritabanı bağlantı hatası: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesap Oluştur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Orijinal koddaki stiller */
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

        .btn-primary {
            background-color: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 20px;
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav" dir="ltr">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Giriş Yap</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="register.php">Kayıt Ol</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
<div class="container" dir="ltr">
    <h2 style="margin-top: 50px">Hesap Oluştur</h2>
    <form method="post" onsubmit="return validatePassword()">
        <div class="mb-3">
            <label for="first_name" class="form-label">İsim</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Soyisim</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Telefon Numarası</label>
            <input type="text" class="form-control" id="phone" name="phone" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-posta</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Adres</label>
            <input type="text" class="form-control" id="address" name="address" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Şifre</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Şifreyi Onayla</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="show_password" onclick="togglePasswordVisibility()">
            <label class="form-check-label" for="show_password">Şifreyi Göster</label>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Hesap Oluştur</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function togglePasswordVisibility() {
        var passwordField = document.getElementById("password");
        var confirmPasswordField = document.getElementById("confirm_password");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            confirmPasswordField.type = "text";
        } else {
            passwordField.type = "password";
            confirmPasswordField.type = "password";
        }
    }

    function validatePassword() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm_password").value;
        if (password !== confirmPassword) {
            alert("Şifreler eşleşmiyor. Lütfen tekrar deneyin.");
            return false;
        } else if (password.length < 8 || password.length > 16) {
            alert("Şifre 8 ile 16 karakter arasında olmalıdır.");
            return false;
        }
        return true;
    }
</script>
</body>
</html>
