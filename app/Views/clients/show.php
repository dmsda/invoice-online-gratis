<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Back Navigation -->
        <a href="/clients" class="btn btn-sm btn-link text-muted px-0 mb-3 d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start text-center text-md-start gap-4">
                    <!-- Avatar -->
                    <div class="client-avatar-xl bg-primary text-white flex-shrink-0" style="width: 80px; height: 80px; font-size: 2.5rem; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                        <?= strtoupper(mb_substr($client['client_name'], 0, 1)) ?>
                    </div>
                    
                    <div class="flex-grow-1 w-100">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start mb-3">
                            <div>
                                <h4 class="fw-bold mb-1 text-dark"><?= esc($client['client_name']) ?></h4>
                                <p class="text-muted small mb-0">Dibambahkan pada <?= date('d M Y', strtotime($client['created_at'])) ?></p>
                            </div>
                            
                            <!-- Desktop Actions -->
                            <div class="d-none d-md-flex gap-2 mt-3 mt-md-0">
                                <a href="/clients/edit/<?= $client['id'] ?>" class="btn btn-outline-primary rounded-pill px-4">
                                    <i class="bi bi-pencil me-1"></i> Ubah
                                </a>
                                <form action="/clients/delete/<?= $client['id'] ?>" method="post" onsubmit="return confirm('Yakin hapus pelanggan ini? Data invoice terkait mungkin akan kehilangan referensi.');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                                        <i class="bi bi-trash3 me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="row g-3 text-start">
                            <div class="col-12 border-top pt-3">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Kontak</label>
                                <div class="d-flex flex-column gap-2">
                                    <?php if(!empty($client['client_phone'])): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-phone text-muted"></i>
                                            <span><?= esc($client['client_phone']) ?></span>
                                            <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $client['client_phone'])) ?>" target="_blank" class="text-decoration-none small text-success fw-bold">
                                                <i class="bi bi-whatsapp me-1"></i>Chat WA
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if(!empty($client['client_email'])): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-envelope text-muted"></i>
                                            <span><?= esc($client['client_email']) ?></span>
                                            <a href="mailto:<?= esc($client['client_email']) ?>" class="text-decoration-none small text-primary fw-bold">
                                                <i class="bi bi-envelope me-1"></i>Kirim Email
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if(empty($client['client_phone']) && empty($client['client_email'])): ?>
                                        <span class="text-muted small fst-italic">Tidak ada informasi kontak.</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-12 border-top pt-3">
                                <label class="small text-muted text-uppercase fw-bold mb-1">Alamat</label>
                                <p class="mb-0 text-dark">
                                    <?php if(!empty($client['client_address'])): ?>
                                        <?= nl2br(esc($client['client_address'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted small fst-italic">Alamat belum diisi.</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Actions (Sticky Bottom if needed, or just standard buttons below card) -->
            <!-- For Detail page, standard buttons below card are often safer than sticky delete buttons. -->
            <!-- But let's follow the pattern: Primary Action (Edit) vs Destructive (Delete). -->
            <div class="card-footer bg-white p-3 d-md-none border-top-0">
                <div class="d-grid gap-2">
                    <a href="/clients/edit/<?= $client['id'] ?>" class="btn btn-outline-primary rounded-pill">
                        <i class="bi bi-pencil me-1"></i> Ubah Data Pelanggan
                    </a>
                    <form action="/clients/delete/<?= $client['id'] ?>" method="post" onsubmit="return confirm('Yakin hapus pelanggan ini?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">
                            <i class="bi bi-trash3 me-1"></i> Hapus Pelanggan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Linked Invoices Info (Future Proofing) -->
        <div class="card shadow-sm border-0">
             <div class="card-header bg-white py-3 border-bottom">
                 <h6 class="mb-0 fw-bold">Riwayat Invoice</h6>
             </div>
             <div class="card-body text-center py-4">
                 <p class="text-muted small mb-3">Fitur riwayat invoice pelanggan akan segera hadir.</p>
                 <a href="/invoices/create?client_id=<?= $client['id'] ?>" class="btn btn-primary rounded-pill">
                     <i class="bi bi-plus-lg me-1"></i> Buat Invoice Baru
                 </a>
             </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
