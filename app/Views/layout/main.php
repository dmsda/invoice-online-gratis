<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? esc($title) : 'Invoice Online' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/css/custom.css" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" crossorigin="anonymous"></script>
  </head>
  <body>
    <?php
        $currentPath = uri_string();
        function navActive($segment) {
            global $currentPath;
            return str_contains($currentPath, $segment) ? 'active' : '';
        }
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4">
      <div class="container">
        <a class="navbar-brand" href="/dashboard">
            <i class="bi bi-receipt me-1"></i>Invoice Online
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link <?= navActive('dashboard') ?>" href="/dashboard">
                    <i class="bi bi-grid me-1"></i>Beranda
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= navActive('clients') ?>" href="/clients">
                    <i class="bi bi-people me-1"></i>Pelanggan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= navActive('invoices') ?>" href="/invoices">
                    <i class="bi bi-receipt me-1"></i>Invoice
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= navActive('reports') ?>" href="/reports">
                    <i class="bi bi-bar-chart-line me-1"></i>Laporan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= navActive('settings') ?>" href="/settings/profile">
                    <i class="bi bi-gear me-1"></i>Pengaturan
                </a>
            </li>
            <li class="nav-item ms-lg-2">
                <a class="nav-link text-danger" href="/logout">
                    <i class="bi bi-box-arrow-right me-1"></i>Keluar
                </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container pb-5">
        <?php if(session()->getFlashdata('success')):?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert"
                 x-data x-init="setTimeout(() => $el.remove(), 5000)">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div><?= session()->getFlashdata('success') ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif;?>
        
        <?php if(session()->getFlashdata('errors')):?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5 mt-1"></i>
                <div>
                    <ul class="mb-0">
                    <?php foreach(session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach ?>
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif;?>

        <?= $this->renderSection('content') ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  </body>
</html>
