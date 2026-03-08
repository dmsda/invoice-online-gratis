<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Laporan SaaS & Analitik</h4>
        <p class="text-muted small mb-0">Pantau pertumbuhan bisnis, Monthly Recurring Revenue (MRR), dan aktivitas pengguna.</p>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small fw-semibold mb-0 text-uppercase" style="letter-spacing: 0.5px;">Est. MRR</p>
                    <h4 class="fw-bold mb-0 text-dark">Rp <?= number_format($mrr, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small fw-semibold mb-0 text-uppercase" style="letter-spacing: 0.5px;">Total Revenue</p>
                    <h4 class="fw-bold mb-0 text-dark">Rp <?= number_format($totalRevenue, 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small fw-semibold mb-0 text-uppercase" style="letter-spacing: 0.5px;">Active Subs</p>
                    <h4 class="fw-bold mb-0 text-dark"><?= number_format($activeSubscribers) ?> <span class="fs-6 fw-normal text-muted">Akun</span></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="bi bi-person-badge fs-4"></i>
                </div>
                <div>
                    <p class="text-muted small fw-semibold mb-0 text-uppercase" style="letter-spacing: 0.5px;">Total UMKM</p>
                    <h4 class="fw-bold mb-0 text-dark"><?= number_format($totalUsers) ?> <span class="fs-6 fw-normal text-muted">Akun</span></h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart Pendapatan -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold mb-0">Tren Pendapatan (6 Bulan Terakhir)</h6>
            </div>
            <div class="card-body p-4">
                <canvas id="revenueChart" style="min-height: 250px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Distribusi Paket -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                <h6 class="fw-bold mb-0">Distribusi Pelanggan Aktif</h6>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <?php if(empty($planDistribution)): ?>
                    <div class="text-center text-muted my-5">
                        <i class="bi bi-pie-chart fs-1 opacity-25 d-block mb-2"></i>
                        <small>Belum ada data pelanggan aktif berlangganan saat ini.</small>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush border-0 mt-2">
                        <?php foreach($planDistribution as $pd): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 py-3 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 bg-<?= strtolower($pd['name']) == 'pro' ? 'primary' : 'warning' ?> rounded-circle" style="width: 12px; height: 12px;"></div>
                                    <span class="fw-medium text-dark"><?= esc($pd['name']) ?></span>
                                </div>
                                <span class="badge bg-secondary bg-opacity-10 text-dark px-3 py-2 rounded-pill"><?= $pd['total'] ?> Pengguna</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const chartData = <?= $chartData ?>;
    const ctx = document.getElementById('revenueChart');
    
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: chartData.data,
                    backgroundColor: '#DBEAFE', // Bootstrap primary opacity-10
                    borderColor: '#2563EB', // Bootstrap primary
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverBackgroundColor: '#2563EB'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e2e8f0',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            callback: function(value, index, values) {
                                if (value >= 1000000) {
                                    return (value / 1000000) + 'jt';
                                } else if (value >= 1000) {
                                    return (value / 1000) + 'rb';
                                }
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
