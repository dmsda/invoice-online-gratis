# Invoice Online Gratis

Aplikasi SaaS (Software as a Service) untuk pembuatan invoice secara online, ditujukan khusus untuk membantu UMKM dan freelancer di Indonesia dalam mengelola tagihan, klien, dan pembayaran dengan mudah, aman, dan profesional.

## Fitur Utama 🌟

- **Manajemen Klien**: Simpan dan kelola data klien Anda dengan mudah.
- **Pembuatan Invoice Cepat**: Buat invoice untuk produk maupun jasa dengan tampilan antarmuka yang ramah pengguna.
- **Ekspor PDF Profesional**: Unduh invoice dalam format PDF dengan templat yang rapi, siap untuk dikirim ke klien.
- **Integrasi Pembayaran (QRIS)**: Dukungan pengaturan pembayaran menggunakan standar QRIS untuk kemudahan transaksi.
- **Sistem Berlangganan (Subscriptions)**: Paket fleksibel (Gratis & Premium) untuk menyesuaikan dengan kebutuhan bisnis.
- **Dashboard Analitik**: Pantau statistik pemasukan, invoice, dan performa bisnis Anda (serta Dashboard terpisah untuk Admin Super).

## Persyaratan Sistem ⚙️

Aplikasi ini dibangun menggunakan framework **CodeIgniter 4**.

- **PHP**: Versi 8.2 atau lebih baru
- **Ekstensi PHP**: `intl`, `mbstring`, `json`, `mysqlnd` (atau PDO MySQL), `curl`, `gd`
- **Database**: MySQL / MariaDB (atau SQLite untuk testing)
- **Web Server**: Apache atau Nginx
- **Composer**: Untuk manajemen dependensi PHP

## Petunjuk Instalasi 🚀

1. **Clone repositori**
   ```bash
   git clone https://github.com/dmsda/invoice-online-gratis.git
   cd invoice-online-gratis
   ```

2. **Instal dependensi**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**
   Salin file bawaan `env` menjadi `.env`:
   ```bash
   cp env .env
   ```
   Kemudian, buka file `.env` dan sesuaikan pengaturan berikut:
   - `CI_ENVIRONMENT = development` (ubah menjadi `production` jika di server live)
   - `app.baseURL = 'http://localhost:8080/'` (sesuaikan dengan URL Anda)
   - Konfigurasi Database (Host, Database, Username, Password)

4. **Migrasi Database & Seeding**
   Jalankan perintah berikut untuk membuat struktur tabel dan mengisi data awal:
   ```bash
   php spark migrate
   php spark db:seed TrialPlanSeeder
   # (Gunakan seeder lain seperti DummyAccountSeeder jika diperlukan untuk testing)
   ```

5. **Jalankan Aplikasi Lokal**
   ```bash
   php spark serve
   ```
   Aplikasi dapat diakses melalui browser di `http://localhost:8080`.

## Struktur Direktori Utama 📂

- `app/`: Logika inti aplikasi (Controllers, Models, Views, Database Migrations, dll).
- `public/`: Direktori root untu web server. Berisi aset statis (CSS, JS, Gambar) dan `index.php`.
- `writable/`: Direktori untuk file yang dihasilkan oleh sistem (Cache, Logs, Uploads pengguna, Session).
- `tests/`: Kumpulan unit test untuk aplikasi.

## Berkontribusi 🤝

Jika Anda menemukan bug atau memiliki ide fitur baru, silakan buat _Issue_ atau ajukan _Pull Request_ di repositori ini. 

---
_Dibuat untuk mendukung pertumbuhan UMKM Indonesia._ 🇮🇩
