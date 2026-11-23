# Rencana Pengujian Sistem (Test Plan) untuk Laporan Skripsi
**Sistem Kasir POS & Pembayaran Kartu NFC Kantin Polbeng Food Area**

---

## 1. Pendahuluan & Landasan Pengujian

Dokumen perencanaan pengujian ini dirancang khusus untuk kebutuhan pelaporan **BAB IV (Hasil dan Pembahasan / Pengujian Sistem)** pada laporan skripsi. Pengujian bertujuan untuk memverifikasi bahwa fungsionalitas, integrasi modul, aturan bisnis (*business logic*), serta interaksi antarmuka telah berjalan sesuai dengan spesifikasi kebutuhan yang ditentukan.

### 1.1 Metodologi Pengujian
1. **Black Box Testing (Pengujian Kotak Hitam)**:
   - Menggunakan teknik *Equivalence Partitioning* dan *Boundary Value Analysis*.
   - Fokus pada masukan (*input*), keluaran (*output*), dan alur fungsional tanpa memeriksa struktur kode internal secara langsung.
2. **Integration & Hardware Testing**:
   - Pengujian pembacaan UID kartu NFC fisik via endpoint `/nfc/check-balance` dan `/pedagang/transaction/nfc`.
   - Pengujian integrasi *Payment Gateway* Midtrans (Snap Pop-up & Webhook Callback).
3. **Business Logic & Edge Cases Testing**:
   - Validasi limit pengeluaran jajan (harian/mingguan) oleh wali murid.
   - Penanganan saldo tidak cukup (*insufficient funds*).
   - Penggantian kartu hilang (*card replacement*) dan persetujuan kartu (*approval*).
4. **Non-Functional Testing**:
   - Pengujian performa (Google Lighthouse) pada Landing Page & Dashboard.
   - Pengujian kompatibilitas lintas peramban (*cross-browser*) dan responsivitas layar.
5. **User Acceptance Testing (UAT) & System Usability Scale (SUS)**:
   - Pengukuran tingkat penerimaan dan kegunaan sistem dari sudut pandang 4 kelompok pengguna (Admin, Pedagang, Mahasiswa, Wali Murid).

---

## 2. Matriks Rencana Pengujian (Testing Matrix)

| ID Kategori | Komponen / Aspek | Metode Uji | Sasaran Pengujian |
| :--- | :--- | :--- | :--- |
| **TEST-01** | Landing Page & Profil Publik | Black Box & Responsive | Tampilan profil, katalog menu terlaris, Google Maps live embed, navigasi |
| **TEST-02** | Autentikasi & Kontrol Akses (RBAC) | Black Box | Login multi-role (Admin, Pedagang, Mahasiswa, Wali), proteksi rute, session |
| **TEST-03** | Modul Manajemen Admin | Black Box | CRUD pengguna, verifikasi kartu NFC, sewa stan kantin, override limit |
| **TEST-04** | Modul Katalog & Stan Pedagang | Black Box | Tambah/ubah/hapus menu, upload foto makanan, kelola stok & kategori |
| **TEST-05** | Transaksi Kasir POS & NFC | Black Box & Hardware | Input pesanan, kalkulasi total, tap kartu NFC, cetak/lihat struk pesanan |
| **TEST-06** | Modul Mahasiswa | Black Box | Cek saldo kartu, histori belanja di kantin, detail mutasi |
| **TEST-07** | Modul Wali Murid & Limit Jajan | Business Logic & Integration | Pencarian NIM anak, set limit transaksi harian, transfer saldo dompet wali |
| **TEST-08** | Payment Gateway Midtrans | Integration & Webhook | Pembuatan Snap Token, pembayaran simulasi (GoPay/VA), verifikasi webhook saldo |
| **TEST-09** | Non-Fungsional (Performa & UI) | Automated Tools | Skor Lighthouse (Performance, Accessibility, SEO), uji resolusi layar |
| **TEST-10** | Penerimaan Pengguna (UAT / SUS) | Kuesioner Skala Likert | Tingkat kepuasan & kemudahan penggunaan pengguna akhir |

