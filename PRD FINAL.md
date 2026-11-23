# Product Requirements Document (PRD)
## Implementasi Metode DevOps pada Rancang Bangun Sistem Point of Sale (POS) Unit Usaha Inkubator Polbeng Food Area

**Versi:** 3.0
**Tanggal:** 27 September 2026
**Disusun oleh:** Muhammad Riofaldhi Nur Nasta'in
**Metode Pengembangan:** DevOps (Plan, Code, Build, Test, Release, Deploy, Operate, Monitor)

---

## 1. Latar Belakang

Transaksi di unit usaha inkubator/food area kampus (kantin) pada umumnya masih dilakukan secara tunai, yang menimbulkan beberapa permasalahan:
- Proses pembayaran lambat saat jam sibuk (antrean panjang)
- Rawan kesalahan hitung dan kehilangan uang fisik
- Tidak ada rekapitulasi transaksi maupun pencatatan penjualan (kasir/POS) yang otomatis bagi pedagang
- Orang tua/wali tidak memiliki visibilitas maupun kontrol atas pengeluaran anak di kantin

Untuk itu dikembangkan **Sistem Point of Sale (POS)** yang terintegrasi dengan pembayaran berbasis **Near Field Communication (NFC)**. Sistem POS berfungsi sebagai aplikasi kasir bagi pedagang (pencatatan penjualan, manajemen menu, dan pemrosesan transaksi), sedangkan NFC menjadi metode pembayaran non-tunai yang digunakan mahasiswa, lengkap dengan fitur transparansi dan kontrol saldo bagi wali/orang tua.

## 2. Tujuan Produk

1. Menyediakan sistem **Point of Sale (POS)** bagi pedagang untuk mengelola menu, memproses transaksi, dan mendapatkan rekap penjualan secara otomatis.
2. Menyediakan mekanisme pembayaran non-tunai yang cepat dan akurat menggunakan kartu NFC yang terintegrasi dengan POS.
3. Memberikan mahasiswa kemudahan bertransaksi tanpa uang tunai beserta riwayat transaksi.
4. Memberikan wali/orang tua kemampuan memonitor saldo dan riwayat transaksi anak secara real-time.
5. Memberikan wali/orang tua kemampuan membatasi penggunaan saldo (limit harian/per transaksi/kategori).
6. Menyediakan kontrol administratif bagi pihak kampus untuk mengelola perizinan (approval) pedagang, akun pengguna, dan kartu NFC dalam sistem.
7. Menerapkan metode DevOps dengan CI/CD untuk memastikan proses pengembangan yang efisien dan berkelanjutan.

## 3. Target Pengguna & Aktor

| Aktor | Deskripsi |
|---|---|
| **Pedagang** | Pemilik/penjaga stan kantin yang menerima pembayaran via NFC dan mengelola menu |
| **Mahasiswa** | Pengguna kartu NFC untuk melakukan pembayaran di kantin |
| **Wali/Orang Tua** | Pihak yang memonitor saldo & transaksi anak, serta mengatur limit penggunaan saldo |
| **Admin** | Pihak internal kampus/inkubator yang berperan sebagai pengelola izin (approval authority) — menyetujui pendaftaran pedagang, akun pengguna, dan kartu NFC, serta melakukan pengawasan lintas sistem |

## 4. Ruang Lingkup (Scope)

**Termasuk dalam scope:**
- Registrasi & manajemen akun untuk 4 aktor (Pedagang, Mahasiswa, Wali, Admin)
- Manajemen approval & perizinan oleh Admin (persetujuan pedagang baru, akun pengguna, dan kartu NFC)
- **Aplikasi POS (kasir)** untuk pedagang: input pesanan, pemrosesan pembayaran (tunai & NFC), cetak/tampilkan struk transaksi
- Manajemen kartu NFC (aktivasi, saldo, status)
- Proses transaksi pembayaran via tap NFC pada POS
- **Integrasi Payment Gateway Midtrans** untuk proses top-up saldo kartu NFC secara online (transfer bank, e-wallet, QRIS, virtual account)
- Manajemen menu & produk oleh pedagang melalui POS
- Laporan penjualan (harian/mingguan/bulanan) bagi pedagang
- Riwayat transaksi untuk mahasiswa dan wali
- Pengaturan limit saldo oleh wali
- Notifikasi transaksi
- Dashboard untuk masing-masing aktor
- CI/CD pipeline (GitHub Actions) untuk build, test, dan deployment

**Di luar scope (fase awal):**
- Metode pembayaran selain NFC untuk transaksi di kantin (QR code langsung, e-wallet eksternal saat transaksi di POS)

## 5. Kebutuhan Fungsional (Functional Requirements)

