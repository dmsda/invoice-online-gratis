<?= $this->extend('layout/landing') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">Buat Invoice Online Gratis dalam 30 Detik (Tanpa Ribet)</h1>
                <p class="lead mb-5 opacity-90">Kirim tagihan profesional ke pelanggan lewat WhatsApp langsung dari HP Anda. Cepat, mudah, dan gratis selamanya.</p>
                <div class="d-flex justify-content-center gap-3 flex-column flex-sm-row">
                    <a href="/register" class="btn btn-light btn-lg text-primary fw-bold px-5">Buat Invoice Sekarang</a>
                    <a href="#cara-kerja" class="btn btn-light btn-lg px-4">Lihat Cara Kerja</a>
                </div>
                <div class="mt-3 small opacity-75">
                    Tanpa kartu kredit. Langsung aktif.
                </div>
                <div class="mt-4 small opacity-75">
                    <i class="bi bi-check-circle-fill me-1"></i> Gratis Selamanya &nbsp;
                    <i class="bi bi-check-circle-fill me-1"></i> Langsung Jadi PDF &nbsp;
                    <i class="bi bi-check-circle-fill me-1"></i> Kirim via WhatsApp &nbsp;
                    <i class="bi bi-check-circle-fill me-1"></i> Tanpa Install Aplikasi
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Problem Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Masih Pusing Ngurusin Tagihan Manual?</h2>
            <p class="text-muted">Jangan biarkan urusan administrasi menghambat rezeki Anda. Sering mengalami ini?</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="p-3">
                    <div class="display-6 mb-3">🤯</div>
                    <h5>Ribet Pakai Excel</h5>
                    <p class="small text-muted">Harus buka laptop, atur kolom, dan rumus sering error.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <div class="display-6 mb-3">📝</div>
                    <h5>Nota Tulis Tangan</h5>
                    <p class="small text-muted">Kertas sering hilang, tulisan sulit dibaca, dan terlihat kurang profesional.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <div class="display-6 mb-3">💬</div>
                    <h5>Tagihan Tenggelam</h5>
                    <p class="small text-muted">Kirim foto nota di WhatsApp seringkali tidak terbaca atau tertumpuk chat lain.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <div class="display-6 mb-3">❓</div>
                    <h5>Pelanggan Bingung</h5>
                    <p class="small text-muted">Total bayar tidak jelas karena tulisan tangan atau format berantakan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section (Moved Up) -->
<section id="cara-kerja" class="py-5 bg-light-soft">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Cara Membuat Invoice Online dalam 3 Langkah</h2>
            <p class="text-muted">Proses cepat, mudah, dan bisa langsung cair.</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-3">
                    <div class="display-1 fw-bold text-primary mb-3">1</div>
                    <h3 class="h5 fw-bold">Daftar Gratis & Masuk</h3>
                    <p class="text-muted small">Buat akun hanya dalam hitungan detik. Tidak perlu kartu kredit, tidak ada masa uji coba, langsung aktif selamanya.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="display-1 fw-bold text-primary mb-3">2</div>
                    <h3 class="h5 fw-bold">Isi Data Invoice</h3>
                    <p class="text-muted small">Masukkan nama klien dan rincian produk/jasa. Sistem otomatis menghitung total harga, jadi Anda tidak perlu repot.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3">
                    <div class="display-1 fw-bold text-primary mb-3">3</div>
                    <h3 class="h5 fw-bold">Download atau Kirim</h3>
                    <p class="text-muted small">Simpan invoice sebagai PDF siap cetak atau salin link unik untuk dikirim via WhatsApp. Praktis!</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="/register" class="btn btn-primary btn-lg px-5 shadow-sm">Mulai Gratis</a>
            <p class="small text-muted mt-2">Hanya butuh email. 30 detik selesai.</p>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="fitur" class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Fitur Utama Invoice Online Gratis</h2>
            <p class="text-muted">Semua yang Anda butuhkan untuk kelola tagihan dengan mudah.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <!-- Feature 1 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-lightning-charge-fill feature-icon"></i>
                        <h3 class="h5 fw-bold">Buat Invoice Online</h3>
                        <p class="text-muted small">Cukup isi data pelanggan, sistem kami membuatkan invoice profesional secara otomatis.</p>
                    </div>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-file-earmark-pdf-fill feature-icon"></i>
                        <h3 class="h5 fw-bold">Download PDF</h3>
                        <p class="text-muted small">Simpan sebagai file PDF ukuran A4 yang rapi dan siap cetak atau kirim.</p>
                    </div>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-whatsapp feature-icon"></i>
                        <h3 class="h5 fw-bold">Kirim via WhatsApp</h3>
                        <p class="text-muted small">Kirim link tagihan langsung ke WA klien denga pesan yang sudah disiapkan.</p>
                    </div>
                </div>
            </div>
            <!-- Feature 4 -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-link-45deg feature-icon"></i>
                        <h3 class="h5 fw-bold">Link Online Aman</h3>
                        <p class="text-muted small">Klien tidak perlu install aplikasi untuk melihat tagihan, cukup buka link dari browser.</p>
                    </div>
                </div>
            </div>
            <!-- Feature 5 -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-gift-fill feature-icon"></i>
                        <h3 class="h5 fw-bold">Gratis Selamanya</h3>
                        <p class="text-muted small">Tanpa biaya langganan, tanpa komisi potong. Buat invoice sepuasnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Target Audience Section -->