---

## 3. Rincian Skenario Black Box Testing (Tabel Siap Pakai Skripsi)

### 3.1 TEST-01: Pengujian Landing Page & Profil Publik
| No | Skenario Pengujian | Data Masukan / Aksi | Hasil yang Diharapkan | Kriteria Keberhasilan |
| :---: | :--- | :--- | :--- | :---: |
| 1.1 | Akses Halaman Utama (`/`) | Buka URL root sistem di peramban | Halaman landing page tampil utuh, bebas error 500, logo Polbeng & Food Area muncul | Valid |
| 1.2 | Tampilan Menu Terlaris | Load katalog menu favorit | Menampilkan maksimal 3 produk terlaris berdasarkan akumulasi kuantitas terjual | Valid |
| 1.3 | Akses Preview Google Maps | Scroll ke bagian lokasi & zoom peta | Peta Google Maps interaktif termuat via iframe Rusunawa Polbeng tanpa kendala CORS | Valid |
| 1.4 | Navigasi Anchor Link | Klik menu "Keunggulan", "Menu Favorit", "Lokasi & Rute" | Halaman otomatis melakukan scroll halus (*smooth scroll*) ke target anchor section | Valid |
| 1.5 | Tombol Rute Eksternal | Klik "Buka Rute Langsung di Google Maps" | Membuka tab baru menuju URL Google Maps rute kampus Polbeng | Valid |

---

### 3.2 TEST-02: Autentikasi & Hak Akses (Multi-Role RBAC)
| No | Skenario Pengujian | Data Masukan / Aksi | Hasil yang Diharapkan | Kriteria Keberhasilan |
| :---: | :--- | :--- | :--- | :---: |
| 2.1 | Login dengan Kredensial Valid (Admin) | Email & password Admin terdaftar | Berhasil login dan dialihkan ke `/admin/dashboard` | Valid |
| 2.2 | Login dengan Kredensial Valid (Pedagang) | Email & password Pedagang | Berhasil login dan dialihkan ke `/pedagang/dashboard` | Valid |
| 2.3 | Login dengan Kredensial Valid (Mahasiswa) | Email & password Mahasiswa | Berhasil login dan dialihkan ke `/mahasiswa/dashboard` | Valid |
| 2.4 | Login dengan Kredensial Valid (Wali) | Email & password Wali Murid | Berhasil login dan dialihkan ke `/wali/dashboard` | Valid |
| 2.5 | Login Password Salah | Email benar, password salah | Sistem menolak login dan memunculkan pesan peringatan error | Valid |
| 2.6 | Proteksi URL Tanpa Login (*Direct Access*) | Akses manual URL `/admin/dashboard` tanpa session | Sistem memblokir akses dan mengarahkan otomatis ke halaman login | Valid |
| 2.7 | Pencegahan Eskalasi Hak Akses | User login sebagai Mahasiswa mencoba akses `/admin/dashboard` | Akses ditolak (*Error 403 Forbidden* atau redirect) | Valid |
| 2.8 | Proses Logout | Klik tombol logout | Sesi dihapus (*session invalidated*), token CSRF di-regenerate | Valid |

---

### 3.3 TEST-03: Modul Admin (Manajemen Kartu NFC & Stan)
| No | Skenario Pengujian | Data Masukan / Aksi | Hasil yang Diharapkan | Kriteria Keberhasilan |
| :---: | :--- | :--- | :--- | :---: |
| 3.1 | Registrasi Kartu NFC Baru | Input UID kartu, pilih mahasiswa | Kartu tersimpan dengan status *pending/active* | Valid |
| 3.2 | Approval Kartu Mahasiswa | Klik "Approve" pada antrean kartu | Status kartu berubah menjadi aktif dan saldo siap digunakan | Valid |
| 3.3 | Penolakan / Pemblokiran Kartu | Klik "Reject" pada kartu bermasalah | Kartu berstatus ditolak, tidak dapat digunakan untuk tap di POS | Valid |
| 3.4 | Penggantian Kartu Hilang (*Replace Card*) | Pilih kartu lama, input UID kartu baru | UID lama dinonaktifkan, saldo lama otomatis dimigrasi ke UID baru | Valid |
| 3.5 | Update Sewa Stan Pedagang | Set status/biaya sewa pedagang kantin | Data sewa stan tersimpan dan tercatat di histori pembayaran sewa | Valid |
| 3.6 | Override Limit Jajan | Admin menonaktifkan/mengubah limit mahasiswa | Limit jajan berhasil diperbarui sesuai instruksi khusus | Valid |

