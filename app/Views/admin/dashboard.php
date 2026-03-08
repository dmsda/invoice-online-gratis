<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<?php helper('subscription') ?>

<!-- 1️⃣ HEADER HALAMAN -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Admin Dashboard</h4>
        <p class="text-muted small mb-0">
            Ringkasan pendapatan dan performa berlangganan SaaS
        </p>
    </div>
</div>

<!-- 📊 STATISTIK UTAMA -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3 p-md-4 h-100 bg-primary bg-opacity-10 text-primary" style="backdrop-filter: blur(10px);">
            <small class="fw-semibold text-uppercase mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">Pendapatan</small>
            <h4 class="fw-bold mb-0 text-truncate">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h4>
        </div>
    </div>
    <div class="col-6 col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3 p-md-4 h-100 bg-success bg-opacity-10 text-success" style="backdrop-filter: blur(10px);">
            <small class="fw-semibold text-uppercase mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">Langganan Aktif</small>
            <h4 class="fw-bold mb-0 text-truncate"><?= number_format($totalActive, 0, ',', '.') ?></h4>
        </div>
    </div>
    <div class="col-6 col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3 p-md-4 h-100 bg-warning bg-opacity-10 text-warning" style="backdrop-filter: blur(10px);">
            <small class="fw-semibold text-uppercase mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">Bayar Pending</small>
            <h4 class="fw-bold mb-0 text-truncate"><?= number_format($totalPending, 0, ',', '.') ?></h4>
        </div>
    </div>
    <div class="col-6 col-md-6 col-lg-3">
        <div class="card shadow-sm border-0 rounded-4 text-center p-3 p-md-4 h-100 bg-dark bg-opacity-10 text-dark" style="backdrop-filter: blur(10px);">
            <small class="fw-semibold text-uppercase mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">Semua User</small>
            <h4 class="fw-bold mb-0 text-truncate"><?= number_format($totalUsers, 0, ',', '.') ?></h4>
        </div>
    </div>
</div>

<!-- 🕒 TRANSAKSI TERAKHIR -->
<h5 class="fw-bold mt-5 mb-3">5 Transaksi Langganan Terakhir</h5>
<div class="card shadow-sm border-0 rounded-3 d-none d-md-block">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Tgl Pembayaran</th>
                    <th>Nama Usaha</th>
                    <th>Paket</th>
                    <th>Metode</th>
                    <th class="text-end pe-4">Nominal</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($recentPayments)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada transaksi diverifikasi</td></tr>
                <?php else: ?>
                    <?php foreach($recentPayments as $payment): ?>
                    <tr>
                        <td class="ps-4 text-muted small"><?= date('d M Y H:i', strtotime($payment['created_at'])) ?></td>
                        <td class="fw-medium text-dark"><?= esc($payment['business_name']) ?></td>
                        <td><span class="badge bg-primary rounded-pill px-3"><?= esc($payment['plan_name']) ?></span></td>
                        <td><?= esc($payment['method']) ?></td>
                        <td class="text-end fw-semibold pe-4 text-success">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards untuk Transaksi Terakhir -->
<div class="d-md-none">
    <?php if(empty($recentPayments)): ?>
        <div class="card border-0 shadow-sm text-center py-4 text-muted">Belum ada transaksi diverifikasi</div>
    <?php else: ?>
        <?php foreach($recentPayments as $payment): ?>
        <div class="card border-0 shadow-sm rounded-3 mb-2">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold text-dark"><?= esc($payment['business_name']) ?></span>
                    <span class="fw-semibold text-success">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></span>
                </div>
                <div class="small text-muted d-flex justify-content-between">
                    <span><?= date('d M Y', strtotime($payment['created_at'])) ?> &bull; <?= esc($payment['method']) ?></span>
                    <span class="badge bg-primary rounded-pill"><?= esc($payment['plan_name']) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="mt-4">
    <a href="/admin/subscriptions" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
        <i class="bi bi-gear-fill me-1"></i> Kelola Semua Langganan
    </a>
</div>

<?= $this->endSection() ?>
