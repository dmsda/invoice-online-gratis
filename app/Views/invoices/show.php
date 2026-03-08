<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
    // Build WA link once, reuse everywhere
    $waLink = null;
    if(!empty($invoice['client_phone']) && $invoice['status'] !== 'draft') {
        $waMessage = "Halo " . $invoice['client_name'] . ",\n" .
                     "Berikut kami kirimkan invoice *" . $invoice['invoice_number'] . "*\n" .
                     "Total: " . format_rupiah($invoice['total_amount']) . "\n\n" .
                     "Silakan cek melalui link berikut:\n" .
                     base_url('v/' . $invoice['uuid']) . "\n\n" .
                     "Terima kasih.";
        $waLink = generate_wa_link($invoice['client_phone'], $waMessage);
    }

    $isOverdue = $isOverdue ?? false; // Ensure variable exists
    
    // Status Config (Updated with Overdue Logic)
    $statusConfig = [
        'draft'    => ['class' => 'bg-gradient-draft', 'bg' => '#fffbeb', 'border' => '#fbbf24', 'text' => '#92400e', 'icon' => 'bi-pencil-square', 'label' => 'Draf'],
        'sent'     => ['class' => 'bg-gradient-sent', 'bg' => '#eff6ff', 'border' => '#60a5fa', 'text' => '#1e40af', 'icon' => 'bi-send-check-fill', 'label' => 'Terkirim'],
        'paid'     => ['class' => 'bg-gradient-paid', 'bg' => '#ecfdf5', 'border' => '#34d399', 'text' => '#065f46', 'icon' => 'bi-check-circle-fill', 'label' => 'Lunas'],
        'canceled' => ['class' => 'bg-gradient-canceled', 'bg' => '#fef2f2', 'border' => '#f87171', 'text' => '#991b1b', 'icon' => 'bi-x-circle-fill', 'label' => 'Dibatalkan'],
    ];
    
    // Override visual if Overdue
    if ($isOverdue) {
        $sc = [
            'class' => 'bg-gradient-overdue', // We need to define this CSS or use inline style
            'bg' => '#fff7ed', 
            'border' => '#f97316', 
            'text' => '#9a3412', 
            'icon' => 'bi-exclamation-circle-fill', 
            'label' => 'Telat Bayar'
        ];
    } else {
        $sc = $statusConfig[$invoice['status']] ?? $statusConfig['draft'];
    }
?>

<style>
.bg-gradient-overdue {
    background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 100%);
    border: 1px solid #fdba74;
}
</style>

<!-- Back Navigation -->
<div class="mb-3">
    <a href="/invoices" class="text-decoration-none text-muted d-inline-flex align-items-center gap-1 small back-link">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Invoice
    </a>
</div>

<!-- Hero Status Banner -->
<div class="invoice-status-hero rounded-4 p-4 mb-4 position-relative overflow-hidden <?= $isOverdue ? 'bg-gradient-overdue' : $sc['class'] ?>">
    <div class="position-relative d-flex align-items-center gap-3 <?= $isOverdue ? 'text-dark' : 'text-white' ?>">
        <div class="d-flex align-items-center justify-content-center rounded-circle <?= $isOverdue ? 'bg-warning bg-opacity-25' : 'bg-white bg-opacity-25' ?> status-icon-circle">
            <i class="bi <?= $sc['icon'] ?> <?= $isOverdue ? 'text-warning-emphasis' : 'text-white' ?> status-icon-fs"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-bold fs-5 <?= $isOverdue ? 'text-warning-emphasis' : 'text-white' ?>"><?= $sc['label'] ?></div>
            <div class="<?= $isOverdue ? 'text-muted' : 'text-white text-opacity-75' ?> small"><?= esc($invoice['invoice_number']) ?></div>
            
            <?php if ($isOverdue): ?>
                <div class="mt-1 badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                    Jatuh Tempo: <?= date('d M Y', strtotime($invoice['due_date'])) ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
    <!-- Decorative circles -->
    <div class="position-absolute top-0 end-0 bg-white bg-opacity-10 rounded-circle decorative-circle-1"></div>
    <div class="position-absolute bottom-0 start-0 bg-white bg-opacity-10 rounded-circle decorative-circle-2"></div>
</div>

