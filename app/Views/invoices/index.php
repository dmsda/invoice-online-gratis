<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
helper('subscription');
$plan = current_plan(session()->get('id'));
?>
<!-- Page Header -->
<div class="invoice-page-header mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div>
            <h3 class="fw-bold mb-1">Daftar Invoice</h3>
            <p class="text-muted mb-0 small">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                    <i class="bi bi-receipt me-1"></i><?= count($invoices) ?> invoice
                </span>
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Export Dropdown (Desktop & Tablet) -->
            <div class="dropdown d-none d-sm-block">
                <?php
                    $exportQueryStr = '';
                    $currentStatus = service('request')->getGet('status');
                    if ($currentStatus) {
                        $exportQueryStr = '?status=' . $currentStatus . '&';
                    } else {
                        $exportQueryStr = '?';
                    }
                ?>
                <button class="btn btn-outline-primary dropdown-toggle rounded-pill px-3" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="exportDropdown">
                    <li><h6 class="dropdown-header">Pilih Format Laporan</h6></li>
                    <?php if(has_feature('export_excel')): ?>
                        <li><a class="dropdown-item py-2" href="/invoices/export<?= $exportQueryStr ?>format=xlsx"><i class="bi bi-file-earmark-excel text-success me-2"></i> Excel (.xlsx)</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item py-2 premium-blur text-muted bg-light" href="#" onclick="return false;" title="Tersedia di Paket Pro"><i class="bi bi-lock-fill text-warning me-2"></i> Excel (.xlsx)<span class="badge bg-warning text-dark ms-2" style="font-size:0.6rem;">PRO</span></a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item py-2" href="/invoices/export<?= $exportQueryStr ?>format=csv"><i class="bi bi-filetype-csv text-secondary me-2"></i> CSV Fallback</a></li>
                </ul>
            </div>

            <?php if(subscription_expired()): ?>
                <button class="btn btn-secondary d-inline-flex align-items-center gap-2 rounded-pill px-4" disabled>
                    <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Buat Invoice</span><span class="d-inline d-sm-none">Baru</span>
                </button>
            <?php else: ?>
                <a href="/invoices/create" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-pill px-4">
                    <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Buat Invoice</span><span class="d-inline d-sm-none">Baru</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Filter Pills -->
    <div class="invoice-status-pills d-flex gap-2 overflow-auto pb-2" style="white-space: nowrap;">
        <?php 
            $filters = [
                '' => ['label' => 'Semua', 'icon' => 'bi-grid'],
                'draft' => ['label' => 'Draf', 'icon' => 'bi-pencil-square'],
                'sent' => ['label' => 'Terkirim', 'icon' => 'bi-send'],
                'paid' => ['label' => 'Lunas', 'icon' => 'bi-check-circle'],
                'canceled' => ['label' => 'Batal', 'icon' => 'bi-x-circle'],
            ];
        ?>
        <?php foreach($filters as $value => $filter): ?>
            <a href="/invoices<?= $value ? '?status='.$value : '' ?>" 
               class="btn btn-sm rounded-pill px-3 <?= $currentStatus === $value ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="bi <?= $filter['icon'] ?> me-1"></i><?= $filter['label'] ?>
            </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Mobile Export Buttons (Only Visible on Extra Small Screens < 576px) -->
    <div class="d-flex d-sm-none mt-2 gap-2">
        <?php if(has_feature('export_excel')): ?>
            <a href="/invoices/export<?= $exportQueryStr ?>format=xlsx" class="btn btn-sm btn-outline-primary flex-fill rounded-pill">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
        <?php else: ?>
            <button class="btn btn-sm btn-light text-muted border flex-fill rounded-pill premium-blur" disabled>
                <i class="bi bi-lock-fill text-warning"></i> Excel <span class="badge bg-warning text-dark ms-1" style="font-size:0.5rem;">PRO</span>
            </button>
        <?php endif; ?>
        <a href="/invoices/export<?= $exportQueryStr ?>format=csv" class="btn btn-sm btn-outline-primary flex-fill rounded-pill">
            <i class="bi bi-filetype-csv"></i> CSV
        </a>
    </div>
</div>

