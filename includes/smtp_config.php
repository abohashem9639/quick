    <?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    // إعدادات الخادم
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'wasseli.info@gmail.com';
    $mail->Password = 'zvxwndrhchdfafdk';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // إعدادات البريد الإلكتروني
    $mail->setFrom('wasseli.info@gmail.com', 'Quick');
    $mail->isHTML(true);
} catch (Exception $e) {
    echo "حدث خطأ أثناء إعداد البريد الإلكتروني: {$mail->ErrorInfo}";
}
?>
