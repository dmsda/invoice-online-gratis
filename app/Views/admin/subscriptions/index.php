<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<?php helper('subscription') ?>

<!-- 1️⃣ HEADER HALAMAN -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Manajemen Langganan</h4>
        <p class="text-muted small mb-0">
            Kelola verifikasi pembayaran dan status user
        </p>
    </div>
</div>

<!-- 📊 BONUS: STATS HEADER -->
<div class="row mb-4">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 rounded-3 text-center p-3">
            <small class="text-muted">Pending</small>
            <h5 class="fw-bold mb-0 text-warning"><?= $stats['pending'] ?></h5>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 rounded-3 text-center p-3">
            <small class="text-muted">Aktif</small>
            <h5 class="fw-bold mb-0 text-success"><?= $stats['active'] ?></h5>
        </div>
    </div>
</div>

<!-- 2️⃣ FILTER STATUS (MOBILE FRIENDLY) -->
<form method="get" class="mb-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div class="d-flex gap-2 flex-wrap">
        <a href="?status=all" class="btn btn-sm <?= $status=='all'?'btn-primary':'btn-outline-primary' ?> rounded-pill px-3">Semua</a>
        <a href="?status=pending" class="btn btn-sm <?= $status=='pending'?'btn-warning':'btn-outline-warning' ?> rounded-pill px-3">Pending</a>
        <a href="?status=active" class="btn btn-sm <?= $status=='active'?'btn-success':'btn-outline-success' ?> rounded-pill px-3">Aktif</a>
        <a href="?status=expired" class="btn btn-sm <?= $status=='expired'?'btn-secondary':'btn-outline-secondary' ?> rounded-pill px-3">Expired</a>
        <a href="?status=rejected" class="btn btn-sm <?= $status=='rejected'?'btn-danger':'btn-outline-danger' ?> rounded-pill px-3">Ditolak</a>
    </div>
    
    <div>
        <a href="/admin/subscriptions/export?status=<?= $status ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-4 shadow-sm" target="_blank">
            <i class="bi bi-filetype-csv me-1"></i> Export CSV
        </a>
    </div>
</form>

<?php if(empty($subscriptions)): ?>
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body py-5 text-center">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
            <h5 class="mt-3 text-dark fw-bold">Belum ada langganan</h5>
            <p class="text-muted mb-0">Tidak ada data langganan yang sesuai filter.</p>
        </div>
    </div>
<?php else: ?>

