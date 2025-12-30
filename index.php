<?php
session_start();

// Bu kod, kullanıcının oturum açmış olup olmadığını kontrol eder.
function checkLogin() {
    // Veritabanı bağlantı bilgileri
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "kullanicilar_db";

    // Eğer oturum zaten açıksa, işlem yapmaya gerek yok
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        return true;
    }

    // Eğer oturum açık değilse, "Beni hatırla" çerezini kontrol et
    if (isset($_COOKIE['remember_me'])) {
        try {
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                throw new Exception("Veritabanı bağlantı hatası: " . $conn->connect_error);
            }

            $remember_token = $_COOKIE['remember_me'];
            $sql = "SELECT id, email FROM kullanicilar WHERE remember_token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $remember_token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                // Token doğru, yeni bir oturum başlat
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                return true;
            } else {
                // Token geçersiz, çerezi sil
                setcookie('remember_me', '', time() - 3600, "/");
            }

            $stmt->close();
            $conn->close();

        } catch (Exception $e) {
            // Hata durumunda çerezi sil ve giriş sayfasına yönlendir
            setcookie('remember_me', '', time() - 3600, "/");
            header("Location: sign-in.php");
            exit();
        }
    }

    // Ne oturum ne de geçerli bir çerez var, kullanıcıyı giriş sayfasına yönlendir
    return false;
}

// checkLogin() fonksiyonunu çağır ve eğer oturum açık değilse giriş sayfasına yönlendir
if (!checkLogin()) {
    header("Location: sign-in.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <title>Tabler Örneği</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css">
    
    <link rel="stylesheet" href="style.css">
    
    <link href="https://unpkg.com/tabulator-tables/dist/css/tabulator_site.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="page">
        <aside class="navbar navbar-vertical navbar-expand-sm position-absolute" data-bs-theme="dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="#">
                        <img src="https://preview.tabler.io/static/logo-white.svg" width="110" height="32" alt="Tabler" class="navbar-brand-image" />
                    </a>
                </h1>
                
                <div class="d-flex align-items-center py-2 px-3 pb-3 mb-3 border-bottom border-white border-opacity-10">
                    <div class="me-2">
                        <span class="avatar avatar-sm rounded-circle" style="background-image: url(/static/avatars/022m.jpg)"></span>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block text-white text-decoration-none">Kullanıcı</a>
                    </div>
                </div>

                <div class="input-icon my-3 px-3">
                    <input type="text" value="" class="form-control" placeholder="Search…">
                    <span class="input-icon-addon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                    </span>
                </div>

                <div class="navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l4 -4l4 4l-2 0" /><path d="M9 12l-2 0l4 -4l4 4l-2 0" /><path d="M15 12l-2 0l4 -4l4 4l-2 0" /><path d="M12 18l0 -6" /><path d="M12 12l0 -6" /><path d="M12 6l0 -6" /><path d="M12 18l0 6" /></svg>
                                </span>
                                <span class="nav-link-title"> Dashboard </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-icon  d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M18 4h-8l-8 8v6a2 2 0 0 0 2 2h6l8-8z" /><path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /></svg>
                                </span>
                                <span class="nav-link-title"> Widgets </span>
                            </a>
                        </li>
                        <li class="nav-item dropdown active">
                            <a class="nav-link dropdown-toggle show" href="#sidebar-layout-options" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="true" >
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 9l16 0" /><path d="M4 15l16 0" /><path d="M9 4l0 16" /><path d="M15 4l0 16" /></svg>
                                </span>
                                <span class="nav-link-title"> Layout Options </span>
                            </a>
                            <div class="dropdown-menu show">
                                <a class="dropdown-item" href="#">Simple Link 1</a>
                                <a class="dropdown-item" href="#">Simple Link 2</a>
                                <a class="dropdown-item" href="#">Simple Link 3</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
        
        <div class="page-wrapper">
            <div class="d-flex align-items-center bg-body text-body border-bottom border-secondary" id="date-and-ticker-container">
                <div id="date-display" class="p-3 fw-bold flex-shrink-0 text-nowrap"></div>
                <div class="ticker-wrap">
                    <div id="currency-ticker" class="ticker-text ml-3"></div>
                </div>
                <div class="ms-auto me-3 d-flex">
                    <button id="theme-toggle" class="btn btn-icon ml-3 me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"></path>
                            <path d="M12 3v.01"></path>
                        </svg>
                    </button>
                    <a href="cikis.php" class="btn btn-icon" title="Çıkış Yap" data-bs-toggle="tooltip" data-bs-placement="bottom">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M7 12h14l-3 -3m0 6l3 -3" /></svg>
                    </a>
                </div>
            </div>

            <div class="page-body">
                <div class="container-xl">
                    <div class="tabulator-container">
                        <h1>Envanterim</h1>
                        <div class="toolbar">
                            <div class="filter-controls">
                                <select id="filter-durum" class="search-select">
                                    <option value="">Durum</option>
                                    <option value="Yeni Gelen">Yeni Gelen</option>
                                    <option value="Geri Gelen">Geri Gelen</option>
                                    <option value="B:">B:</option>
                                    <option value="C:">C:</option>
                                </select>
                                <select id="filter-surecDurumu" class="search-select">
                                    <option value="">Süreç Durumu</option>
                                    <option value="Çıkış Yapıldı">Çıkış Yapıldı</option>
                                    <option value="A:">A:</option>
                                    <option value="B:">B:</option>
                                    <option value="C:">C:</option>
                                </select>
                                <input type="text" id="filter-cihazDurumu" placeholder="Cihaz Durumu ara..." class="search-input">
                                <select id="filter-cikisDurumu" class="search-select">
                                    <option value="">Çıkış Durumu</option>
                                    <option value="Tamir Edildi">Tamir Edildi</option>
                                    <option value="Tamir Edilmedi">Tamir Edilmedi</option>
                                </select>
                                <input type="text" id="filter-barkod" placeholder="Barkod ara..." class="search-input">
                                <select id="filter-cihazKategorisi" class="search-select">
                                    <option value="">Cihaz Kategorisi</option>
                                    <option value="SÜRÜCÜ">SÜRÜCÜ</option>
                                    <option value="SENSÖR">SENSÖR</option>
                                    <option value="PANEL">PANEL</option>
                                    <option value="I/O MODÜL">I/O MODÜL</option>
                                    <option value="ENDÜSTRİYEL PC">ENDÜSTRİYEL PC</option>
                                    <option value="DİĞER">DİĞER</option>
                                </select>
                                <input type="text" id="filter-cihazBilgisi" placeholder="Cihaz Bilgisi ara..." class="search-input">
                                
                                <div class="input-icon-group">
                                    <input type="text" id="filter-dateRange" placeholder="Tarih aralığı seç..." class="search-input">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path>
                                            <path d="M16 3v4"></path>
                                            <path d="M8 3v4"></path>
                                            <path d="M4 11h16"></path>
                                            <path d="M12 11v-4"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="action-buttons">
                                <button id="clear-filters-button" class="action-button">Filtreleri Temizle</button>
                                <button id="excel-export-button" class="action-button">Excel</button>
                            </div>
                        </div>
                        <div id="envanter-tablosu"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <script type="text/javascript" src="https://oss.sheetjs.com/sheetjs/xlsx.full.min.js"></script>
    <script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    
    <script src="script.js"></script>
    <script src="app.js"></script>
</body>
</html>