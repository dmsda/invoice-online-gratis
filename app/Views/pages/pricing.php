<?= $this->extend('layout/landing') ?>

<?= $this->section('content') ?>

<!-- SECTION 1 — HEADLINE -->
<section class="py-5 text-center mt-5 position-relative">
    <!-- Dekorasi Background -->
    <div class="position-absolute top-50 start-50 translate-middle w-100 h-100" style="z-index: -1; pointer-events: none;">
        <div style="position: absolute; top: -10%; left: 20%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(13,110,253,0.1) 0%, rgba(255,255,255,0) 70%); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: 0; right: 20%; width: 250px; height: 250px; background: radial-gradient(circle, rgba(25,135,84,0.08) 0%, rgba(255,255,255,0) 70%); border-radius: 50%;"></div>
    </div>

    <div class="container mt-4">
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-warning border-0 shadow-sm rounded-4 mx-auto mb-4 text-start d-flex align-items-center" role="alert" style="max-width: 600px;">
                <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3">
                    <i class="bi bi-info-circle-fill fs-4 text-warning-emphasis"></i> 
                </div>
                <div>
                    <strong class="text-dark d-block">Pemberitahuan Sistem</strong>
                    <span class="text-muted small"><?= session()->getFlashdata('error') ?></span>
                </div>
            </div>
        <?php endif; ?>
        
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold mb-3">Investasi Terbaik untuk UMKM</span>
        <h1 class="display-4 fw-bold mb-3 text-dark" style="letter-spacing: -1px;">Satu Harga, <span class="text-primary">Banyak Manfaat</span></h1>
        <p class="lead text-muted mb-0 mx-auto" style="max-width: 600px;">
            Mulai gratis selamanya. Upgrade hanya jika usaha Anda makin ramai untuk fitur otomatisasi yang menghemat waktu Anda.
        </p>
    </div>
</section>

<div x-data="{ cycle: 'yearly', basePrice: 469000, 
    monthlyPrice: 49000, yearlyPrice: 469000, 
    normalYearly: 588000 }">