<form action="/admin/subscriptions/bulk-action" method="POST" id="bulkActionForm">
    <?= csrf_field() ?>

    <?php if($status === 'pending' || $status === 'all'): ?>
    <div class="d-flex justify-content-end mb-3 gap-2">
        <select name="bulk_action" class="form-select form-select-sm w-auto rounded-pill px-3 shadow-sm" required>
            <option value="">-- Aksi Massal --</option>
            <option value="approve">Approve Terpilih</option>
            <option value="reject">Tolak Terpilih</option>
        </select>
        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm">Terapkan</button>
    </div>
    <?php endif; ?>

    <!-- 📱 3️⃣ MOBILE CARD VIEW -->
    <div class="d-md-none">
    <?php foreach($subscriptions as $sub): ?>
    <div class="card shadow-sm border-0 rounded-3 mb-3">
        <div class="card-body p-3">

            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center gap-2">
                    <?php if($sub['status']=='pending'): ?>
                        <input class="form-check-input bulk-checkbox" type="checkbox" name="sub_ids[]" value="<?= $sub['id'] ?>">
                    <?php endif; ?>
                    <div>
                        <h6 class="fw-bold mb-0"><?= esc($sub['business_name']) ?></h6>
                        <small class="text-muted"><?= esc($sub['plan_name']) ?></small>
                    </div>
                </div>
                <span class="badge rounded-pill text-bg-<?= status_color($sub['status']) ?>">
                    <?= ucfirst($sub['status']) ?>
                </span>
            </div>

            <div class="mt-2 small text-muted">
                <i class="bi bi-credit-card me-1"></i> Metode: <?= esc($sub['payment_method']) ?><br>
                <i class="bi bi-calendar3 me-1"></i> Dibuat: <?= date('d M Y', strtotime($sub['created_at'])) ?>
            </div>

            <div class="d-flex gap-2 mt-3 pt-3 border-top">

                <?php if($sub['status']=='pending'): ?>
                <button class="btn btn-sm btn-success flex-fill"
                        data-bs-toggle="modal"
                        data-bs-target="#approveModal<?= $sub['id'] ?>">
                    <i class="bi bi-check-circle me-1"></i> Approve
                </button>

                <button class="btn btn-sm btn-danger flex-fill"
                        data-bs-toggle="modal"
                        data-bs-target="#rejectModal<?= $sub['id'] ?>">
                    <i class="bi bi-x-circle me-1"></i> Tolak
                </button>
                <?php endif; ?>

                <?php if($sub['proof_file']): ?>
                <a href="<?= base_url('admin/view-proof/'.$sub['proof_file']) ?>"
                   target="_blank"
                   class="btn btn-sm btn-outline-primary <?= $sub['status']!='pending' ? 'flex-fill' : '' ?>">
                    <i class="bi bi-eye"></i> Bukti
                </a>
                <?php endif; ?>

            </div>

        </div>
    </div>
    <?php endforeach; ?>
    </div>

    <!-- 🖥 4️⃣ DESKTOP TABLE VIEW -->
    <div class="card shadow-sm border-0 rounded-3 mt-4 d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" style="width: 40px;">
                            <?php if($status === 'pending' || $status === 'all'): ?>
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <?php endif; ?>
                        </th>
                        <th>Usaha</th>
                        <th>Paket</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($subscriptions as $sub): ?>
                    <tr>
                        <td class="ps-4">
                            <?php if($sub['status']=='pending'): ?>
                            <input class="form-check-input bulk-checkbox" type="checkbox" name="sub_ids[]" value="<?= $sub['id'] ?>">
                            <?php endif; ?>
                        </td>
                        <td class="fw-medium"><?= esc($sub['business_name']) ?></td>
                        <td><?= esc($sub['plan_name']) ?></td>
                        <td><?= esc($sub['payment_method']) ?></td>
                        <td>
                            <span class="badge rounded-pill text-bg-<?= status_color($sub['status']) ?>">
                                <?= ucfirst($sub['status']) ?>
                            </span>
                        </td>
                        <td class="text-muted small"><?= date('d M Y', strtotime($sub['created_at'])) ?></td>
                        <td class="pe-4 text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <?php if($sub['status']=='pending'): ?>
                                <button class="btn btn-sm btn-success rounded-pill px-3"
                                        title="Approve"
                                        data-bs-toggle="modal"
                                        data-bs-target="#approveModal<?= $sub['id'] ?>">
                                    <i class="bi bi-check-circle"></i>
                                </button>

                                <button class="btn btn-sm btn-danger rounded-pill px-3"
                                        title="Tolak"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectModal<?= $sub['id'] ?>">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                <?php endif; ?>

                                <?php if($sub['proof_file']): ?>
                                <a href="<?= base_url('admin/view-proof/'.$sub['proof_file']) ?>"
                                   target="_blank"
                                   title="Cek Bukti"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($pager): ?>
        <div class="card-footer bg-white border-top-0 py-3">
            <?= $pager->links('default', 'bootstrap_pagination') ?>
        </div>
        <?php endif; ?>
    </div>
</form>

<?php endif; ?>

<!-- MODALS FOR APPROVE/REJECT -->
<?php foreach($subscriptions as $sub): ?>
    <?php if($sub['status']=='pending'): ?>
        <!-- 🔐 5️⃣ MODAL APPROVE -->
        <div class="modal fade" id="approveModal<?= $sub['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" action="/admin/approve">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $sub['id'] ?>">

                    <div class="modal-content rounded-4 border-0 shadow">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold">Konfirmasi Aktivasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            Yakin ingin mengaktifkan langganan <strong><?= esc($sub['plan_name']) ?></strong> untuk usaha <strong><?= esc($sub['business_name']) ?></strong>?
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                Ya, Aktifkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ❌ 6️⃣ MODAL REJECT -->
        <div class="modal fade" id="rejectModal<?= $sub['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" action="/admin/reject">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= $sub['id'] ?>">

                    <div class="modal-content rounded-4 border-0 shadow">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold">Tolak Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body py-4">
                            <p class="mb-3">Penolakan langganan <strong><?= esc($sub['business_name']) ?></strong></p>
                            <textarea name="reason"
                                      class="form-control rounded-3"
                                      placeholder="Tulis alasan penolakan..."
                                      rows="3"
                                      required></textarea>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4">
                                Tolak Langganan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.bulk-checkbox');

    if (selectAllBtn) {
        selectAllBtn.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAllBtn.checked;
            });
        });
    }
});
</script>

<?= $this->endSection() ?>
