<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <h4 class="fw-bold mb-4">Pengaturan Profil Usaha</h4>

        <div class="card shadow-sm">
            <div class="card-body">
                <form id="profileForm" action="/settings/update_profile" method="post" enctype="multipart/form-data"
                      x-data="{ loading: false }" @submit="loading = true">
                    <?= csrf_field() ?>
                    
                    <h5 class="mb-3 text-primary">Identitas Usaha</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Logo Usaha</label>
                            <?php if(!empty($profile['logo_path'])): ?>
                                <div class="mb-2">
                                    <img src="/<?= esc($profile['logo_path']) ?>" alt="Logo" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control form-control-sm">
                            <small class="text-muted">Format: JPG/PNG, Max 1MB.</small>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Nama Bisnis / Usaha <span class="text-danger">*</span></label>
                                <input type="text" name="business_name" class="form-control" value="<?= esc($profile['business_name']) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. Telepon / WA</label>
                                    <input type="text" name="business_phone" class="form-control" value="<?= esc($profile['business_phone']) ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email Publik</label>
                                    <input type="email" name="business_email" class="form-control" value="<?= esc($profile['business_email']) ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="business_address" class="form-control" rows="2"><?= esc($profile['business_address']) ?></textarea>
                        <small class="text-muted">Akan muncul di Kop Invoice.</small>
                    </div>

                    <hr>
                    <h5 class="mb-3 text-primary">Data Pembayaran (Bank)</h5>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nama Bank</label>
                            <input type="text" name="bank_name" class="form-control" placeholder="BCA / Mandiri" value="<?= esc($profile['bank_name']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nomor Rekening</label>
                            <input type="text" name="bank_number" class="form-control" placeholder="1234567890" value="<?= esc($profile['bank_number']) ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Atas Nama</label>
                            <input type="text" name="bank_account_name" class="form-control" placeholder="Nama Pemilik" value="<?= esc($profile['bank_account_name']) ?>">
                        </div>
                    </div>

                    <!-- SECTION QR PEMBAYARAN -->
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h5 class="mb-2 text-primary">QR Pembayaran (Opsional)</h5>
                        <p class="text-muted small mb-3">QR ini akan ditampilkan di invoice agar pelanggan bisa langsung membayar.</p>

                        <?php if(!empty($profile['qr_code_path'])): ?>
                            <!-- KONDISI 2: QR SUDAH ADA -->
                            <div class="text-center p-3 bg-white border rounded">
                                <label class="d-block text-muted small mb-2">QR Pembayaran Anda</label>
                                <img src="/<?= esc($profile['qr_code_path']) ?>?t=<?= time() ?>" alt="QRIS" class="img-fluid border mb-3" style="max-height: 200px; max-width: 200px;">
                                
                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Tombol Ganti (Trigger File Input via JS/Label) -->
                                    <label for="qr_input" class="btn btn-sm btn-outline-primary cursor-pointer">
                                        <i class="bi bi-upload me-1"></i> Ganti QR
                                    </label>
                                    
                                    <!-- Tombol Hapus: Trigger Modal -->
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteQrModal">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </div>
                                <input type="file" id="qr_input" name="qr_code" class="d-none" onchange="document.getElementById('profileForm').submit()">
                                <small class="d-block text-muted mt-2" style="font-size: 10px;">Format: PNG/JPG (Max 300KB)</small>
                            </div>
                        <?php else: ?>
                            <!-- KONDISI 1: QR BELUM ADA -->
                            <div class="text-center p-4 border border-dashed rounded bg-white">
                                <div class="mb-3 text-muted">
                                    <i class="bi bi-qr-code-scan" style="font-size: 2rem;"></i>
                                </div>
                                <h6 class="text-dark">Belum ada QR pembayaran</h6>
                                <p class="text-muted small mb-3">Upload QRIS atau QR rekening bank Anda (PNG/JPG)</p>
                                
                                <label class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="bi bi-upload me-1"></i> Upload QR Pembayaran
                                    <input type="file" name="qr_code" class="d-none" onchange="document.getElementById('profileForm').submit()">
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Desktop Actions -->
                    <div class="form-actions mt-4 d-none d-md-block">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/dashboard" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4" :disabled="loading">
                                <span x-show="loading" class="spinner-border spinner-border-sm me-1"></span>
                                <span x-show="!loading"><i class="bi bi-check-lg me-1"></i></span>
                                <span x-text="loading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Sticky Submit Bar -->
                    <div class="sticky-bottom-bar d-md-none d-flex justify-content-between align-items-center gap-2 p-3 bg-white border-top shadow-lg" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 1040;">
                        <a href="/dashboard" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill flex-grow-1" :disabled="loading">
                            <span x-show="loading" class="spinner-border spinner-border-sm me-1"></span>
                            <span x-text="loading ? 'Menyimpan...' : 'Simpan'"></span>
                            <span x-show="!loading"><i class="bi bi-check-lg ms-1"></i></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- 
    MODAL KONFIRMASI HAPUS QR 
    Fitur Safety: Checkbox wajib dicentang untuk aktifkan tombol hapus
-->
<div class="modal fade" id="deleteQrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">Hapus QR Pembayaran?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">
                    QR ini digunakan pelanggan untuk membayar invoice. 
                    Jika dihapus, <strong>pelanggan tidak bisa scan QR lagi</strong> dari invoice mereka.
                </p>
                
                <form action="/settings/delete_qr" method="post" x-data="{ confirmed: false }">
                    <?= csrf_field() ?>
                    
                    <div class="form-check p-3 bg-light rounded mb-3 border">
                        <input class="form-check-input" type="checkbox" id="confirmCheck" x-model="confirmed">
                        <label class="form-check-label small user-select-none" for="confirmCheck">
                            Saya mengerti risiko menghapus QR pembayaran.
                        </label>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" :disabled="!confirmed">
                            Ya, Hapus QR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
