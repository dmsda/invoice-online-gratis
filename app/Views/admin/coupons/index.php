<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <h2 class="fw-bold mb-1">Manajemen Kupon Diskon</h2>
            <p class="text-muted small mb-0">Kelola dan pantau penggunaan kode promo pelanggan Anda.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#couponModal">
                <i class="bi bi-plus-lg me-1"></i> Buat Promo Baru
            </button>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success border-0 rounded-3 small fw-semibold"><i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?></div>
    <?php endif ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger border-0 rounded-3 small fw-semibold"><i class="bi bi-x-circle-fill me-2"></i><?= session()->getFlashdata('error') ?></div>
    <?php endif ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4 fw-semibold text-muted small">KODE KUPON</th>
                        <th class="py-3 fw-semibold text-muted small">JENIS DISKON</th>
                        <th class="py-3 fw-semibold text-muted small">BATAS PEMAKAIAN</th>
                        <th class="py-3 fw-semibold text-muted small">MASA BERLAKU</th>
                        <th class="py-3 fw-semibold text-muted small text-center">STATUS</th>
                        <th class="py-3 fw-semibold text-muted small text-end px-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($coupons)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">Belum ada promo berjalan. Segera buat kupon untuk memacu konversi.</td></tr>
                    <?php else: ?>
                        <?php foreach($coupons as $c): ?>
                        <tr>
                            <td class="px-4">
                                <span class="d-block text-dark fw-bold fs-5 text-uppercase" style="letter-spacing: 1px;"><?= esc($c['code']) ?></span>
                            </td>
                            <td>
                                <?php if($c['type'] == 'percentage'): ?>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2">Diskon <?= (float)$c['value'] ?>%</span>
                                <?php else: ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">Minus Rp <?= number_format($c['value'], 0, ',', '.') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="d-block fw-semibold text-dark"><?= $c['used_count'] ?> / <?= $c['max_usage'] === null ? '∞' : $c['max_usage'] ?></span>
                                <span class="small text-muted">Aktivasi</span>
                            </td>
                            <td>
                                <span class="d-block small text-muted"><i class="bi bi-calendar-event me-1"></i> Start: <strong class="text-dark"><?= date('d M Y H:i', strtotime($c['valid_from'])) ?></strong></span>
                                <span class="d-block small text-muted"><i class="bi bi-hourglass-split me-1"></i> End: <strong class="text-dark"><?= date('d M Y H:i', strtotime($c['valid_until'])) ?></strong></span>
                            </td>
                            <td class="text-center">
                                <?php 
                                    $now = date('Y-m-d H:i:s');
                                    $isExpired = ($now < $c['valid_from'] || $now > $c['valid_until']);
                                    $isDepleted = ($c['max_usage'] !== null && $c['used_count'] >= $c['max_usage']);
                                ?>
                                <?php if($c['is_active'] == 0): ?>
                                    <span class="badge bg-secondary rounded-pill">Dihentikan</span>
                                <?php elseif($isExpired): ?>
                                    <span class="badge bg-danger rounded-pill">Kadaluwarsa</span>
                                <?php elseif($isDepleted): ?>
                                    <span class="badge bg-warning text-dark rounded-pill">Habis Limit</span>
                                <?php else: ?>
                                    <span class="badge bg-success rounded-pill">Active Live</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end px-4">
                                <form action="/admin/coupons/toggle/<?= $c['id'] ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <?php if($c['is_active'] == 1): ?>
                                        <button class="btn btn-outline-warning btn-sm rounded-pill fw-semibold mb-1" title="Nonaktifkan Kupon">
                                            <i class="bi bi-pause-circle"></i> <span class="d-none d-lg-inline">Pause / Stop</span>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-outline-success btn-sm rounded-pill fw-semibold mb-1" title="Aktifkan Kupon">
                                            <i class="bi bi-play-circle"></i> <span class="d-none d-lg-inline">Resume</span>
                                        </button>
                                    <?php endif; ?>
                                </form>
                                <form action="/admin/coupons/delete/<?= $c['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin mau menghapus permanen kode promo ini? Penghapusan akan ditolak bila kode ini sudah ada dalam laporan riwayat traksaksi pelanggan.');">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-outline-danger btn-sm rounded-pill mb-1" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create Coupon -->
<div class="modal fade" id="couponModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Rilis Promo Baru <i class="bi bi-megaphone-fill ms-1 text-primary"></i></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/admin/coupons/store" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Kode Promo Spesial</label>
                        <input type="text" class="form-control text-uppercase tracking-wider fw-semibold" name="code" required placeholder="MISAL: DISKON50">
                    </div>

                    <div class="row mb-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Tipe Diskon</label>
                            <select class="form-select fw-semibold" name="type" required>
                                <option value="percentage">Potong Persentase (%)</option>
                                <option value="fixed">Potongan Uang Flat (Rp)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Nilai Potongan</label>
                            <input type="text" class="form-control fw-semibold" name="value" required placeholder="Maks: 100 kalau Pilih %">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Batas Penggunaan Global (Opsional)</label>
                        <input type="number" class="form-control" name="max_usage" placeholder="Kosongkan jika tak terbatas">
                        <div class="form-text" style="font-size: 0.75rem;">Limit untuk teknik scarcity, misal "Cuma buat 10 orang!"</div>
                    </div>

                    <div class="row mb-4 g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Masa Aktif Dibuka</label>
                            <input type="datetime-local" class="form-control" name="valid_from" required value="<?= date('Y-m-d\TH:i') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Tanggal Kedaluwarsa</label>
                            <input type="datetime-local" class="form-control" name="valid_until" required value="<?= date('Y-m-d\TH:i', strtotime('+7 days')) ?>">
                        </div>
                    </div>

                    <div class="mb-3 form-check form-switch px-0 bg-light p-2 rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between px-2">
                            <label class="form-check-label fw-bold small text-dark" for="isActiveSwitch">Langsung Live?</label>
                            <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="isActiveSwitch" name="is_active" value="1" checked style="transform: scale(1.3);">
                        </div>
                    </div>

                    <div class="d-grid mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm">Simpan Kupon ke Server</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