---

### 3.4 TEST-04: Modul Stan Pedagang & Pengelolaan Menu
| No | Skenario Pengujian | Data Masukan / Aksi | Hasil yang Diharapkan | Kriteria Keberhasilan |
| :---: | :--- | :--- | :--- | :---: |
| 4.1 | Tambah Menu Makanan/Minuman | Input nama, kategori, harga, upload foto | Menu baru tersimpan di database dan muncul di daftar kasir pedagang | Valid |
| 4.2 | Validasi Upload Gambar Menu | Upload file non-gambar (misal .pdf atau .exe) | Sistem menolak upload dengan notifikasi validasi format file | Valid |
| 4.3 | Ubah Data Menu | Ubah nama atau harga menu | Data menu berhasil terupdate tanpa merusak histori transaksi lama | Valid |
| 4.4 | Hapus Menu Makanan | Klik hapus menu | Menu terhapus dari daftar aktif pedagang | Valid |

---

### 3.5 TEST-05: Transaksi Kasir POS, Tap NFC, & Aturan Bisnis (*Business Logic*)
| No | Skenario Pengujian | Data Masukan / Aksi | Hasil yang Diharapkan | Kriteria Keberhasilan |
| :---: | :--- | :--- | :--- | :---: |
| 5.1 | Pemilihan Item Keranjang POS | Klik beberapa menu makanan dan minuman | Daftar item pesanan masuk ke keranjang, subtotal dihitung otomatis | Valid |
| 5.2 | Pembayaran NFC: Kondisi Normal | Tap kartu dengan saldo Rp 50.000 (total belanja Rp 15.000, limit aman) | Transaksi sukses, saldo berkurang Rp 15.000, stok tercatat berkurang | Valid |
| 5.3 | Pembayaran NFC: Saldo Tidak Cukup | Tap kartu dengan saldo Rp 5.000 (total belanja Rp 20.000) | Transaksi ditolak dengan pesan error *"Saldo kartu tidak mencukupi"* | Valid |
| 5.4 | Pembayaran NFC: Melebihi Limit Harian | Total belanja Rp 25.000 (sisa limit jajan harian mahasiswa tinggal Rp 10.000) | Transaksi ditolak dengan pesan *"Transaksi melebihi limit harian"* | Valid |
| 5.5 | Pembayaran NFC: Kartu Belum Disetujui | Tap kartu fisik yang berstatus *pending/rejected* | Transaksi dibatalkan dengan pesan *"Kartu belum aktif/disetujui admin"* | Valid |
| 5.6 | Pembayaran NFC: UID Tidak Dikenali | Tap kartu sembarang / kartu busway / e-KTP tak terdaftar | Sistem menampilkan pesan error *"Kartu tidak terdaftar dalam sistem"* | Valid |
| 5.7 | Pembaruan Status Pesanan | Pedagang mengubah status menjadi *selesai* / *dibatalkan* | Status pesanan di database terbarui, tercermin di riwayat | Valid |

---

