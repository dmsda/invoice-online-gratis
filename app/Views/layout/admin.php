<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? esc($title) . ' - Admin Panel' : 'Admin Panel - Invoice Online' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="/css/custom.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .admin-sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background-color: #0f172a; /* Darker admin bg */
            color: #94a3b8;
            overflow-y: auto;
            z-index: 1030;
            transition: all 0.3s ease;
        }
        .admin-sidebar .sidebar-header {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid #1e293b;
        }
        .admin-sidebar .sidebar-brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .admin-panel-badge {
            font-size: 0.65rem;
            letter-spacing: 1px;
            background: #2563EB;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
            text-transform: uppercase;
        }
        .admin-nav {
            padding: 1rem 0;
            list-style: none;
            margin: 0;
        }
        .admin-nav .nav-label {
            padding: 0.5rem 1.25rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        .admin-nav .nav-item { padding: 0.15rem 1rem; }
        .admin-nav .nav-link {
            display: flex;
            align-items: center;
            color: #cbd5e1;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .admin-nav .nav-link:hover {
            color: #ffffff;
            background-color: #1e293b;
        }
        .admin-nav .nav-link.active {
            color: #ffffff;
            background-color: #2563EB;
            font-weight: 500;
        }
        .admin-nav .nav-link i { font-size: 1.1rem; width: 24px; text-align: center; margin-right: 10px; }
        
        .admin-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }
        .admin-header {
            height: 70px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .admin-main { flex: 1; padding: 2rem; }
        
        /* Mobile Toggle */
        .sidebar-overlay { 
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; 
            background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);
            z-index: 1025; display: none; transition: opacity 0.3s ease;
        }
        .card { border-radius: 1rem !important; }
        
        @media (max-width: 991.98px) {
            .admin-sidebar { transform: translateX(-100%); width: 280px; }
            .admin-sidebar.show { transform: translateX(0); box-shadow: 4px 0 25px rgba(0,0,0,0.15); }
            .admin-content { margin-left: 0; }
            .admin-header { padding: 0 1.25rem; position: sticky; top: 0; z-index: 1020; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
            .admin-main { padding: 1.25rem; margin-top: 0.5rem; overflow-x: hidden; }
            .sidebar-overlay.show { display: block; opacity: 1; }
            .card-body { padding: 1.25rem !important; }
            .table-responsive { margin: 0; padding: 0; border: 1px solid #e2e8f0; border-radius: 0.75rem; }
            .table-responsive .table { margin-bottom: 0; }
            .admin-header .fw-semibold { font-size: 0.9rem !important; }
            h4 { font-size: 1.15rem !important; }
            .nav-pills { flex-wrap: nowrap; overflow-x: auto; padding-bottom: 0.5rem; }
            .nav-pills::-webkit-scrollbar { display: none; }
            .nav-pills .nav-link { white-space: nowrap; padding: 0.4rem 0.8rem; font-size: 0.8rem; margin-bottom: 0; }
            
            /* Forms and Filters Adjustments for mobile */
            .input-group, form.d-flex { max-width: 100% !important; margin-top: 0.75rem !important; width: 100%; }
            .card-header.d-flex { flex-direction: column; align-items: stretch !important; }
        }
        
        @media (max-width: 575.98px) {
            .admin-header .dropdown-menu { width: 100vw; position: fixed !important; top: 70px !important; left: 0 !important; border-radius: 0; border-top: 1px solid #eee; margin-top: 0; transform: none !important; }
        }
    </style>
</head>
<body x-data="{ sidebarOpen: false }">

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" :class="sidebarOpen ? 'show' : ''" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" :class="sidebarOpen ? 'show' : ''">
        <div class="sidebar-header">
            <a href="/admin" class="sidebar-brand">
                <i class="bi bi-shield-check text-primary me-2"></i>
                InvoiceOnline
                <span class="admin-panel-badge">Admin</span>
            </a>
        </div>
        
        <?php
            $currentPath = uri_string();
            function aNav($segment) {
                global $currentPath;
                return ($currentPath === $segment || str_starts_with($currentPath, $segment.'/')) ? 'active' : '';
            }
        ?>

        <ul class="admin-nav">
            <li class="nav-label">Sistem SaaS</li>
            <li class="nav-item">
                <a href="/admin" class="nav-link <?= $currentPath === 'admin' ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/users" class="nav-link <?= aNav('admin/users') ?>">
                    <i class="bi bi-people"></i> Manajemen User
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/subscriptions" class="nav-link <?= aNav('admin/subscriptions') ?>">
                    <i class="bi bi-card-heading"></i> Langganan
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/payments" class="nav-link <?= aNav('admin/payments') ?>">
                    <i class="bi bi-wallet2"></i> Pembayaran
                </a>
            </li>
            
            <li class="nav-label mt-2">Produk & Marketing</li>
            <li class="nav-item">
                <a href="/admin/plans" class="nav-link <?= aNav('admin/plans') ?>">
                    <i class="bi bi-box-seam"></i> Paket & Pricing
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/coupons" class="nav-link <?= aNav('admin/coupons') ?>">
                    <i class="bi bi-ticket-perforated"></i> Kupon Diskon
                </a>
            </li>
            
            <li class="nav-label mt-2">Analitik & Log</li>
            <li class="nav-item">
                <a href="/admin/reports" class="nav-link <?= aNav('admin/reports') ?>">
                    <i class="bi bi-graph-up-arrow"></i> Laporan SaaS
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/system" class="nav-link <?= aNav('admin/system') ?>">
                    <i class="bi bi-cpu"></i> Sistem
                </a>
            </li>
            
            <li class="nav-label mt-4">Akun</li>
            <li class="nav-item">
                <a href="/dashboard" class="nav-link text-info">
                    <i class="bi bi-box-arrow-left"></i> Ke User Panel
                </a>
            </li>
            <li class="nav-item">
                <a href="/logout" class="nav-link text-danger">
                    <i class="bi bi-power"></i> Keluar
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-content">
        <!-- Topbar -->
        <header class="admin-header">
            <div class="d-flex align-items-center">
                <button class="btn btn-light d-lg-none me-2" @click="sidebarOpen = true">
                    <i class="bi bi-list"></i>
                </button>
                
                <!-- Simple Breadcrumb logic -->
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item text-muted small"><a href="/admin" class="text-decoration-none text-muted">Admin Panel</a></li>
                        <li class="breadcrumb-item active small fw-semibold" aria-current="page"><?= isset($title) ? esc($title) : 'Dashboard' ?></li>
                    </ol>
                </nav>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn border-0 p-0 text-start d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                            A
                        </div>
                        <div class="d-none d-md-block line-height-1">
                            <span class="d-block fw-semibold text-dark fs-6" style="line-height:1.2;">Admin</span>
                            <small class="text-muted" style="font-size: 11px;">Super User</small>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                        <li><a class="dropdown-item" href="/admin/profile"><i class="bi bi-person me-2"></i> Profil Admin</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Container -->
        <div class="admin-main">
            <!-- Flash Messages -->
            <?php if(session()->getFlashdata('success')):?>
                <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4" role="alert" x-data x-init="setTimeout(() => $el.remove(), 5000)">
                    <i class="bi bi-check-circle-fill fs-5 me-3"></i>
                    <div><?= session()->getFlashdata('success') ?></div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            <?php endif;?>
            
            <?php if(session()->getFlashdata('errors') || session()->getFlashdata('error')):?>
                <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-start mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-3"></i>
                    <div>
                        <?php if (session()->getFlashdata('error')): ?>
                            <?= session()->getFlashdata('error') ?>
                        <?php endif; ?>
                        
                        <?php if (session()->getFlashdata('errors')): ?>
                            <ul class="mb-0 ps-3 mt-1">
                            <?php foreach(session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn-close ms-auto mt-1" data-bs-dismiss="alert"></button>
                </div>
            <?php endif;?>

            <?= $this->renderSection('content') ?>
            
            <footer class="mt-5 text-center text-muted small pt-3 border-top">
                &copy; <?= date('Y') ?> InvoiceOnline. All rights reserved. Version 1.0.0
            </footer>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
