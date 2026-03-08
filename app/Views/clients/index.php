<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="client-page-header mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="fw-bold mb-1">Data Pelanggan</h3>
            <p class="text-muted mb-0 small">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                    <i class="bi bi-people-fill me-1"></i><?= $totalClients ?> pelanggan
                </span>
            </p>
        </div>
        <a href="/clients/create" class="btn btn-primary d-none d-md-inline-flex align-items-center gap-2 rounded-pill px-4">
            <i class="bi bi-plus-lg"></i> Tambah Pelanggan
        </a>
    </div>

    <!-- Search Bar -->
    <form action="/clients" method="get" class="client-search-form">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" name="q" class="form-control border-start-0 ps-0" 
                   placeholder="Cari nama, telepon, atau email..." 
                   value="<?= esc($search ?? '') ?>" 
                   autocomplete="off">
            <?php if(!empty($search)): ?>
                <a href="/clients" class="btn btn-outline-primary" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if(!empty($search)): ?>
    <div class="alert alert-info alert-dismissible fade show py-2 small" role="alert">
        <i class="bi bi-info-circle me-1"></i>
        Hasil pencarian untuk "<strong><?= esc($search) ?></strong>" — <?= count($clients) ?> ditemukan
        <a href="/clients" class="btn-close" aria-label="Close" style="padding: 0.5rem;"></a>
    </div>
<?php endif; ?>

<?php if(empty($clients)): ?>
    <!-- Enhanced Empty State -->
    <div class="card shadow-sm border-0">
        <div class="card-body text-center py-5">
            <div class="empty-state-icon mb-3">
                <i class="bi bi-people text-primary" style="font-size: 4rem; opacity: 0.3;"></i>
            </div>
            <?php if(!empty($search)): ?>
                <h5 class="fw-semibold text-dark">Tidak ada hasil</h5>
                <p class="text-muted mb-3">Pelanggan dengan kata kunci "<strong><?= esc($search) ?></strong>" tidak ditemukan.</p>
                <a href="/clients" class="btn btn-outline-primary rounded-pill px-4 mt-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pelanggan
                </a>
            <?php else: ?>
                <h5 class="fw-semibold text-dark">Belum ada pelanggan</h5>
                <p class="text-muted mb-3">Tambahkan pelanggan pertama Anda untuk mulai membuat invoice.</p>
                <a href="/clients/create" class="btn btn-primary rounded-pill px-4 mt-3">
                    <i class="bi bi-plus-lg me-1"></i> Buat Data Pelanggan Baru
                </a>
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
                            <th class="ps-4" style="width: 30%;">Pelanggan</th>
                            <th>No HP / WhatsApp</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th class="text-end pe-4" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($clients as $c): ?>
                        <tr class="position-relative">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="client-avatar">
                                        <?= strtoupper(mb_substr($c['client_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?= esc($c['client_name']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if(!empty($c['client_phone'])): ?>
                                    <i class="bi bi-phone text-muted me-1"></i><?= esc($c['client_phone']) ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($c['client_email'])): ?>
                                    <i class="bi bi-envelope text-muted me-1"></i><?= esc($c['client_email']) ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(!empty($c['client_address'])): ?>
                                    <span class="text-muted small"><?= esc(mb_substr($c['client_address'], 0, 40)) ?><?= mb_strlen($c['client_address']) > 40 ? '...' : '' ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="/clients/show/<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary action-icon-btn rounded-pill" title="Lihat Detail" aria-label="Lihat detail <?= esc($c['client_name']) ?>">
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
        <?php foreach($clients as $c): ?>
        <a href="/clients/show/<?= $c['id'] ?>" class="text-decoration-none">
            <div class="client-card card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="client-avatar client-avatar-lg flex-shrink-0">
                            <?= strtoupper(mb_substr($c['client_name'], 0, 1)) ?>
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <h6 class="fw-bold mb-1 text-truncate text-dark"><?= esc($c['client_name']) ?></h6>
                            <?php if(!empty($c['client_address'])): ?>
                                <p class="text-muted small mb-0 text-truncate">
                                    <i class="bi bi-geo-alt me-1"></i><?= esc($c['client_address']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                    </div>

                    <!-- Contact Info Preview -->
                    <?php if(!empty($c['client_phone']) || !empty($c['client_email'])): ?>
                        <div class="mt-2 pt-2 border-top d-flex gap-2">
                             <?php if(!empty($c['client_phone'])): ?>
                                <small class="text-muted"><i class="bi bi-phone me-1"></i><?= esc($c['client_phone']) ?></small>
                             <?php endif; ?>
                        </div>
                    <?php endif; ?>
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
<a href="/clients/create" class="fab d-md-none" title="Tambah Pelanggan" aria-label="Tambah Pelanggan Baru">
    <i class="bi bi-plus-lg"></i>
</a>

<?= $this->endSection() ?>