<!-- SECTION 2 — CARD PAKET -->
<section class="py-4 pb-5">
    <div class="container">
        <!-- TOGGLE BULANAN / TAHUNAN -->
        <div class="d-flex justify-content-center mb-5">
            <div class="bg-white p-2 rounded-pill d-inline-flex border shadow-sm align-items-center position-relative">
                <input type="radio" class="btn-check" name="main_cycle" id="cycleMonthly" value="monthly" x-model="cycle" 
                       @change="basePrice = monthlyPrice; document.getElementById('promoMessage').style.display='none'; document.getElementById('promoResultContainer').classList.add('d-none'); document.getElementById('appliedCouponId').value='';">
                <label class="btn rounded-pill px-4 fw-bold mb-0 border-0" :class="cycle == 'monthly' ? 'btn-dark shadow-sm text-white' : 'text-muted'" for="cycleMonthly" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 2;">Bulanan</label>

                <input type="radio" class="btn-check" name="main_cycle" id="cycleYearly" value="yearly" x-model="cycle" 
                       @change="basePrice = yearlyPrice; document.getElementById('promoMessage').style.display='none'; document.getElementById('promoResultContainer').classList.add('d-none'); document.getElementById('appliedCouponId').value='';">
                <label class="btn rounded-pill px-4 fw-bold mb-0 border-0 d-flex align-items-center" :class="cycle == 'yearly' ? 'btn-primary shadow-sm text-white' : 'text-muted'" for="cycleYearly" style="transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 2;">
                    Tahunan 
                    <span class="badge ms-2 rounded-pill px-2 py-1" :class="cycle == 'yearly' ? 'bg-white text-primary' : 'bg-success text-white'" style="font-size: 0.7rem; transform: translateY(-1px);">
                        Hemat 20%
                    </span>
                </label>
            </div>
        </div>

        <div class="row g-4 justify-content-center px-2 px-md-0 align-items-md-stretch" style="max-width: 1000px; margin: 0 auto;">

            <!-- PAKET 1 — GRATIS -->
            <div class="col-md-6 col-lg-5">
                <div class="card pricing-card h-100 rounded-4 overflow-hidden" style="border: 1px solid rgba(0,0,0,0.08); background: #ffffff; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div class="card-body p-4 p-xl-5 mt-3 d-flex flex-column">
                        <div class="mb-4">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 mb-3 fw-semibold">PEMULA</span>
                            <h2 class="fw-bold mb-2">Gratis</h2>
                            <p class="text-muted small mb-0">Usaha yang baru mulai beroperasi</p>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="display-5 fw-bold text-dark mb-0">Rp 0</h3>
                            <span class="text-muted d-block mt-1">Selamanya, tanpa kartu kredit</span>
                        </div>
                        
                        <div style="height: 1px; background: linear-gradient(90deg, transparent, rgba(0,0,0,0.1), transparent);" class="w-100 mb-4"></div>
                        
                        <ul class="list-unstyled text-start mb-auto">
                            <li class="mb-3 d-flex align-items-start"><i class="bi bi-check2 text-primary fs-5 me-3 flex-shrink-0 mt-n1"></i> <span class="text-secondary fw-medium">Buat invoice tanpa batas</span></li>
                            <li class="mb-3 d-flex align-items-start"><i class="bi bi-check2 text-primary fs-5 me-3 flex-shrink-0 mt-n1"></i> <span class="text-secondary fw-medium">Download file PDF</span></li>
                            <li class="mb-3 d-flex align-items-start"><i class="bi bi-check2 text-primary fs-5 me-3 flex-shrink-0 mt-n1"></i> <span class="text-secondary fw-medium">Kirim via WhatsApp manually</span></li>
                            <li class="mb-3 d-flex align-items-start"><i class="bi bi-check2 text-primary fs-5 me-3 flex-shrink-0 mt-n1"></i> <span class="text-secondary fw-medium">Kelola data pelanggan</span></li>
                            <li class="mb-2 d-flex align-items-start"><i class="bi bi-x text-muted fs-5 me-3 flex-shrink-0 mt-n1 opacity-50"></i> <span class="text-muted text-decoration-line-through">Laporan penjualan</span></li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 p-xl-5 pt-0 mt-auto">
                        <a href="/register" class="btn btn-outline-dark btn-lg w-100 rounded-pill fw-bold" style="border-width: 2px;">Gunakan Gratis</a>
                    </div>
                </div>
            </div>

            <!-- PAKET 2 — PRO -->
            <div class="col-md-6 col-lg-5 position-relative mt-5 mt-md-4">
                <!-- Glowing background effect behind the pro card -->
                <div class="position-absolute w-100 h-100 bg-primary opacity-25" style="filter: blur(40px); z-index: -1; transform: scale(0.9); border-radius: 2rem;"></div>
                
                <div class="card pricing-card pro-card h-100 rounded-4 position-relative border-0 shadow-lg" style="background: linear-gradient(145deg, #ffffff, #f8faff); overflow: visible; z-index: 1;">
                    <!-- Badge Paling Populer -->
                    <div class="position-absolute top-0 start-50 translate-middle" style="z-index: 2;">
                        <div class="badge bg-gradient-primary text-white rounded-pill px-4 py-2 shadow border border-white border-2" style="font-size: 0.85rem; letter-spacing: 0.5px; background: linear-gradient(45deg, #0d6efd, #0b5ed7);">
                            <i class="bi bi-star-fill text-warning me-1"></i> PALING POPULER
                        </div>
                    </div>

                    <div class="card-body p-4 p-xl-5 mt-3 d-flex flex-column position-relative">
                        <!-- Decorative bg -->
                        <div class="position-absolute top-0 end-0 opacity-10" style="width: 150px; height: 150px; background: radial-gradient(circle, #0d6efd 0%, transparent 70%);"></div>

                        <div class="mb-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 mb-3 fw-semibold">PROFESIONAL</span>
                            <h2 class="fw-bold mb-2">PRO</h2>
                            <p class="text-muted small mb-0">Usaha yang butuh sistem otomatis</p>
                        </div>
                        
                        <div class="mb-4" style="min-height: 85px;">
                            <!-- Harga Bulanan -->
                            <div x-show="cycle == 'monthly'" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                                <h3 class="display-5 fw-bold text-dark mb-0 d-flex align-items-center">
                                    Rp 49<span class="fs-2">.000</span>
                                </h3>
                                <span class="text-muted d-block mt-1">Ditagih setiap bulan</span>
                            </div>
                            
                            <!-- Harga Tahunan -->
                            <div x-show="cycle == 'yearly'" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                                <div class="d-flex align-items-center mb-1">
                                    <h3 class="display-5 fw-bold text-primary mb-0 me-2" style="letter-spacing: -1px;">
                                        Rp 469<span class="fs-2">.000</span>
                                    </h3>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 mt-1 border border-danger border-opacity-25">-20%</span>
                                </div>
                                <div class="text-muted small">
                                    <s>Rp 588.000</s> <i class="bi bi-arrow-right mx-1"></i> <span class="text-success fw-bold">Hemat Rp 119.000</span> setahun
                                </div>
                            </div>
                        </div>
                        
                        <div style="height: 1px; background: linear-gradient(90deg, transparent, rgba(13,110,253,0.3), transparent);" class="w-100 mb-4"></div>
                        
                        <ul class="list-unstyled text-start mb-auto">
                            <li class="mb-3 d-flex align-items-start"><i class="bi bi-check-circle-fill text-primary fs-5 me-3 flex-shrink-0 mt-n1"></i> <span class="text-dark fw-bold">Semua fitur Gratis, ditambah:</span></li>
                            <li class="mb-3 d-flex align-items-start"><i class="bi bi-check-circle-fill text-primary fs-5 me-3 flex-shrink-0 mt-n1"></i> <span class="text-secondary fw-medium">Pengingat WhatsApp otomatis</span></li>
                            <li class="mb-3 d-flex align-items-start"><i class="bi bi-check-circle-fill text-primary fs-5 me-3 flex-shrink-0 mt-n1"></i> <span class="text-secondary fw-medium">Laporan penjualan rutin</span></li>
                            <li class="mb-3 d-flex align-items-start"><i class="bi bi-check-circle-fill text-primary fs-5 me-3 flex-shrink-0 mt-n1"></i> <span class="text-secondary fw-medium">Export rekap ke Excel</span></li>
                            <li class="mb-3 d-flex align-items-start"><i class="bi bi-check-circle-fill text-primary fs-5 me-3 flex-shrink-0 mt-n1"></i> <span class="text-secondary fw-medium">QR pembayaran di invoice</span></li>
                            <li class="mb-2 d-flex align-items-start"><i class="bi bi-check-circle-fill text-primary fs-5 me-3 flex-shrink-0 mt-n1"></i> <span class="text-secondary fw-medium">Kop surat tanpa watermark</span></li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-4 p-xl-5 pt-0 mt-auto position-relative" style="z-index: 2;">
                        <?php if(session()->get('isLoggedIn')): ?>
                            <button type="button" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center" data-bs-toggle="modal" data-bs-target="#paymentModal" style="background: linear-gradient(45deg, #0d6efd, #0a58ca); border: none;">
                                <span>Upgrade Sekarang</span>
                                <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                            </button>
                        <?php else: ?>
                            <a href="/login" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm d-flex justify-content-center align-items-center" style="background: linear-gradient(45deg, #0d6efd, #0a58ca); border: none;">
                                <span>Login untuk Upgrade</span>
                                <i class="bi bi-box-arrow-in-right ms-2"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- MODAL PEMBAYARAN -->
<?php if(session()->get('isLoggedIn')): ?>
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            
            <div class="bg-primary bg-gradient p-4 text-white text-center position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="mb-2">
                    <i class="bi bi-bag-check-fill display-4 opacity-75"></i>
                </div>
                <h4 class="fw-bold mb-0">Selesaikan Pembayaran</h4>
                <p class="text-white-50 small mb-0 mt-1">Satu langkah lagi menuju fitur premium</p>
            </div>

            <div class="modal-body p-4 bg-light">
                <form action="/subscribe" method="post">
                    <?= csrf_field() ?>
                    <?php 
                        $proPlanId = null; 
                        if (isset($plans) && is_array($plans)) {
                            foreach($plans as $p) {
                                if($p['slug'] == 'pro') $proPlanId = $p['id'];
                            }
                        }
                    ?>
                    <input type="hidden" name="plan_id" value="<?= $proPlanId ?? 2 ?>">
                    <input type="hidden" name="billing_cycle" :value="cycle">
                    
                    <!-- Review Pesanan -->
                    <div class="card border-0 shadow-sm rounded-3 mb-4">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark fs-5">Paket PRO</span>
                                <span class="badge bg-primary rounded-pill px-2" x-text="cycle == 'yearly' ? 'Tahunan' : 'Bulanan'"></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Periode tagihan</span>
                                <span class="fw-bold fs-5 text-primary" x-text="cycle == 'yearly' ? 'Rp 469.000' : 'Rp 49.000'"></span>
                            </div>
                        </div>
                    </div>

                    <label class="form-label fw-bold text-dark mb-3"><i class="bi bi-wallet2 me-1 text-primary"></i> Pilih Metode Pembayaran</label>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="payment_method" id="methodTransfer" value="transfer" checked autocomplete="off">
                            <label class="btn btn-outline-primary w-100 h-100 mb-0 p-3 rounded-3 text-center payment-option d-flex flex-column align-items-center justify-content-center" for="methodTransfer">
                                <div class="icon-payment mb-2 rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px;">
                                    <i class="bi bi-bank fs-4"></i>
                                </div>
                                <span class="fw-bold d-block">Bank Transfer</span>
                                <span class="small text-muted d-block mt-1" style="font-size: 0.70rem;">BCA, Mandiri, BRI</span>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="payment_method" id="methodQris" value="qris" autocomplete="off">
                            <label class="btn btn-outline-primary w-100 h-100 mb-0 p-3 rounded-3 text-center payment-option d-flex flex-column align-items-center justify-content-center" for="methodQris">
                                <div class="icon-payment mb-2 rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px;">
                                    <i class="bi bi-qr-code-scan fs-4"></i>
                                </div>
                                <span class="fw-bold d-block">QRIS</span>
                                <span class="small text-muted d-block mt-1" style="font-size: 0.70rem;">Gopay, OVO, Dana</span>
                            </label>
                        </div>
                    </div>

                    <!-- Kode Promo Area -->
                    <div class="accordion accordion-flush bg-white rounded-3 shadow-sm border overflow-hidden" id="promoAccordion">
                        <div class="accordion-item border-0">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-3 px-3 fw-semibold bg-white text-dark small" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePromo" aria-expanded="false" style="box-shadow: none;">
                                    <i class="bi bi-ticket-perforated text-warning me-2 fs-5"></i> Punya Kode Kupon Promo?
                                </button>
                            </h2>
                            <div id="collapsePromo" class="accordion-collapse collapse" data-bs-parent="#promoAccordion">
                                <div class="accordion-body border-top pt-3 pb-3 px-3">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control text-uppercase" id="promoCodeInput" placeholder="Ketik kode di sini" autocomplete="off">
                                        <button class="btn btn-dark px-3 fw-medium ms-2 rounded-2" type="button" id="btnApplyPromo">
                                            <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true" id="promoSpinner"></span>
                                            Pakai
                                        </button>
                                    </div>
                                    <div id="promoMessage" class="mt-2 text-danger" style="font-size: 0.75rem; display: none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rincian Harga Dinamis -->
                    <div id="promoResultContainer" class="mt-3 bg-white p-3 rounded-3 shadow-sm border d-none" style="border-left: 4px solid #198754 !important;">
                        <h6 class="fw-bold mb-2 text-success small text-uppercase" style="letter-spacing: 0.5px;"><i class="bi bi-check-circle-fill me-1"></i> Promo Berhasil</h6>
                        <div class="d-flex justify-content-between text-muted mb-1" style="font-size: 0.85rem;">
                            <span>Harga Asli</span>
                            <s x-text="cycle == 'yearly' ? 'Rp 469.000' : 'Rp 49.000'"></s>
                        </div>
                        <div class="d-flex justify-content-between text-success mb-2" style="font-size: 0.85rem;">
                            <span>Nilai Diskon</span>
                            <span id="promoDiscountInfo" class="fw-semibold">- Rp 0</span>
                        </div>
                        <div style="border-top: 1px dashed #dee2e6;" class="my-2"></div>
                        <div class="d-flex justify-content-between fw-bold text-dark" style="font-size: 1.1rem;">
                            <span>Tagihan Akhir</span>
                            <span id="promoFinalPrice">Rp 49.000</span>
                        </div>
                    </div>
                    
                    <input type="hidden" name="coupon_id" id="appliedCouponId" value="">
                    <!-- Nilai Harga x-model binding dari Alpine.js -->
                    <input type="hidden" id="basePlanPrice" :value="basePrice">

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light px-4 rounded-pill fw-medium text-dark" style="border: 1px solid #ced4da;" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm d-flex align-items-center">
                            Lanjutkan <i class="bi bi-arrow-right-short fs-5 ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Style untuk Payment Radio Buttons */
.pricing-card:hover {
    transform: translateY(-5px);
}
.payment-option {
    border-width: 2px !important;
    transition: all 0.2s ease;
    background-color: #ffffff;
    border-color: #e9ecef !important;
    color: #495057;
}
.payment-option .icon-payment {
    transition: all 0.2s ease;
}
.btn-check:checked + .payment-option {
    background-color: rgba(13, 110, 253, 0.05);
    border-color: #0d6efd !important;
}
.btn-check:checked + .payment-option .icon-payment {
    background-color: #0d6efd !important;
    color: white !important;
}
.btn-check:checked + .payment-option .fw-bold {
    color: #0d6efd !important;
}

/* Accordion Custom */
#promoAccordion .accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #212529;
}
#promoAccordion .accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,0,0,.125);
}
</style>
<?php endif; ?>

