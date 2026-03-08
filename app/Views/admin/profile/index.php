<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h5 class="mb-0 fw-bold">Pengaturan Profil Admin</h5>
                <p class="text-muted small mt-1">Ubah detail kredensial dan informasi profil Anda.</p>
            </div>
            <div class="card-body p-4">
                <form action="/admin/profile/update" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Nama Bisnis / Lengkap</label>
                        <input type="text" name="business_name" class="form-control form-control-lg fs-6" value="<?= esc($profile['business_name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium small">Alamat Email</label>
                        <input type="email" name="email" class="form-control form-control-lg fs-6" value="<?= esc($admin['email']) ?>" required>
                    </div>
                    
                    <hr class="my-4 text-muted">
                    
                    <h6 class="fw-bold mb-3">Ubah Password (Opsional)</h6>
                    <div class="mb-4">
                        <label class="form-label fw-medium small">Password Baru</label>
                        <input type="password" name="password" class="form-control form-control-lg fs-6" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <small class="text-muted d-block mt-1">Gunakan kombinasi huruf dan angka agar lebih aman.</small>
                    </div>
                    
                    <div class="d-grid mt-2">
                        <button type="submit" class="btn btn-primary btn-lg rounded-3 fs-6">
                            <i class="bi bi-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
