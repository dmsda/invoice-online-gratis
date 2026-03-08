<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Paket & Pricing</h4>
        <p class="text-muted small mb-0">Kelola pengaturan harga, kuota invoice, dan detail paket berlangganan.</p>
    </div>
</div>

<div class="row g-4">
    <?php foreach($plans as $plan): ?>
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 position-relative">
            <?php if($plan['slug'] == 'pro'): ?>
                <div class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-primary px-3 py-2 shadow-sm">
                    <i class="bi bi-star-fill text-warning me-1"></i> Terpopuler
                </div>
            <?php endif; ?>
            
            <div class="card-body p-4 d-flex flex-column">
                <div class="text-center mb-4">
                    <div class="d-inline-flex justify-content-center align-items-center flex-shrink-0 bg-<?= $plan['slug'] == 'pro' ? 'primary' : ($plan['slug'] == 'trial' ? 'warning' : 'secondary') ?> bg-opacity-10 text-<?= $plan['slug'] == 'pro' ? 'primary' : ($plan['slug'] == 'trial' ? 'warning text-dark' : 'secondary') ?> rounded-circle mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-<?= $plan['slug'] == 'pro' ? 'rocket-takeoff-fill' : ($plan['slug'] == 'trial' ? 'clock-history' : 'box-seam') ?> fs-2"></i>
                    </div>
                    <h5 class="fw-bold fs-4 mb-1"><?= esc($plan['name']) ?></h5>
                    <p class="text-muted small mb-0" style="min-height: 40px;"><?= esc($plan['description'] ?? 'Deskripsi paket belum diisi.') ?></p>
                </div>
                
                <div class="bg-light rounded-3 p-3 text-center mb-4 flex-grow-1">
                    <div class="mb-2">
                        <small class="text-muted text-uppercase fw-semibold" style="letter-spacing: 1px; font-size: 0.65rem;">Harga Bulanan</small>
                        <h4 class="fw-bold text-dark mb-0">Rp <?= number_format($plan['price_monthly'], 0, ',', '.') ?></h4>
                    </div>
                    <?php if($plan['price_yearly'] > 0): ?>
                    <hr class="text-muted border-dashed opacity-25 my-2">
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="letter-spacing: 1px; font-size: 0.65rem;">Harga Tahunan</small>
                        <h5 class="fw-bold text-dark mb-0 fs-5">Rp <?= number_format($plan['price_yearly'], 0, ',', '.') ?></h5>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <span class="fs-6 text-dark fw-medium">Batas Invoice: <?= $plan['invoice_limit'] == 999999 ? 'Tak Terbatas' : $plan['invoice_limit'] . ' Dokumen' ?></span>
                    </div>
                    <div class="d-flex align-items-center mb-2 opacity-75">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <span class="small text-dark">Durasi: <?= $plan['price_yearly'] > 0 ? 'Bulanan / Tahunan' : 'Bulanan' ?></span>
                    </div>
                </div>
                
                <button type="button" class="btn <?= $plan['slug'] == 'pro' ? 'btn-primary' : 'btn-outline-secondary' ?> w-100 mt-auto rounded-3 fw-medium" data-bs-toggle="modal" data-bs-target="#editPlanModal<?= $plan['id'] ?>">
                    <i class="bi bi-pencil-square me-1"></i> Edit Pengaturan
                </button>
            </div>
        </div>
        
        <!-- Modal Edit Plan -->
        <div class="modal fade" id="editPlanModal<?= $plan['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <form action="/admin/plans/update" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= $plan['id'] ?>">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold">Edit Paket: <?= esc($plan['name']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-medium small text-muted">Nama Tampilan</label>
                                <input type="text" class="form-control bg-light" name="name" value="<?= esc($plan['name']) ?>" required>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small text-muted">Harga Bulanan (Rp)</label>
                                    <input type="number" class="form-control bg-light" name="price_monthly" value="<?= $plan['price_monthly'] ?>" <?= $plan['slug'] == 'free' ? 'readonly' : '' ?> required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small text-muted">Harga Tahunan (Rp)</label>
                                    <input type="number" class="form-control bg-light" name="price_yearly" value="<?= $plan['price_yearly'] ?>" <?= $plan['slug'] == 'free' ? 'readonly' : '' ?> required>
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small text-muted">Batas Invoice</label>
                                    <input type="number" class="form-control bg-light" name="invoice_limit" value="<?= $plan['invoice_limit'] ?>" required>
                                    <small class="text-muted d-block mt-1" style="font-size:10px;">999999 = Unlimited</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-medium small text-muted">Durasi Harga Aktif</label>
                                    <input type="text" class="form-control bg-light bg-opacity-50 text-muted" value="<?= $plan['price_yearly'] > 0 ? 'Monthly/Yearly' : 'Monthly' ?>" readonly disabled>
                                    <small class="text-muted d-block mt-1" style="font-size:10px;">Sistem Default</small>
                                </div>
                            </div>
                            
                            <div class="mb-1">
                                <label class="form-label fw-medium small text-muted">Deskripsi Singkat</label>
                                <textarea class="form-control bg-light" name="description" rows="2" required><?= esc($plan['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-3 px-4"><i class="bi bi-save me-1"></i> Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="alert alert-info border-0 shadow-sm rounded-4 mt-5 d-flex align-items-center">
    <i class="bi bi-info-circle-fill fs-3 text-info me-3"></i>
    <div>
        <h6 class="fw-bold mb-1 text-dark">Informasi Manajemen Harga</h6>
        <p class="mb-0 small text-muted">Harga yang Anda ubah di sini akan langsung berlaku untuk transaksi pengguna UMKM berikutnya. Langganan aktif yang sedang berjalan milik pengguna belum akan terpengaruh sampai masa perpanjangan berikutnya tiba.</p>
    </div>
</div>

<style>
    .border-dashed { border-style: dashed !important; }
</style>
<?= $this->endSection() ?>
