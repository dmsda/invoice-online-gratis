<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex align-items-center mb-2">
        <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i class="bi bi-bar-chart-fill fs-4"></i>
        </div>
        <div>
            <h1 class="h4 mb-0 text-dark fw-bold">Laporan</h1>
            <p class="text-muted small mb-0">Ringkasan keuangan usaha Anda</p>
        </div>
    </div>
    <div class="mb-2">
        <a href="/reports/clients" class="btn btn-outline-primary rounded-pill d-flex align-items-center gap-2 fw-semibold border-2">
            <i class="bi bi-person-lines-fill"></i> Laporan Pelanggan
        </a>
    </div>
</div>

<!-- FILTER SECTION (MOBILE OPTIMIZED) -->
<div class="mb-4">
    <form method="GET" action="/reports" id="reportFilterForm" class="d-flex gap-2 w-100">
        <!-- Opsi Type Laporan (Hidden Default ke Sales jika tidak butuh ditekankan) -->
        <input type="hidden" name="type" value="sales" id="reportTypeInput">
        
        <select name="period" class="form-select form-select-lg w-100 shadow-sm border-0 fw-semibold text-dark rounded-3" onchange="document.getElementById('reportFilterForm').submit();" style="min-height: 48px;">
            <option value="hari_ini" <?= $meta['period'] === 'hari_ini' ? 'selected' : '' ?>>Hari Ini</option>
            <option value="minggu_ini" <?= $meta['period'] === 'minggu_ini' ? 'selected' : '' ?>>Minggu Ini</option>
            <option value="bulan_ini" <?= $meta['period'] === 'bulan_ini' ? 'selected' : '' ?>>Bulan Ini</option>
            <option value="bulan_lalu" <?= $meta['period'] === 'bulan_lalu' ? 'selected' : '' ?>>Bulan Lalu</option>
            <option value="tahun_ini" <?= $meta['period'] === 'tahun_ini' ? 'selected' : '' ?>>Tahun Ini</option>
        </select>
    </form>
</div>

<!-- HIGHLIGHT CARDS (HORIZONTAL SCROLL) -->
<div class="d-flex gap-3 mb-4 overflow-x-auto pb-2" style="scrollbar-width: none; -ms-overflow-style: none;">
    <!-- Hide scrollbar for webkit but keep scrolling -->
    <style> .overflow-x-auto::-webkit-scrollbar { display: none; } </style>

    <!-- UANG MASUK -->
    <div class="card border-0 shadow-sm rounded-3 flex-shrink-0" style="width: 280px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-success bg-opacity-10 rounded-circle text-success me-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-arrow-down-left fs-5"></i>
                </div>
                <div class="text-muted small fw-bold text-uppercase">Uang Masuk</div>
            </div>
            <div class="fs-2 fw-black text-dark mb-1">Rp <?= number_format($summary['total_paid'] ?? 0, 0, ',', '.') ?></div>
            <div class="small text-muted">Dari total <?= $summary['total_invoice'] ?? 0 ?> invoice</div>
        </div>
    </div>
    
    <!-- BELUM DIBAYAR -->
    <div class="card border-0 shadow-sm rounded-3 flex-shrink-0" style="width: 280px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-warning bg-opacity-10 rounded-circle text-warning me-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </div>
                <div class="text-muted small fw-bold text-uppercase">Belum Dibayar</div>
            </div>
            <div class="fs-2 fw-black text-dark mb-1">Rp <?= number_format($summary['total_unpaid'] ?? 0, 0, ',', '.') ?></div>
            <div class="small text-muted">Menunggu pembayaran klien</div>
        </div>
    </div>

    <!-- LEWAT JATUH TEMPO -->
    <div class="card border-0 shadow-sm rounded-3 flex-shrink-0" style="width: 280px; background-color: #fff5f5;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-danger bg-opacity-10 rounded-circle text-danger me-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <div class="text-danger small fw-bold text-uppercase">Telat Bayar</div>
            </div>
            <div class="fs-2 fw-black text-danger mb-1">Rp <?= number_format($summary['total_overdue'] ?? 0, 0, ',', '.') ?></div>
            <div class="small text-danger opacity-75">Resiko gagal bayar, segera tagih!</div>
        </div>
    </div>
</div>

