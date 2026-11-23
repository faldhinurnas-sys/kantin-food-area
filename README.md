<p align="center">
  <img src="public/images/polbeng-header.png" alt="Logo Politeknik Negeri Bengkalis" width="320">
</p>

<h1 align="center">Polbeng Food Area</h1>
<p align="center">
  <strong>Sistem Kasir POS & Pembayaran Digital Kartu Mahasiswa Berbasis NFC Kantin Kampus</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Midtrans-Payment_Gateway-002855?style=for-the-badge" alt="Midtrans Gateway">
  <img src="https://img.shields.io/badge/Hardware-NFC_Contactless-0052CC?style=for-the-badge" alt="NFC Contactless">
  <img src="https://img.shields.io/badge/Kampus-Politeknik_Negeri_Bengkalis-1e3a8a?style=for-the-badge" alt="Polbeng">
</p>

---

## 📌 Tentang Sistem

**Polbeng Food Area** adalah platform kasir digital (*Point of Sale*) terintegrasi yang dikembangkan khusus untuk unit usaha kantin di lingkungan kampus **Politeknik Negeri Bengkalis** (berlokasi di area **Rusunawa Polbeng**). 

Sistem ini mentransformasi transaksi kantin konvensional menjadi ekosistem *cashless* nirkontak (*contactless*) menggunakan **Kartu Mahasiswa berbasis NFC**, terhubung dengan *payment gateway* **Midtrans** untuk pengisian saldo (*top-up*), serta dilengkapi fitur kontrol pengeluaran jajan (*spending limit*) oleh orang tua/wali murid secara *real-time*.

---

## 🚀 Fitur Utama

### 1. 🌐 Landing Page Modern & Ramah Pengguna
- **Desain Ergonomis & Bersih**: Menghindari gaya klise AI (*anti-slop aesthetic*), diperkaya aksen *kinetic lime*, *paperclip cards*, dan tipografi responsif.
- **Preview Google Maps Interaktif**: Sematan langsung (*live embed*) peta lokasi kawasan Rusunawa Polbeng lengkap dengan navigasi rute.
- **Katalog Menu Terlaris**: Rekomendasi otomatis hidangan favorit berdasarkan kuantitas transaksi riil.
- **Bebas Icon Emoji**: Menggunakan vektor SVG yang tajam dan profesional.

### 2. 👨‍💼 Panel Administrator Kampus
- Manajemen pengguna multi-peran (*Admin, Pedagang, Mahasiswa, Wali*).
- Verifikasi dan persetujuan (*approval/rejection*) kartu NFC mahasiswa.
- Fitur penggantian kartu hilang (*replace card*) dengan migrasi saldo otomatis.
- Pengelolaan data sewa stan pedagang dan laporan rekapitulasi.
- Hak istimewa *override limit* pengeluaran jajan mahasiswa.

### 3. 🍳 Panel Stan Pedagang (Point of Sale / POS)
- Pengelolaan katalog menu, kategori (Makanan/Minuman), harga, dan foto hidangan.
- Antarmuka kasir cepat dengan keranjang pesanan otomatis.
- Pembayaran instan sekali *tap* menggunakan kartu NFC fisik.
- Pemantauan status pemrosesan pesanan hingga selesai.

### 4. 🎓 Panel Mahasiswa
- Pengecekan saldo kartu NFC secara *real-time*.
- Riwayat transaksi belanja kantin dan histori mutasi saldo.
- Pengisian saldo online (*top-up*) mandiri via integrasi Midtrans Snap.

### 5. 👨‍👩‍👧 Panel Orang Tua / Wali Murid
- Pengecekan dan validasi data anak melalui NIM Mahasiswa.
- Pengaturan batas maksimal belanja (*daily / weekly spending limit*) untuk kontrol gizi dan kehematan anak.
- Dompet digital wali (*wali balance*) dan fitur transfer saldo langsung ke kartu mahasiswa.
- Pemantauan histori jajan anak dari rumah.

### 6. 💳 Integrasi Payment Gateway Midtrans
- Pembayaran online menggunakan **Snap Pop-up** (QRIS, GoPay, ShopeePay, Virtual Account Bank).
- Otomatisasi penambahan saldo via **HTTP Webhook Notification** dengan verifikasi *Signature Key*.

