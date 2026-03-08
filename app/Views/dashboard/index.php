<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<?php
helper('subscription');
$plan = current_plan(session()->get('id'));
$daysLeft = null;
if (!empty($plan['expires_at'])) {
    $now = new DateTime();
    $exp = new DateTime($plan['expires_at']);
    $daysLeft = (int)$now->diff($exp)->format('%R%a');
}
?>
<div class="row align-items-center mb-4">
    <div class="col-md-6 mb-3 mb-md-0">
        <h4 class="fw-bold text-dark mb-1">Halo, <?= esc($businessName) ?>! 👋</h4>
        <p class="text-muted small mb-0">Ringkasan bisnis Anda hari ini</p>
    </div>
    <div class="col-md-6 d-flex justify-content-md-end align-items-center gap-3">
        <!-- Widget Langganan -->
        <div class="border rounded-3 p-2 bg-white d-flex align-items-center shadow-sm">
            <div class="me-3 ps-1">
                <span class="d-block text-muted" style="font-size: 0.7rem; line-height: 1;">Paket Anda: <strong class="<?= escapeshellarg($plan['plan_name']) === "'pro'" ? 'text-primary' : 'text-dark' ?>"><?= esc(strtoupper($plan['plan_title'])) ?></strong></span>
                <?php if($plan['plan_name'] === 'pro' && $daysLeft !== null): ?>
                    <span class="d-block text-muted mt-1" style="font-size: 0.7rem; line-height: 1;">Aktif hg: <?= date('d M Y', strtotime($plan['expires_at'])) ?></span>
                <?php endif; ?>
            </div>
            <div>
                <?php if($plan['plan_name'] === 'pro'): ?>
                    <a href="/pricing" class="btn btn-sm btn-outline-primary px-3 rounded-pill" style="font-size: 0.75rem;">Perpanjang</a>
                <?php else: ?>
                    <a href="/pricing" class="btn btn-sm btn-primary px-3 rounded-pill" style="font-size: 0.75rem;">Upgrade</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if(subscription_expired()): ?>
            <button class="btn btn-secondary rounded-pill px-4 d-none d-md-inline-flex align-items-center shadow-sm" disabled>
                <i class="bi bi-plus-lg me-1"></i> Buat Invoice
            </button>
        <?php else: ?>
            <a href="/invoices/create" class="btn btn-primary rounded-pill px-4 d-none d-md-inline-flex align-items-center shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Buat Invoice
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if(subscription_expired()): ?>
    <div class="alert alert-warning border-0 rounded-4 p-3 mb-4 shadow-sm d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill fs-4 text-warning-emphasis me-3"></i>
        <div>
            <h6 class="fw-bold mb-0 text-warning-emphasis">Masa Aktif Berakhir</h6>
            <span class="small text-warning-emphasis">Masa aktif paket Anda telah berakhir. <a href="/pricing" class="fw-bold text-decoration-underline text-warning-emphasis">Perpanjang sekarang</a> untuk membuat invoice baru.</span>
        </div>
    </div>
<?php endif; ?>

<?php if($plan['plan_name'] === 'pro' && $daysLeft !== null && $daysLeft <= 7 && $daysLeft >= 0): ?>
    <div class="alert alert-warning border-0 rounded-4 p-3 mb-4 shadow-sm d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill fs-4 text-warning-emphasis me-3"></i>
        <div>
            <h6 class="fw-bold mb-0 text-warning-emphasis">Pemberitahuan</h6>
            <span class="small text-warning-emphasis">Paket Anda akan berakhir dalam <strong><?= $daysLeft ?> hari</strong>. Segera perpanjang agar tidak kehilangan akses fitur Pro.</span>
        </div>
    </div>
<?php endif; ?>

<?php if($plan['plan_name'] === 'trial' && $daysLeft !== null): ?>
    <style>
        .premium-trial-banner {
            background: linear-gradient(135deg, #fffcf0 0%, #fff8e1 100%);
            border: 1px solid #ffeeba;
        }
        .pulse-btn {
            animation: pulse-animation 2s infinite;
        }
        @keyframes pulse-animation {
            0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); transform: scale(1); }
            70% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); transform: scale(1.03); }
            100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); transform: scale(1); }
        }
    </style>
    
    <?php if($daysLeft > 0): ?>
        <div class="alert premium-trial-banner rounded-4 p-3 mb-4 shadow-sm d-flex align-items-center">
            <i class="bi bi-stars fs-3 text-warning me-3"></i>
            <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
                <div class="mb-2 mb-md-0">
                    <h6 class="fw-bold mb-0 text-dark">Menggunakan Fitur Lengkap (Masa Trial)</h6>
                    <span class="small text-muted">
                        Masa percobaan Anda aktif hingga <?= date('d M Y', strtotime($plan['expires_at'])) ?>. 
                        <?php if($daysLeft <= 3): ?>
                            <strong class="text-danger">Sisa <span class="fs-6"><?= $daysLeft ?></span> hari!</strong>
                        <?php endif; ?>
                    </span>
                </div>
                <?php if($daysLeft <= 3): ?>
                    <a href="/pricing" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold pulse-btn shadow-sm">
                        Amankan Pro Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                <?php else: ?>
                    <a href="/pricing" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-semibold">
                        Lihat Paket Pro
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 shadow-sm d-flex align-items-center">
            <i class="bi bi-lock-fill fs-3 text-danger me-3"></i>
            <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
                <div class="mb-2 mb-md-0">
                    <h6 class="fw-bold mb-0 text-danger">Masa Trial Telah Berakhir</h6>
                    <span class="small text-danger">
                        Akses ke fitur Premium telah dikunci sementara. Aktifkan paket Pro untuk mengelola bisnis kembali tanpa hambatan.
                    </span>
                </div>
                <a href="/pricing" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold pulse-btn shadow-sm mt-3 mt-md-0">
                    Buka Fitur Pro <i class="bi bi-unlock-fill ms-1"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php if(session()->getFlashdata('limit_warning') || session()->getFlashdata('error')): ?>
    <div class="alert alert-danger border-0 rounded-4 p-3 mb-4 shadow-sm">
        <i class="bi bi-x-circle-fill me-2"></i> <?= session()->getFlashdata('error') ?? 'Anda telah mencapai batas maksimal pembuatan invoice.' ?>
    </div>