<!-- UX Flow: Overdue Reminder Templates -->
<?php if ($isOverdue): ?>
<div class="card border-warning mb-4 shadow-sm">
    <div class="card-header bg-warning bg-opacity-10 border-warning border-opacity-25 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-alarm-fill text-warning-emphasis"></i>
            <span class="fw-semibold text-warning-emphasis">Pengingat Keterlambatan</span>
        </div>
        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#reminderTemplates">
            Tampilkan Template
        </button>
    </div>
    <div id="reminderTemplates" class="collapse show">
        <div class="card-body">
            <div class="row g-3">
                <!-- Level 1: Sopan -->
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-success bg-opacity-10 text-success">Level 1: Sopan (H+1)</span>
                            <button class="btn btn-xs btn-link text-decoration-none p-0" onclick="copyToClipboard('wa-template-1')"><i class="bi bi-clipboard"></i> Salin</button>
                        </div>
                        <div class="small text-muted fst-italic mb-3" id="wa-template-1">Halo <?= esc($invoice['client_name']) ?>,<br>kami ingin mengingatkan bahwa invoice *<?= esc($invoice['invoice_number']) ?>* telah melewati tanggal jatuh tempo.<br><br>Jika pembayaran sudah dilakukan, mohon abaikan pesan ini. Jika belum, silakan melakukan pembayaran saat berkenan.<br><br>Terima kasih atas perhatiannya 🙏</div>
                        <a href="<?= generate_wa_link($invoice['client_phone'], "Halo " . $invoice['client_name'] . ",\nkami ingin mengingatkan bahwa invoice *" . $invoice['invoice_number'] . "* telah melewati tanggal jatuh tempo.\n\nJika pembayaran sudah dilakukan, mohon abaikan pesan ini. Jika belum, silakan melakukan pembayaran saat berkenan.\n\nTerima kasih atas perhatiannya 🙏") ?>" target="_blank" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-whatsapp"></i> Kirim Pengingat
                        </a>
                    </div>
                </div>
                <!-- Level 2: Tegas -->
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100 border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis">Level 2: Tegas (H+7)</span>
                            <button class="btn btn-xs btn-link text-decoration-none p-0" onclick="copyToClipboard('wa-template-2')"><i class="bi bi-clipboard"></i> Salin</button>
                        </div>
                        <div class="small text-muted fst-italic mb-3" id="wa-template-2">Halo <?= esc($invoice['client_name']) ?>,<br>kami ingin menindaklanjuti invoice *<?= esc($invoice['invoice_number']) ?>* yang telah melewati tanggal jatuh tempo.<br><br>Kami siap membantu jika ada kendala terkait pembayaran.<br>Terima kasih atas kerja samanya 🙏</div>
                        <a href="<?= generate_wa_link($invoice['client_phone'], "Halo " . $invoice['client_name'] . ",\nkami ingin menindaklanjuti invoice *" . $invoice['invoice_number'] . "* yang telah melewati tanggal jatuh tempo.\n\nKami siap membantu jika ada kendala terkait pembayaran.\nTerima kasih atas kerja samanya 🙏") ?>" target="_blank" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-whatsapp"></i> Kirim Follow-up
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(elementId) {
    var text = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(text).then(function() {
        alert('Template berhasil disalin!');
    }, function(err) {
        console.error('Gagal menyalin: ', err);
    });
}
</script>
<?php endif; ?>