---

## 🏛️ Arsitektur & Teknologi

| Komponen | Teknologi |
| :--- | :--- |
| **Backend Framework** | [Laravel 11](https://laravel.com/) (PHP 8.2+) |
| **Frontend Styling** | [Tailwind CSS](https://tailwindcss.com/) & Modern Blade Templating |
| **Database** | MySQL / MariaDB (Eloquent ORM) |
| **Hardware Pembaca** | NFC Card Reader (ACR122U / PN532 / Web NFC API) |
| **Payment Gateway** | [Midtrans Snap API & Webhook](https://midtrans.com/) |
| **Maps Service** | Google Maps Embed API |
| **Testing Document** | [RENCANA_PENGUJIAN_SISTEM.md](RENCANA_PENGUJIAN_SISTEM.md) *(Black Box, UAT, SUS)* |

---

## 🛠️ Panduan Instalasi Lokal

### 1. Prasyarat Sistem
- PHP >= 8.2 (ekstensi `bcmath`, `ctype`, `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`)
- Composer >= 2.x
- Web Server (Apache / Nginx via XAMPP / Laragon)
- MySQL / MariaDB

### 2. Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/faldhinurnas-sys/kantin-food-area.git
cd kantin-food-area

# 2. Install dependensi PHP
composer install

# 3. Salin berkas environment
cp .env.example .env

# 4. Generate App Key
php artisan key:generate

# 5. Konfigurasi Database di file .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kantin_polbeng
DB_USERNAME=root
DB_PASSWORD=

# 6. Konfigurasi Kunci Midtrans di .env
MIDTRANS_SERVER_KEY=your-midtrans-server-key
MIDTRANS_CLIENT_KEY=your-midtrans-client-key
MIDTRANS_IS_PRODUCTION=false

# 7. Jalankan Migrasi & Seeder Database
php artisan migrate --seed

# 8. Buat Symlink Storage
php artisan storage:link

# 9. Jalankan Server Pengembangan
php artisan serve
```

Aplikasi dapat diakses melalui peramban di: `http://localhost:8000`

---

## 🔑 Kredensial Akun Bawaan (Database Seeder)

Untuk keperluan pengujian dan demonstrasi, jalankan `php artisan db:seed` untuk membuat akun-akun berikut:

| Peran (Role) | Email Login | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@kantin.com` | `password` | Pengelola BPU / Unit Usaha Kampus |
| **Pedagang** | `pedagang1@kantin.com` | `password` | Pemilik Stan Kantin Makanan |
| **Mahasiswa** | `mahasiswa1@kantin.com` | `password` | NIM: `2101092001` (UID: `NFC-11111`) |
| **Wali Murid** | `wali1@kantin.com` | `password` | Orang Tua Mahasiswa 1 |

---

## 🧪 Pengujian Sistem (Kebutuhan Skripsi)

Sistem ini telah dilengkapi dengan dokumen perencanaan pengujian formal untuk keperluan pelaporan **BAB IV Skripsi**:
- 📄 Berkas: **[`RENCANA_PENGUJIAN_SISTEM.md`](RENCANA_PENGUJIAN_SISTEM.md)**
- Mencakup:
  1. **Black Box Testing**: Tabel uji fungsional `TEST-01` s/d `TEST-07`.
  2. **Integrasi & Hardware**: Simulasi *tap* kartu NFC dan *webhook settlement* Midtrans.
  3. **Non-Fungsional**: Standar audit Google Lighthouse dan uji kompatibilitas *cross-browser*.
  4. **User Acceptance Testing (UAT)**: Instrumen kuesioner 10 butir pertanyaan **System Usability Scale (SUS)** beserta rumus skornya.

---

## 📄 Lisensi & Kontributor

- **Pengembang**: [faldhinurnas-sys](https://github.com/faldhinurnas-sys) (`faldhinurnas@gmail.com`)
- **Institusi**: Politeknik Negeri Bengkalis
- **Lisensi**: Proyek ini dilisensikan di bawah [MIT License](LICENSE).
