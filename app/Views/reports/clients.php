<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
helper('subscription');
?>

<!-- Page Header -->
<div class="row align-items-center mb-4">
    <div class="col-12 col-md-6 mb-3 mb-md-0">
        <h3 class="fw-bold mb-1">Laporan Per Pelanggan</h3>
        <p class="text-muted mb-0">Analisis performa, omzet, dan kepatuhan bayar pelanggan.</p>
    </div>
    <div class="col-12 col-md-6 d-flex justify-content-md-end mt-2 mt-md-0">
        <div class="dropdown w-100" style="max-width: 250px;">
            <button class="btn btn-outline-primary w-100 dropdown-toggle rounded-pill px-4 shadow-sm fw-medium d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="min-height: 48px;">
                <i class="bi bi-download me-2"></i> Export Laporan
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 w-100 mt-2" aria-labelledby="exportDropdown">
                <li><h6 class="dropdown-header fw-bold text-dark">Pilih Format Laporan</h6></li>
                <li>
                    <a class="dropdown-item py-2 d-flex align-items-center" href="#" onclick="exportReport('pdf'); return false;">
                        <i class="bi bi-file-earmark-pdf-fill text-danger me-3 fs-4"></i> 
                        <div>
                            <div class="fw-bold text-dark">Dokumen PDF</div>
                            <div class="small text-muted" style="font-size: 0.75rem;">Siap untuk dicetak</div>
                        </div>
                    </a>
                </li>
                <li><hr class="dropdown-divider opacity-10"></li>
                <li>
                    <?php if(has_feature('export_excel')): ?>
                        <a class="dropdown-item py-2 d-flex align-items-center" href="#" onclick="exportReport('xlsx'); return false;">
                            <i class="bi bi-file-earmark-excel-fill text-success me-3 fs-4"></i> 
                            <div>
                                <div class="fw-bold text-dark">File Excel (.xlsx)</div>
                                <div class="small text-muted" style="font-size: 0.75rem;">Untuk analisis lanjutan</div>
                            </div>
                        </a>
                    <?php else: ?>
                        <a class="dropdown-item py-2 d-flex align-items-center premium-blur bg-light" href="#" onclick="return false;" title="Tersedia di Paket Pro">
                            <i class="bi bi-lock-fill text-warning me-3 fs-4"></i> 
                            <div>
                                <div class="fw-bold text-dark">File Excel (.xlsx) <span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;">PRO</span></div>
                                <div class="small text-muted" style="font-size: 0.75rem;">Untuk analisis lanjutan</div>
                            </div>
                        </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card shadow-sm border-0 mb-4 rounded-3">
    <div class="card-body p-4">
        <form id="filterForm" action="/reports/clients" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-medium text-secondary">Pilih Periode</label>
                <select name="period" class="form-select" onchange="this.form.submit()">
                    <option value="hari_ini" <?= $meta['period'] == 'hari_ini' ? 'selected' : '' ?>>Hari Ini</option>
                    <option value="minggu_ini" <?= $meta['period'] == 'minggu_ini' ? 'selected' : '' ?>>Minggu Ini</option>
                    <option value="bulan_ini" <?= $meta['period'] == 'bulan_ini' ? 'selected' : '' ?>>Bulan Ini</option>
                    <option value="bulan_lalu" <?= $meta['period'] == 'bulan_lalu' ? 'selected' : '' ?>>Bulan Lalu</option>
                    <option value="tahun_ini" <?= $meta['period'] == 'tahun_ini' ? 'selected' : '' ?>>Tahun Ini</option>
                    <option value="semua" <?= $meta['period'] == 'semua' ? 'selected' : '' ?>>Semua Waktu</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium text-secondary">Status Invoice</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="Semua" <?= $meta['status'] == 'Semua' ? 'selected' : '' ?>>Semua Status</option>
                    <option value="draft" <?= $meta['status'] == 'draft' ? 'selected' : '' ?>>Draf</option>
                    <option value="sent" <?= $meta['status'] == 'sent' ? 'selected' : '' ?>>Terkirim (Belum Lunas)</option>
                    <option value="paid" <?= $meta['status'] == 'paid' ? 'selected' : '' ?>>Lunas</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium text-secondary">Pelanggan</label>
                <select name="client_id" class="form-select" onchange="this.form.submit()">
                    <option value="Semua" <?= $meta['client_id'] == 'Semua' ? 'selected' : '' ?>>[Semua Pelanggan]</option>
                    <?php foreach($clients as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $meta['client_id'] == $c['id'] ? 'selected' : '' ?>><?= esc($c['client_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100 fw-medium">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (empty($report['data'])): ?>
    <?php if ($meta['view_mode'] === 'detail'): ?>
        <div class="d-flex align-items-center justify-content-between mb-3 px-2">
            <h5 class="fw-bold mb-0">
                Detail Transaksi: <span class="text-primary"><?= esc($report['client_name']) ?></span>
            </h5>
            <div class="text-end">
                <span class="text-muted small">Total Omzet:</span><br>
                <span class="fs-5 fw-bold text-dark">Rp 0</span>
            </div>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body text-center py-5 px-4">
            <div class="empty-state-icon mb-4">
                <div class="d-inline-flex bg-primary bg-opacity-10 rounded-circle align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="bi bi-file-earmark-x text-primary" style="font-size: 2.5rem;"></i>
                </div>
            </div>
            <h5 class="fw-bold text-dark mb-2">Belum ada transaksi pada periode ini</h5>
            <p class="text-muted mb-4 fs-6">Cobalah melonggarkan filter periode atau status di atas.</p>
        </div>
    </div>
<?php else: ?>

    <?php if ($meta['view_mode'] === 'summary'): ?>
        <!-- Mode 1: Summary Atas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 h-100 rounded-3">
                    <div class="card-body p-3 p-md-4">
                        <div class="text-muted small fw-semibold mb-1 text-uppercase letter-spacing-wide">Total Pelanggan</div>
                        <h3 class="fw-bold mb-0 text-dark"><?= number_format($report['meta']['total_clients'], 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm border-0 h-100 rounded-3">
                    <div class="card-body p-3 p-md-4">
                        <div class="text-muted small fw-semibold mb-1 text-uppercase letter-spacing-wide">Pelanggan Aktif</div>
                        <h3 class="fw-bold mb-0 text-success"><?= number_format($report['meta']['active_clients'], 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card shadow-sm border-0 h-100 rounded-3 bg-primary text-white position-relative overflow-hidden">
                    <div class="card-body p-3 p-md-4 position-relative z-1 d-flex flex-column justify-content-center">
                        <div class="text-white-50 small fw-semibold mb-1 text-uppercase letter-spacing-wide">Total Omzet Periode Ini</div>
                        <h2 class="fw-bold mb-0 text-white">Rp <?= number_format($report['meta']['total_revenue'], 0, ',', '.') ?></h2>
                    </div>
                    <div class="position-absolute end-0 bottom-0 opacity-25 p-3">
                        <i class="bi bi-graph-up-arrow" style="font-size: 5rem; margin-right: -20px; margin-bottom: -30px; display:block;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mode 1: Mobile Stack View -->
        <div class="d-flex flex-column gap-3 d-md-none mb-4">
            <?php foreach($report['data'] as $row): ?>
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-bold text-dark text-truncate pe-2">
                            <i class="bi bi-person-circle text-secondary opacity-50 me-1"></i>
                            <?= esc($row['client_name']) ?>
                        </div>
                        <?php
                            $badge = 'bg-secondary';
                            if ($row['client_status'] == 'Aktif') $badge = 'bg-success bg-opacity-10 text-success';
                            if ($row['client_status'] == 'Bermasalah') $badge = 'bg-danger bg-opacity-10 text-danger';
                            if ($row['client_status'] == 'Jarang Transaksi') $badge = 'bg-warning bg-opacity-25 text-dark';
                            if ($row['client_status'] == 'Tidak Aktif') $badge = 'bg-light text-secondary border';
                        ?>
                        <span class="badge rounded-pill px-2 py-1 <?= $badge ?>" style="font-size: 0.70rem;">
                            <?= $row['client_status'] ?>
                        </span>
                    </div>
                    
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <div class="small text-muted mb-1">Total Omzet</div>
                            <div class="fw-bold text-dark fs-6">Rp <?= number_format($row['total_revenue'], 0, ',', '.') ?></div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="small text-muted mb-1">Invoice</div>
                            <div class="fw-semibold text-dark"><?= $row['total_invoices'] ?> Transaksi</div>
                        </div>
                    </div>
                    
                    <div class="row g-2 pt-2 border-top">
                        <div class="col-6">
                            <div class="small text-success text-opacity-75">Sdh Dibayar:</div>
                            <div class="fw-semibold text-success">Rp <?= number_format($row['total_paid'], 0, ',', '.') ?></div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="small text-danger text-opacity-75">Blm Dibayar:</div>
                            <div class="fw-semibold text-danger">Rp <?= number_format($row['total_unpaid'], 0, ',', '.') ?></div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Mode 1: Desktop Table -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-nowrap">Nama Pelanggan</th>
                            <th class="text-center">Jml Invoice</th>
                            <th class="text-end">Total Omzet</th>
                            <th class="text-end">Total Lunas</th>
                            <th class="text-end">Belum Lunas</th>
                            <th class="text-center text-nowrap">Invoice Terakhir</th>
                            <th class="pe-4">Status Pelanggan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($report['data'] as $row): ?>
                        <tr>
                            <td class="ps-4 fw-medium text-dark text-nowrap">
                                <i class="bi bi-person-circle text-secondary opacity-50 me-2"></i>
                                <?= esc($row['client_name']) ?>
                            </td>
                            <td class="text-center fw-semibold"><?= $row['total_invoices'] ?></td>
                            <td class="text-end text-nowrap fw-semibold">Rp <?= number_format($row['total_revenue'], 0, ',', '.') ?></td>
                            <td class="text-end text-nowrap text-success text-opacity-75">Rp <?= number_format($row['total_paid'], 0, ',', '.') ?></td>
                            <td class="text-end text-nowrap text-danger text-opacity-75">Rp <?= number_format($row['total_unpaid'], 0, ',', '.') ?></td>
                            <td class="text-center text-muted small text-nowrap">
                                <?= $row['last_invoice_date'] ? date('d M Y', strtotime($row['last_invoice_date'])) : '-' ?>
                            </td>
                            <td class="pe-4">
                                <?php
                                    $badge = 'bg-secondary';
                                    if ($row['client_status'] == 'Aktif') $badge = 'bg-success bg-opacity-10 text-success';
                                    if ($row['client_status'] == 'Bermasalah') $badge = 'bg-danger bg-opacity-10 text-danger';
                                    if ($row['client_status'] == 'Jarang Transaksi') $badge = 'bg-warning bg-opacity-25 text-dark';
                                    if ($row['client_status'] == 'Tidak Aktif') $badge = 'bg-light text-secondary border';
                                ?>
                                <span class="badge rounded-pill px-3 py-2 fw-medium <?= $badge ?>">
                                    <?= $row['client_status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <!-- Mode 2: Drill Down Table -->
        <div class="d-flex align-items-center justify-content-between mb-3 px-2">
            <h5 class="fw-bold mb-0">
                Detail Transaksi: <span class="text-primary"><?= esc($report['client_name']) ?></span>
            </h5>
            <div class="text-end">
                <span class="text-muted small">Total Omzet:</span><br>
                <span class="fs-5 fw-bold text-dark">Rp <?= number_format($report['meta']['total_revenue'], 0, ',', '.') ?></span>
            </div>
        </div>

        <!-- Mode 2: Mobile Stack View -->
        <div class="d-flex flex-column gap-3 d-md-none mb-4">
            <?php foreach($report['data'] as $row): ?>
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-bold text-dark text-truncate pe-2">
                            <?= esc($row['invoice_number']) ?>
                        </div>
                        <?php
                            $sMap = [
                                'draft' => ['label' => 'Draf', 'class' => 'bg-secondary'],
                                'sent' => ['label' => 'Terkirim', 'class' => 'bg-primary'],
                                'paid' => ['label' => 'Lunas', 'class' => 'bg-success'],
                                'canceled' => ['label' => 'Batal', 'class' => 'bg-danger'],
                            ];
                            $sInfo = $sMap[$row['status']] ?? ['label' => $row['status'], 'class' => 'bg-dark'];
                        ?>
                        <span class="badge <?= $sInfo['class'] ?>"><?= $sInfo['label'] ?></span>
                    </div>
                    
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <div class="small text-muted mb-1">Total Invoice</div>
                            <div class="fw-bold text-dark fs-6">Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="small text-muted mb-1">Tgl Invoice</div>
                            <div class="fw-semibold text-dark"><?= date('d M Y', strtotime($row['date_issued'])) ?></div>
                        </div>
                    </div>
                    
                    <div class="row g-2 pt-2 border-top">
                        <div class="col-6">
                            <div class="small text-muted mb-1">Jatuh Tempo:</div>
                            <div class="fw-semibold text-danger text-opacity-75"><?= date('d M Y', strtotime($row['due_date'])) ?></div>
                        </div>
                        <div class="col-6 text-end">
                            <div class="small text-muted mb-1">Keterangan:</div>
                            <?php
                                $kBadge = 'text-muted';
                                if ($row['keterangan'] == 'Tepat Waktu') $kBadge = 'text-success fw-bold';
                                if ($row['keterangan'] == 'Terlambat Dibayar' || $row['keterangan'] == 'Overdue') $kBadge = 'text-danger fw-bold';
                            ?>
                            <div class="<?= $kBadge ?> small"><?= $row['keterangan'] ?></div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Mode 2: Desktop Table -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Tgl Invoice</th>
                            <th>No. Invoice</th>
                            <th class="text-center">Status</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-end">Total Invoice</th>
                            <th class="text-center">Tgl Lunas</th>
                            <th class="pe-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($report['data'] as $row): ?>
                        <tr>
                            <td class="ps-4 text-nowrap"><?= date('d M Y', strtotime($row['date_issued'])) ?></td>
                            <td class="fw-medium text-nowrap text-primary"><?= esc($row['invoice_number']) ?></td>
                            <td class="text-center">
                                <?php
                                    $sMap = [
                                        'draft' => ['label' => 'Draf', 'class' => 'bg-secondary'],
                                        'sent' => ['label' => 'Terkirim', 'class' => 'bg-primary'],
                                        'paid' => ['label' => 'Lunas', 'class' => 'bg-success'],
                                        'canceled' => ['label' => 'Batal', 'class' => 'bg-danger'],
                                    ];
                                    $sInfo = $sMap[$row['status']] ?? ['label' => $row['status'], 'class' => 'bg-dark'];
                                ?>
                                <span class="badge <?= $sInfo['class'] ?>"><?= $sInfo['label'] ?></span>
                            </td>
                            <td class="text-nowrap"><?= date('d M Y', strtotime($row['due_date'])) ?></td>
                            <td class="text-end fw-semibold text-nowrap">Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></td>
                            <td class="text-center text-muted small text-nowrap">
                                <?= $row['paid_date'] ? date('d M Y', strtotime($row['paid_date'])) : '-' ?>
                            </td>
                            <td class="pe-4">
                                <?php
                                    $kBadge = 'text-muted';
                                    if ($row['keterangan'] == 'Tepat Waktu') $kBadge = 'text-success fw-medium';
                                    if ($row['keterangan'] == 'Terlambat Dibayar' || $row['keterangan'] == 'Overdue') $kBadge = 'text-danger fw-medium';
                                ?>
                                <span class="<?= $kBadge ?>"><?= $row['keterangan'] ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>

<script>
function exportReport(format) {
    const form = document.getElementById('filterForm');
    const url = new URL(form.action, window.location.origin);
    const formData = new FormData(form);
    
    for (const [key, value] of formData) {
        url.searchParams.append(key, value);
    }
    url.searchParams.append('export', format);
    
    window.location.href = url.toString();
}
</script>

<?= $this->endSection() ?>