<!-- NOTIFIKASI REMINDER NONAKTIF -->
<?php if(isset($invoice['reminders_enabled']) && $invoice['reminders_enabled'] == 0): ?>
<div class="alert alert-secondary border-secondary border-opacity-25 d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-bell-slash-fill fs-4 text-secondary me-3"></i>
    <div>
        <strong>Invoice Lunas &mdash; Reminder Otomatis Dimatikan</strong><br>
        <span class="small">Sistem berhenti mengirimkan pengingat apapun ke klien untuk invoice ini karena sudah ditandai lunas atau dimatikan manual.</span>
    </div>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Invoice Content -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4" style="background-color: #FFFFFF;">
            <!-- Top Color Accent -->
            <div class="<?= $sc['class'] ?>" style="height: 6px;"></div>

            <!-- Auto Split Navigation Header -->
            <?php if (!empty($invoice['split_group_id'])): ?>
                <div class="bg-light border-bottom px-4 py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <span class="badge bg-warning text-dark border border-warning px-2 py-1 mb-1">
                            <i class="bi bi-diagram-3-fill me-1"></i> Auto Split
                        </span>
                        <div class="fw-bold text-dark mb-0">Part <?= $invoice['split_part'] ?> dari <?= $invoice['split_total'] ?></div>
                        <div class="small text-muted">Invoice ini dibagi karena panjang item berlebih.</div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <?php if(isset($prevSplit) && $prevSplit): ?>
                            <a href="/invoices/show/<?= $prevSplit['uuid'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-chevron-left"></i> Part <?= $prevSplit['split_part'] ?>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-outline-primary rounded-pill" disabled><i class="bi bi-chevron-left"></i></button>
                        <?php endif; ?>

                        <?php if(isset($nextSplit) && $nextSplit): ?>
                            <a href="/invoices/show/<?= $nextSplit['uuid'] ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                Part <?= $nextSplit['split_part'] ?> <i class="bi bi-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-sm btn-outline-primary rounded-pill" disabled><i class="bi bi-chevron-right"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="card-body p-4 p-md-5" style="color: #1E293B; font-family: 'Inter', sans-serif;">
                
                <!-- 1. HEADER -->
                <div class="row mb-4 pb-4 border-bottom" style="border-color: #E2E8F0 !important;">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <?php if(!empty($profile['logo_path'])): ?>
                            <img src="/<?= esc($profile['logo_path']) ?>" alt="Logo" class="mb-3" style="max-height: 60px;">
                        <?php endif; ?>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <h2 class="fw-bold mb-1" style="color: #1E293B; letter-spacing: 1px;">INVOICE</h2>
                        <div class="text-uppercase fw-bold mb-2" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px;">NOMOR INVOICE</div>
                        <div class="fs-5 fw-bold mb-3" style="color: #1E293B;">#<?= esc($invoice['invoice_number']) ?></div>
                        
                        <?php 
                            // Render Status Badge specifically for Web View
                            if ($invoice['status'] === 'paid') {
                                $wbClass = 'bg-success text-success bg-opacity-10 border border-success border-opacity-25';
                                $wbText = 'LUNAS';
                            } elseif ($isOverdue) {
                                $wbClass = 'bg-danger text-danger bg-opacity-10 border border-danger border-opacity-25';
                                $wbText = 'TELAT BAYAR';
                            } elseif ($invoice['status'] === 'canceled') {
                                $wbClass = 'bg-danger text-danger bg-opacity-10 border border-danger border-opacity-25';
                                $wbText = 'DIBATALKAN';
                            } elseif ($invoice['status'] === 'sent') {
                                $wbClass = 'bg-primary text-primary bg-opacity-10 border border-primary border-opacity-25';
                                $wbText = 'TERKIRIM';
                            } else {
                                $wbClass = 'bg-warning text-warning-emphasis bg-opacity-10 border border-warning border-opacity-50';
                                $wbText = 'DRAF';
                            }
                        ?>
                        <span class="badge <?= $wbClass ?> px-3 py-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?= $wbText ?></span>
                    </div>
                </div>

                <!-- 2. IDENTITY (FROM/TO) -->
                <div class="row mb-5">
                    <div class="col-sm-6 mb-4 mb-sm-0">
                        <div class="text-uppercase fw-bold mb-2 pb-1 border-bottom d-inline-block" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px; border-color: #E2E8F0 !important;">DARI</div>
                        <div class="fw-bold fs-6 mb-1" style="color: #1E293B;"><?= esc($profile['business_name'] ?? 'Nama Bisnis') ?></div>
                        <div style="font-size: 0.875rem; color: #64748B; line-height: 1.5;">
                            <?= nl2br(esc($profile['business_address'] ?? '')) ?><br>
                            <?php if(!empty($profile['business_phone'])): ?>
                                WA/Telp: <?= esc($profile['business_phone']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-uppercase fw-bold mb-2 pb-1 border-bottom d-inline-block" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px; border-color: #E2E8F0 !important;">FAKTUR KEPADA</div>
                        <div class="fw-bold fs-6 mb-1" style="color: #1E293B;"><?= esc($invoice['client_name']) ?></div>
                        <?php if(!empty($invoice['client_company'])): ?>
                            <div class="fw-semibold mb-1" style="font-size: 0.875rem; color: #1E293B;"><?= esc($invoice['client_company']) ?></div>
                        <?php endif; ?>
                        <div style="font-size: 0.875rem; color: #64748B; line-height: 1.5;">
                            <?= nl2br(esc($invoice['client_address'])) ?><br>
                            <?php if(!empty($invoice['client_phone'])): ?>
                                WA/Telp: <?= esc($invoice['client_phone']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 3. TRANSACTION DATES -->
                <div class="row mb-5 py-3 border-top border-bottom" style="border-color: #E2E8F0 !important; background-color: #F8FAFC; border-radius: 8px;">
                    <div class="col-6">
                        <div class="text-uppercase fw-bold mb-1" style="font-size: 0.7rem; color: #64748B; letter-spacing: 0.5px;">TANGGAL TERBIT</div>
                        <div class="fw-bold" style="color: #1E293B;"><?= date('d F Y', strtotime($invoice['date_issued'])) ?></div>
                    </div>
                    <div class="col-6 text-end">
                        <?php if($invoice['due_date']): ?>
                            <div class="text-uppercase fw-bold mb-1" style="font-size: 0.7rem; color: #64748B; letter-spacing: 0.5px;">JATUH TEMPO</div>
                            <div class="fw-bold <?= $isOverdue ? 'text-danger' : '' ?>" style="color: #1E293B;"><?= date('d F Y', strtotime($invoice['due_date'])) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 4. ITEMS TABLE -->
                <div class="table-responsive mb-5">
                    <table class="table <?= ($invoice['type'] === 'jasa') ? 'table-borderless' : 'table-bordered text-center' ?> align-middle mb-0">
                        <thead style="background-color: #F8FAFC;">
                            <tr>
                                <th class="py-3 text-uppercase fw-bold <?= ($invoice['type'] === 'jasa') ? 'text-start' : 'text-center' ?>" style="font-size: 0.75rem; color: #64748B;">
                                    <?= ($invoice['type'] === 'jasa') ? 'Deskripsi Layanan' : 'Nama Item' ?>
                                </th>
                                <th class="py-3 text-uppercase fw-bold text-center" style="font-size: 0.75rem; color: #64748B; width: 100px;">
                                    <?= ($invoice['type'] === 'jasa') ? 'Kuantitas' : 'Qty' ?>
                                </th>
                                <th class="py-3 text-uppercase fw-bold text-end" style="font-size: 0.75rem; color: #64748B; width: 150px;">
                                    <?= ($invoice['type'] === 'jasa') ? 'Biaya' : 'Harga Satuan' ?>
                                </th>
                                <?php if ($invoice['type'] === 'produk'): ?>
                                <th class="py-3 text-uppercase fw-bold text-end" style="font-size: 0.75rem; color: #64748B; width: 180px;">Subtotal</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody style="border-bottom: 1px solid #E2E8F0;">
                            <?php foreach($items as $item): ?>
                            <tr style="<?= ($invoice['type'] === 'jasa') ? 'border-bottom: 1px solid #F1F5F9;' : '' ?>">
                                <td class="py-3 <?= ($invoice['type'] === 'jasa') ? 'text-start' : 'text-start' ?>">
                                    <div class="fw-bold" style="color: #1E293B; font-size: 0.9rem;"><?= esc($item['item_name']) ?></div>
                                    <?php if(!empty($item['description'])): ?>
                                        <div class="mt-1" style="color: #64748B; font-size: 0.8rem;"><?= esc($item['description']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 text-center" style="color: #1E293B;"><?= number_format($item['quantity'], 0, ',', '.') ?></td>
                                <?php if ($invoice['type'] === 'produk'): ?>
                                    <td class="py-3 text-end text-nowrap" style="color: #1E293B;">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                    <td class="py-3 text-end fw-bold text-nowrap" style="color: #1E293B;">Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
                                <?php else: ?>
                                    <td class="py-3 text-end fw-bold text-nowrap" style="color: #1E293B;">Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 5. TOTALS & PAYMENT INFO -->
                <div class="row">
                    <!-- Left: Payment Info -->
                    <div class="col-lg-6 mb-4 mb-lg-0 pe-lg-5">
                        <?php if($invoice['status'] !== 'paid'): ?>
                            <div class="text-uppercase fw-bold mb-3 d-inline-block pb-1 border-bottom" style="font-size: 0.75rem; color: #64748B; letter-spacing: 0.5px; border-color: #E2E8F0 !important;">INFO PEMBAYARAN</div>
                            
                            <div class="d-flex flex-wrap gap-4 align-items-start">
                                <div class="flex-grow-1 p-3 rounded-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <?php if(!empty($profile['bank_name']) && !empty($profile['bank_number'])): ?>
                                        <div class="fw-bold mb-1" style="color: #1E293B; font-size: 0.9rem;"><?= strtoupper(esc($profile['bank_name'])) ?></div>
                                        <div class="fw-bold fs-5 mb-1" style="color: #2563EB; font-family: 'Courier New', Courier, monospace; letter-spacing: 1px;"><?= esc($profile['bank_number']) ?></div>
                                        <div style="font-size: 0.8rem; color: #64748B;">a.n. <?= esc($profile['bank_account_name']) ?></div>
                                    <?php else: ?>
                                        <div class="text-danger small fst-italic">
                                            <i class="bi bi-exclamation-circle me-1"></i>Belum ada info bank dietalase.
                                            <a href="/settings/profile" class="text-decoration-none fw-bold">Atur sekarang</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
<?php
if (!isset($plan)) {
    helper('subscription');
    $plan = current_plan($invoice['user_id']); // get owner's plan
}
?>
                                <?php if(!empty($profile['qr_code_path']) && file_exists(FCPATH . ltrim($profile['qr_code_path'], '/'))): ?>
                                    <div class="text-center position-relative">
                                        <div class="p-2 border rounded-3 bg-white shadow-sm d-inline-block position-relative overflow-hidden" style="border-color: #E2E8F0 !important;">
                                            <img src="<?= base_url(esc($profile['qr_code_path'])) ?>" alt="QRIS" style="width: 100px; height: 100px; object-fit: contain; <?= $plan['plan_name'] === 'free' ? 'filter: blur(4px) grayscale(100%); opacity: 0.5;' : '' ?>">
                                            <?php if($plan['plan_name'] === 'free'): ?>
                                                <div class="position-absolute w-100 h-100 top-0 start-0 d-flex flex-column justify-content-center align-items-center" style="background: rgba(255,255,255,0.7);">
                                                    <i class="bi bi-lock-fill text-warning fs-4 mb-0"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($plan['plan_name'] === 'free'): ?>
                                            <a href="/pricing" class="d-block mt-2 badge bg-warning text-dark text-decoration-none py-1">Upgrade QRIS</a>
                                        <?php else: ?>
                                            <div class="fw-bold mt-2 text-uppercase" style="font-size: 0.65rem; color: #64748B; letter-spacing: 1px;">Scan Bayar</div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                             <div class="p-4 rounded-3 text-center d-inline-block w-100" style="background-color: #F0FDF4; border: 2px solid #16A34A;">
                                <div class="fw-bold" style="color: #16A34A; font-size: 1.25rem; letter-spacing: 2px;">PAID / LUNAS</div>
                                <div class="small mt-1" style="color: #16A34A; opacity: 0.8;">Terima kasih atas pembayaran Anda.</div>
                             </div>
                        <?php endif; ?>
                    </div>

                    <!-- Right: Totals Summary -->
                    <div class="col-lg-6">
                        <div class="ms-lg-auto" style="max-width: 400px; background-color: #F8FAFC; border-radius: 8px; border: 1px solid #E2E8F0; padding: 20px;">
                            <table class="table table-borderless table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td style="color: #64748B; padding-bottom: 12px;">Subtotal</td>
                                        <td class="text-end fw-semibold" style="color: #1E293B; padding-bottom: 12px;">Rp <?= number_format($invoice['subtotal'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php if($invoice['discount'] > 0): ?>
                                    <tr>
                                        <td style="color: #DC2626; padding-bottom: 12px;">Diskon</td>
                                        <td class="text-end fw-semibold" style="color: #DC2626; padding-bottom: 12px;">- Rp <?= number_format($invoice['discount'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <?php if($invoice['tax'] > 0): ?>
                                    <tr>
                                        <td style="color: #64748B; padding-bottom: 12px;">Pajak (PPN)</td>
                                        <td class="text-end fw-semibold" style="color: #1E293B; padding-bottom: 12px;">Rp <?= number_format($invoice['tax'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    
                                    <!-- GRAND TOTAL -->
                                    <tr style="border-top: 2px solid #1E293B;">
                                        <td class="pt-3 pb-0 text-uppercase fw-bold align-middle" style="color: #1E293B; font-size: 0.875rem; letter-spacing: 0.5px;">Total Tagihan</td>
                                        <td class="pt-3 pb-0 text-end fw-black" style="color: #2563EB; font-size: 1.5rem; letter-spacing: -0.5px;">
                                            Rp <?= number_format($invoice['total_amount'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 6. NOTES & FOOTER -->
                <?php if($invoice['notes']): ?>
                <div class="mt-5 pt-4 border-top" style="border-color: #E2E8F0 !important;">
                    <div class="fw-bold mb-2" style="font-size: 0.875rem; color: #64748B;">Catatan:</div>
                    <div style="font-size: 0.875rem; color: #64748B; line-height: 1.6;">
                        <?= nl2br(esc($invoice['notes'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Mobile Actions (Delete) - Kept intact from original -->
                <div class="d-md-none mt-5">
                    <form action="/invoices/delete/<?= $invoice['uuid'] ?>" method="post" onsubmit="return confirm('Yakin hapus invoice <?= esc($invoice['invoice_number']) ?>?');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill">
                            <i class="bi bi-trash3 me-1"></i> Hapus Invoice
                        </button>
                    </form>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Sidebar — Desktop Only -->
    <div class="col-lg-4 d-none d-lg-block">
        <!-- Quick Actions Card -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-3">
            <div class="card-header bg-white border-0 pt-3 pb-0 px-4">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-lightning-charge-fill me-1 text-warning"></i>Aksi Cepat</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <!-- Status Change in Sidebar -->
                <div class="mb-3">
                    <label class="form-label small text-muted fw-semibold mb-1">
                        <i class="bi bi-arrow-repeat me-1"></i>Ubah Status
                    </label>
                    <form action="/invoices/status/<?= $invoice['uuid'] ?>" method="post">
                        <?= csrf_field() ?>
                        <select name="status" class="form-select form-select-sm rounded-3" onchange="if(confirm('Ubah status invoice menjadi ' + this.options[this.selectedIndex].text.trim() + '?')) this.form.submit(); else this.value='<?= $invoice['status'] ?>';" <?= $invoice['status'] == 'paid' ? 'disabled' : '' ?>>
                            <option value="draft" <?= $invoice['status'] == 'draft' ? 'selected' : '' ?>>📝 Draf</option>
                            <option value="sent" <?= $invoice['status'] == 'sent' ? 'selected' : '' ?>>📤 Terkirim</option>
                            <option value="paid" <?= $invoice['status'] == 'paid' ? 'selected' : '' ?>>✅ Lunas</option>
                            <option value="canceled" <?= $invoice['status'] == 'canceled' ? 'selected' : '' ?>>❌ Dibatalkan</option>
                        </select>
                    </form>
                </div>
                <hr class="my-2">
                <?php if($invoice['status'] == 'draft'): ?>
                    <a href="/invoices/edit/<?= $invoice['uuid'] ?>" class="btn btn-outline-primary w-100 mb-2 rounded-3 fw-semibold">
                        <i class="bi bi-pencil me-1"></i> Edit Invoice
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($invoice['split_group_id']) && $invoice['split_total'] > 1): ?>
                    <!-- ZIP DOWNLOAD BUTTON UNTUK AUTO SPLIT -->
                    <a href="/invoices/zip/<?= $invoice['uuid'] ?>" class="btn btn-primary w-100 mb-2 rounded-3 text-start zip-dl-btn" onclick="let b=this; setTimeout(()=>{b.classList.add('disabled'); b.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>⏳ Menyiapkan ZIP...'; setTimeout(()=>{b.classList.remove('disabled'); b.innerHTML='📦 Download Semua Invoice (ZIP)';}, 8000);}, 50);">
                        📦 Download Semua Invoice (ZIP)
                        <div style="font-size: 0.65rem; color: #e2e8f0; font-weight: normal; margin-top: 2px;">Berisi semua invoice (1/<?= $invoice['split_total'] ?>, 2/<?= $invoice['split_total'] ?>...) dalam satu file.</div>
                    </a>
                    
                    <!-- WA ZIP BUTTON -->
                    <?php if(isset($waZipLink) && $waZipLink): ?>
                        <a href="<?= $waZipLink ?>" class="btn btn-outline-primary w-100 mb-2 rounded-3 text-start zip-dl-btn" target="_blank">
                            <i class="bi bi-whatsapp me-1"></i> Kirim Semua via WhatsApp
                            <div style="font-size: 0.65rem; color: #d1fae5; font-weight: normal; margin-top: 2px;">Pesan otomatis berisi link ZIP invoice.</div>
                        </a>
                    <?php else: ?>
                        <span class="d-inline-block w-100 mb-2" tabindex="0" data-bs-toggle="tooltip" title="ZIP invoice belum tersedia atau No HP klien kosong">
                            <button class="btn btn-outline-primary w-100 rounded-3 text-start zip-dl-btn disabled" type="button" disabled style="opacity: 0.6;">
                                <i class="bi bi-whatsapp me-1"></i> Kirim Semua via WhatsApp
                                <div style="font-size: 0.65rem; color: #d1fae5; font-weight: normal; margin-top: 2px;">No HP Klien Kosong</div>
                            </button>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <a href="/invoices/pdf/<?= $invoice['uuid'] ?>" class="btn btn-outline-primary w-100 mb-2 rounded-3" target="_blank">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Unduh PDF
                </a>
                <a href="/v/<?= $invoice['uuid'] ?>" class="btn btn-outline-primary w-100 mb-2 rounded-3" target="_blank">
                    <i class="bi bi-link-45deg me-1"></i> Lihat Public Link
                </a>
                
                <!-- WA REMINDER BUTTON (H-3 & OVERDUE) -->
                <?php if(isset($waReminderLink) && $waReminderLink): ?>
                    <?php 
                        $btnClass = $waReminderType === 'overdue' ? 'btn-outline-danger' : 'btn-outline-primary';
                        $iconClass = $waReminderType === 'overdue' ? 'bi-exclamation-triangle-fill' : 'bi-bell-fill';
                        $btnText = $waReminderType === 'overdue' ? 'Kirim Pengingat Telat Bayar' : 'Kirim Pengingat H-3';
                    ?>
                    <hr class="my-2">
                    <span class="d-inline-block w-100 mb-2" tabindex="0" data-bs-toggle="tooltip" title="Pengingat dikirim otomatis dengan bahasa sopan">
                        <a href="<?= $waReminderLink ?>" class="btn <?= $btnClass ?> w-100 rounded-3 text-start fw-semibold" target="_blank">
                            <i class="bi <?= $iconClass ?> me-1"></i> <?= $btnText ?>
                            <div style="font-size: 0.65rem; color: inherit; font-weight: normal; margin-top: 2px;">Via WhatsApp</div>
                        </a>
                    </span>
                    <hr class="my-2">
                <?php elseif($invoice['status'] !== 'paid' && isset($invoice['reminders_enabled']) && $invoice['reminders_enabled'] == 0): ?>
                    <hr class="my-2">
                    <span class="d-inline-block w-100 mb-2" tabindex="0" data-bs-toggle="tooltip" title="Reminder otomatis dimatikan.">
                        <button class="btn btn-light w-100 rounded-3 text-start fw-semibold disabled" type="button" disabled style="opacity: 0.6;">
                            <i class="bi bi-bell-slash-fill me-1"></i> Pengingat Dimatikan
                            <div style="font-size: 0.65rem; color: #f8f9fa; font-weight: normal; margin-top: 2px;">Invoice telah lunas/dimatikan manual</div>
                        </button>
                    </span>
                    <hr class="my-2">
                <?php endif; ?>

                <!-- WA THANK YOU BUTTON (PAID) -->
                <?php if(isset($waThankYouLink) && $waThankYouLink): ?>
                    <hr class="my-2">
                    <span class="d-inline-block w-100 mb-2" tabindex="0" data-bs-toggle="tooltip" title="Kirim ucapan terima kasih karena invoice telah lunas">
                        <a href="<?= $waThankYouLink ?>" class="btn btn-primary w-100 rounded-3 text-start fw-semibold" target="_blank">
                            <i class="bi bi-heart-fill me-1 text-danger"></i> Kirim WA Terima Kasih
                            <div style="font-size: 0.65rem; color: #d1fae5; font-weight: normal; margin-top: 2px;">Terima Kasih Pembayaran via WA</div>
                        </a>
                    </span>
                    <hr class="my-2">
                <?php endif; ?>
                
                <?php if($waLink && $invoice['status'] !== 'paid'): ?>
                    <a href="<?= $waLink ?>" class="btn btn-primary w-100 rounded-3 fw-semibold mb-2" target="_blank">
                        <i class="bi bi-whatsapp me-1"></i> Kirim via WhatsApp
                    </a>
                <?php elseif($invoice['status'] !== 'draft' && empty($invoice['client_phone'])): ?>
                    <button class="btn btn-light border w-100 rounded-3 mb-2" disabled>
                        <i class="bi bi-whatsapp me-1 text-muted"></i> No HP belum diisi
                    </button>
                <?php elseif($invoice['status'] == 'draft'): ?>
                    <button class="btn btn-light border w-100 rounded-3 mb-2" disabled>
                        <i class="bi bi-whatsapp me-1 text-muted"></i> Tersedia setelah dikirim
                    </button>
                <?php endif; ?>

                <hr class="my-2">
                
                <!-- TOGGLE REMINDER MANUAL -->
                <?php 
                    $isRemEnabled = !isset($invoice['reminders_enabled']) || $invoice['reminders_enabled'] == 1;
                    $toggleUrl = base_url('invoices/toggle-reminder/' . $invoice['uuid']);
                ?>
                <a href="<?= $toggleUrl ?>" class="btn btn-sm w-100 rounded-3 mb-2 <?= $isRemEnabled ? 'btn-outline-primary' : 'btn-outline-primary' ?>" onclick="return confirm('<?= $isRemEnabled ? "Matikan reminder otomatis untuk invoice ini?" : "Aktifkan kembali reminder otomatis?" ?>')">
                    <i class="bi <?= $isRemEnabled ? 'bi-bell-slash' : 'bi-bell' ?> me-1"></i>
                    <?= $isRemEnabled ? 'Matikan Reminder' : 'Aktifkan Reminder' ?>
                </a>

                <form action="/invoices/delete/<?= $invoice['uuid'] ?>" method="post" onsubmit="return confirm('Yakin hapus invoice <?= esc($invoice['invoice_number']) ?>?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 rounded-3">
                        <i class="bi bi-trash3 me-1"></i> Hapus Invoice
                    </button>
                </form>
        </div>

        <!-- Invoice Info Summary -->
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-body px-4 py-3">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle-fill me-1 text-primary"></i>Ringkasan</h6>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Status</span>
                    <span class="badge rounded-pill fw-semibold <?= $sc['class'] ?>"><?= $sc['label'] ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Tanggal</span>
                    <span class="fw-medium small"><?= date('d M Y', strtotime($invoice['date_issued'])) ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Jatuh Tempo</span>
                    <span class="fw-medium small"><?= $invoice['due_date'] ? date('d M Y', strtotime($invoice['due_date'])) : '—' ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Pelanggan</span>
                    <span class="fw-medium small"><?= esc($invoice['client_name']) ?></span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted small">Total</span>
                    <span class="fw-bold" style="color: <?= $sc['text'] ?>;">Rp <?= number_format($invoice['total_amount'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Sticky Bottom Bar -->
<div class="sticky-bottom-bar d-lg-none">
    <div class="d-flex gap-2 align-items-center">
        <!-- Status selector -->
        <form action="/invoices/status/<?= $invoice['uuid'] ?>" method="post" class="flex-shrink-0">
            <?= csrf_field() ?>
            <select name="status" class="form-select form-select-sm rounded-3" onchange="if(confirm('Ubah status invoice menjadi ' + this.options[this.selectedIndex].text.trim() + '?')) this.form.submit(); else this.value='<?= $invoice['status'] ?>';" style="width: auto;" <?= $invoice['status'] == 'paid' ? 'disabled' : '' ?>>
                <option value="draft" <?= $invoice['status'] == 'draft' ? 'selected' : '' ?>>Draf</option>
                <option value="sent" <?= $invoice['status'] == 'sent' ? 'selected' : '' ?>>Terkirim</option>
                <option value="paid" <?= $invoice['status'] == 'paid' ? 'selected' : '' ?>>Lunas</option>
                <option value="canceled" <?= $invoice['status'] == 'canceled' ? 'selected' : '' ?>>Batal</option>
            </select>
        </form>

        <a href="/invoices/pdf/<?= $invoice['uuid'] ?>" class="btn btn-sm btn-primary flex-fill rounded-3 fw-semibold" target="_blank">
            <i class="bi bi-file-pdf me-1"></i>PDF
        </a>
        
        <?php if (!empty($invoice['split_group_id']) && $invoice['split_total'] > 1): ?>
             <a href="/invoices/zip/<?= $invoice['uuid'] ?>" class="btn btn-sm btn-outline-primary flex-fill rounded-3 fw-semibold" onclick="let b=this; setTimeout(()=>{b.classList.add('disabled'); b.innerHTML='<span class=\'spinner-border spinner-border-sm\'></span>'; setTimeout(()=>{b.classList.remove('disabled'); b.innerHTML='<i class=\'bi bi-file-zip me-1\'></i>ZIP';}, 8000);}, 50);">
                 <i class="bi bi-file-zip me-1"></i>ZIP
             </a>
             <?php if(isset($waZipLink) && $waZipLink): ?>
                 <a href="<?= $waZipLink ?>" class="btn btn-sm btn-outline-primary flex-fill rounded-3 fw-semibold" target="_blank">
                     <i class="bi bi-whatsapp me-1"></i>WA ZIP
                 </a>
             <?php else: ?>
                  <button class="btn btn-sm btn-outline-primary flex-fill rounded-3 fw-semibold disabled" disabled title="No HP Klien Kosong" style="opacity:0.6;">
                     <i class="bi bi-whatsapp me-1"></i>WA ZIP
                 </button>
             <?php endif; ?>
        <?php endif; ?>

        <?php if($waLink): ?>
            <?php if(isset($waReminderLink) && $waReminderLink): ?>
                 <?php $btnClass = $waReminderType === 'overdue' ? 'btn-outline-danger' : 'btn-outline-primary'; ?>
                 <a href="<?= $waReminderLink ?>" class="btn btn-sm <?= $btnClass ?> flex-fill rounded-3 fw-semibold" target="_blank" title="Kirim Pengingat (Sopan)">
                     <i class="bi bi-bell-fill me-1"></i>WA Remind
                 </a>
            <?php elseif(isset($waThankYouLink) && $waThankYouLink): ?>
                 <a href="<?= $waThankYouLink ?>" class="btn btn-sm btn-primary flex-fill rounded-3 fw-semibold" target="_blank" title="Kirim Terima Kasih">
                     <i class="bi bi-heart-fill me-1 text-danger"></i>WA Thanks
                 </a>
            <?php elseif($invoice['status'] !== 'paid'): ?>
                <a href="<?= $waLink ?>" class="btn btn-sm btn-primary flex-fill rounded-3 fw-semibold" target="_blank">
                    <i class="bi bi-whatsapp me-1"></i>WA
                </a>
            <?php endif; ?>
        <?php elseif($invoice['status'] == 'draft'): ?>
            <a href="/invoices/edit/<?= $invoice['uuid'] ?>" class="btn btn-sm btn-outline-primary flex-fill rounded-3 fw-semibold">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
