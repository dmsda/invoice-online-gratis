<?php 
$pager->setSurroundCount(2); 
?>

<nav aria-label="Navigasi Halaman" class="mb-4 d-flex justify-content-center w-100">
    <!-- Unified Responsive Pagination: Scrollable on mobile, wrapping on desktop -->
    <ul class="pagination mb-0 d-flex flex-nowrap flex-sm-wrap gap-2 overflow-auto" style="padding-bottom: 5px; -webkit-overflow-scrolling: touch; scrollbar-width: none;">
        <style>
            .pagination::-webkit-scrollbar { display: none; }
        </style>
        
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item flex-shrink-0">
                <a class="page-link rounded-circle border-0 text-dark bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="<?= $pager->getFirst() ?>" aria-label="First" title="Pertama">
                    <i class="bi bi-chevron-double-left"></i>
                </a>
            </li>
            <li class="page-item flex-shrink-0">
                <a class="page-link rounded-circle border-0 text-dark bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="<?= $pager->getPrevious() ?>" aria-label="Previous" title="Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled flex-shrink-0">
                <span class="page-link rounded-circle border-0 text-muted bg-light border opacity-75 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-chevron-left"></i>
                </span>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?> flex-shrink-0">
                <a class="page-link rounded-circle border-0 <?= $link['active'] ? 'bg-primary text-white shadow fw-bold' : 'text-dark bg-white shadow-sm fw-medium hover-bg-light' ?> d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="page-item flex-shrink-0">
                <a class="page-link rounded-circle border-0 text-dark bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="<?= $pager->getNext() ?>" aria-label="Next" title="Selanjutnya">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
            <li class="page-item flex-shrink-0">
                <a class="page-link rounded-circle border-0 text-dark bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" href="<?= $pager->getLast() ?>" aria-label="Last" title="Terakhir">
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            </li>
        <?php else: ?>
            <li class="page-item disabled flex-shrink-0">
                <span class="page-link rounded-circle border-0 text-muted bg-light border opacity-75 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-chevron-right"></i>
                </span>
            </li>
        <?php endif ?>
    </ul>
</nav>
