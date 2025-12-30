<?php

session_start();

// Ortam değişkenlerini yüklemek için dotenv'i dahil et
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// PHPMailer'ı dahil edin
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kullanicilar_db";

$message = "";
$message_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Veritabanı bağlantı hatası: " . $conn->connect_error);
        }

        // Kullanıcının e-posta adresinin var olup olmadığını kontrol et
        $stmt = $conn->prepare("SELECT id FROM kullanicilar WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        if ($user) {
            // Güvenli ve benzersiz bir token oluştur
            $token = bin2hex(random_bytes(50));

            // Token'ı veritabanına ekle
            $stmt = $conn->prepare("INSERT INTO forget_password (email, temp_key, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $email, $token);
            $stmt->execute();
            $stmt->close();
            
            // Şifre sıfırlama bağlantısını oluştur
            $resetLink = "http://localhost/kayit/fpassword_reset.php?key=" . urlencode($token) . "&email=" . urlencode($email);

            // E-posta gönderme işlemi (PHPMailer ile)
            $mail = new PHPMailer(true);
            try {
                // Sunucu ayarları
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['MAIL_USERNAME'];
                $mail->Password   = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;

                // Türkçe karakter desteği
                $mail->CharSet = 'UTF-8';

                // Alıcılar
                $mail->setFrom($_ENV['MAIL_USERNAME'], 'Sirket Adi');
                $mail->addAddress($email);

                // İçerik
                $mail->isHTML(false);
                $mail->Subject = 'Şifre Sıfırlama Talebi';
                $mail->Body    = "Merhaba,\n\nŞifrenizi sıfırlamak için lütfen aşağıdaki bağlantıya tıklayın:\n\n" . $resetLink . "\n\nBu link 1 saat sonra geçersiz olacaktır.\n\nİyi günler dileriz,\nWeb Sitesi Ekibi";

                $mail->send();
                // E-posta başarıyla gönderilirse bu mesajı göster
                $message_success = "Şifre sıfırlama linki e-posta adresinize gönderildi. Lütfen gelen kutunuzu veya spam klasörünüzü kontrol edin.";
            } catch (Exception $e) {
                // E-posta gönderimi başarısız olursa sadece bu mesajı göster
                $message = "E-posta gönderilemedi. Hata: {$mail->ErrorInfo}";
            }
        }
        $conn->close();
        
    } catch (Exception $e) {
        $message = "Bir hata oluştu. Lütfen tekrar deneyin.";
    }
}
?>
<!doctype html>
<html lang="tr">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Şifremi Unuttum</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet"/>
    <style>
      body {
        min-height: 100vh;
      }
      .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
      }
      .is-invalid {
        border-color: #dc3545;
      }
    </style>
  </head>
  <body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
            <a href="." class="navbar-brand navbar-brand-autodark"><img src="https://preview.tabler.io/static/logo.svg" height="36" alt=""></a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">Şifremi unuttum</h2>
                
                <?php if ($message <> ""): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($message_success <> ""): ?>
                    <div class="alert alert-success" role="alert"><?= htmlspecialchars($message_success) ?></div>
                <?php endif; ?>
                
                <form role="form" method="POST" autocomplete="off" novalidate>
                    <div class="mb-3">
                        <label class="form-label">E-posta Adresi</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" placeholder="senin@mailin.com" required autocomplete="off">
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100" name="submit">E-posta Gönder</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center text-muted mt-3">
            <a href="sign-in.php" tabindex="-1">Giriş Sayfasına Dön</a>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
  </body>
</html>