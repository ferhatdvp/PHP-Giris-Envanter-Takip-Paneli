<?php
session_start();

// Oturumdan hataları ve formu doldurmak için verileri al
$errors = $_SESSION['errors'] ?? [];
$formData = $_SESSION['form_data'] ?? [];

// Oturum değişkenlerini temizle
unset($_SESSION['errors']);
unset($_SESSION['form_data']);

?>
<!doctype html>
<html lang="tr">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Kayıt Ol</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler.min.css" rel="stylesheet"/>
    <style>
      body {
        min-height: 100vh;
      }
      .card {
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }
      .form-label {
        font-weight: 500;
        color: #333;
      }
      .form-control {
        border-radius: 4px;
      }
      .btn-primary {
        background-color: #1a73e8;
        border-color: #1a73e8;
      }
      .invalid-feedback {
        display: block; /* Hataları her zaman görünür yapmak için */
        color: #dc3545; /* Bootstrap'in hata rengi */
        font-size: 0.875em; /* Daha küçük font */
        margin-top: 0.25rem;
      }
      .is-invalid {
        border-color: #dc3545;
      }
    </style>
  </head>
  <body class="d-flex flex-column">
    <div class="page page-center">
      <div class="container container-tight py-4">
        
        <?php
        // Başarı mesajını kontrol et ve göster
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success" role="alert" style="margin: 20px;">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        
        // Veritabanı bağlantı hatasını göster (eğer varsa)
        if (isset($errors['database'])): ?>
            <div class="alert alert-danger" role="alert" style="margin: 20px;">
                Veritabanı hatası: <?= htmlspecialchars($errors['database']) ?>
            </div>
        <?php endif; ?>

        <div class="card card-md">
          <div class="card-body">
            <div class="text-center mb-4">
                <h2 class="h2 text-center mb-2">Kayıt Ol</h2>
            </div>
            
            <form action="kayit.php" method="POST" autocomplete="off" novalidate>
              <div class="row">
                
                <!-- İsim Soyisim -->
                <div class="col-md-6 mb-3">
                  <div class="input-group input-group-flat">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path></svg>
                    </span>
                    <input type="text" class="form-control <?= isset($errors['isim_soyisim']) ? 'is-invalid' : '' ?>" placeholder="İsim Soyisim*" required name="isim_soyisim" pattern="[A-Za-zÇŞĞİÖÜçşğıöü\s]+" title="İsim ve soyisim sadece harf içermelidir" value="<?= htmlspecialchars($formData['isim_soyisim'] ?? '') ?>">
                  </div>
                  <?php if (isset($errors['isim_soyisim'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['isim_soyisim']) ?></div>
                  <?php endif; ?>
                </div>

                <!-- Kullanıcı Adı -->
                <div class="col-md-6 mb-3">
                  <div class="input-group input-group-flat">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path></svg>
                    </span>
                    <input type="text" class="form-control <?= isset($errors['kullanici_adi']) ? 'is-invalid' : '' ?>" placeholder="Kullanıcı Adı*" required name="kullanici_adi" value="<?= htmlspecialchars($formData['kullanici_adi'] ?? '') ?>">
                  </div>
                  <?php if (isset($errors['kullanici_adi'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['kullanici_adi']) ?></div>
                  <?php endif; ?>
                </div>

                <!-- Firmadaki Görevi -->
                <div class="col-md-6 mb-3">
                  <div class="input-group input-group-flat">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path></svg>
                    </span>
                    <input type="text" class="form-control <?= isset($errors['firmadaki_gorevi']) ? 'is-invalid' : '' ?>" placeholder="Firmadaki Görevi*" required name="firmadaki_gorevi" value="<?= htmlspecialchars($formData['firmadaki_gorevi'] ?? '') ?>">
                  </div>
                  <?php if (isset($errors['firmadaki_gorevi'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['firmadaki_gorevi']) ?></div>
                  <?php endif; ?>
                </div>
                
                <!-- Firma Adı -->
                <div class="col-md-6 mb-3">
                  <div class="input-group input-group-flat">
                    <span class="input-group-text">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-building"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21l18 0" /><path d="M9 8l1 0" /><path d="M9 12l1 0" /><path d="M9 16l1 0" /><path d="M14 8l1 0" /><path d="M14 12l1 0" /><path d="M14 16l1 0" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" /></svg>
                    </span>
                    <input type="text" class="form-control <?= isset($errors['firma_adi']) ? 'is-invalid' : '' ?>" placeholder="Firma Adı*" required name="firma_adi" value="<?= htmlspecialchars($formData['firma_adi'] ?? '') ?>">
                  </div>
                  <?php if (isset($errors['firma_adi'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['firma_adi']) ?></div>
                  <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="col-md-6 mb-3">
                  <div class="input-group input-group-flat">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path><path d="M3 7l9 6l9 -6"></path></svg>
                    </span>
                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="Email*" required name="email" value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
                  </div>
                  <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                  <?php endif; ?>
                </div>

                <!-- GSM -->
                <div class="col-md-6 mb-3">
                  <div class="input-group input-group-flat">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path></svg>
                    </span>
                    <input type="tel" class="form-control <?= isset($errors['gsm']) ? 'is-invalid' : '' ?>" placeholder="GSM*" required name="gsm" pattern="[0-9]+" maxlength="15" value="<?= htmlspecialchars($formData['gsm'] ?? '') ?>">
                  </div>
                  <?php if (isset($errors['gsm'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['gsm']) ?></div>
                  <?php endif; ?>
                </div>

                <!-- Sabit Telefon -->
                <div class="col-md-6 mb-3">
                  <div class="input-group input-group-flat">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path></svg>
                    </span>
                    <input type="tel" class="form-control <?= isset($errors['sabit_telefon']) ? 'is-invalid' : '' ?>" placeholder="Sabit Telefon*" required name="sabit_telefon" pattern="[0-9]+" maxlength="15" value="<?= htmlspecialchars($formData['sabit_telefon'] ?? '') ?>">
                  </div>
                  <?php if (isset($errors['sabit_telefon'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['sabit_telefon']) ?></div>
                  <?php endif; ?>
                </div>

                <!-- Dahili Telefon -->
                <div class="col-md-6 mb-3">
                  <div class="input-group input-group-flat">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"></path></svg>
                    </span>
                    <input type="tel" class="form-control <?= isset($errors['dahili_telefon']) ? 'is-invalid' : '' ?>" placeholder="Dahili Telefon" name="dahili_telefon" pattern="[0-9]+" maxlength="15" value="<?= htmlspecialchars($formData['dahili_telefon'] ?? '') ?>">
                  </div>
                  <?php if (isset($errors['dahili_telefon'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['dahili_telefon']) ?></div>
                  <?php endif; ?>
                </div>

                <!-- Parola -->
                <div class="col-md-6 mb-3">
                  <div class="input-group input-group-flat">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"></path><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0"></path><path d="M8 11v-4a4 4 0 1 1 8 0v4"></path></svg>
                    </span>
                    <input type="password" class="form-control <?= isset($errors['parola']) ? 'is-invalid' : '' ?>" id="password-input" placeholder="Parola*" required name="parola">
                  </div>
                  <?php if (isset($errors['parola'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['parola']) ?></div>
                  <?php endif; ?>
                </div>

                <!-- Parola Tekrar -->
                <div class="col-md-6 mb-3">
                  <div class="input-group input-group-flat">
                    <span class="input-group-text">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z"></path><path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0"></path><path d="M8 11v-4a4 4 0 1 1 8 0v4"></path></svg>
                    </span>
                    <input type="password" class="form-control <?= isset($errors['parola_tekrar']) ? 'is-invalid' : '' ?>" id="password-repeat-input" placeholder="Parola Tekrar*" required name="parola_tekrar">
                  </div>
                  <?php if (isset($errors['parola_tekrar'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['parola_tekrar']) ?></div>
                  <?php endif; ?>
                </div>

              </div>
              
              <!-- Kuralları Kabul Etme Checkbox -->
              <div class="mb-3">
                  <label class="form-check">
                      <input type="checkbox" class="form-check-input <?= isset($errors['kurallar_kabul']) ? 'is-invalid' : '' ?>" name="kurallar_kabul" value="1" required <?= ($formData['kurallar_kabul'] ?? '') == '1' ? 'checked' : '' ?>/>
                      <span class="form-check-label">Kuralları okudum ve kabul ediyorum.</span>
                  </label>
                  <?php if (isset($errors['kurallar_kabul'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['kurallar_kabul']) ?></div>
                  <?php endif; ?>
              </div>
              
              <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Yeni Kayıt Oluştur</button>
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