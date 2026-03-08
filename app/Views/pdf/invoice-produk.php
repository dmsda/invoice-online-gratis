<?= $this->extend('pdf/invoice-base') ?>
<?= $this->section('content') ?>

<style>
    /* PRODUK INVOICE SPECIFIC STYLES - DOMPDF SAFE */
    table { width: 100%; border-collapse: collapse; font-family: 'Inter', Arial, sans-serif; color: #1E293B; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .muted { color: #64748B; }
    .bold { font-weight: bold; }
    .primary { color: #2563EB; }

    /* Info Pelanggan & Meta */
    .info-table { margin-bottom: 25px; font-size: 11px; }
    .info-table td { vertical-align: top; }
    .client-box { width: 60%; }
    .meta-box { width: 40%; text-align: right; }

    /* Tabel Produk */
    .product-table { margin-bottom: 20px; font-size: 11px; width: 100%; border: 1px solid #E2E8F0; }
    .product-table thead { display: table-header-group; }
    .product-table tr { page-break-inside: avoid; }
    .product-table th { background-color: #F1F5F9; color: #1E293B; font-weight: bold; padding: 8px 10px; font-size: 11px; border-bottom: 1px solid #E2E8F0; border-right: 1px solid #E2E8F0; }
    .product-table th:last-child { border-right: none; }
    
    .product-table td { padding: 10px; border-bottom: 1px solid #E2E8F0; border-right: 1px solid #E2E8F0; vertical-align: top; }
    .product-table td:last-child { border-right: none; }
    
    .col-no { width: 5%; text-align: center; }
    .col-name { width: 45%; }
    .col-qty { width: 10%; text-align: center; }
    .col-price { width: 22%; text-align: right; }
    .col-subtotal { width: 18%; text-align: right; }

    .product-name { font-weight: bold; font-size: 11px; color: #1E293B; margin-bottom: 3px; }
    .product-desc { font-size: 10px; color: #64748B; line-height: 1.4; }

    /* Ringkasan Biaya & QR Layout */
    .bottom-container { width: 100%; margin-top: 15px; }
    
    .summary-table { width: 45%; float: right; font-size: 11px; }
    .summary-table td { padding: 6px 0; border-bottom: 1px dashed #E2E8F0; }
    .summary-total-row td { border-bottom: none; border-top: 2px solid #1E293B; padding-top: 8px; font-size: 14px; font-weight: bold; color: #1E293B; }

    .qr-box { width: 50%; float: left; font-size: 11px; }
    .qr-img { width: 120px; height: 120px; border: 1px solid #E2E8F0; padding: 4px; margin-top: 8px; }

    .clearfix { clear: both; }

    /* Catatan */
    .notes-box { margin-top: 30px; font-size: 10px; color: #64748B; }
    .notes-title { font-weight: bold; color: #1E293B; margin-bottom: 4px; font-size: 11px; }
</style>

<!-- 2. INFO PELANGGAN & META (Meneruskan Header dari invoice-base) -->
<table class="info-table">
    <tr>
        <td class="client-box">
            <div class="muted" style="margin-bottom: 4px; font-size: 10px; text-transform: uppercase;">Ditagihkan Kepada:</div>
            <div class="bold" style="font-size: 13px; margin-bottom: 4px; color: #1E293B;"><?= esc($invoice['client_name']) ?></div>
            <?php if(!empty($invoice['client_address'])): ?>
                <div style="line-height: 1.4; color: #1E293B; margin-bottom: 4px;"><?= nl2br(esc($invoice['client_address'])) ?></div>
            <?php endif; ?>
            <?php if(!empty($invoice['client_phone'])): ?>
                <div class="muted">Telp/WA: <?= esc($invoice['client_phone']) ?></div>
            <?php endif; ?>
        </td>
        <td class="meta-box">
            <?php if(!empty($profile['bank_name']) && !empty($profile['bank_number'])): ?>
                <div class="muted" style="margin-bottom: 4px; font-size: 10px; text-transform: uppercase;">Metode Pembayaran:</div>
                <div class="bold" style="font-size: 11px; color: #1E293B;"><?= esc($profile['bank_name']) ?></div>
                <div style="font-size: 11px; color: #1E293B;"><?= esc($profile['bank_number']) ?></div>
                <div style="font-size: 11px; color: #1E293B;">A.n <?= esc($profile['bank_account_name'] ?? '') ?></div>
            <?php endif; ?>
        </td>
    </tr>
</table>

<!-- 3. TABEL PRODUK -->
<div class="bold" style="font-size: 13px; margin-bottom: 8px; color: #1E293B;">Daftar Produk</div>
<table class="product-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th class="col-no">No</th>
            <th class="col-name text-left">Nama Produk</th>
            <th class="col-qty">Qty</th>
            <th class="col-price">Harga Satuan</th>
            <th class="col-subtotal">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($items as $item): ?>
        <tr>
            <td class="col-no"><?= $no++ ?></td>
            <td class="col-name">
                <div class="product-name"><?= esc($item['item_name']) ?></div>
                <?php if(!empty($item['description'])): ?>
                    <div class="product-desc"><?= nl2br(esc($item['description'])) ?></div>
                <?php endif; ?>
            </td>
            <td class="col-qty"><?= number_format($item['quantity'], 0, ',', '.') ?></td>
            <td class="col-price">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
            <td class="col-subtotal bold">Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- 4 & 5. RINGKASAN BIAYA & QR PEMBAYARAN -->
<div class="bottom-container">
    
    <div class="qr-box">
        <?php if($plan['plan_name'] === 'pro' && !empty($qr_path) && file_exists($qr_path)): ?>
            <div class="bold" style="margin-bottom: 4px; color: #1E293B;">QRIS Pembayaran</div>
            <?php $qrImgSrc = (isset($isPdf) && $isPdf) ? $qr_path : (isset($qr_url) ? $qr_url : ''); ?>
            <img src="<?= $qrImgSrc ?>" class="qr-img" alt="QRIS">
            <div class="muted" style="font-size: 10px; margin-top: 4px;">Scan dari aplikasi e-wallet / mobile banking.</div>
        <?php elseif(empty($profile['bank_name'])): ?>
            <div class="muted" style="font-style: italic; margin-top: 10px;">Silakan hubungi kami untuk metode pembayaran.</div>
        <?php endif; ?>
    </div>

    <table class="summary-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="muted">Subtotal</td>
            <td class="text-right">Rp <?= number_format($invoice['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php if(isset($invoice['discount']) && $invoice['discount'] > 0): ?>
        <tr>
            <td style="color: #dc2626;">Diskon</td>
            <td class="text-right" style="color: #dc2626;">- Rp <?= number_format($invoice['discount'], 0, ',', '.') ?></td>
        </tr>
        <?php endif; ?>
        <?php if(isset($invoice['tax']) && $invoice['tax'] > 0): ?>
        <tr>
            <td class="muted">Pajak / PPN</td>
            <td class="text-right">Rp <?= number_format($invoice['tax'], 0, ',', '.') ?></td>
        </tr>
        <?php endif; ?>
        <tr class="summary-total-row">
            <td>TOTAL TAGIHAN</td>
            <td class="text-right primary">Rp <?= number_format($invoice['total_amount'], 0, ',', '.') ?></td>
        </tr>
    </table>
    
    <div class="clearfix"></div>
</div>

<!-- 6. CATATAN & FOOTER -->
<?php if(!empty($invoice['notes'])): ?>
<div class="notes-box">
    <div class="notes-title">Catatan:</div>
    <div><?= nl2br(esc($invoice['notes'])) ?></div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
