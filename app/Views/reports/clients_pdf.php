<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0 0 10px 0;
            color: #1e293b;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }
        th {
            background-color: #2563EB;
            color: #ffffff;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .status-aktif { color: #16A34A; }
        .status-bermasalah { color: #DC2626; font-weight: bold; }
        .status-tidak-aktif { color: #94a3b8; }
        .status-jarang { color: #D97706; }

        .summary-box {
            display: inline-block;
            width: 23%;
            margin-right: 1.5%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            box-sizing: border-box;
            vertical-align: top;
        }
        .summary-box:last-child { margin-right: 0; }
        .summary-title { font-size: 10px; color: #64748b; text-transform: uppercase; margin-bottom: 5px; }
        .summary-value { font-size: 18px; font-weight: bold; color: #0f172a; margin: 0; }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #64748b;
            font-style: italic;
        }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="header">
        <h2><?= $meta['view_mode'] === 'summary' ? 'LAPORAN RINGKASAN PELANGGAN' : 'DETAIL INVOICE: ' . esc($report['client_name']) ?></h2>
        <p>Nama Usaha: <?= esc($user['business_name'] ?? 'Perusahaan Saya') ?></p>
        <p>Periode: <?= esc($meta['periodLabel']) ?></p>
    </div>

    <?php if (empty($report['data'])): ?>
        <div class="empty-state">
            Belum ada transaksi pada periode ini.
        </div>
    <?php else: ?>

        <?php if ($meta['view_mode'] === 'summary'): ?>
            <!-- Laporan Summary -->
            <table>
                <thead>
                    <tr>
                        <th>NAMA PELANGGAN</th>
                        <th class="text-center">INVOICES</th>
                        <th class="text-right">TOTAL OMZET</th>
                        <th class="text-right">TOTAL LUNAS</th>
                        <th class="text-right">BELUM LUNAS</th>
                        <th class="text-center">INVOICE TERAKHIR</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['data'] as $row): ?>
                        <tr>
                            <td><?= esc($row['client_name']) ?></td>
                            <td class="text-center"><?= $row['total_invoices'] ?></td>
                            <td class="text-right">Rp <?= number_format($row['total_revenue'], 0, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($row['total_paid'], 0, ',', '.') ?></td>
                            <td class="text-right">Rp <?= number_format($row['total_unpaid'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= $row['last_invoice_date'] ? date('d-m-Y', strtotime($row['last_invoice_date'])) : '-' ?></td>
                            <td>
                                <?php
                                    $class = 'status-tidak-aktif';
                                    if ($row['client_status'] === 'Aktif') $class = 'status-aktif';
                                    if ($row['client_status'] === 'Bermasalah') $class = 'status-bermasalah';
                                    if ($row['client_status'] === 'Jarang Transaksi') $class = 'status-jarang';
                                ?>
                                <span class="<?= $class ?>"><?= $row['client_status'] ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Footers -->
            <div style="margin-top: 30px;">
                <div class="summary-box">
                    <div class="summary-title">TOTAL PELANGGAN</div>
                    <p class="summary-value"><?= number_format($report['meta']['total_clients'], 0, ',', '.') ?></p>
                </div>
                <div class="summary-box">
                    <div class="summary-title">PELANGGAN AKTIF</div>
                    <p class="summary-value" style="color: #16A34A;"><?= number_format($report['meta']['active_clients'], 0, ',', '.') ?></p>
                </div>
                <div class="summary-box">
                    <div class="summary-title">RATA-RATA INVOICE</div>
                    <p class="summary-value"><?= number_format($report['meta']['avg_invoice_per_client'], 1, ',', '.') ?></p>
                </div>
                <div class="summary-box">
                    <div class="summary-title">OMZET PERIODE</div>
                    <p class="summary-value" style="color: #2563EB;">Rp <?= number_format($report['meta']['total_revenue'], 0, ',', '.') ?></p>
                </div>
            </div>

        <?php else: ?>
            <!-- Laporan Drill-down -->
            <table>
                <thead>
                    <tr>
                        <th>TANGGAL</th>
                        <th>NO. INVOICE</th>
                        <th class="text-center">STATUS</th>
                        <th>JATUH TEMPO</th>
                        <th class="text-right">TOTAL INVOICE</th>
                        <th class="text-center">TGL LUNAS</th>
                        <th>KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report['data'] as $row): ?>
                        <tr>
                            <td><?= date('d-m-Y', strtotime($row['date_issued'])) ?></td>
                            <td><?= esc($row['invoice_number']) ?></td>
                            <td class="text-center">
                                <?php
                                    $sMap = ['draft' => 'Draf', 'sent' => 'Terkirim', 'paid' => 'Lunas', 'canceled' => 'Batal'];
                                    echo $sMap[$row['status']] ?? $row['status'];
                                ?>
                            </td>
                            <td><?= date('d-m-Y', strtotime($row['due_date'])) ?></td>
                            <td class="text-right">Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></td>
                            <td class="text-center"><?= $row['paid_date'] ? date('d-m-Y', strtotime($row['paid_date'])) : '-' ?></td>
                            <td>
                                <?php
                                    $kClass = '';
                                    if ($row['keterangan'] === 'Tepat Waktu') $kClass = 'status-aktif';
                                    if ($row['keterangan'] === 'Terlambat Dibayar' || $row['keterangan'] === 'Overdue') $kClass = 'status-bermasalah';
                                ?>
                                <span class="<?= $kClass ?>"><?= $row['keterangan'] ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 20px; text-align: right;">
                <h3 style="margin: 0; color: #64748b; font-size: 14px;">Total Omzet Pelanggan Ini:</h3>
                <h2 style="margin: 5px 0 0 0; color: #1e293b; font-size: 20px;">Rp <?= number_format($report['meta']['total_revenue'], 0, ',', '.') ?></h2>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</body>
</html>
