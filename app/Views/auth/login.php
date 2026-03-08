<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Invoice Online Gratis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" integrity="sha384-tViUnnbYAV00FLmN642dAi5h0kSYJfiv1buhVmN06ZHAuo0Hj5lD4g7ce4x6L8Tq" crossorigin="anonymous">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/css/custom.css" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
    <style>
        .login-card { max-width: 400px; margin: 80px auto; }
        @media (max-width: 576px) {
            .login-card { margin: 40px auto; }
        }
    </style>
  </head>
  <body>
    <div class="container">
        <div class="card login-card shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="mb-4 text-center">
                    <a href="/" class="text-decoration-none small text-muted mb-3 d-block">← Kembali ke Beranda</a>
                    <h3 class="fw-bold text-primary">Invoice Online</h3>
                    <p class="text-muted small">Masuk untuk kelola invoice bisnis Anda</p>
                </div>
                
                <?php if(session()->getFlashdata('msg')):?>
                    <div class="alert alert-danger py-2 small d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= session()->getFlashdata('msg') ?>
                    </div>
                <?php endif;?>

                <?php if(session()->getFlashdata('success')):?>
                    <div class="alert alert-success py-2 small d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif;?>

                <form action="/login" method="post" x-data="{ loading: false }" @submit="loading = true">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="email" class="form-label small text-muted">Alamat Email</label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="email@contoh.com" required>
                    </div>
                    <div class="mb-3" x-data="{ show: false }">
                        <label for="password" class="form-label small text-muted">Kata Sandi</label>
                        <div class="d-flex gap-2">
                            <input :type="show ? 'text' : 'password'" name="password" class="form-control" id="password" required>
                            <button class="btn btn-outline-primary p-0 flex-shrink-0 d-flex align-items-center justify-content-center" type="button" @click="show = !show" aria-label="Toggle Kata Sandi" style="width: 46px; height: 46px; border-radius: 50% !important;">
                                <!-- Icon Eye (Show) -->
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                  <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                  <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                </svg>
                                <!-- Icon Eye Slash (Hide) -->
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                  <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                  <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.829-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-2 rounded-pill" :disabled="loading">
                            <span x-show="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            <span x-text="loading ? 'Memproses...' : 'Masuk Aplikasi'"></span>
                        </button>
                    </div>
                </form>
                <div class="mt-4 text-center small">
                    Belum punya akun? <a href="/register" class="fw-bold text-decoration-none">Daftar Gratis</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
