<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) : 'Buat Invoice Online Gratis & Kirim via WhatsApp - Tanpa Ribet' ?></title>
    <meta name="description" content="<?= isset($meta_description) ? esc($meta_description) : 'Buat invoice online gratis dalam 30 detik. Langsung jadi PDF siap kirim via WhatsApp. Solusi praktis untuk UMKM & Freelancer.' ?>">
    
    <!-- Open Graph / Social Media -->
    <!-- Canonical -->
    <link rel="canonical" href="<?= isset($canonical_url) ? esc($canonical_url) : 'https://invoiceonlinegratis.com/' ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= isset($canonical_url) ? esc($canonical_url) : 'https://invoiceonlinegratis.com/' ?>">
    <meta property="og:title" content="<?= isset($title) ? esc($title) : 'Buat Invoice Online Gratis & Kirim via WhatsApp - Tanpa Ribet' ?>">
    <meta property="og:description" content="<?= isset($meta_description) ? esc($meta_description) : 'Cara termudah buat invoice UMKM. Tanpa ribet, langsung jadi PDF siap kirim via WhatsApp. Gratis selamanya.' ?>">
    <meta property="og:image" content="https://invoiceonlinegratis.com/assets/img/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= isset($canonical_url) ? esc($canonical_url) : 'https://invoiceonlinegratis.com/' ?>">
    <meta property="twitter:title" content="<?= isset($title) ? esc($title) : 'Buat Invoice Online Gratis & Kirim via WhatsApp' ?>">
    <meta property="twitter:description" content="<?= isset($meta_description) ? esc($meta_description) : 'Cara termudah buat invoice UMKM. Tanpa ribet, langsung jadi PDF siap kirim via WhatsApp. Gratis selamanya.' ?>">
    <meta property="twitter:image" content="https://invoiceonlinegratis.com/assets/img/og-image.jpg">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
    
    <!-- Alpine.js untuk Reaktivitas UI (Toggle Paket, Modal, dll) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <?= $this->renderSection('extra_css') ?>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand text-primary" href="/">Invoice Online <span class="text-dark">Gratis</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link py-2 py-lg-3" href="/">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link py-2 py-lg-3 <?= uri_string() === 'pricing' ? 'active fw-semibold' : '' ?>" href="/pricing">Harga</a></li>
                    <li class="nav-item"><a class="nav-link py-2 py-lg-3" href="/#faq">FAQ</a></li>
                    
                    <?php if (session()->get('user_id')): ?>
                        <li class="nav-item ms-lg-3 my-2 my-lg-0 pb-2 pb-lg-0">
                            <a href="/dashboard" class="btn btn-outline-primary rounded-pill d-block d-lg-inline-block px-4">Dashboard</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-lg-4 my-1 my-lg-0">
                            <a href="/login" class="nav-link py-2 py-lg-3 fw-semibold">Login</a>
                        </li>
                        <li class="nav-item ms-lg-2 my-2 my-lg-0 pb-2 pb-lg-0">
                            <a href="/register" class="btn btn-primary rounded-pill d-block d-lg-inline-block fw-semibold px-4 py-lg-2">Buat Invoice Gratis</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?= $this->renderSection('content') ?>

    <footer class="bg-dark text-white py-4 text-center mt-auto">
        <div class="container">
            <p class="mb-0">&copy; <?= date('Y') ?> Invoice Online Gratis. Dibuat dengan ❤️ untuk UMKM Indonesia.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('extra_js') ?>
</body>
</html>
