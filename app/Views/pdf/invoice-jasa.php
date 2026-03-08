<?= $this->extend('pdf/invoice-base') ?>
<?= $this->section('content') ?>

<style>
    /* JASA INVOICE SPECIFIC STYLES - DOMPDF SAFE */
    table { width: 100%; border-collapse: collapse; font-family: 'Inter', Arial, sans-serif; color: #1E293B; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .muted { color: #64748B; }
    .bold { font-weight: bold; }
    .primary { color: #2563EB; }

    /* Info Klien & Meta */
    .info-table { margin-bottom: 30px; font-size: 11px; }
    .info-table td { vertical-align: top; }
    .client-box { width: 60%; }
    .meta-box { width: 40%; text-align: right; border-left: 1px solid #E2E8F0; padding-left: 15px; }

    .section-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; color: #1E293B; border-bottom: 2px solid #E2E8F0; padding-bottom: 5px; }

    /* Rincian Layanan */
    .service-table { margin-bottom: 30px; font-size: 11px; }
    .service-table th { border-bottom: 2px solid #1E293B; padding: 10px 5px; color: #1E293B; font-weight: bold; text-align: left; text-transform: uppercase; font-size: 10px; }
    .service-table th.col-no, .service-table td.col-no { width: 5%; text-align: center; }
    .service-table th.col-desc, .service-table td.col-desc { width: 60%; }
    .service-table th.col-period, .service-table td.col-period { width: 15%; text-align: center; }
    .service-table th.col-price, .service-table td.col-price { width: 20%; text-align: right; }

    .service-table td { padding: 12px 5px; border-bottom: 1px solid #E2E8F0; vertical-align: top; }
    .service-table td.col-no { color: #64748B; }
    .service-desc-title { font-weight: bold; font-size: 12px; color: #1E293B; margin-bottom: 4px; }
    .service-desc-detail { font-size: 11px; color: #64748B; line-height: 1.5; }

    /* Ringkasan Biaya */
    .summary-wrapper { width: 100%; margin-bottom: 30px; }
    .summary-table { width: 40%; float: right; font-size: 11px; }
    .summary-table td { padding: 6px 0; }
    .summary-total-row td { border-top: 2px solid #1E293B; padding-top: 10px; font-size: 14px; font-weight: bold; color: #1E293B; }

    /* Custom Clearing for Float */
    .clearfix { clear: both; }

    /* QR Code */
    .qr-box { margin-top: 10px; font-size: 11px; }
    .qr-img { width: 120px; height: 120px; border: 1px solid #E2E8F0; padding: 5px; }

    /* Catatan */
    .notes-box { margin-top: 30px; font-size: 11px; background-color: #F8FAFC; padding: 15px; border-left: 3px solid #2563EB; }
    .notes-title { font-weight: bold; font-size: 12px; margin-bottom: 5px; color: #1E293B; }
</style>

<!-- 2. INFO KLIEN & INVOICE META -->
<table class="info-table">
    <tr>
        <td class="client-box">
            <div class="muted" style="margin-bottom: 5px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">Ditagihkan Kepada:</div>
            <div class="bold" style="font-size: 14px; margin-bottom: 4px;"><?= esc($invoice['client_name']) ?></div>
            <?php if(!empty($invoice['client_address'])): ?>
                <div style="line-height: 1.5; color: #1E293B; margin-bottom: 4px;"><?= nl2br(esc($invoice['client_address'])) ?></div>
            <?php endif; ?>
            <?php if(!empty($invoice['client_phone'])): ?>
                <div class="muted">Telp/WA: <?= esc($invoice['client_phone']) ?></div>
            <?php endif; ?>
        </td>
    </tr>
</table>

<!-- 3. RINCIAN LAYANAN -->
<div class="section-title">Rincian Layanan</div>
<table class="service-table">
    <thead>
        <tr>
            <th class="col-no">No</th>
            <th class="col-desc">Deskripsi Layanan</th>
            <th class="col-period">Kuantitas</th>
            <th class="col-price">Biaya</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($items as $item): ?>
        <tr>
            <td class="col-no"><?= $no++ ?></td>
            <td>
                <div class="service-desc-title"><?= esc($item['item_name']) ?></div>
                <?php if(!empty($item['description'])): ?>
                    <div class="service-desc-detail">
                        <?= nl2br(esc($item['description'])) ?>
                    </div>
                <?php endif; ?>
            </td>
            <td class="col-period"><?= number_format($item['quantity'], 0, ',', '.') ?></td>
            <td class="col-price bold">Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- 4. RINGKASAN BIAYA -->
<div class="summary-wrapper">
    <table class="summary-table">
        <tr>
            <td class="muted">Subtotal:</td>
            <td class="text-right">Rp <?= number_format($invoice['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php if(isset($invoice['discount']) && $invoice['discount'] > 0): ?>
        <tr>
            <td style="color: #dc2626;">Diskon:</td>
            <td class="text-right" style="color: #dc2626;">- Rp <?= number_format($invoice['discount'], 0, ',', '.') ?></td>
        </tr>
        <?php endif; ?>
        <?php if(isset($invoice['tax']) && $invoice['tax'] > 0): ?>
        <tr>
            <td class="muted">Pajak / PPN:</td>
            <td class="text-right">Rp <?= number_format($invoice['tax'], 0, ',', '.') ?></td>
        </tr>
        <?php endif; ?>
        <tr class="summary-total-row">
            <td>TOTAL TAGIHAN:</td>
            <td class="text-right primary">Rp <?= number_format($invoice['total_amount'], 0, ',', '.') ?></td>
        </tr>
    </table>
    <div class="clearfix"></div>
</div>

<!-- 5. QR PEMBAYARAN -->
<div class="qr-box">
    <?php if(!empty($profile['bank_name']) && !empty($profile['bank_number'])): ?>
        <div class="bold" style="font-size: 12px; margin-bottom: 5px;">Instruksi Pembayaran:</div>
        <div style="margin-bottom: 10px; line-height: 1.4;">
            Transfer ke Bank <strong><?= esc($profile['bank_name']) ?></strong><br>
            No. Rekening: <strong><?= esc($profile['bank_number']) ?></strong><br>
            A.n: <?= esc($profile['bank_account_name'] ?? '') ?>
        </div>
    <?php endif; ?>

    <?php if($plan['plan_name'] === 'pro' && !empty($qr_path) && file_exists($qr_path)): ?>
        <?php $qrImgSrc = (isset($isPdf) && $isPdf) ? $qr_path : (isset($qr_url) ? $qr_url : ''); ?>
        <img src="<?= $qrImgSrc ?>" class="qr-img" alt="QRIS Payment">
        <div class="muted" style="font-size: 10px; margin-top: 5px;">Scan kode QR untuk membayar otomatis.</div>
    <?php elseif(empty($profile['bank_name'])): ?>
        <div class="muted" style="font-style: italic;">Silakan hubungi kami untuk metode pembayaran.</div>
    <?php endif; ?>
</div>

<!-- 6. CATATAN & FOOTER -->
<?php if(!empty($invoice['notes'])): ?>
<div class="notes-box">
    <div class="notes-title">Catatan:</div>
    <div style="line-height: 1.5; color: #64748B;">
        <?= nl2br(esc($invoice['notes'])) ?>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
