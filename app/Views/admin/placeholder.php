<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center p-5">
                <i class="bi bi-clock-history text-muted mb-3" style="font-size: 3rem;"></i>
                <h4 class="fw-bold"><?= esc($page_title) ?></h4>
                <p class="text-muted"><?= esc($desc) ?></p>
                <p class="text-primary small mb-0 rounded-pill bg-light d-inline-block px-3 py-1 fw-medium">
                    <i class="bi bi-tools me-1"></i> Segera Hadir
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
