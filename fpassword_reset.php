<?php

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kullanicilar_db";

$message = "";
$message_success = "";

if (isset($_GET['key']) && isset($_GET['email'])) {
    $key = $_GET['key'];
    $email = $_GET['email'];

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Veritabanı bağlantı hatası: " . $conn->connect_error);
        }

        // Token'ın geçerliliğini ve süresini kontrol et
        $stmt = $conn->prepare("SELECT * FROM forget_password WHERE email = ? AND temp_key = ? AND created_at >= NOW() - INTERVAL 1 HOUR");
        $stmt->bind_param("ss", $email, $key);
        $stmt->execute();
        $result = $stmt->get_result();
        $resetRequest = $result->fetch_assoc();
        $stmt->close();

        if (!$resetRequest) {
            // Die yerine kullanıcı dostu bir hata mesajı gösterelim.
            $message = "Bu link geçersiz veya süresi dolmuş. Lütfen tekrar şifre sıfırlama talebinde bulunun.";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $password_new = $_POST['password_new'];
            $password_repeat = $_POST['password_repeat'];

            if ($password_new === $password_repeat) {
                // Şifreyi hash'le (güvenlik için çok önemlidir)
                $hashed_parola = password_hash($password_new, PASSWORD_DEFAULT);

                // Kullanıcının şifresini güncelle
                $stmt = $conn->prepare("UPDATE kullanicilar SET parola = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashed_parola, $email);
                $stmt->execute();
                $stmt->close();

                // Güvenlik için kullanılan token'ı veritabanından sil
                $stmt = $conn->prepare("DELETE FROM forget_password WHERE email = ? AND temp_key = ?");
                $stmt->bind_param("ss", $email, $key);
                $stmt->execute();
                $stmt->close();

                $message_success = "Yeni şifreniz başarıyla ayarlandı. Giriş sayfasına yönlendiriliyorsunuz...";
                // Başarılı olursa 3 saniye sonra giriş sayfasına yönlendir
                header("refresh:3; url=sign-in.php");
            } else {
                $message = "Şifreleriniz eşleşmiyor.";
            }
        }
        $conn->close();

    } catch (Exception $e) {
        $message = "Bir hata oluştu: " . $e->getMessage();
    }

} else {
    // key veya email parametreleri yoksa yönlendir
    header('location: sign-in.php');
    exit;
}
?>
<!doctype html>
<html lang="tr">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Şifre Sıfırla</title>
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
                <h2 class="h2 text-center mb-4">Yeni Şifrenizi Belirleyin</h2>
                
                <?php if ($message <> ""): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                <?php if ($message_success <> ""): ?>
                    <div class="alert alert-success" role="alert"><?= htmlspecialchars($message_success) ?></div>
                <?php endif; ?>
                
                <form role="form" method="POST" autocomplete="off" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Yeni Şifre</label>
                        <input type="password" class="form-control" name="password_new" placeholder="Yeni Şifre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yeni Şifre Tekrar</label>
                        <input type="password" class="form-control" name="password_repeat" placeholder="Yeni Şifre Tekrar" required>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100" name="submit">Şifreyi Kaydet</button>
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