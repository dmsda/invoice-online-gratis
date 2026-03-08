<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= esc($invoice['invoice_number']) ?></title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b; /* Slate 800 */
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            background: #f1f5f9;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 8px;
        }
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        .logo img {
            max-height: 60px;
            margin-bottom: 15px;
        }
        .business-info h1 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 5px 0;
        }
        .business-info p {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }
        .invoice-title-block {
            text-align: right;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 2px;
            margin: 0;
            line-height: 1;
        }
        .invoice-number {
            font-size: 16px;
            color: #64748b;
            font-weight: 500;
            margin-top: 5px;
        }
        
        /* Grid Layout for Details */
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .detail-group h4 {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin: 0 0 8px 0;
            font-weight: 600;
        }
        .client-info strong {
            font-size: 16px;
            color: #0f172a;
            display: block;
            margin-bottom: 4px;
        }
        .client-info p {
            font-size: 14px;
            color: #334155;
            margin: 0;
        }
        .meta-values {
            text-align: right;
        }
        .meta-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 4px;
            font-size: 14px;
        }
        .meta-label {
            color: #64748b;
            margin-right: 15px;
        }
        .meta-value {
            font-weight: 600;
            color: #0f172a;
            min-width: 100px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }
        .items-table td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 14px;
        }
        .items-table td strong {
            display: block;
            color: #0f172a;
        }
        .items-table td small {
            color: #64748b;
            font-size: 13px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Summary Section */
        .summary-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 20px;
        }
        .payment-box {
            width: 50%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 20px;
        }
        .payment-box h4 {
            margin: 0 0 10px 0;
            font-size: 13px;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }
        .bank-details {
            font-size: 14px;
            color: #334155;
        }
        .bank-name {
            font-weight: 700;
            color: #0f172a;
        }
        .bank-number {
            font-family: monospace;
            font-size: 16px;
            letter-spacing: 1px;
            background: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            margin: 4px 0;
            display: inline-block;
        }
        
        /* Totals */
        .totals-box {
            width: 40%;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
            color: #64748b;
        }
        .total-row strong {
            color: #0f172a;
        }
        .grand-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #0f172a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .grand-total-label {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .grand-total-amount {
            font-size: 26px; /* Increased Size */
            font-weight: 800;
            color: #0f172a;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }
        .status-unpaid {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .status-paid {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        
        .notes {
            margin-top: 30px;
            font-size: 13px;
            color: #64748b;
            font-style: italic;
        }

        /* Print Override */
        @media print {
            body { background: none; padding: 0; }
            .container { box-shadow: none; border: none; padding: 0; max-width: 100%; }
            .no-print { display: none; }
            .items-table th { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
            .status-badge { -webkit-print-color-adjust: exact; }
            .payment-box { background-color: #f8fafc !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #0f172a; color: #fff; border: none; border-radius: 6px; font-weight: 600;">Cetak / Simpan PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; background: #e2e8f0; color: #475569; border: none; border-radius: 6px; margin-left: 10px; font-weight: 600;">Tutup</button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="business-info">
                <?php if(!empty($profile['logo_path'])): ?>
                    <div class="logo">
                        <img src="/<?= esc($profile['logo_path']) ?>" alt="Logo">
                    </div>
                <?php endif; ?>
                <h1><?= esc($profile['business_name'] ?? 'Nama Bisnis') ?></h1>
                <p>
                    <?= nl2br(esc($profile['business_address'])) ?><br>
                    <?= esc($profile['business_phone']) ?>
                </p>
            </div>
            <div class="invoice-title-block">
                <h2 class="invoice-title">INVOICE</h2>
                <div class="invoice-number">No. #<?= esc($invoice['invoice_number']) ?></div>
                
                <!-- Status Badge -->
                <?php 
                    $status = strtolower($invoice['status']);
                    $statusLabel = $status === 'paid' ? 'LUNAS' : 'BELUM LUNAS';
                    $statusClass = $status === 'paid' ? 'status-paid' : 'status-unpaid';
                ?>
                <div class="status-badge <?= $statusClass ?>">
                    <?= $statusLabel ?>
                </div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="invoice-details">
            <div class="client-info">
                <div class="detail-group">
                    <h4>DITAGIHKAN KEPADA:</h4>
                    <strong><?= esc($invoice['client_name']) ?></strong>
                    <p>
                        <?= nl2br(esc($invoice['client_address'])) ?><br>
                        <?= esc($invoice['client_phone']) ?>
                    </p>
                </div>
            </div>
            <div class="meta-values">
                <div class="meta-row">
                    <span class="meta-label">Tanggal Invoice:</span>
                    <span class="meta-value"><?= date('d/m/Y', strtotime($invoice['date_issued'])) ?></span>
                </div>
                <!-- Highlight Due Date if Unpaid -->
                <?php if($status !== 'paid' && $invoice['due_date']): ?>
                <div class="meta-row" style="color: #dc2626;">
                    <span class="meta-label" style="color: #dc2626;">Jatuh Tempo:</span>
                    <span class="meta-value"><?= date('d/m/Y', strtotime($invoice['due_date'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table" style="<?= ($invoice['type'] === 'produk') ? 'border: 1px solid #e2e8f0;' : '' ?>">
            <thead style="<?= ($invoice['type'] === 'produk') ? 'background: #0f172a; color: white;' : '' ?>">
                <tr>
                    <th style="<?= ($invoice['type'] === 'produk') ? 'color: white;' : '' ?>">
                        <?= ($invoice['type'] === 'jasa') ? 'Deskripsi Layanan' : 'Nama Item' ?>
                    </th>
                    <th class="text-center" width="80" style="<?= ($invoice['type'] === 'produk') ? 'color: white;' : '' ?>">
                        <?= ($invoice['type'] === 'jasa') ? 'Kuantitas' : 'Qty' ?>
                    </th>
                    <th class="text-right" width="150" style="<?= ($invoice['type'] === 'produk') ? 'color: white;' : '' ?>">
                        <?= ($invoice['type'] === 'jasa') ? 'Biaya' : 'Harga Satuan' ?>
                    </th>
                    <?php if ($invoice['type'] === 'produk'): ?>
                    <th class="text-right" width="150" style="<?= ($invoice['type'] === 'produk') ? 'color: white;' : '' ?>">Jumlah</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td style="<?= ($invoice['type'] === 'produk') ? 'border-right: 1px solid #e2e8f0;' : '' ?>">
                        <strong><?= esc($item['item_name']) ?></strong>
                        <?php if(!empty($item['description'])): ?>
                            <small><?= esc($item['description']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center" style="<?= ($invoice['type'] === 'produk') ? 'border-right: 1px solid #e2e8f0;' : '' ?>">
                        <?= number_format($item['quantity'], 0, ',', '.') ?>
                    </td>
                    <?php if ($invoice['type'] === 'produk'): ?>
                        <td class="text-right" style="border-right: 1px solid #e2e8f0;">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                        <td class="text-right">Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
                    <?php else: ?>
                        <td class="text-right">Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Summary & Payment -->
        <div class="summary-section">
            <!-- Payment Box (Left/Main) -->
            <div class="payment-box">
                <?php if(!empty($profile['bank_name']) && !empty($profile['bank_number'])): ?>
                    <h4>Info Pembayaran</h4>
                    <div style="display: flex; gap: 20px;">
                        <div class="bank-details" style="flex: 1;">
                            Transfer ke Bank <span class="bank-name"><?= esc($profile['bank_name']) ?></span><br>
                            No. Rekening:<br>
                            <span class="bank-number"><?= esc($profile['bank_number']) ?></span><br>
                            a.n <?= esc($profile['bank_account_name']) ?>
                        </div>
                        <?php if(!empty($profile['qr_code_path'])): ?>
                            <div style="text-align: center;">
                                <img src="/<?= esc($profile['qr_code_path']) ?>" style="width: 80px; height: 80px; border: 1px solid #ddd; padding: 2px;">
                                <div style="font-size: 9px; margin-top: 4px; font-weight: bold; color: #64748b;">SCAN QRIS</div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <h4>Catatan</h4>
                    <div style="color: #64748b; font-size: 13px;">
                        Terima kasih telah bekerja sama dengan kami.
                    </div>
                <?php endif; ?>
                
                <?php if($invoice['notes']): ?>
                    <div class="notes">
                        "<?= esc($invoice['notes']) ?>"
                    </div>
                <?php endif; ?>
            </div>

            <!-- Totals (Right) -->
            <div class="totals-box">
                <div class="total-row">
                    <span>Subtotal</span>
                    <strong>Rp <?= number_format($invoice['subtotal'], 0, ',', '.') ?></strong>
                </div>
                <?php if($invoice['discount'] > 0): ?>
                <div class="total-row" style="color: #dc2626;">
                    <span>Diskon</span>
                    <span>- Rp <?= number_format($invoice['discount'], 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>
                <?php if($invoice['tax'] > 0): ?>
                <div class="total-row">
                    <span>Pajak</span>
                    <span>+ Rp <?= number_format($invoice['tax'], 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>
                
                <div class="grand-total">
                    <span class="grand-total-label">TOTAL TAGIHAN</span>
                    <span class="grand-total-amount">Rp <?= number_format($invoice['total_amount'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 50px; text-align: center; color: #cbd5e1; font-size: 11px;">
            Invoice ini sah dan diproses otomatis oleh Invoice Online Gratis
        </div>
    </div>

</body>
</html>