<!-- SECTION 3 — PERBANDINGAN SEDERHANA -->
<section class="py-5 bg-white border-top">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-2">Mengapa Anda Harus Upgrade?</h2>
                    <p class="text-muted">Simpan tenaga dan waktu Anda, biar sistem kami yang bekerja.</p>
                </div>
                
                <div class="row g-4 align-items-center">
                    <div class="col-md-6 pe-md-4 text-center text-md-end">
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-x-lg fs-3"></i>
                            </div>
                            <h4 class="fw-bold">Tanpa Pro (Gratis)</h4>
                            <p class="text-muted small">Anda harus mengecek WhatsApp pelanggan satu per satu, menagih secara manual, dan mereka bingung transfer ke mana karena tak ada QR.</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 ps-md-4 text-center text-md-start" style="border-left: 1px solid #dee2e6;">
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-check-lg fs-3"></i>
                            </div>
                            <h4 class="fw-bold text-dark">Dengan Paket Pro</h4>
                            <p class="text-muted small">Sistem mengirimkan Auto-Reminder via WA. Pelanggan langsung scan QRIS di PDF, uang masuk seketika. Pembukuan rekap excel siap disetorkan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 4 — FAQ PRICING -->
<section class="py-5 bg-light pb-6">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Masih ragu? Berikut jawaban yang mungkin Anda cari.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion shadow-sm" id="accordionFAQ">
                    <div class="accordion-item border-0 border-bottom mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Apakah paket Gratis benar-benar tanpa batas?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted" style="line-height: 1.6;">
                                Ya. Anda bisa membuat dan mengirim invoice sebanyak apapun tanpa batas waktu. Kami percaya usaha UMKM yang baru mulai harus mendapat dukungan penuh tanpa dibebani biaya.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 border-bottom mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Apakah ada kontrak atau biaya tersembunyi?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted" style="line-height: 1.6;">
                                Tidak ada. Semua biaya transparan. Anda hanya membayar sesuai nominal paket tanpa biaya provisi atau *hidden fee* lainnya.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 border-bottom mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Apakah bisa berhenti kapan saja?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted" style="line-height: 1.6;">
                                Tentu. Tidak ada kontrak yang mengikat. Anda bisa berhenti berlangganan kapan pun Anda mau. Akun Anda akan utuh dan berubah menjadi paket Gratis kembali. Data invoice Anda dipastikan tetap aman.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button fw-bold text-dark collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Bagaimana Jika Masa Trial 14 Hari Habis?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted" style="line-height: 1.6;">
                                Jika masa uji coba gratis Pro Anda habis dan Anda belum upgrade, maka secara otomatis akun Anda akan dibatasi layaknya Paket Gratis. Invoice lama tetap aman dan bisa dibuka, namun Anda tidak dapat lagi menggunakan pengingat WA otomatis atau menghapus Watermark Invoice hingga Anda memutuskan Upgrade.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CLOSING CTA -->
