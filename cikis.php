<?php
session_start();

// Oturum değişkenlerini temizle
$_SESSION = array();

// Oturumu sonlandır
session_destroy();

// "Beni hatırla" çerezini sil
if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, "/");
}

// Kullanıcıyı giriş sayfasına yönlendir
header("Location: sign-in.php");
exit();
?>