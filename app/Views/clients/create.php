<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Back Navigation -->
        <a href="/clients" class="btn btn-sm btn-link text-muted px-0 mb-3 d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="client-avatar bg-primary text-white">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Tambah Pelanggan Baru</h5>
                        <small class="text-muted">Isi data pelanggan untuk membuat invoice</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if(session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger py-2 small">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <ul class="mb-0 ps-3">
                            <?php foreach(session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/clients/store" method="post" x-data="{ loading: false }" @submit="if(loading) { $event.preventDefault(); return; }; loading = true;">
                    <?= csrf_field() ?>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Nama Pelanggan / Perusahaan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 pe-2"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="client_name" 
                                   class="form-control form-control-lg border-start-0 ps-2" 
                                   placeholder="Contoh: PT Maju Bersama"
                                   value="<?= old('client_name') ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No HP / WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 pe-2"><i class="bi bi-phone text-muted"></i></span>
                                <input type="text" name="client_phone" 
                                       class="form-control border-start-0 ps-2" 
                                       placeholder="08xxxxxxxxxx"
                                       value="<?= old('client_phone') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0 pe-2"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="client_email" 
                                       class="form-control border-start-0 ps-2" 
                                       placeholder="email@contoh.com"
                                       value="<?= old('client_email') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Alamat Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 pe-2" style="align-items: flex-start; padding-top: 0.7rem;"><i class="bi bi-geo-alt text-muted"></i></span>
                            <textarea name="client_address" class="form-control border-start-0 ps-2" rows="3" 
                                      placeholder="Jl. Contoh No. 123, Kota, Provinsi"><?= esc(old('client_address')) ?></textarea>
                        </div>
                    </div>

                    <!-- Sticky Actions (mobile) -->
                    <!-- Desktop Actions -->
                    <div class="form-actions d-none d-md-block">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/clients" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill" :disabled="loading">
                                <span x-show="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                <span x-show="!loading"><i class="bi bi-check-lg me-1"></i> Simpan Pelanggan</span>
                                <span x-show="loading">Menyimpan...</span>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Sticky Submit Bar -->
                    <div class="sticky-bottom-bar d-md-none d-flex justify-content-between align-items-center gap-2 p-3 bg-white border-top shadow-lg" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 1040;">
                        <a href="/clients" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill flex-grow-1" :disabled="loading">
                            <span x-show="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <span x-show="!loading"><i class="bi bi-check-lg me-1"></i> Simpan</span>
                            <span x-show="loading">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
