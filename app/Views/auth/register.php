<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Invoice Online Gratis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-tViUnnbYAV00FLmN642dAi5h0kSYJfiv1buhVmN06ZHAuo0Hj5lD4g7ce4x6L8Tq" crossorigin="anonymous">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/css/custom.css" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    <style>
        .register-card { max-width: 500px; margin: 50px auto; }
        @media (max-width: 576px) {
            .register-card { margin: 30px auto; }
        }
    </style>
  </head>
  <body>
    <div class="container">
        <div class="card register-card shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="mb-3 text-center">
                    <a href="/" class="text-decoration-none small text-muted mb-2 d-block">← Kembali</a>
                    <h4 class="fw-bold text-primary mb-1">Daftar Akun Baru</h4>
                    <p class="text-muted small mb-0">Gratis untuk bisnis Anda</p>
                </div>
                
                <?php if(session()->getFlashdata('errors')):?>
                    <div class="alert alert-danger py-2 small d-flex align-items-start mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                        <ul class="mb-0 ps-3">
                        <?php foreach(session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif;?>

                <form action="/register" method="post" x-data="{ loading: false, showPass: false, showConf: false }" @submit="loading = true">
                    <?= csrf_field() ?>
                    <div class="mb-2">
                        <label for="email" class="form-label small text-muted mb-1">Alamat Email</label>
                        <input type="email" name="email" class="form-control" id="email" value="<?= old('email') ?>" placeholder="email@contoh.com" required>
                    </div>
                    <div class="mb-2">
                        <label for="password" class="form-label small text-muted mb-1">Kata Sandi</label>
                        <div class="d-flex gap-2">
                            <input :type="showPass ? 'text' : 'password'" name="password" class="form-control" id="password" required>
                            <button class="btn btn-outline-primary p-0 flex-shrink-0 d-flex align-items-center justify-content-center" type="button" @click="showPass = !showPass" aria-label="Toggle Kata Sandi" style="width: 46px; height: 46px; border-radius: 50% !important;">
                                <svg x-show="!showPass" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                  <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                  <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                </svg>
                                <svg x-show="showPass" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                  <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                  <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.829-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="form-text small" style="font-size: 0.7rem;">Minimal 6 karakter</div>
                    </div>
                    <div class="mb-3">
                        <label for="confpassword" class="form-label small text-muted mb-1">Konfirmasi Kata Sandi</label>
                        <div class="d-flex gap-2">
                            <input :type="showConf ? 'text' : 'password'" name="confpassword" class="form-control" id="confpassword" required>
                            <button class="btn btn-outline-primary p-0 flex-shrink-0 d-flex align-items-center justify-content-center" type="button" @click="showConf = !showConf" aria-label="Toggle Konfirmasi Kata Sandi" style="width: 46px; height: 46px; border-radius: 50% !important;">
                                <svg x-show="!showConf" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                  <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                  <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                </svg>
                                <svg x-show="showConf" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                  <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                  <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.829-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Desktop Button -->
                    <div class="d-none d-md-grid mt-4">
                        <button type="submit" class="btn btn-primary py-2 rounded-pill" :disabled="loading">
                            <span x-show="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            <span x-text="loading ? 'Mendaftar...' : 'Daftar Gratis'"></span>
                        </button>
                    </div>

                    <!-- Mobile Sticky Button -->
                    <div class="fixed-bottom p-3 bg-white border-top d-md-none shadow-lg">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold" :disabled="loading">
                             <span x-show="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                             <span x-text="loading ? 'Mendaftar...' : 'Daftar Sekarang (Gratis)'"></span>
                        </button>
                         <div class="text-center mt-2">
                             <small class="text-muted" style="font-size: 0.7rem;">Setuju dengan Syarat & Ketentuan.</small>
                         </div>
                    </div>
                    
                    <div class="d-none d-md-block text-center mt-2">
                        <small class="text-muted" style="font-size: 0.75rem;">Dengan mendaftar, Anda menyetujui Syarat & Ketentuan kami.</small>
                    </div>
                </form>
                <div class="mt-4 text-center small">
                    Sudah punya akun? <a href="/login" class="fw-bold text-decoration-none">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