### 3.6 TEST-06: Modul Wali Murid & Kontrol Jajan
| No | Skenario Pengujian | Data Masukan / Aksi | Hasil yang Diharapkan | Kriteria Keberhasilan |
| :---: | :--- | :--- | :--- | :---: |
| 6.1 | Verifikasi Mahasiswa via NIM | Input NIM mahasiswa yang valid | Nama mahasiswa, jurusan, dan kartu aktif anak muncul terverifikasi | Valid |
| 6.2 | Verifikasi Mahasiswa via NIM Salah | Input NIM sembarang yang tidak ada di DB | Sistem memunculkan notifikasi *"Mahasiswa tidak ditemukan"* | Valid |
| 6.3 | Pengaturan Batas Belanja (Limit) | Input nominal limit per hari (misal Rp 25.000) | Limit tersimpan di tabel `limits`, berlaku pada transaksi POS hari itu | Valid |
| 6.4 | Transfer Saldo ke Akun Mahasiswa | Transfer Rp 50.000 dari dompet wali ke mahasiswa | Saldo wali berkurang Rp 50.000, saldo kartu mahasiswa bertambah Rp 50.000 | Valid |
| 6.5 | Transfer Saldo Melebihi Saldo Wali | Saldo dompet wali Rp 10.000, mencoba transfer Rp 50.000 | Sistem menolak transfer karena saldo dompet wali tidak mencukupi | Valid |

---

### 3.7 TEST-07: Integrasi Payment Gateway Midtrans (Top-Up Saldo)
| No | Skenario Pengujian | Data Masukan / Aksi | Hasil yang Diharapkan | Kriteria Keberhasilan |
| :---: | :--- | :--- | :--- | :---: |
| 7.1 | Permintaan Snap Token Top-Up | Input nominal top-up (misal Rp 50.000) | Sistem menghubungi API Midtrans dan memunculkan pop-up Snap payment | Valid |
| 7.2 | Simulasi Pembayaran Sukses | Selesaikan pembayaran di Midtrans Sandbox (Virtual Account / QRIS) | Midtrans mengirim HTTP status *settlement*, saldo mahasiswa/wali otomatis bertambah | Valid |
| 7.3 | Penanganan Pembayaran Pending | Menutup pop-up sebelum menyelesaikan pembayaran | Status transaksi tetap *pending*, saldo tidak bertambah | Valid |
| 7.4 | Penanganan Pembayaran Kedaluwarsa/Batal | Batalkan transaksi di simulator Midtrans | Webhook menerima status *expire/cancel*, transaksi ditandai gagal | Valid |
| 7.5 | Keamanan Webhook Signature Key | Kirim payload webhook palsu tanpa signature key valid | Server menolak payload dan mengembalikan response error 400/403 | Valid |

---

## 4. Pengujian Non-Fungsional

### 4.1 Pengujian Performa (Lighthouse Audit)
Pengujian dilakukan pada Landing Page dan Dashboard Utama pada kondisi jaringan standar kampus:

| Parameter Metrik | Target Nilai Skripsi | Rekomendasi Standar Google |
| :--- | :---: | :---: |
| **Performance** | ≥ 90 / 100 | Hijau (90 - 100) |
| **Accessibility** | ≥ 90 / 100 | Hijau (90 - 100) |
| **Best Practices** | ≥ 90 / 100 | Hijau (90 - 100) |
| **SEO** | ≥ 90 / 100 | Hijau (90 - 100) |
| **First Contentful Paint (FCP)** | < 1.5 detik | Baik (< 1.8 detik) |
| **Largest Contentful Paint (LCP)** | < 2.5 detik | Baik (< 2.5 detik) |
| **Cumulative Layout Shift (CLS)** | < 0.1 | Baik (< 0.1) |

### 4.2 Pengujian Kompatibilitas Lintas Peramban (Cross-Browser)
| Peramban (Browser) | Versi Uji | Hasil Rendering Tampilan | Hasil Fungsionalitas | Kesimpulan |
| :--- | :--- | :---: | :---: | :---: |
| Google Chrome Desktop | v120+ | Responsif, layout presisi | Seluruh fitur berjalan lancar | Valid |
| Mozilla Firefox Desktop | v120+ | Responsif, efek blur & glass rapi | Seluruh fitur berjalan lancar | Valid |
| Microsoft Edge Desktop | v120+ | Responsif, font & SVG tajam | Seluruh fitur berjalan lancar | Valid |
| Chrome Mobile (Android) | v120+ | Touch target pas, iframe maps lancar | Modal & form kasir responsif | Valid |
| Safari Mobile (iOS) | iOS 17+ | Layout sesuai, navigasi halus | Transaksi & top-up berjalan lancar | Valid |