### 5.1 Pedagang (POS)
- FR-1: Pedagang dapat login ke aplikasi POS.
- FR-2: Pedagang dapat menambah, mengubah, dan menghapus data menu/produk (termasuk harga & kategori) melalui POS.
- FR-3: Pedagang dapat menginput pesanan/keranjang transaksi pada antarmuka POS.
- FR-4: Pedagang dapat memproses pembayaran melalui POS, baik secara tunai maupun NFC (tap kartu di reader).
- FR-5: POS menampilkan/mencetak struk setelah transaksi berhasil.
- FR-6: Pedagang dapat melihat rekap dan laporan penjualan (harian/periode tertentu) melalui POS.

### 5.2 Mahasiswa
- FR-7: Mahasiswa dapat login ke sistem.
- FR-8: Mahasiswa dapat melihat saldo kartu NFC miliknya.
- FR-9: Mahasiswa dapat melakukan pembayaran dengan tap kartu NFC ke reader pada POS pedagang. Apabila kartu NFC gagal terbaca, pedagang dapat menginput ID kartu secara manual pada POS sebagai fallback.
- FR-10: Mahasiswa dapat melihat riwayat transaksi pribadi.
- FR-11: Sistem menolak transaksi apabila saldo tidak mencukupi atau melebihi limit yang ditetapkan wali.
- FR-12: Mahasiswa dapat melakukan top-up saldo kartu NFC melalui payment gateway Midtrans (transfer bank/VA, e-wallet, QRIS).
- FR-13: Sistem memperbarui saldo kartu secara otomatis setelah menerima notifikasi pembayaran berhasil (callback/webhook) dari Midtrans. Apabila status pembayaran masih "pending" melebihi batas waktu tertentu tanpa notifikasi webhook, sistem melakukan pengecekan status secara berkala (polling) ke API Midtrans sebagai cadangan.

### 5.3 Wali/Orang Tua
- FR-14: Wali dapat login ke sistem.
- FR-15: Wali dapat memonitor saldo kartu NFC anak secara real-time.
- FR-16: Wali dapat melihat riwayat transaksi anak (termasuk riwayat top-up dan mutasi dompet wali).
- FR-17: Wali dapat menetapkan/mengubah limit penggunaan saldo (harian, per transaksi, dan/atau per kategori produk).
- FR-18: Wali menerima notifikasi setiap kali terjadi transaksi pada kartu anak.
- FR-19: Wali memiliki Dompet Digital tersendiri yang dapat diisi saldo (Top Up) baik melalui Midtrans Online (QRIS/VA) maupun Top Up Instan (Simulasi).
- FR-19a: Wali dapat mentransfer saldo langsung dari Dompet Digital Wali ke kartu NFC anak secara instan, atomik, dan tervalidasi.
- FR-19b: Wali dapat melakukan top-up langsung ke kartu NFC anak secara online melalui payment gateway Midtrans tanpa memotong saldo dompet wali.
- FR-19c: Wali dapat mentransfer saldo dompet atau melakukan top-up online langsung ke kartu NFC mahasiswa lain (selain anak asuh) yang terdaftar di kampus dengan memilih nama/kartu mahasiswa penerima.
- FR-19d: Wali dapat mentransfer saldo dompet atau melakukan top-up langsung ke kartu NFC mahasiswa melalui input Nomor Induk Mahasiswa (NIM) dengan verifikasi otomatis identitas penerima dan keaktifan kartu NFC.

### 5.4 Sistem
- FR-20: Sistem (POS) melakukan validasi saldo dan limit sebelum transaksi NFC diproses.
- FR-21: Sistem mencatat setiap transaksi (waktu, nominal, pedagang, produk, metode pembayaran) ke database.
- FR-22: Sistem mengirim notifikasi ke pedagang dan wali setelah transaksi berhasil.
- FR-23: Sistem menghasilkan laporan penjualan agregat untuk kebutuhan rekap pedagang.
- FR-24: Sistem terintegrasi dengan API Midtrans untuk membuat transaksi pembayaran top-up dan menerima status pembayaran melalui webhook/notifikasi.
- FR-25: Sistem mencatat riwayat top-up (jumlah, metode pembayaran, status) secara terpisah dari transaksi belanja.
- FR-25a: Apabila koneksi terputus saat proses pemotongan saldo berlangsung, sistem menerapkan mekanisme transaksi atomik (rollback) sehingga saldo tidak berubah dan transaksi berstatus "gagal".
- FR-25b: Apabila perangkat POS/reader NFC mengalami downtime, sistem menyediakan mode pencatatan transaksi manual sementara oleh pedagang, yang disinkronkan ke sistem saat perangkat kembali online.

