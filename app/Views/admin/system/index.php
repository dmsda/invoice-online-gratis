<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Status Sistem</h4>
        <p class="text-muted small mb-0">Informasi server, database, dan aktivitas login terakhir.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Server Health -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold mb-0">Informasi Server & Database</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <i class="bi bi-hdd-network text-primary fs-3 me-3"></i>
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="letter-spacing: 0.5px;">PHP Version</small>
                                <div class="fw-bold text-dark fs-5"><?= esc($phpVersion) ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <i class="bi bi-code-square text-danger fs-3 me-3"></i>
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Framework</small>
                                <div class="fw-bold text-dark fs-5">CI <?= esc($ciVersion) ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <i class="bi bi-database text-success fs-3 me-3"></i>
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Database Size</small>
                                <div class="fw-bold text-dark fs-5"><?= esc($dbSize) ?> MB</div>
                                <small class="text-muted" style="font-size: 0.7rem;"><?= esc($totalTables) ?> Tables</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                            <i class="bi bi-clock-history text-warning fs-3 me-3"></i>
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Waktu Server</small>
                                <div class="fw-bold text-dark fs-5"><?= date('H:i') ?> <span class="fs-6 fw-normal text-muted">WIB</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Users -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold mb-0">Pendaftar Terbaru</h6>
            </div>
            <div class="card-body p-4">
                <?php if(empty($recentActivities)): ?>
                    <div class="text-center text-muted my-3">
                        <small>Belum ada data pendaftar.</small>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach($recentActivities as $act): ?>
                        <div class="list-group-item px-0 py-3 border-bottom d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold me-3" style="width: 36px; height: 36px;">
                                <?= strtoupper(substr($act['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="fw-medium text-dark text-truncate" style="max-width: 200px;"><?= esc($act['name']) ?></div>
                                <div class="text-muted small" style="font-size: 0.75rem;"><?= date('d M Y - H:i', strtotime($act['created_at'])) ?></div>
                            </div>
                            <span class="badge <?= $act['role'] == 'admin' ? 'bg-danger' : 'bg-success' ?> ms-auto" style="font-size: 0.65rem;">
                                <?= strtoupper($act['role']) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Database Migrations -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <h6 class="fw-bold mb-0">Riwayat Migrasi Database (10 Terakhir)</h6>
    </div>
    <div class="card-body p-0 mt-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted" style="font-size: 0.8rem;">
                    <tr>
                        <th class="ps-4">VERSION</th>
                        <th>CLASS MIGRATION</th>
                        <th>GROUP</th>
                        <th>NAMESPACE</th>
                        <th class="text-end pe-4">TIMESTAMP EXECUTED</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if(empty($migrations)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada data migrasi ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($migrations as $m): ?>
                        <tr>
                            <td class="ps-4 font-monospace small"><?= esc($m['version']) ?></td>
                            <td class="fw-medium text-dark"><?= esc($m['class']) ?></td>
                            <td><span class="badge bg-secondary bg-opacity-10 text-secondary"><?= esc($m['group']) ?></span></td>
                            <td class="text-muted small"><?= esc($m['namespace']) ?></td>
                            <td class="text-end pe-4 text-muted small"><?= date('d M Y H:i', $m['time']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
