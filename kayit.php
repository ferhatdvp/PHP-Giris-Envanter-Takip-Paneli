<?php
session_start();

// Veritabanı bağlantı bilgileri
$servername = "localhost";
$username = "root"; // phpMyAdmin'in varsayılan kullanıcı adı
$password = ""; // phpMyAdmin'in varsayılan şifresi (boş)
$dbname = "kullanicilar_db"; // İstenilen veritabanı adı

// Hata mesajlarını saklamak için boş bir dizi oluştur
$errors = [];

// Form gönderildi mi kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Gelen verileri al ve trim (boşlukları temizle)
    $isim_soyisim = trim($_POST['isim_soyisim'] ?? '');
    $kullanici_adi = trim($_POST['kullanici_adi'] ?? '');
    $firmadaki_gorevi = trim($_POST['firmadaki_gorevi'] ?? '');
    $firma_adi = trim($_POST['firma_adi'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $gsm = trim($_POST['gsm'] ?? '');
    $sabit_telefon = trim($_POST['sabit_telefon'] ?? '');
    $dahili_telefon = trim($_POST['dahili_telefon'] ?? '');
    $parola = $_POST['parola'] ?? '';
    $parola_tekrar = $_POST['parola_tekrar'] ?? '';
    $kurallar_kabul = $_POST['kurallar_kabul'] ?? ''; // Checkbox değeri
    
    // ------------------ Doğrulama İşlemleri ------------------
    
    // İsim Soyisim Doğrulama
    if (empty($isim_soyisim)) {
        $errors['isim_soyisim'] = 'İsim Soyisim alanı zorunludur.';
    } elseif (!preg_match("/^[A-Za-zÇŞĞİÖÜçşğıöü\s]+$/u", $isim_soyisim)) {
        $errors['isim_soyisim'] = 'İsim ve soyisim sadece harf ve boşluk içermelidir.';
    }

    // Kullanıcı Adı Doğrulama
    if (empty($kullanici_adi)) {
        $errors['kullanici_adi'] = 'Kullanıcı Adı alanı zorunludur.';
    }

    // Firmadaki Görevi Doğrulama
    if (empty($firmadaki_gorevi)) {
        $errors['firmadaki_gorevi'] = 'Firmadaki Görevi alanı zorunludur.';
    }

    // Firma Adı Doğrulama
    if (empty($firma_adi)) {
        $errors['firma_adi'] = 'Firma Adı alanı zorunludur.';
    }

    // Email Doğrulama
    if (empty($email)) {
        $errors['email'] = 'Email adresi alanı zorunludur.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Geçerli bir e-posta adresi girin.';
    }

    // GSM Doğrulama
    if (empty($gsm)) {
        $errors['gsm'] = 'GSM numarası alanı zorunludur.';
    } elseif (!preg_match("/^[0-9]+$/", $gsm)) {
        $errors['gsm'] = 'GSM numarası sadece rakam içermelidir.';
    } elseif (strlen($gsm) > 15) { // Uzunluk kontrolü eklendi
        $errors['gsm'] = 'GSM numarası en fazla 15 hane olmalıdır.';
    }

    // Sabit Telefon Doğrulama (Zorunlu)
    if (empty($sabit_telefon)) {
        $errors['sabit_telefon'] = 'Sabit telefon numarası alanı zorunludur.';
    } elseif (!preg_match("/^[0-9]+$/", $sabit_telefon)) {
        $errors['sabit_telefon'] = 'Sabit telefon numarası sadece rakam içermelidir.';
    } elseif (strlen($sabit_telefon) > 15) { // Uzunluk kontrolü eklendi
        $errors['sabit_telefon'] = 'Sabit telefon numarası en fazla 15 hane olmalıdır.';
    }
    
    // Dahili Telefon Doğrulama
    if (!empty($dahili_telefon) && !preg_match("/^[0-9]+$/", $dahili_telefon)) {
        $errors['dahili_telefon'] = 'Dahili telefon numarası sadece rakam içermelidir.';
    } elseif (!empty($dahili_telefon) && strlen($dahili_telefon) > 15) { // Uzunluk kontrolü eklendi
        $errors['dahili_telefon'] = 'Dahili telefon numarası en fazla 15 hane olmalıdır.';
    }
    
    // Parola Doğrulama
    if (empty($parola)) {
        $errors['parola'] = 'Parola alanı zorunludur.';
    } elseif (strlen($parola) < 6) {
        $errors['parola'] = 'Parola en az 6 karakter olmalıdır.';
    }

    // Parola Tekrar Doğrulama
    if (empty($parola_tekrar)) {
        $errors['parola_tekrar'] = 'Parola Tekrar alanı zorunludur.';
    } elseif ($parola !== $parola_tekrar) {
        $errors['parola_tekrar'] = 'Parolalar eşleşmiyor.';
    }
    
    // Kurallar Onayını Doğrulama
    if (empty($kurallar_kabul)) {
        $errors['kurallar_kabul'] = 'Kuralları okuyup kabul etmelisiniz.';
    }

    // ------------------ Veritabanı Kontrolleri ve Kayıt İşlemi ------------------

    if (empty($errors)) {
        // Hata yoksa, veritabanına kaydetme işlemi burada yapılır
        try {
            // Veritabanı bağlantısı oluştur
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Bağlantıyı kontrol et
            if ($conn->connect_error) {
                throw new Exception("Veritabanı bağlantı hatası: " . $conn->connect_error);
            }

            // E-posta veya kullanıcı adının zaten var olup olmadığını kontrol et
            $check_sql = "SELECT email, kullanici_adi FROM kullanicilar WHERE email = ? OR kullanici_adi = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $email, $kullanici_adi);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $existing_user = $check_result->fetch_assoc();
                
                // Daha spesifik hata mesajı oluştur
                if ($existing_user['email'] === $email) {
                    $errors['email'] = 'Bu e-posta adresi zaten kullanımda.';
                }
                if ($existing_user['kullanici_adi'] === $kullanici_adi) {
                    $errors['kullanici_adi'] = 'Bu kullanıcı adı zaten kullanımda.';
                }
            }
            
            $check_stmt->close();
            
            // Eğer veritabanı kontrolünden sonra bir hata oluşmadıysa devam et
            if (empty($errors)) {
                // Parolayı hashle (güvenlik için çok önemlidir)
                $hashed_parola = password_hash($parola, PASSWORD_DEFAULT);

                // SQL sorgusu
                $sql = "INSERT INTO kullanicilar (isim_soyisim, kullanici_adi, firmadaki_gorevi, firma_adi, email, gsm, sabit_telefon, dahili_telefon, parola) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                // Sorguyu hazırla
                $stmt = $conn->prepare($sql);

                // Hata kontrolü
                if ($stmt === false) {
                    throw new Exception("Sorgu hazırlama hatası: " . $conn->error);
                }

                // Parametreleri bağla
                $stmt->bind_param("sssssssss", $isim_soyisim, $kullanici_adi, $firmadaki_gorevi, $firma_adi, $email, $gsm, $sabit_telefon, $dahili_telefon, $hashed_parola);

                // Sorguyu çalıştır
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Kayıt başarıyla oluşturuldu!';
                    unset($_SESSION['form_data']);
                } else {
                    throw new Exception("Kayıt oluşturma hatası: " . $stmt->error);
                }

                // Bağlantıyı kapat
                $stmt->close();
            }

            $conn->close();

        } catch (Exception $e) {
            // Hata mesajını oturuma kaydet
            $errors['database'] = $e->getMessage();
        }
    }
    
    // Hata varsa, hataları ve form verilerini oturuma kaydet
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
    }

    // Her durumda, kullanıcıyı sign-up.php sayfasına geri yönlendir
    header("Location: sign-up.php");
    exit();
}
?>