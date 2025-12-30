<?php
session_start();

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!doctype html>
<html lang="tr">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Giriş Yap</title>
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
                <h2 class="h2 text-center mb-4">Hesabınıza giriş yapın</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form action="giris.php" method="post" autocomplete="off" novalidate>
                    <div class="mb-3">
                        <label class="form-label">E-posta Adresi</label>
                        <input type="email" name="email" class="form-control" placeholder="senin@mailin.com" autocomplete="off">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            Şifre
                            <span class="form-label-description">
                                <a href="fpassword.php" class="text-muted">Şifremi unuttum</a>
                            </span>
                        </label>
                        <div class="input-group input-group-flat">
                            <input type="password" id="password-input" name="parola" class="form-control" placeholder="Şifreniz" autocomplete="off">
                            <span class="input-group-text">
                                <a href="#" id="show-password-btn" class="link-secondary" title="Şifreyi göster" data-bs-toggle="tooltip">
                                    <svg id="eye-icon-open" xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                    <svg id="eye-icon-closed" xmlns="http://www.w3.org/2000/svg" class="icon d-none" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" />
                                        <path d="M16.681 16.673a8.973 8.973 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.879 -3.905 4.896 -5.368" />
                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-.342 0 -.679 -.024 -1.011 -.073" />
                                        <path d="M15 15l1 1" />
                                        <path d="M22 22l-1 -1" />
                                    </svg>
                                </a>
                            </span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember_me"/>
                            <span class="form-check-label">Beni hatırla</span>
                        </label>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center text-muted mt-3">
            Hesabınız yok mu? <a href="./sign-up.php" tabindex="-1">Kayıt ol</a>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
    <script src="sign.js"></script>
  </body>
</html>