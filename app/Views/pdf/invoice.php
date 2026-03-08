<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice <?= esc($invoice['invoice_number'] ?? 'DRAFT') ?></title>
    <!-- Force Inter font if viewed in web, fallback to Arial for DOMPDF -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* DOMPDF SAFE STYLESHEET */
        @page {
            margin: 20px 25px; /* Margin A4 Aman */
        }
        body {
            font-family: 'Inter', Arial, sans-serif;
            font-size: 11px;
            color: #1E293B; /* Text */
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #FFFFFF;
        }

        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .m-0 { margin: 0; }
        .mb-5 { margin-bottom: 5px; }
        .mb-10 { margin-bottom: 10px; }
        .mb-20 { margin-bottom: 20px; }
        .mb-30 { margin-bottom: 30px; }

        /* Color Palette */
        .color-primary { color: #2563EB; }
        .color-success { color: #16A34A; }
        .color-warning { color: #D97706; }
        .color-danger { color: #DC2626; }
        .color-muted { color: #64748B; }

        /* 1. Header */
        .header-table { margin-bottom: 30px; }
        .logo-img { max-height: 60px; margin-bottom: 10px; }
        .invoice-title { font-size: 22px; font-weight: bold; color: #1E293B; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 5px; }
        .invoice-number-label { font-size: 10px; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; }
        .invoice-number { font-size: 14px; font-weight: bold; color: #1E293B; margin-bottom: 8px; }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .bg-primary { background-color: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; }
        .bg-success { background-color: #F0FDF4; color: #16A34A; border: 1px solid #BBF7D0; }
        .bg-warning { background-color: #FFFBEB; color: #D97706; border: 1px solid #FEF3C7; }
        .bg-danger { background-color: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }

        /* 2. Identity (From / To) */
        .identity-table { margin-bottom: 20px; }
        .section-title { font-size: 10px; font-weight: bold; color: #64748B; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; border-bottom: 1px solid #E2E8F0; padding-bottom: 4px; }
        .entity-name { font-size: 14px; font-weight: bold; color: #1E293B; margin-bottom: 3px; }
        .entity-info { font-size: 11px; color: #64748B; line-height: 1.5; }

        /* 3. Transaction Info */
        .info-table { margin-bottom: 30px; width: 100%; border-top: 1px solid #E2E8F0; border-bottom: 1px solid #E2E8F0; }
        .info-table td { padding: 8px 0; }
        .info-label { font-size: 10px; color: #64748B; text-transform: uppercase; font-weight: bold; }
        .info-value { font-size: 12px; font-weight: bold; color: #1E293B; }

        /* 4. Items Table */
        .items-table { margin-bottom: 30px; }
        .items-table th {
            background-color: #F8FAFC;
            color: #64748B;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 10px;
            border-bottom: 1px solid #E2E8F0;
            border-top: 1px solid #E2E8F0;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #F1F5F9;
            color: #1E293B;
            font-size: 11px;
        }
        .item-name { font-size: 12px; font-weight: bold; color: #1E293B; display: block; margin-bottom: 3px; }
        .item-desc { font-size: 10px; color: #64748B; }

        /* 5. Bottom Section (Totals & Payment) */
        .totals-table td { padding: 6px 0; font-size: 12px; }
        .total-label { color: #64748B; text-align: right; padding-right: 20px; font-weight: bold; }
        .total-number { font-weight: bold; text-align: right; width: 120px; color: #1E293B; }
        
        .grand-total-row td {
            padding-top: 12px;
            padding-bottom: 12px;
            border-top: 2px solid #1E293B;
        }
        .grand-total-label { font-size: 14px; font-weight: bold; color: #1E293B; text-transform: uppercase; }
        .grand-total-number { font-size: 18px; font-weight: bold; color: #2563EB; text-align: right; }

        /* 6. Payment & Notes */
        .payment-box {
            border: 1px solid #E2E8F0;
            background-color: #F8FAFC;
            padding: 15px;
            border-radius: 4px;
        }
        .bank-name { font-weight: bold; font-size: 12px; color: #1E293B; margin-bottom: 3px; }
        .bank-account { font-family: 'Courier New', Courier, monospace; font-size: 14px; font-weight: bold; color: #2563EB; margin-bottom: 3px; }
        .bank-owner { font-size: 11px; color: #64748B; }
        
        .qr-box { text-align: center; }
        .qr-img { max-width: 120px; max-height: 120px; padding: 5px; border: 1px solid #E2E8F0; background: #FFF; }
        .qr-caption { font-size: 9px; font-weight: bold; color: #64748B; margin-top: 5px; text-transform: uppercase; }

        /* 7. Footer */
        .footer-line {
            border-top: 1px solid #E2E8F0;
            margin-top: 40px;
            padding-top: 15px;
            text-align: center;
        }
        .footer-text {
            font-size: 10px;
            color: #64748B;
            font-style: italic;
        }
        
        .notes-title { font-size: 10px; font-weight: bold; color: #64748B; text-transform: uppercase; margin-bottom: 5px; }
        .notes-content { font-size: 11px; color: #64748B; line-height: 1.4; }
    </style>
</head>
<body>

    <?php 
        // Logic Header & Status Data Preparation
        $status = strtolower($invoice['status'] ?? 'draft');
        $isOverdue = (isset($isOverdue) && $isOverdue);
        
        if ($status === 'paid') {
            $badgeClass = 'bg-success';
            $badgeText = 'LUNAS';
        } elseif ($isOverdue) {
            $badgeClass = 'bg-danger';
            $badgeText = 'TELAT BAYAR';
        } elseif ($status === 'canceled') {
            $badgeClass = 'bg-danger';
            $badgeText = 'DIBATALKAN';
        } elseif ($status === 'sent') {
            $badgeClass = 'bg-primary';
            $badgeText = 'TERKIRIM';
        } else {
            $badgeClass = 'bg-warning';
            $badgeText = 'DRAF';
        }
    ?>

    <!-- 1. HEADER -->
    <table class="header-table">
        <tr>
            <td width="50%">
                <?php if(!empty($profile['logo_path'])): ?>
                    <img src="<?= esc($profile['logo_path']) ?>" class="logo-img" alt="Logo">
                <?php else: ?>
                    <div style="height: 40px;"></div> <!-- Spacer -->
                <?php endif; ?>
            </td>
            <td width="50%" class="text-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number-label">Nomor Invoice</div>
                <div class="invoice-number">#<?= esc($invoice['invoice_number'] ?? 'INV-000') ?></div>
                <div class="status-badge <?= $badgeClass ?>"><?= esc($badgeText) ?></div>
            </td>
        </tr>
    </table>

    <!-- 2. INFO USAHA & PELANGGAN -->
    <table class="identity-table">
        <tr>
            <td width="48%">
                <div class="section-title">Dari</div>
                <div class="entity-name"><?= esc($profile['business_name'] ?? 'Nama Usaha') ?></div>
                <div class="entity-info">
                    <?= nl2br(esc($profile['business_address'] ?? '')) ?><br>
                    <?php if(!empty($profile['business_phone'])): ?>
                        WA/Telp: <?= esc($profile['business_phone']) ?>
                    <?php endif; ?>
                </div>
            </td>
            <td width="4%"></td> <!-- Spacer -->
            <td width="48%">
                <div class="section-title">Faktur Kepada</div>
                <div class="entity-name"><?= esc($invoice['client_name'] ?? 'Nama Pelanggan') ?></div>
                <?php if(!empty($invoice['client_company'])): ?>
                    <div class="entity-name" style="font-size: 12px; margin-top:2px;"><?= esc($invoice['client_company']) ?></div>
                <?php endif; ?>
                <div class="entity-info">
                    <?= nl2br(esc($invoice['client_address'] ?? '')) ?><br>
                    <?php if(!empty($invoice['client_phone'])): ?>
                        WA/Telp: <?= esc($invoice['client_phone']) ?>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- 3. INFO INVOICE (TRANSAKSI) -->
    <table class="info-table">
        <tr>
            <td width="33%">
                <div class="info-label">Tanggal Terbit</div>
                <div class="info-value"><?= date('d F Y', strtotime($invoice['date_issued'] ?? 'now')) ?></div>
            </td>
            <td width="33%" class="text-center">
                <?php if(!empty($invoice['due_date'])): ?>
                    <div class="info-label">Jatuh Tempo</div>
                    <div class="info-value <?= $isOverdue ? 'color-danger' : '' ?>">
                        <?= date('d F Y', strtotime($invoice['due_date'])) ?>
                    </div>
                <?php endif; ?>
            </td>
            <td width="33%" class="text-right">
                <div class="info-label">Metode Pembayaran</div>
                <div class="info-value">Transfer Bank</div>
            </td>
        </tr>
    </table>

    <!-- 4. TABEL ITEM -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="45%" class="text-left">Nama Item</th>
                <th width="15%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Harga</th>
                <th width="20%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $items = $items ?? []; 
            foreach($items as $item): 
            ?>
            <tr>
                <td>
                    <span class="item-name"><?= esc($item['item_name']) ?></span>
                    <?php if(!empty($item['description'])): ?>
                        <span class="item-desc"><?= esc($item['description']) ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-center"><?= number_format($item['quantity'], 0, ',', '.') ?></td>
                <td class="text-right">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                <td class="text-right text-bold">Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 5. TOTAL SUMMARY & 6. QR PEMBAYARAN -->
    <table>
        <tr>
            <!-- Left: Payment Info & QR -->
            <td width="55%" style="padding-right: 30px;">
                <?php if($status !== 'paid'): ?>
                    
                    <div class="section-title">Info Pembayaran</div>
                    
                    <table>
                        <tr>
                            <td width="<?= (!empty($profile['qr_code_path']) && file_exists(FCPATH . ltrim($profile['qr_code_path'], '/'))) ? '60%' : '100%' ?>">
                                <div class="payment-box">
                                    <?php if(!empty($profile['bank_name']) && !empty($profile['bank_number'])): ?>
                                        <div class="bank-name"><?= strtoupper(esc($profile['bank_name'])) ?></div>
                                        <div class="bank-account"><?= esc($profile['bank_number']) ?></div>
                                        <div class="bank-owner">a.n. <?= esc($profile['bank_account_name'] ?? '') ?></div>
                                    <?php else: ?>
                                        <div class="color-muted" style="font-style: italic; font-size: 11px;">
                                            Silakan hubungi kami untuk metode pembayaran.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            
                            <!-- 6. QR PEMBAYARAN (OPSIONAL) -->
                            <?php 
                            // Resolving absolute path for DOMPDF
                            $qrAbsolute = '';
                            $qrUrl = '';
                            if(!empty($profile['qr_code_path'])) {
                                $cleanPath = ltrim($profile['qr_code_path'], '/');
                                if(file_exists(FCPATH . $cleanPath)) {
                                    $qrAbsolute = FCPATH . $cleanPath;
                                    $qrUrl = base_url($cleanPath);
                                }
                            }
                            ?>
                            
                            <?php if(!empty($qrAbsolute)): ?>
                            <td width="40%" class="text-right">
                                <div class="qr-box">
                                    <img src="<?= (isset($isPdf) && $isPdf) ? esc($qrAbsolute) : esc($qrUrl) ?>" class="qr-img" alt="QRIS">
                                    <div class="qr-caption">Scan Bayar</div>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                    </table>

                <?php else: ?>
                    <div style="padding: 15px; border: 2px solid #16A34A; border-radius: 4px; display: inline-block; text-align: center;">
                        <div class="color-success text-bold" style="font-size: 16px; letter-spacing: 2px;">PAID / LUNAS</div>
                        <div class="color-muted" style="font-size: 10px; margin-top: 4px;">Terima kasih atas pembayaran Anda.</div>
                    </div>
                <?php endif; ?>
            </td>
            
            <!-- Right: 5. TOTAL SUMMARY -->
            <td width="45%">
                <table class="totals-table">
                    <tr>
                        <td class="total-label">Subtotal</td>
                        <td class="total-number">Rp <?= number_format($invoice['subtotal'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    
                    <?php if(($invoice['discount'] ?? 0) > 0): ?>
                    <tr>
                        <td class="total-label color-danger">Diskon</td>
                        <td class="total-number color-danger">-Rp <?= number_format($invoice['discount'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php if(($invoice['tax'] ?? 0) > 0): ?>
                    <tr>
                        <td class="total-label">Pajak (PPN)</td>
                        <td class="total-number">Rp <?= number_format($invoice['tax'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>

                    <!-- GRAND TOTAL -->
                    <tr class="grand-total-row">
                        <td class="total-label grand-total-label">Total Bayar</td>
                        <td class="total-number grand-total-number">
                            Rp <?= number_format($invoice['total_amount'] ?? 0, 0, ',', '.') ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- 7. CATATAN -->
    <?php if(!empty($invoice['notes'])): ?>
    <div style="margin-top: 30px;">
        <div class="notes-title">Catatan</div>
        <div class="notes-content">
            <?= nl2br(esc($invoice['notes'])) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- 8. FOOTER HALUS -->
    <div class="footer-line">
        <div class="footer-text">Invoice ini dibuat secara otomatis dan sah tanpa tanda tangan fisik.</div>
    </div>

</body>
</html>