<section class="py-5 text-center my-3">
    <div class="container pb-5">
        <h3 class="fw-bold mb-3 display-6">Mulai Transformasi Keuangan Anda</h3>
        <p class="text-muted mx-auto mb-4 lead" style="max-width: 600px">
            Bergabunglah dengan UMKM digital lainnya. Gratis untuk selamanya, tingkatkan jika Anda sudah membuktikan manfaatnya.
        </p>
        <a href="/register" class="btn btn-primary btn-lg rounded-pill px-5 shadow fw-bold">Daftar Sekarang - Gratis</a>
    </div>
</section>

</div> <!-- Penutup Wrap Alpine.js -->

<style>
    /* Styling responsif agar bagian terbawah tidak terpotong */
    .pb-6 { padding-bottom: 5rem !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnApply = document.getElementById('btnApplyPromo');
    const inputCode = document.getElementById('promoCodeInput');
    const msgBox = document.getElementById('promoMessage');
    const resBox = document.getElementById('promoResultContainer');
    const pDiscount = document.getElementById('promoDiscountInfo');
    const pFinal = document.getElementById('promoFinalPrice');
    const hiddenCoupon = document.getElementById('appliedCouponId');
    const spinner = document.getElementById('promoSpinner');

    if (btnApply) {
        btnApply.addEventListener('click', function() {
            const code = inputCode.value.trim();
            const basePrice = document.getElementById('basePlanPrice').value;
            
            if(!code) return;
            
            // UI Loading state
            btnApply.disabled = true;
            spinner.classList.remove('d-none');
            msgBox.style.display = 'none';

            fetch('/coupon/apply', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    'code': code,
                    'plan_price': basePrice,
                    'csrf_test_name': document.querySelector('input[name="csrf_test_name"]').value
                })
            })
            .then(response => response.json())
            .then(data => {
                btnApply.disabled = false;
                spinner.classList.add('d-none');
                
                // Regenerate CSRF Token dynamically to prevent "Action Not Allowed" error on main form submit
                if (data.csrf_token) {
                    document.querySelectorAll('input[name="csrf_test_name"]').forEach(el => {
                        el.value = data.csrf_token;
                    });
                }

                if (data.error) {
                    msgBox.className = 'mt-2 text-danger fw-medium';
                    msgBox.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + data.error;
                    msgBox.style.display = 'block';
                    resBox.classList.add('d-none');
                    hiddenCoupon.value = '';
                } else if (data.success) {
                    msgBox.className = 'mt-2 text-success fw-medium';
                    msgBox.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Kupon berhasil diterapkan!';
                    msgBox.style.display = 'block';
                    
                    // Format Number function
                    const formatRp = (num) => 'Rp ' + parseInt(num).toLocaleString('id-ID');
                    
                    pDiscount.innerText = '- ' + formatRp(data.discount);
                    pFinal.innerText = formatRp(data.final_price);
                    hiddenCoupon.value = data.coupon_id;
                    
                    // Open accordion if it's closed
                    const bsCollapse = new bootstrap.Collapse(document.getElementById('collapsePromo'), { toggle: false });
                    bsCollapse.hide();
                    
                    resBox.classList.remove('d-none');
                }
            })
            .catch(error => {
                btnApply.disabled = false;
                spinner.classList.add('d-none');
                msgBox.className = 'mt-2 text-danger fw-medium';
                msgBox.innerHTML = '<i class="bi bi-wifi-off me-1"></i> Terjadi kesalahan jaringan.';
                msgBox.style.display = 'block';
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
