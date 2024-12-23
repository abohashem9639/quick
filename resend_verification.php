<!-- resend_verification.php -->
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'includes/db.php';
include 'includes/smtp_config.php';  // SMTP yapılandırmasını dahil ettiğinizden emin olun

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendVerificationEmail($email, $verification_code) {
    $mail = new PHPMailer(true);

    try {
        // SMTP ayarları
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wasseli.info@gmail.com';
        $mail->Password = 'zvxw ndrh chdf afdk';
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
    $email = $_POST['email'];

    $sql = "SELECT id, email FROM users WHERE email = '$email' AND verified = 0";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $verification_code = rand(100000, 999999);
        $verification_expiry = date("Y-m-d H:i:s", strtotime("+2 minutes"));

        $sql = "UPDATE users SET verification_code = '$verification_code', verification_expiry = '$verification_expiry' WHERE id = " . $row['id'];
        if ($conn->query($sql) === TRUE) {
            sendVerificationEmail($email, $verification_code);

            $_SESSION['verification_email'] = $email;
            header("Location: verify_code.php");  // Doğru sayfaya yönlendirildiğinden emin olun
            exit();
        } else {
            $error_message = "Doğrulama kodu güncellenirken hata oluştu. Lütfen tekrar deneyin.";
        }
    } else {
        $error_message = "E-posta mevcut değil veya daha önce doğrulandı.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doğrulama Kodunu Tekrar Gönder</title>
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
<div class="container" style="margin-top: 50px;" dir="ltr">
    <h2>Doğrulama Kodunu Tekrar Gönder</h2>
    <br>
    <br>
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="email" class="form-label">E-posta Adresi</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Kodu Tekrar Gönder</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
