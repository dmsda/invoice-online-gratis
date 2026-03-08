<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center mt-4 mb-5 pb-5">
    <div class="col-md-8 col-lg-6">
        
        <!-- Header -->
        <div class="text-center mb-4">
            <h3 class="fw-bold mb-1">Selesaikan Pembayaran</h3>
            <p class="text-muted small">Paket <?= esc($plan['name']) ?> • Menunggu Pembayaran</p>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <!-- Amount Section -->
            <div class="bg-primary bg-opacity-10 p-4 text-center border-bottom border-primary border-opacity-25">
                <span class="d-block text-muted small fw-semibold text-uppercase letter-spacing-wide mb-2">Total Tagihan</span>
                <h1 class="display-6 fw-bold text-primary mb-0">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></h1>
                <?php if($payment['method'] === 'transfer'): ?>
                    <p class="small text-danger fw-semibold mt-2 mb-0 bg-white d-inline-block px-3 py-1 rounded-pill shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Transfer PERSIS MENTOK dengan 3 digit terakhir!
                    </p>
                <?php else: ?>
                    <p class="small text-muted mt-2 mb-0">Pastikan nominal transfer sesuai agar proses verifikasi lebih cepat.</p>
                <?php endif; ?>
            </div>

            <div class="card-body p-4 p-md-5">

                <!-- Payment Details Instruksi -->
                <?php if($payment['method'] === 'transfer'): ?>
                    <h5 class="fw-bold mb-3"><i class="bi bi-bank me-2 text-primary"></i>Instruksi Transfer</h5>
                    <div class="bg-light p-3 rounded-3 mb-4">
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted small">Bank</div>
                            <div class="col-sm-8 fw-semibold">BCA (Bank Central Asia)</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted small">Nomor Rekening</div>
                            <div class="col-sm-8 fw-bold fs-5 text-dark font-monospace">8123 456 789</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 text-muted small">Atas Nama</div>
                            <div class="col-sm-8 fw-semibold">PT Invois Online Nusantara</div>
                        </div>
                    </div>
                <?php else: ?>
                    <h5 class="fw-bold text-center mb-3"><i class="bi bi-qr-code-scan me-2 text-primary"></i>Scan QRIS Berikut</h5>
                    <div class="bg-light p-3 rounded-3 mb-4 text-center">
                        <div class="mb-3">
                            <img src="<?= base_url('assets/images/sample-qris.png') ?>" alt="QRIS" class="img-fluid rounded shadow-sm" style="max-height: 250px; width: auto; object-fit: contain; background: white; padding: 10px;" onerror="this.src='https://placehold.co/250x250/ffffff/0d6efd?text=QRIS+Image'">
                        </div>
                        <p class="small text-muted mb-0">
                            Buka aplikasi e-Wallet atau M-Banking Anda (Gopay, OVO, Dana, ShopeePay, BCA Mobile, dll) lalu scan kode QR di atas.
                        </p>
                    </div>
                <?php endif; ?>

                <hr class="my-4">

                <!-- Upload Form -->
                <h5 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-up me-2 text-primary"></i>Upload Bukti Transfer</h5>
                
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger px-3 py-2 small fw-semibold border-0 rounded-3"><i class="bi bi-exclamation-circle-fill me-2"></i><?= session()->getFlashdata('error') ?></div>
                <?php endif ?>

                <form action="/subscription/upload-proof" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <input type="hidden" name="subscription_id" value="<?= $subscription['id'] ?>">
                    <input type="hidden" name="payment_id" value="<?= $payment['id'] ?>">

                    <?php if($payment['method'] === 'transfer'): ?>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Bank Pengirim <span class="text-danger">*</span></label>
                                <input type="text" name="bank_name" class="form-control" placeholder="Contoh: BCA, Mandiri" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Nama Pemilik Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="account_name" class="form-control" placeholder="Atas nama pengirim" required>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-muted">File Bukti <span class="text-danger">*</span></label>
                        <input class="form-control" type="file" name="proof" id="formFile" accept=".jpg,.jpeg,.png,.pdf" required>
                        <div class="form-text small"><i class="bi bi-info-circle me-1"></i>Format: JPG, PNG, atau PDF. Maksimal 2MB.</div>
                    </div>

                    <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                            Kirim Bukti Pembayaran
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <div class="text-center">
            <a href="/pricing" class="btn btn-link text-muted text-decoration-none small">
                <i class="bi bi-arrow-left me-1"></i> Batal dan ganti metode
            </a>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