### 5.5 Admin (Kampus)
- FR-26: Admin dapat login ke dashboard admin.
- FR-27: Admin dapat meninjau dan menyetujui (approve) atau menolak pendaftaran akun pedagang baru sebelum pedagang dapat menggunakan aplikasi POS.
- FR-28: Admin dapat meninjau dan menyetujui (approve) atau menonaktifkan akun mahasiswa dan wali, termasuk aktivasi maupun penonaktifan kartu NFC (misal karena hilang/rusak).
- FR-29: Admin dapat memberikan persetujuan atas pengecualian (override) limit saldo dalam kondisi sengketa antara wali, mahasiswa, dan/atau pedagang.
- FR-30: Admin dapat memantau log transaksi dan laporan penjualan agregat lintas seluruh pedagang untuk keperluan pengawasan, tanpa mengubah data transaksi secara langsung.

## 6. Kebutuhan Non-Fungsional (Non-Functional Requirements)

| ID | Kebutuhan | Deskripsi |
|---|---|---|
| NFR-1 | Performa | Validasi transaksi via NFC selesai dalam < 2 detik |
| NFR-2 | Keamanan | Data saldo dan transaksi terenkripsi; autentikasi berbasis role (pedagang/mahasiswa/wali/admin); akses role Admin dibatasi hanya untuk fungsi approval dan monitoring, tanpa kewenangan mengubah data transaksi secara langsung; komunikasi dengan Midtrans menggunakan server key/signature verification untuk validasi keaslian notifikasi pembayaran |
| NFR-3 | Ketersediaan | Sistem dapat diakses selama jam operasional kantin tanpa gangguan signifikan |
| NFR-4 | Maintainability | Proses build, test, dan deploy diotomatisasi melalui CI/CD (GitHub Actions) |
| NFR-5 | Usability | Antarmuka dashboard mudah digunakan oleh pedagang dan wali dengan latar belakang teknis yang beragam |

## 7. Alur Sistem (System Flow)

### 7.1 Alur Transaksi Pembayaran (via POS)

1. Pedagang menginput pesanan mahasiswa ke aplikasi POS (pilih menu/produk, jumlah).
2. Pedagang memilih metode pembayaran NFC pada POS, lalu mahasiswa melakukan tap kartu NFC pada reader.
   - **Jika kartu NFC gagal terbaca** → pedagang menginput ID kartu secara manual pada POS sebagai fallback, lalu sistem melanjutkan validasi seperti langkah 3.
3. Sistem memeriksa saldo dan limit yang berlaku pada kartu tersebut.
4. **Jika saldo tidak mencukupi atau melebihi limit** → transaksi ditolak, notifikasi ditampilkan pada POS ke pedagang/mahasiswa.
5. **Jika saldo mencukupi** → saldo dipotong sesuai nominal transaksi, transaksi dicatat ke sistem, dan POS menampilkan/mencetak struk.
   - **Jika koneksi terputus saat proses pemotongan saldo** → sistem melakukan rollback otomatis (transaksi atomik), saldo tidak berubah, dan transaksi berstatus "gagal".
6. Sistem mengirim notifikasi transaksi ke pedagang dan wali.

> **Catatan fallback perangkat:** Apabila perangkat POS/reader NFC mengalami downtime, pedagang menggunakan mode pencatatan transaksi manual sementara, yang akan disinkronkan ke sistem begitu perangkat kembali online (lihat FR-25b).

### 7.2 Alur Monitoring & Pengaturan Limit oleh Wali

1. Wali login ke dashboard wali.
2. Wali melihat riwayat transaksi dan saldo anak secara real-time.
3. Wali mengatur/mengubah limit penggunaan saldo (harian/per transaksi).
4. Sistem menyimpan pengaturan limit baru.
5. Limit baru diterapkan pada transaksi berikutnya.

### 7.3 Alur Top-Up Saldo via Payment Gateway Midtrans

1. Mahasiswa/wali memilih menu top-up saldo dan memasukkan nominal pada dashboard.
2. Sistem membuat transaksi pembayaran melalui API Midtrans dan menampilkan opsi metode pembayaran (VA/e-wallet/QRIS).
3. Mahasiswa/wali menyelesaikan pembayaran di halaman/metode yang disediakan Midtrans.
4. Midtrans mengirim notifikasi status pembayaran (webhook) ke sistem.
   - **Jika webhook gagal/terlambat diterima** (status masih "pending" melebihi batas waktu tertentu) → sistem melakukan pengecekan status secara berkala (polling) ke API Midtrans sebagai cadangan.
5. Sistem memverifikasi keaslian notifikasi, lalu memperbarui saldo kartu NFC jika pembayaran berhasil.
6. Sistem mencatat riwayat top-up dan mengirim notifikasi konfirmasi ke mahasiswa/wali.

### 7.4 Alur Manajemen Menu & Laporan Penjualan (POS)