<section id="cocok-untuk" class="py-5 bg-light-soft">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Cocok Untuk Skala Usaha Apa Saja?</h2>
            <p class="text-muted">Desain kami dibuat fleksibel untuk berbagai macam bisnis UMKM dan pekerja lepas.</p>
        </div>
        <div class="row g-4 text-center justify-content-center">
            <div class="col-md-3 col-6">
                <div class="p-4 bg-white rounded-3 shadow-sm h-100 border border-light">
                    <div class="display-5 mb-3">🔧</div>
                    <h5 class="fw-bold">Jasa Service</h5>
                    <p class="small text-muted mb-0">AC, Elektronik, Bengkel, Cuci Sepatu.</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-4 bg-white rounded-3 shadow-sm h-100 border border-light">
                    <div class="display-5 mb-3">💻</div>
                    <h5 class="fw-bold">Freelancer</h5>
                    <p class="small text-muted mb-0">Desainer, Penulis, Programmer, Event Organizer.</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-4 bg-white rounded-3 shadow-sm h-100 border border-light">
                    <div class="display-5 mb-3">🍳</div>
                    <h5 class="fw-bold">Katering & F&B</h5>
                    <p class="small text-muted mb-0">Kue Pesanan, Nasi Kotak, Supplier Warung.</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="p-4 bg-white rounded-3 shadow-sm h-100 border border-light">
                    <div class="display-5 mb-3">📦</div>
                    <h5 class="fw-bold">Toko & Grosir</h5>
                    <p class="small text-muted mb-0">Toko Bangunan, Grosir Pakaian, Penjual Online.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Comparison Section -->
<section class="py-5 bg-white">
    <div class="container">
         <div class="text-center mb-5">
            <h2 class="fw-bold">Kenapa Lebih Baik dari Cara Manual?</h2>
            <p class="text-muted">Bandingkan sendiri kemudahannya.</p>
        </div>
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <ul class="list-unstyled">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success fs-5 me-3"></i>
                        <div>
                            <strong>Bisa Pakai HP</strong>
                            <p class="small text-muted mb-0">Bikin invoice sambil rebahan pun bisa, tanpa laptop.</p>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success fs-5 me-3"></i>
                        <div>
                            <strong>Otomatis Rapi</strong>
                            <p class="small text-muted mb-0">Tidak perlu pusing mengatur garis tabel Excel yang berantakan.</p>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success fs-5 me-3"></i>
                        <div>
                            <strong>Aman Tersimpan</strong>
                            <p class="small text-muted mb-0">Data aman di akun Anda, tidak hilang meski ganti HP.</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success fs-5 me-3"></i>
                        <div>
                            <strong>Terlihat Bonafide</strong>
                            <p class="small text-muted mb-0">Format PDF bersih meningkatkan kepercayaan klien.</p>
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="col-lg-6 text-center d-none d-lg-block">
                <!-- SVG Illustration: Person checking invoice on phone -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 400" width="100%" height="auto" class="img-fluid" style="max-width: 450px;">
                    <!-- Background blobs -->
                    <path fill="#eff6ff" d="M350.5,302Q290,354,204,336.5Q118,319,101.5,234.5Q85,150,157.5,95.5Q230,41,320.5,68Q411,95,411,172.5Q411,250,350.5,302Z" />
                    <!-- Phone shape -->
                    <rect x="200" y="80" width="120" height="240" rx="15" fill="#1e293b" />
                    <rect x="205" y="90" width="110" height="220" rx="10" fill="#ffffff" />
                    <!-- Invoice graphic inside phone -->
                    <rect x="220" y="110" width="40" height="40" rx="5" fill="#2563eb" />
                    <circle cx="240" cy="130" r="10" fill="#ffffff" />
                    <rect x="270" y="115" width="30" height="8" rx="4" fill="#cbd5e1" />
                    <rect x="270" y="130" width="20" height="8" rx="4" fill="#e2e8f0" />
                    <!-- List items -->
                    <rect x="220" y="165" width="80" height="6" rx="3" fill="#e2e8f0" />
                    <rect x="220" y="180" width="60" height="6" rx="3" fill="#e2e8f0" />
                    <rect x="220" y="195" width="70" height="6" rx="3" fill="#e2e8f0" />
                    <!-- Total block -->
                    <rect x="220" y="220" width="80" height="20" rx="5" fill="#f1f5f9" />
                    <rect x="230" y="227" width="60" height="6" rx="3" fill="#2563eb" />
                    <!-- Chart/Check overlay -->
                    <circle cx="260" cy="270" r="25" fill="#16a34a" />
                    <path d="M248,270 L256,278 L272,260" fill="none" stroke="#ffffff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                    
                    <!-- Decorative elements -->
                    <circle cx="120" cy="180" r="15" fill="#f59e0b" opacity="0.8" />
                    <circle cx="380" cy="120" r="10" fill="#0ea5e9" opacity="0.8" />
                    <rect x="360" y="280" width="25" height="25" rx="5" fill="#6366f1" opacity="0.8" transform="rotate(15 360 280)" />
                    <!-- Lines connecting pieces -->
                    <path d="M140,180 Q170,160 190,180" fill="none" stroke="#cbd5e1" stroke-width="2" stroke-dasharray="4,4" />
                </svg>
            </div>
        </div>
    </div>
