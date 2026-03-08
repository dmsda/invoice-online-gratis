<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Log Pembayaran</h4>
        <p class="text-muted small mb-0">Kelola riwayat transaksi dan bukti transfer dari UMKM berlangganan.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
        <div class="w-100 overflow-hidden">
            <ul class="nav nav-pills flex-nowrap overflow-x-auto pb-2" style="font-size: 0.85rem; scrollbar-width: none;">
                <li class="nav-item">
                    <a class="nav-link text-nowrap <?= $status == 'all' ? 'active bg-primary bg-opacity-10 text-primary fw-semibold' : 'text-muted' ?>" href="/admin/payments?status=all">Semua</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap <?= $status == 'pending' ? 'active bg-warning bg-opacity-10 text-warning fw-semibold' : 'text-muted' ?>" href="/admin/payments?status=pending">Pending</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap <?= $status == 'verified' ? 'active bg-success bg-opacity-10 text-success fw-semibold' : 'text-muted' ?>" href="/admin/payments?status=verified">Verified</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-nowrap <?= $status == 'failed' ? 'active bg-danger bg-opacity-10 text-danger fw-semibold' : 'text-muted' ?>" href="/admin/payments?status=failed">Failed/Rejected</a>
                </li>
            </ul>
        </div>
        
        <form action="/admin/payments" method="GET" class="d-flex w-100 gap-2" style="max-width: 350px;">
            <input type="hidden" name="status" value="<?= esc($status) ?>">
            <input type="text" name="search" class="form-control rounded-3" placeholder="Cari nama/bank..." value="<?= esc($search ?? '') ?>">
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
                        <th class="ps-4">TANGGAL BAYAR</th>
                        <th>PELANGGAN & BISNIS</th>
                        <th>PAKET</th>
                        <th>NOMINAL</th>
                        <th>METODE & BANK</th>
                        <th>STATUS</th>
                        <th class="text-end pe-4">BUKTI</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-wallet2 fs-1 text-light mb-2 d-block"></i>
                            Tidak ada data histori pembayaran ditemukan.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($payments as $pay): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-medium text-dark"><?= date('d M Y', strtotime($pay['created_at'])) ?></div>
                                <div class="text-muted small" style="font-size: 0.75rem;"><?= date('H:i', strtotime($pay['created_at'])) ?> WIB</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark fs-6"><?= esc($pay['user_name']) ?></div>
                                <?php if($pay['profile_bus_name']): ?>
                                    <div class="text-muted small" style="font-size: 0.8rem;"><i class="bi bi-shop me-1"></i> <?= esc($pay['profile_bus_name']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1">
                                    <?= esc(strtoupper($pay['plan_name'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-dark">Rp <?= number_format($pay['amount'], 0, ',', '.') ?></span>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><i class="bi bi-credit-card me-1 text-muted"></i> <?= strtoupper(esc($pay['method'] ?? 'MANUAL')) ?></div>
                                <?php if($pay['bank_name']): ?>
                                    <div class="text-muted small" style="font-size: 0.8rem;">Bank: <?= esc($pay['bank_name']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($pay['status'] == 'verified'): ?>
                                    <span class="badge bg-success px-2 py-1 rounded-pill"><i class="bi bi-check-circle me-1"></i> Verified</span>
                                <?php elseif($pay['status'] == 'pending'): ?>
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill"><i class="bi bi-clock me-1"></i> Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-danger px-2 py-1 rounded-pill"><i class="bi bi-x-circle me-1"></i> <?= ucfirst($pay['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <?php if ($pay['proof_file']): ?>
                                    <a href="<?= base_url('uploads/proofs/' . $pay['proof_file']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary btn-icon-only-mobile" title="Lihat Bukti">
                                        <i class="bi bi-file-earmark-image"></i> <span class="d-none d-lg-inline">Cek</span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small fst-italic">T/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php if (isset($pager) && $pager): ?>
    <div class="card-footer bg-white border-top-0 py-3">
        <?= $pager->links('default', 'bootstrap_pagination') ?>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