<!-- REPORT DATA -->
<div class="mb-5 pb-5"> <!-- pb-5 for sticky CTA clearance -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark">Rincian Transaksi</h5>
        
        <!-- Toggle Tipe Laporan Sederhana Jika Dibutuhkan -->
        <div class="dropdown">
            <button class="btn btn-sm btn-light border-0 fw-semibold shadow-sm rounded-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <?php 
                    if ($meta['type'] === 'all') echo 'Semua Transaksi';
                    elseif ($meta['type'] === 'sales') echo 'Uang Masuk';
                    elseif ($meta['type'] === 'receivables') echo 'Belum/Telat Bayar';
                    elseif ($meta['type'] === 'clients') echo 'Pelanggan Terbaik';
                ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                <li><a class="dropdown-item py-2" href="#" onclick="switchReportType('all'); return false;">Semua Transaksi</a></li>
                <li><a class="dropdown-item py-2" href="#" onclick="switchReportType('sales'); return false;">Uang Masuk</a></li>
                <li><a class="dropdown-item py-2" href="#" onclick="switchReportType('receivables'); return false;">Belum/Telat Bayar</a></li>
                <li><a class="dropdown-item py-2" href="#" onclick="switchReportType('clients'); return false;">Pelanggan Terbaik</a></li>
            </ul>
        </div>
    </div>

    <!-- EMPTY STATE -->
    <?php if(empty($rows)): ?>
        <div class="text-center py-5 my-4 bg-white rounded-3 shadow-sm">
            <div class="mb-3 text-muted">
                <i class="bi bi-file-earmark-text text-black-50" style="font-size: 3rem;"></i>
            </div>
            <h6 class="fw-bold text-dark mb-2">Belum ada transaksi di periode ini</h6>
            <p class="text-muted small mb-4">Pastikan Anda sudah membuat dan mengirim invoice ke pelanggan Anda.</p>
            <a href="/invoices/create" class="btn btn-primary rounded-pill px-4 fw-semibold" style="min-height: 44px; display: inline-flex; align-items: center;">Buat Invoice Pertama</a>
        </div>
    <?php else: ?>

        <?php if($meta['type'] === 'sales' || $meta['type'] === 'receivables' || $meta['type'] === 'all'): ?>
            
            <!-- MOBILE CARD VIEW (STACK) -->
            <div class="d-flex flex-column gap-3 d-md-none">
                <?php foreach($rows as $row): ?>
                    <?php 
                        $isOverdue = (!empty($row['due_date']) && strtotime($row['due_date']) < strtotime(date('Y-m-d')));
                    ?>
                    <a href="/invoices/<?= esc($row['uuid']) ?>" target="_blank" class="text-decoration-none">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="fw-bold text-dark text-truncate pe-2" style="max-width: 70%;"><?= esc($row['client_name'] ?? 'Tanpa Nama') ?></div>
                                    
                                    <?php if($row['status'] === 'paid'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">Lunas</span>
                                    <?php elseif($isOverdue || $row['status'] === 'overdue'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">Telat</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">Belum</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-end">
                                    <div class="text-muted small">
                                        <?= esc($row['invoice_number']) ?><br>
                                        <?= date('d M Y', strtotime($row['date_issued'])) ?>
                                    </div>
                                    <div class="fw-black text-dark fs-5">
                                        <?= number_format($row['total_amount'], 0, ',', '.') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- DESKTOP TABLE VIEW (HIDDEN ON MOBILE) -->
            <div class="card border-0 shadow-sm rounded-3 d-none d-md-block">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase small fw-bold text-muted ps-4 py-3">Pelanggan</th>
                                    <th class="text-uppercase small fw-bold text-muted py-3">No. Invoice & Tanggal</th>
                                    <?php if($meta['type'] === 'receivables' || $meta['type'] === 'all'): ?>
                                        <th class="text-uppercase small fw-bold text-muted py-3">Status</th>
                                    <?php endif; ?>
                                    <th class="text-uppercase small fw-bold text-muted text-end pe-4 py-3">Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($rows as $row): ?>
                                <?php 
                                    $isOverdue = (!empty($row['due_date']) && strtotime($row['due_date']) < strtotime(date('Y-m-d')));
                                ?>
                                <tr onclick="window.open('/invoices/<?= esc($row['uuid']) ?>', '_blank')" style="cursor: pointer;">
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark"><?= esc($row['client_name'] ?? 'Tanpa Nama') ?></div>
                                    </td>
                                    <td class="py-3">
                                        <div class="text-muted small"><?= esc($row['invoice_number']) ?></div>
                                        <div class="small fw-semibold text-dark"><?= date('d M Y', strtotime($row['date_issued'])) ?></div>
                                    </td>
                                    <?php if($meta['type'] === 'receivables' || $meta['type'] === 'all'): ?>
                                    <td class="py-3">
                                        <?php if($row['status'] === 'paid'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">Lunas</span>
                                        <?php elseif($isOverdue || $row['status'] === 'overdue'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1">Telat Bayar (<?= !empty($row['due_date']) ? date('d M Y', strtotime($row['due_date'])) : '-' ?>)</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill px-2 py-1">Belum Dibayar</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                    <td class="text-end fw-black text-dark pe-4 py-3 fs-6">
                                        <?= number_format($row['total_amount'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php elseif($meta['type'] === 'clients'): ?>
            <!-- CLIENTS: MOBILE CARD VIEW -->
            <div class="d-flex flex-column gap-3 d-md-none">
                <?php foreach($rows as $row): ?>
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="fw-bold text-dark text-truncate pe-2 fs-5"><?= esc($row['client_name'] ?? 'Tanpa Nama') ?></div>
                                <span class="badge bg-light text-dark rounded-pill px-3 py-2 border shadow-sm"><?= esc($row['total_transactions']) ?> Trx</span>
                            </div>
                            
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="small text-muted mb-1">Sudah Bayar</div>
                                    <div class="fw-bold text-success">Rp <?= number_format($row['total_paid'], 0, ',', '.') ?></div>
                                </div>
                                <div class="col-6 text-end">
                                    <div class="small text-muted mb-1">Total Nilai</div>
                                    <div class="fw-black text-dark">Rp <?= number_format($row['total_value'], 0, ',', '.') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- CLIENTS: DESKTOP TABLE VIEW -->
            <div class="card border-0 shadow-sm rounded-3 d-none d-md-block">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-uppercase small fw-bold text-muted ps-4 py-3">Nama Pelanggan</th>
                                    <th class="text-uppercase small fw-bold text-muted text-center py-3">Total Transaksi</th>
                                    <th class="text-uppercase small fw-bold text-muted text-end py-3">Sudah Dibayar (Rp)</th>
                                    <th class="text-uppercase small fw-bold text-muted text-end py-3">Belum Dibayar (Rp)</th>
                                    <th class="text-uppercase small fw-bold text-muted text-end pe-4 py-3">Total Nilai (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($rows as $row): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark fs-6"><?= esc($row['client_name'] ?? 'Tanpa Nama') ?></div>
                                    </td>
                                    <td class="text-center fw-semibold text-dark py-3">
                                        <span class="badge bg-light text-dark rounded-pill px-3 py-2 border"><?= esc($row['total_transactions']) ?></span>
                                    </td>
                                    <td class="text-end text-success fw-bold py-3">
                                        <?= number_format($row['total_paid'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end text-danger fw-semibold py-3">
                                        <?= number_format($row['total_unpaid'], 0, ',', '.') ?>
                                    </td>
                                    <td class="text-end fw-black text-dark fs-6 pe-4 py-3">
                                        <?= number_format($row['total_value'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="mt-4 text-center text-muted small fst-italic">
            Menampilkan data untuk: <?= esc($meta['periodLabel']) ?>
        </div>
    <?php endif; ?>
</div>

<!-- STICKY BOTTOM DOWNLOAD PDF CTA -->
<div class="fixed-bottom p-3 bg-white border-top shadow-sm d-flex justify-content-center" style="z-index: 1030;">
    <div class="container px-0" style="max-width: 600px;">
        <!-- Alpine State for Download Logic -->
        <div x-data="{
            isDownloading: false,
            downloadReport() {
                if (this.isDownloading) return;
                this.isDownloading = true;
                
                // Show toast via native JS/Toastify or alert if no library
                const alertHtml = `
                    <div id='downloadToast' class='position-fixed top-0 start-50 translate-middle-x p-3' style='z-index: 9999; margin-top: 20px;'>
                        <div class='alert border border-dark rounded-pill bg-dark text-white shadow-lg d-flex align-items-center gap-2'>
                            <div class='spinner-border spinner-border-sm' role='status'></div>
                            <span class='fw-semibold'>Menyiapkan laporan...</span>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', alertHtml);

                // Gather URL parameters
                const params = new URLSearchParams(window.location.search);
                params.set('export', 'pdf');
                
                const downloadUrl = '<?= base_url('reports') ?>?' + params.toString();
                
                // Trigger download implicitly via an invisible iframe 
                // This prevents `window.location` from halting javascript execution 
                // in the current window thread on certain mobile browsers.
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = downloadUrl;
                document.body.appendChild(iframe);
                
                // Set timeout to reset the button state and remove toast safely
                setTimeout(() => {
                    this.isDownloading = false;
                    
                    const toastEl = document.getElementById('downloadToast');
                    if (toastEl) {
                        toastEl.innerHTML = `
                            <div class='alert border border-success rounded-pill bg-success text-white shadow-lg d-flex align-items-center gap-2 px-4'>
                                <i class='bi bi-check-circle-fill'></i>
                                <span class='fw-semibold'>Download dimulai</span>
                            </div>
                        `;
                        setTimeout(() => toastEl.remove(), 3000);
                    }
                    
                    // Cleanup iframe after a generous delay to ensure download started
                    setTimeout(() => iframe.remove(), 10000);
                }, 2000);
            }
        }">
            <button type="button" 
                    @click="downloadReport()" 
                    :disabled="isDownloading"
                    class="btn btn-primary w-100 rounded-pill fw-bold shadow-lg d-flex align-items-center justify-content-center" 
                    style="min-height: 52px; font-size: 16px;">
                <template x-if="!isDownloading">
                    <span>
                        <i class="bi bi-file-earmark-pdf-fill me-2 fs-5"></i> Download Laporan PDF
                    </span>
                </template>
                <template x-if="isDownloading">
                    <span>
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Menyiapkan laporan...
                    </span>
                </template>
            </button>
        </div>
    </div>
</div>

<script>
    function switchReportType(type) {
        document.getElementById('reportTypeInput').value = type;
        document.getElementById('reportFilterForm').submit();
    }
</script>

<?= $this->endSection() ?>