<?php if(empty($invoices)): ?>
    <!-- Enhanced Empty State -->
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <div class="empty-state-icon mb-3">
                <i class="bi bi-receipt text-primary" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
            <?php if(!empty($currentStatus)): ?>
                <h5 class="fw-semibold text-dark">Tidak ada invoice <?= $filters[$currentStatus]['label'] ?? '' ?></h5>
                <p class="text-muted mb-3">Belum ada invoice dengan status ini.</p>
                <a href="/invoices" class="btn btn-outline-primary rounded-pill px-4 mt-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Invoice
                </a>
            <?php else: ?>
                <h5 class="fw-semibold text-dark">Belum ada invoice</h5>
                <p class="text-muted mb-3">Buat invoice pertama Anda dan kirim ke pelanggan dalam hitungan menit.</p>
                <?php if(subscription_expired()): ?>
                    <button class="btn btn-secondary rounded-pill px-4 mt-3" disabled>
                        <i class="bi bi-plus-lg me-1"></i> Buat Invoice Baru Sekarang
                    </button>
                <?php else: ?>
                    <a href="/invoices/create" class="btn btn-primary rounded-pill px-4 mt-3">
                        <i class="bi bi-plus-lg me-1"></i> Buat Invoice Baru Sekarang
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>

    <!-- Desktop Table View -->
    <div class="card shadow-sm border-0 d-none d-md-block">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No Invoice</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end pe-4" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($invoices as $inv): ?>
                        <tr>
                            <td class="ps-4">
                                <strong><?= esc($inv['invoice_number']) ?></strong>
                                <?php if(!empty($inv['title'])): ?>
                                    <br><small class="text-muted"><?= esc($inv['title']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="client-avatar" style="width:32px;height:32px;font-size:0.75rem;">
                                        <?= strtoupper(mb_substr($inv['client_name'], 0, 1)) ?>
                                    </div>
                                    <?= esc($inv['client_name']) ?>
                                </div>
                            </td>
                            <td><?= date('d/m/Y', strtotime($inv['date_issued'])) ?></td>
                            <td class="fw-semibold">Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge rounded-pill <?= status_badge_class($inv['status'], $inv['due_date']) ?>"><?= status_label_id($inv['status'], $inv['due_date']) ?></span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="/invoices/show/<?= $inv['uuid'] ?>" class="btn btn-sm btn-outline-primary action-icon-btn rounded-pill" title="Lihat Detail" aria-label="Lihat detail invoice <?= esc($inv['invoice_number']) ?>">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="d-md-none">
        <?php foreach($invoices as $inv): ?>
        <a href="/invoices/show/<?= $inv['uuid'] ?>" class="text-decoration-none">
            <div class="invoice-card card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <!-- Top Row: Client + Status -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-2 min-width-0">
                            <div class="client-avatar flex-shrink-0" style="width:36px;height:36px;font-size:0.85rem;">
                                <?= strtoupper(mb_substr($inv['client_name'], 0, 1)) ?>
                            </div>
                            <div class="min-width-0">
                                <h6 class="fw-bold mb-0 text-dark text-truncate"><?= esc($inv['client_name']) ?></h6>
                                <small class="text-muted"><?= esc($inv['invoice_number']) ?></small>
                            </div>
                        </div>
                        <span class="badge rounded-pill <?= status_badge_class($inv['status'], $inv['due_date']) ?> flex-shrink-0"><?= status_label_id($inv['status'], $inv['due_date']) ?></span>
                    </div>

                    <?php if(!empty($inv['title'])): ?>
                        <div class="small text-muted mb-2 ps-5">
                            <?= esc($inv['title']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Bottom Row: Amount + Date + Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-2 ps-5">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?></h5>
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($inv['date_issued'])) ?>
                            </small>
                        </div>
                        <div class="text-primary small fw-semibold">
                            Detail <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        <?= $pager->links('default', 'bootstrap_pagination') ?>
    </div>

<?php endif; ?>

<!-- Mobile FAB -->
<?php if(subscription_expired()): ?>
    <button class="fab d-md-none border-0" title="Buat Invoice Baru (Terkunci)" aria-label="Buat Invoice Baru" disabled style="background: #cbd5e1; cursor: not-allowed; box-shadow: none;">
        <i class="bi bi-lock-fill"></i>
    </button>
<?php else: ?>
    <a href="/invoices/create" class="fab d-md-none" title="Buat Invoice Baru" aria-label="Buat Invoice Baru">
        <i class="bi bi-plus-lg"></i>
    </a>
<?php endif; ?>

<?= $this->endSection() ?>