<?php endif; ?>

<div class="row mb-4 g-3">
    <div class="col-6 col-md-3">
        <div class="card text-white text-bg-primary shadow-sm h-100">
            <div class="card-body p-3">
                <h6 class="card-title fw-normal opacity-75 mb-1" style="font-size: 0.8rem;">Total Invoice</h6>
                <h3 class="fw-bold mb-0"><?= $totalInvoices ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-white text-bg-warning shadow-sm h-100">
            <div class="card-body p-3">
                <h6 class="card-title fw-normal opacity-75 mb-1" style="font-size: 0.8rem;">Belum Lunas</h6>
                <h3 class="fw-bold mb-0"><?= $unpaidInvoices ?></h3>
                <small class="opacity-75" style="font-size: 0.7rem">Draf / Terkirim</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-white text-bg-success shadow-sm h-100">
            <div class="card-body p-3">
                <h6 class="card-title fw-normal opacity-75 mb-1" style="font-size: 0.8rem;">Lunas</h6>
                <h3 class="fw-bold mb-0"><?= $paidInvoices ?></h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-white text-bg-info shadow-sm h-100">
            <div class="card-body p-3">
                <h6 class="card-title fw-normal opacity-75 mb-1" style="font-size: 0.8rem;">Pelanggan</h6>
                <h3 class="fw-bold mb-0"><?= $totalClients ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3 border-0">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Invoice Terakhir</h5>
            <a href="/invoices" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if(empty($recentInvoices)): ?>
            <!-- Empty State -->
            <div class="text-center py-5 px-3">
                <div class="text-muted mb-3">
                    <i class="bi bi-receipt" style="font-size: 3rem;"></i>
                    <p class="mt-2">Belum ada invoice yang dibuat.</p>
                </div>
                <?php if(subscription_expired()): ?>
                    <button class="btn btn-secondary rounded-pill px-4" disabled>
                        <i class="bi bi-plus-lg me-1"></i> Buat Invoice Baru
                    </button>
                    <div class="small text-muted mt-2">Upgrade/Perpanjang untuk membuat invoice</div>
                <?php else: ?>
                    <a href="/invoices/create" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-plus-lg me-1"></i> Buat Invoice Baru
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Desktop Table -->
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">No Invoice</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentInvoices as $inv): ?>
                            <tr>
                                <td class="ps-4"><strong><?= esc($inv['invoice_number']) ?></strong></td>
                                <td><?= esc($inv['client_name']) ?></td>
                                <td><?= date('d/m/Y', strtotime($inv['date_issued'])) ?></td>
                                <td>Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge rounded-pill <?= status_badge_class($inv['status'], $inv['due_date']) ?>"><?= status_label_id($inv['status'], $inv['due_date']) ?></span>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="/invoices/show/<?= $inv['uuid'] ?>" class="btn btn-sm btn-outline-primary action-icon-btn" title="Detail" aria-label="Lihat detail invoice">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                <?php foreach($recentInvoices as $inv): ?>
                <a href="/invoices/show/<?= $inv['uuid'] ?>" class="text-decoration-none">
                    <div class="border-bottom px-3 py-3" style="transition: background 0.15s;" onmouseenter="this.style.background='#f8fafc'" onmouseleave="this.style.background='transparent'">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div>
                                <div class="fw-semibold text-dark"><?= esc($inv['client_name']) ?></div>
                                <small class="text-muted"><?= esc($inv['invoice_number']) ?></small>
                            </div>
                            <span class="badge rounded-pill <?= status_badge_class($inv['status'], $inv['due_date']) ?>">
                                <?= status_label_id($inv['status'], $inv['due_date']) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <strong class="text-dark">Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?></strong>
                            <small class="text-muted"><?= date('d/m/Y', strtotime($inv['date_issued'])) ?></small>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- FAB Mobile -->
<?php if(subscription_expired()): ?>
    <button class="fab d-md-none border-0" title="Buat Invoice (Terkunci)" aria-label="Buat Invoice Baru" disabled style="background: #cbd5e1; cursor: not-allowed; box-shadow: none;">
        <i class="bi bi-lock-fill"></i>
    </button>
<?php else: ?>
    <a href="/invoices/create" class="fab d-md-none" title="Buat Invoice" aria-label="Buat Invoice Baru">
        <?php if($totalInvoices == 0): ?>
            <div class="fab-hint">Tekan + untuk buat invoice</div>
        <?php endif; ?>
        <i class="bi bi-plus-lg"></i>
    </a>
<?php endif; ?>
<?= $this->endSection() ?>