1. Pedagang login ke aplikasi POS.
2. Pedagang menambah/mengubah/menghapus data menu (nama, harga, kategori, stok).
3. Sistem menyimpan perubahan menu dan menerapkannya pada transaksi berikutnya di POS.
4. Pedagang membuka menu laporan pada POS untuk melihat rekap penjualan harian/periode tertentu.
5. Sistem menampilkan agregat transaksi (total penjualan, jumlah transaksi, produk terlaris).

### 7.5 Alur Approval oleh Admin (Kampus)

1. Pedagang/mahasiswa/wali melakukan pendaftaran akun baru pada sistem.
2. Sistem meneruskan pengajuan pendaftaran ke dashboard Admin dengan status "menunggu persetujuan".
3. Admin login ke dashboard admin dan meninjau data pengajuan (identitas pedagang/pengguna, kartu NFC yang akan diaktifkan).
4. **Jika pengajuan disetujui** → Admin melakukan approve, akun/kartu NFC berstatus aktif dan dapat digunakan pada sistem.
5. **Jika pengajuan ditolak** → Admin melakukan reject beserta alasan, notifikasi dikirim ke pemohon.
6. Untuk kondisi sengketa limit saldo (mis. antara wali dan mahasiswa terkait pembatasan), Admin dapat meninjau kasus dan memberikan persetujuan override pada limit yang berlaku.
7. Admin dapat memantau log transaksi dan laporan agregat lintas pedagang melalui dashboard admin untuk kebutuhan pengawasan.

## 8. Metrik Keberhasilan (Success Metrics)

- Waktu transaksi rata-rata via NFC lebih cepat dibandingkan transaksi tunai.
- Tingkat keberhasilan (success rate) validasi transaksi ≥ 95%.
- Tingkat penerimaan pengguna (UAT dengan skala Likert) berada pada kategori "baik" atau lebih tinggi.
- Berkurangnya keluhan terkait kesalahan pencatatan transaksi dibandingkan sistem manual.

## 9. Metode Pengujian

- **Black Box Testing** — menguji fungsionalitas sistem berdasarkan kebutuhan fungsional (FR-1 s.d. FR-30) tanpa melihat struktur kode internal.
- **User Acceptance Test (UAT)** — menggunakan kuesioner skala Likert kepada responden pedagang, mahasiswa, wali, dan admin untuk mengukur tingkat penerimaan sistem.

## 10. Rencana Rilis (High-Level, berbasis DevOps)

| Tahap DevOps | Aktivitas |
|---|---|
| Plan | Analisis kebutuhan, penyusunan use case & desain sistem |
| Code | Implementasi fitur sesuai FR/NFR |
| Build | Proses build otomatis via GitHub Actions |
| Test | Pengujian otomatis (unit/integration) + Black Box Testing |
| Release | Persiapan rilis versi ke lingkungan staging |
| Deploy | Deployment ke production (shared hosting/Railway) |
| Operate | Sistem berjalan dan digunakan oleh pedagang, mahasiswa, wali |
| Monitor | Pemantauan performa sistem dan pengumpulan feedback (UAT) |

## 11. Risiko & Mitigasi

| Risiko | Mitigasi |
|---|---|
| Kegagalan pembacaan kartu NFC | Sediakan mekanisme fallback berupa input manual ID kartu oleh pedagang, sebagaimana diatur pada FR-9 dan Alur 7.1 langkah 2 |
| Saldo tidak sinkron akibat koneksi terputus saat transaksi | Terapkan mekanisme transaksi atomik (rollback jika gagal), sebagaimana diatur pada FR-25a dan Alur 7.1 langkah 5 |
| Wali tidak familiar dengan dashboard | Sediakan panduan penggunaan dan antarmuka yang sederhana |
| Pedagang tidak terbiasa menggunakan aplikasi POS | Desain antarmuka POS yang sederhana + sesi pelatihan singkat bagi pedagang |
| Perangkat POS/reader NFC mengalami downtime | Sediakan mode pencatatan transaksi manual sementara sebagai fallback, sebagaimana diatur pada FR-25b dan catatan pada Alur 7.1 |
| Kegagalan/keterlambatan notifikasi (webhook) dari Midtrans | Sediakan mekanisme pengecekan status transaksi secara berkala (polling) ke API Midtrans sebagai cadangan, sebagaimana diatur pada FR-13 dan Alur 7.3 langkah 4 |
| Pendaftaran akun/kartu NFC palsu atau tidak sah | Terapkan proses approval oleh Admin (kampus) sebelum akun/kartu aktif digunakan, sebagaimana diatur pada FR-27, FR-28, dan Alur 7.5 |
| Sengketa limit saldo antara wali, mahasiswa, dan pedagang | Sediakan mekanisme peninjauan dan persetujuan override oleh Admin, sebagaimana diatur pada FR-29 dan Alur 7.5 langkah 6 |

---

*Dokumen ini menjadi acuan pengembangan sistem dan dapat diperbarui seiring berjalannya proses analisis dan perancangan lebih lanjut.*