---

## 5. Pengujian Penerimaan Pengguna (User Acceptance Testing - UAT & SUS)

Untuk memperkuat bab Hasil dan Pembahasan, pengujian melibatkan responden langsung menggunakan kuesioner **System Usability Scale (SUS)** yang telah distandardisasi secara akademik (10 pertanyaan dengan skala Likert 1–5):

### 5.1 Instrumen Kuesioner SUS
1. Saya rasa saya akan sering menggunakan sistem kantin ini. *(Positif)*
2. Saya merasa sistem ini terlalu rumit untuk digunakan. *(Negatif)*
3. Saya merasa sistem ini mudah digunakan. *(Positif)*
4. Saya rasa saya memerlukan bantuan orang teknis untuk dapat menggunakan sistem ini. *(Negatif)*
5. Saya merasa fitur-fitur dalam sistem ini terintegrasi dengan baik. *(Positif)*
6. Saya merasa ada terlalu banyak hal yang tidak konsisten pada sistem ini. *(Negatif)*
7. Saya merasa kebanyakan orang akan cepat belajar menggunakan sistem ini. *(Positif)*
8. Saya merasa sistem ini membingungkan dan tidak praktis. *(Negatif)*
9. Saya merasa percaya diri saat menggunakan sistem ini untuk transaksi/monitoring. *(Positif)*
10. Saya perlu mempelajari banyak hal baru sebelum saya bisa menggunakan sistem ini. *(Negatif)*

### 5.2 Formula Perhitungan Skor SUS
Untuk setiap responden:
- Pertanyaan bernomor ganjil (1, 3, 5, 7, 9): **Skor Kontribusi = Skor Jawaban - 1**
- Pertanyaan bernomor genap (2, 4, 6, 8, 10): **Skor Kontribusi = 5 - Skor Jawaban**
- **Total Skor SUS per Responden** = (Jumlah seluruh Skor Kontribusi) × 2.5
- **Rata-rata Skor SUS**:
  - Target kelayakan skripsi: **≥ 70.0** (Kategori *"Acceptable"*, Grade *"B"* atau *"A"*, Predikat *"Good / Excellent"*).

### 5.3 Distribusi Target Responden Pengujian
- **Admin Pengelola Kantin / BPU**: 2 - 3 Orang
- **Pedagang / Pemilik Stan Kantin**: 5 - 10 Orang
- **Mahasiswa Polbeng**: 20 - 30 Orang
- **Orang Tua / Wali Mahasiswa**: 10 - 15 Orang
- **Total Responden**: ± 35 - 50 Responden

---

## 6. Template Kesimpulan Pengujian untuk Bab IV Skripsi

```markdown
Berdasarkan hasil pengujian yang telah dilaksanakan menggunakan metode Black Box Testing, 
seluruh skenario pengujian fungsional pada modul Landing Page, Multi-Role Authentication, 
Manajemen Kartu NFC, Kasir POS Pedagang, Monitoring Wali Murid, dan Integrasi Payment Gateway Midtrans 
menghasilkan status "Valid". Sistem mampu menangani kondisi pengecualian (edge cases) seperti 
saldo tidak mencukupi, kartu tidak aktif, dan pembatasan limit harian belanja secara akurat.

Hasil pengujian non-fungsional menunjukkan sistem memiliki kompatibilitas yang baik pada berbagai 
peramban modern dan perangkat mobile. Pengujian penerimaan pengguna melalui metode System Usability Scale 
(SUS) menghasilkan skor rata-rata [X.X] yang termasuk dalam kategori [Good / Excellent], membuktikan bahwa 
sistem dapat diterima dengan baik dan siap diimplementasikan di lingkungan kampus Politeknik Negeri Bengkalis.
```