</section>

<!-- Social Proof / Trust Section -->
<section class="py-5 bg-white text-center">
    <div class="container">
        <h2 class="fw-bold mb-4">Dipercaya oleh UMKM & Freelancer Indonesia</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card bg-light border-0 shadow-sm p-4 h-100 text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="mb-3">"Sangat membantu bisnis service AC saya. Klien jadi lebih percaya karena invoicenya rapi dan bisa dikirim ke WA."</p>
                    <strong>- Jajang, Teknisi AC</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light border-0 shadow-sm p-4 h-100 text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="mb-3">"Biasanya repot bikin nota pakai kertas, sekarang tinggal buka HP 30 detik tagihan beres. Praktis banget!"</p>
                    <strong>- Husein, Owner Katering</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light border-0 shadow-sm p-4 h-100 text-center">
                    <div class="text-warning mb-2">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    </div>
                    <p class="mb-3">"Kirim tagihan lewat link sangat memudahkan pencatatan freelance saya. UI-nya juga bersih."</p>
                    <strong>- Ari, Graphic Designer</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section id="faq" class="py-5 bg-light-soft">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-muted">Punya pertanyaan lain? Kami siap membantu.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion shadow-sm" id="accordionFAQ">
                    <div class="accordion-item border-0 mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Benarkah ini 100% gratis?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted">
                                Ya, fitur dasar pembuatan invoice online gratis selamanya. Kami memiliki opsi paket PRO untuk fitur tambahan, tapi Anda tetap bisa membuat dan mengirim invoice PDF tanpa biaya apapun.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Apakah klien harus download aplikasi?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted">
                                Tidak sama sekali. Klien Anda hanya perlu klik link WhatsApp yang Anda kirim, dan invoice akan terbuka di browser HP mereka (bisa didownload juga sebagai PDF).
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-2 rounded-3 overflow-hidden">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Apakah data invoice saya aman?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionFAQ">
                            <div class="accordion-body text-muted">
                                Tentu saja. Data invoice Anda tersimpan secara aman di server, tidak akan hilang meskipun Anda berganti perangkat HP.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Closing CTA Section -->
<section class="cta-section text-center">
    <div class="container py-4">
        <h2 class="fw-bold mb-3">Siap Merapikan Tagihan Bisnis Anda?</h2>
        <p class="lead opacity-90 mb-4">Mulai gunakan Invoice Online Gratis dan fokus pada apa yang benar-benar penting untuk bisnis Anda.</p>
        <a href="/register" class="btn btn-light btn-lg text-primary fw-bold px-5 rounded-pill shadow-sm">Buat Invoice Gratis Sekarang</a>
        <p class="mt-3 mb-0 small opacity-75">Hanya butuh 30 detik untuk mulai.</p>
    </div>
</section>

<?= $this->endSection() ?>
