<?php
helper('subscription');
$plan = current_plan($invoice['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?= esc($invoice['invoice_number']) ?></title>
    <style>
        /* Base Styling - Aman untuk Dompdf */
        body { font-family: 'Arial', Helvetica, sans-serif; font-size: 11pt; color: #222; margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        /* Header System */
        .header { width: 100%; border-bottom: 2px solid #eaeaea; padding-bottom: 15px; margin-bottom: 25px; }
        .header-table td { vertical-align: middle; }
        .logo-container { width: 50%; }
        .logo-img { max-width: 160px; max-height: 80px; }
        .invoice-meta { width: 50%; text-align: right; color: #555; }
        .invoice-title { font-size: 24pt; color: #333; font-weight: bold; margin: 0 0 5px 0; letter-spacing: 2px; }
        
        /* Status Badge */
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 10pt; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; }
        .status-paid { background-color: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .status-unpaid { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .status-draft { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

        /* Footer System */
        .footer { width: 100%; position: absolute; bottom: 0; border-top: 1px solid #eaeaea; padding-top: 10px; font-size: 9pt; text-align: center; color: #888; }
        
        /* Content Wrapper */
        .content { margin-bottom: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-container">
                    <?php if($plan['plan_name'] === 'pro' && !empty($profile['logo_path']) && file_exists(FCPATH . ltrim($profile['logo_path'], '/'))): ?>
                        <?php $logoSrc = (isset($isPdf) && $isPdf) ? FCPATH . ltrim($profile['logo_path'], '/') : base_url($profile['logo_path']); ?>
                        <img src="<?= $logoSrc ?>" class="logo-img">
                    <?php else: ?>
                        <h2 style="margin:0; color:#333; font-size: 18pt;"><?= esc($profile['business_name'] ?? 'Nama Bisnis') ?></h2>
                    <?php endif; ?>
                    
                    <div style="font-size: 9pt; color: #666; margin-top: 5px; line-height: 1.4;">
                        <?= nl2br(esc($profile['business_address'] ?? '')) ?><br>
                        <?= esc($profile['business_phone'] ?? '') ?>
                    </div>
                </td>
                <td class="invoice-meta">
                    <h1 class="invoice-title">INVOICE</h1>
                    
                    <?php 
                        $statusStr = strtolower($invoice['status']);
                        $isOverdue = $isOverdue ?? false;
                        
                        if ($statusStr === 'paid') {
                            $statusLabel = 'LUNAS';
                            $statusClass = 'status-paid';
                        } elseif ($isOverdue) {
                            $statusLabel = 'TELAT BAYAR';
                            $statusClass = 'status-unpaid';
                        } elseif ($statusStr === 'draft' || $statusStr === 'sent') {
                            $statusLabel = 'BELUM LUNAS';
                            $statusClass = 'status-unpaid';
                        } else {
                            $statusLabel = strtoupper($statusStr);
                            $statusClass = 'status-draft';
                        }
                    ?>
                    <div class="status-badge <?= $statusClass ?>">
                        <?= $statusLabel ?>
                    </div><br>

                    <strong>#<?= esc($invoice['invoice_number']) ?></strong><br>
                    Tanggal Terbit: <?= date('d M Y', strtotime($invoice['date_issued'])) ?><br>
                    <?php if ($invoice['due_date']): ?>
                    <span <?= $isOverdue ? 'style="color: #dc2626; font-weight: bold;"' : '' ?>>
                        Jatuh Tempo: <?= date('d M Y', strtotime($invoice['due_date'])) ?>
                    </span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- INJECTION POINT UNTUK TEMPLATE JASA / PRODUK -->
    <div class="content">
        <?= $this->renderSection('content') ?>
    </div>

    <div class="footer">
        <?php if(!empty($invoice['split_group_id']) && $invoice['split_total'] > 1): ?>
            <div style="color: #666; font-weight: bold; margin-bottom: 5px;">
                Invoice ini adalah bagian ke-<?= esc($invoice['split_part']) ?> dari <?= esc($invoice['split_total']) ?>
            </div>
        <?php endif; ?>
        <?php if(isset($items) && count($items) > 120): ?>
            <div style="color: #666; font-style: italic; margin-bottom: 5px;">
                * Invoice ini berisi sangat banyak item. Disarankan untuk disimpan secara digital.
            </div>
        <?php endif; ?>
        <?php if($plan['plan_name'] === 'free'): ?>
            <div style="color: #666; font-size: 8pt; margin-bottom: 5px;">Dibuat dengan Invoice Online Gratis App - Bikin invoice praktis dari HP</div>
        <?php endif; ?>
        Dokumen ini diterbitkan secara otomatis oleh sistem.<br>
        <strong><?= esc($profile['business_name'] ?? 'Terima Kasih') ?></strong>
    </div>

    <?php if(isset($view_mode) && $view_mode !== 'pdf'): ?>
        <div class="no-print" style="position: fixed; bottom: 30px; right: 30px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; font-family: Arial, sans-serif;">
            <?php if(!empty($invoice['split_group_id']) && $invoice['split_total'] > 1): ?>
                <a href="/v/zip/<?= $invoice['uuid'] ?>" id="zipBtn" style="padding: 12px 18px; background: #0f172a; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); text-decoration: none; text-align: center; font-size: 10pt;" onclick="let b=this; setTimeout(()=>{b.style.opacity='0.7'; b.innerHTML='⏳ Menyiapkan ZIP...'; setTimeout(()=>{b.style.opacity='1'; b.innerHTML='📦 Download Semua Part (ZIP)';}, 8000);}, 50);">
                    📦 Download Semua Part (ZIP)
                </a>
            <?php endif; ?>
            <button onclick="window.print()" style="padding: 12px 18px; background: #2563eb; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); font-size: 10pt;">
                🖨️ Cetak / Simpan PDF Ini
            </button>
        </div>
        <style>
            @media print {
                .no-print { display: none !important; }
            }
        </style>
        <script>
            // Auto-trigger print when opened in browser as print view
            window.onload = function() { window.print(); }
        </script>
    <?php endif; ?>
</body>
</html>
