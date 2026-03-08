<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Usaha - Invoice Online</title>
    <style>
        /* DOMPDF Safe Stylesheet */
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        table { border-collapse: collapse; width: 100%; border-spacing: 0; }
        td, th { vertical-align: top; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .text-muted { color: #666666; }
        
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 15px; }
        .mb-3 { margin-bottom: 25px; }
        .mt-2 { margin-top: 15px; }

        /* 1. HEADER */
        .doc-header { 
            border-bottom: 1px solid #dddddd; 
            padding-bottom: 15px; 
            margin-bottom: 25px; 
        }
        .company-name { 
            font-size: 18px; 
            font-weight: bold; 
            color: #000000; 
            margin-bottom: 3px;
        }
        .header-title { 
            font-size: 16px; 
            font-weight: bold; 
            color: #555555; 
            text-transform: uppercase; 
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .header-period { 
            font-size: 11px; 
            color: #777777; 
        }
        
        /* 2. KPI RINGKAS (GRID 2x2) */
        .kpi-container {
            margin-bottom: 25px;
        }
        .kpi-box { 
            border: 1px solid #dddddd; 
            padding: 15px; 
            background-color: #fcfcfc;
        }
        .kpi-label { 
            font-size: 10px; 
            text-transform: uppercase; 
            color: #666666; 
            font-weight: bold; 
            margin-bottom: 8px;
        }
        .kpi-value { 
            font-size: 22px; /* Angka besar */
            font-weight: bold; 
            color: #000000; 
        }
        
        /* Specific accent colors (Subtle) */
        .kpi-box.box-success { border-left: 4px solid #10b981; }
        .kpi-box.box-warning { border-left: 4px solid #f59e0b; }
        .kpi-box.box-info { border-left: 4px solid #3b82f6; }
        .kpi-box.box-danger { border-left: 4px solid #ef4444; }

        /* 3. RINGKASAN AKTIVITAS */
        .summary-list {
            margin-bottom: 25px;
            padding-left: 20px;
        }
        .summary-list li {
            margin-bottom: 6px;
            font-size: 11px;
        }

        /* 4. DATA INTI (TABEL SINGKAT) */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #000000;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .data-table { 
            width: 100%; 
        }
        .data-table th {
            background-color: #f5f5f5;
            color: #555555;
            font-size: 10px;
            text-transform: uppercase;
            padding: 10px 8px;
            border-bottom: 2px solid #dddddd;
            border-top: 1px solid #dddddd;
        }
        .data-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #eeeeee;
            font-size: 11px;
        }
        
        /* Status Badges Text */
        .status-lunas { color: #10b981; font-weight: bold; }
        .status-belum { color: #f59e0b; font-weight: bold; }
        .status-telat { color: #ef4444; font-weight: bold; }

        /* 5. & 6. FOOTER & CATATAN */
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            border-top: 1px solid #dddddd;
            padding-top: 10px;
            font-size: 9px;
            color: #888888;
        }
        .footer table { width: 100%; }
        
        /* Page Numbering trick for DOMPDF */
        .page-number:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    <!-- 1. HEADER -->
    <table class="doc-header">
        <tr>
            <td width="55%">
                <div class="company-name"><?= htmlspecialchars($user['business_name'] ?? 'Nama Usaha') ?></div>
                <div class="text-muted"><?= htmlspecialchars($user['business_address'] ?? 'Alamat belum diatur') ?></div>
            </td>
            <td width="45%" class="text-right">
                <div class="header-title">LAPORAN USAHA</div>
                <div class="header-period">
                    Periode: <?= date('d M Y', strtotime($meta['start'])) ?> - <?= date('d M Y', strtotime($meta['end'])) ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- 2. KPI RINGKAS (GRID 2x2) -->
    <table class="kpi-container">
        <!-- Row 1 -->
        <tr>
            <td width="48%" style="padding-bottom: 15px;">
                <div class="kpi-box box-success">
                    <div class="kpi-label">Total Diterima (Lunas)</div>
                    <div class="kpi-value">Rp <?= number_format($summary['total_paid'] ?? 0, 0, ',', '.') ?></div>
                </div>
            </td>
            <td width="4%"></td> <!-- Spacer -->
            <td width="48%" style="padding-bottom: 15px;">
                <div class="kpi-box box-warning">
                    <div class="kpi-label">Belum Dibayar (Piutang)</div>
                    <div class="kpi-value">Rp <?= number_format($summary['total_unpaid'] ?? 0, 0, ',', '.') ?></div>
                </div>
            </td>
        </tr>
        <!-- Row 2 -->
        <tr>
            <td width="48%">
                <div class="kpi-box box-info" style="padding: 10px 15px;">
                    <table width="100%">
                        <tr>
                            <td><div class="kpi-label" style="margin: 0;">Total Invoice</div></td>
                            <td class="text-right"><div class="kpi-value" style="font-size: 16px;"><?= $summary['total_invoice'] ?? 0 ?> Lembar</div></td>
                        </tr>
                    </table>
                </div>
            </td>
            <td width="4%"></td> <!-- Spacer -->
            <td width="48%">
                <div class="kpi-box box-danger" style="padding: 10px 15px;">
                    <table width="100%">
                        <tr>
                            <td><div class="kpi-label" style="text-transform:none; margin: 0; color: #ef4444;">Lewat Jatuh Tempo</div></td>
                            <td class="text-right"><div class="kpi-value" style="font-size: 16px; color: #ef4444;">Rp <?= number_format($summary['total_overdue'] ?? 0, 0, ',', '.') ?></div></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- 3. RINGKASAN AKTIVITAS -->
    <div class="section-title">Ringkasan Aktivitas</div>
    <ul class="summary-list">
        <li>Total nilai tagihan yang diterbitkan pada periode ini: <strong>Rp <?= number_format(($summary['total_paid'] ?? 0) + ($summary['total_unpaid'] ?? 0), 0, ',', '.') ?></strong></li>
        <li>Terdapat <strong><?= $summary['total_invoice'] ?? 0 ?> invoice</strong> yang dikelola dalam periode laporan ini.</li>
        <?php if (($summary['total_overdue'] ?? 0) > 0): ?>
        <li style="color: #ef4444;">Perhatian: Ada tagihan lewat jatuh tempo senilai Rp <?= number_format($summary['total_overdue'] ?? 0, 0, ',', '.') ?> yang perlu segera ditagih.</li>
        <?php else: ?>
        <li>Tidak ada tagihan yang melewati batas jatuh tempo.</li>
        <?php endif; ?>
    </ul>

    <?php
        $typeLabel = 'Semua Transaksi';
        if ($meta['type'] === 'sales') $typeLabel = 'Uang Masuk';
        elseif ($meta['type'] === 'receivables') $typeLabel = 'Belum & Telat Bayar';
        elseif ($meta['type'] === 'clients') $typeLabel = 'Performa Pelanggan';
    ?>
    <div class="section-title mb-1">Rincian Transaksi (<?= $typeLabel ?>)</div>
    
    <?php if(empty($rows)): ?>
        <div style="text-align: center; padding: 30px; color: #777; border: 1px dashed #cccccc; background-color: #fafafa;">
            Tidak ada transaksi pada periode ini.
        </div>
    <?php else: ?>

        <?php if($meta['type'] === 'sales' || $meta['type'] === 'receivables' || $meta['type'] === 'all'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="15%" class="text-left">Tanggal</th>
                        <th width="20%" class="text-left">No. Invoice</th>
                        <th width="35%" class="text-left">Pelanggan</th>
                        <th width="15%" class="text-left">Status</th>
                        <th width="15%" class="text-right">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($rows as $row): ?>
                        <?php 
                            $isOverdue = (!empty($row['due_date']) && strtotime($row['due_date']) < strtotime(date('Y-m-d'))); 
                            $statusClass = '';
                            $statusText = '';
                            
                            if ($row['status'] === 'paid') {
                                $statusClass = 'status-lunas';
                                $statusText = 'Lunas';
                            } elseif ($isOverdue) {
                                $statusClass = 'status-telat';
                                $statusText = 'Telat Bayar';
                            } else {
                                $statusClass = 'status-belum';
                                $statusText = 'Belum Dibayar';
                            }
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['date_issued'])) ?></td>
                            <td class="text-bold"><?= htmlspecialchars($row['invoice_number']) ?></td>
                            <td><?= htmlspecialchars($row['client_name'] ?? 'Tanpa Nama') ?></td>
                            <td class="<?= $statusClass ?>"><?= $statusText ?></td>
                            <td class="text-right text-bold">
                                <?= number_format($row['total_amount'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif($meta['type'] === 'clients'): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="35%" class="text-left">Pelanggan</th>
                        <th width="15%" class="text-center">Jml Transaksi</th>
                        <th width="15%" class="text-right">Dibayar (Rp)</th>
                        <th width="15%" class="text-right">Hutang (Rp)</th>
                        <th width="20%" class="text-right">Total Nilai (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($rows as $row): ?>
                    <tr>
                        <td>
                            <div class="text-bold"><?= htmlspecialchars($row['client_name'] ?? 'Tanpa Nama') ?></div>
                        </td>
                        <td class="text-center text-bold"><?= $row['total_transactions'] ?></td>
                        <td class="text-right text-success"><?= number_format($row['total_paid'], 0, ',', '.') ?></td>
                        <td class="text-right text-danger"><?= number_format($row['total_unpaid'], 0, ',', '.') ?></td>
                        <td class="text-right text-bold"><?= number_format($row['total_value'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php endif; ?>

    <!-- 5. CATATAN & 6. FOOTER -->
    <div class="mt-2 text-muted" style="font-size: 10px; font-style: italic;">
        *Catatan: Data ini diambil secara otomatis dari catatan invoice yang terdaftar di sistem.
    </div>

    <div class="footer">
        <table>
            <tr>
                <td width="33%" class="text-left">Dicetak: <?= date('d/m/Y H:i') ?></td>
                <td width="33%" class="text-center">Invoice Online Gratis</td>
                <td width="34%" class="text-right">Halaman <span class="page-number"></span></td>
            </tr>
        </table>
    </div>

</body>
</html>
