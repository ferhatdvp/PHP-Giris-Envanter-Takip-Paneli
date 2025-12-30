<?php
session_start();


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kullanicilar_db"; 

// Form gönderildi mi kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'] ?? '';
    $parola = $_POST['parola'] ?? '';
    $remember_me = $_POST['remember_me'] ?? '';

    // E-posta ve şifre alanlarının boş olup olmadığını kontrol et
    if (empty($email) || empty($parola)) {
        $_SESSION['error'] = 'E-posta ve şifre alanları zorunludur.';
        header("Location: sign-in.php");
        exit();
    }
    
    // Veritabanı bağlantısı oluştur
    try {
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Bağlantıyı kontrol et
        if ($conn->connect_error) {
            throw new Exception("Veritabanı bağlantı hatası: " . $conn->connect_error);
        }

        // SQL sorgusu ile kullanıcıyı e-posta adresine göre bul
        $sql = "SELECT id, parola FROM kullanicilar WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Veritabanındaki hashlenmiş parola ile girilen parolayı karşılaştır
            if (password_verify($parola, $user['parola'])) {
                
                // Parola doğru, kullanıcıyı oturuma al
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;

                // "Beni hatırla" seçeneği işaretliyse, çerez oluştur
                if ($remember_me == 'on') {
                    $cookie_value = bin2hex(random_bytes(32)); // Güvenli, rastgele bir değer oluştur
                    $cookie_expiry = time() + (86400 * 30); // 30 gün
                    setcookie('remember_me', $cookie_value, $cookie_expiry, "/");
                    
                    // Veritabanına da bu değeri kaydet
                    $sql_cookie = "UPDATE kullanicilar SET remember_token = ? WHERE id = ?";
                    $stmt_cookie = $conn->prepare($sql_cookie);
                    $stmt_cookie->bind_param("si", $cookie_value, $user['id']);
                    $stmt_cookie->execute();
                    $stmt_cookie->close();
                }

                // Başarılı giriş, index.php sayfasına yönlendir
                header("Location: index.php");
                exit();
            } else {
                // Parola yanlış
                $_SESSION['error'] = 'E-posta veya şifre hatalı.';
                header("Location: sign-in.php");
                exit();
            }
        } else {
            // E-posta adresi veritabanında bulunamadı
            $_SESSION['error'] = 'E-posta veya şifre hatalı.';
            header("Location: sign-in.php");
            exit();
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        // Hata oluştuğunda sadece genel bir mesaj gösterilir
        $_SESSION['error'] = 'Giriş işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.';
        header("Location: sign-in.php");
        exit();
    }
}
?>