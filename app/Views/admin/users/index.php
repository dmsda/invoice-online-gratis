<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen User</h4>
        <p class="text-muted small mb-0">Kelola semua pelanggan UMKM yang terdaftar di sistem Invoice Online.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
        <h6 class="mb-0 fw-bold align-self-start align-self-md-center">Daftar Pengguna</h6>
        
        <form action="/admin/users" method="GET" class="d-flex w-100 gap-2" style="max-width: 350px;">
            <input type="text" name="search" class="form-control rounded-3" placeholder="Cari nama atau email..." value="<?= esc($search ?? '') ?>">
            <button class="btn btn-primary rounded-3 px-3 shadow-sm" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>
    
    <div class="card-body p-0 mt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted" style="font-size: 0.8rem;">
                    <tr>
                        <th class="ps-4">PENGGUNA</th>
                        <th>BISNIS UMKM</th>
                        <th>PAKET AKTIF</th>
                        <th>TERDAFTAR</th>
                        <th>STATUS</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 text-light mb-2 d-block"></i>
                            Belum ada pengguna terdaftar atau pencarian tidak ditemukan.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px;">
                                        <?php $displayName = $user['profile_bus_name'] ? $user['profile_bus_name'] : substr($user['email'], 0, strpos($user['email'], '@')); ?>
                                        <?= strtoupper(substr($displayName, 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark fs-6"><?= esc($user['profile_bus_name']) ?: '<span class="text-muted fst-italic small">Belum diisi</span>' ?></div>
                                        <div class="text-muted small" style="font-size: 0.8rem;"><?= esc($user['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-medium text-dark"><?= $user['profile_bus_name'] ? esc($user['profile_bus_name']) : '<span class="text-muted fst-italic">Belum diisi</span>' ?></span>
                            </td>
                            <td>
                                <?php if($user['plan_name']): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                        <i class="bi bi-star-fill me-1"></i> <?= esc(strtoupper($user['plan_name'])) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1">
                                        FREE
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 0.85rem;">
                                    <?= date('d M Y', strtotime($user['created_at'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if($user['is_active']): ?>
                                    <span class="badge bg-success px-2 py-1 rounded-pill"><i class="bi bi-check-circle me-1"></i> Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger px-2 py-1 rounded-pill"><i class="bi bi-slash-circle me-1"></i> Suspend</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4 align-middle">
                                <form action="/admin/users/toggle/<?= $user['id'] ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengganti status akses pengguna ini?');">
                                    <?= csrf_field() ?>
                                    <?php if($user['is_active']): ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-icon-only-mobile" title="Suspend Akun">
                                            <i class="bi bi-pause-circle"></i> <span class="d-none d-lg-inline">Suspend</span>
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success btn-icon-only-mobile" title="Aktifkan Akun">
                                            <i class="bi bi-play-circle"></i> <span class="d-none d-lg-inline">Aktifkan</span>
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if ($pager): ?>
    <div class="card-footer bg-white border-top-0 py-3">
        <?= $pager->links('default', 'bootstrap_pagination') ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
