<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polbeng Food Area — Sistem Kasir POS & Pembayaran NFC Kantin Kampus</title>
    <meta name="description" content="Sistem Point of Sale (POS) dan pembayaran non-tunai berbasis NFC pada Unit Usaha Inkubator Bisnis Politeknik Negeri Bengkalis.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Typography: Inter & Plus Jakarta Sans via Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Intentional Anti-Slop Configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                            950: '#0f172a',
                        },
                        amberbrand: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            600: '#d97706',
                            700: '#b45309',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Anti-slop base hygiene: natural typography, solid contrast, no fuzzy blobs */
        body {
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
            -webkit-font-smoothing: antialiased;
        }
        .code-pill {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans min-h-screen flex flex-col selection:bg-brand-100 selection:text-brand-900 antialiased">

    <!-- ================= TOP STATUS BAR (CAMPUS IDENTITY) ================= -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800 font-medium text-[11px]">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Sistem Aktif
                </span>
                <span class="text-slate-400">Unit Usaha Inkubator Bisnis • Politeknik Negeri Bengkalis</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Protokol NFC ISO 14443A</span>
                <span class="hidden sm:inline">•</span>
                <span class="hidden sm:inline">Gateway Midtrans Terintegrasi</span>
            </div>
        </div>
    </div>

    <!-- ================= MAIN HEADER / NAVBAR ================= -->
    <header class="bg-white/95 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- Logo & Brand Title -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-brand-700 rounded-lg p-1">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-slate-100 border border-slate-200 p-1 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Polbeng Food Area" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="font-extrabold text-base sm:text-lg text-slate-900 tracking-tight leading-tight group-hover:text-brand-700 transition">
                            POLBENG FOOD AREA
                        </div>
                        <div class="text-[11px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Point of Sale & NFC Smart Card
                        </div>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
                    <a href="#fitur" class="hover:text-brand-700 transition py-1">Fitur Utama</a>
                    <a href="#cara-kerja" class="hover:text-brand-700 transition py-1">Alur Transaksi</a>
                    <a href="#peran" class="hover:text-brand-700 transition py-1">4 Peran Pengguna</a>
                    <a href="#menu" class="hover:text-brand-700 transition py-1">Tenant & Menu</a>
                </nav>

                <!-- Action Button (Auth Aware) -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-lg bg-brand-900 text-white font-semibold text-sm hover:bg-brand-800 shadow-sm transition focus:ring-2 focus:ring-brand-600 focus:outline-none">
                            <svg class="w-4 h-4 text-brand-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Buka Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-lg bg-brand-900 text-white font-semibold text-sm hover:bg-brand-800 shadow-sm transition focus:ring-2 focus:ring-brand-600 focus:outline-none">
                            <svg class="w-4 h-4 text-brand-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Masuk ke Sistem</span>
                        </a>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button id="mobile-toggle-btn" class="md:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-slate-200 focus:outline-none" aria-label="Menu navigasi">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Dropdown -->
            <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-slate-200 space-y-2">
                <a href="#fitur" class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-slate-100">Fitur Utama</a>
                <a href="#cara-kerja" class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-slate-100">Alur Transaksi</a>
                <a href="#peran" class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-slate-100">4 Peran Pengguna</a>
                <a href="#menu" class="block px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-slate-100">Tenant & Menu</a>
                <div class="pt-2 border-t border-slate-100">
                    <a href="{{ route('login') }}" class="block text-center py-2.5 px-4 bg-brand-900 text-white rounded-lg font-bold text-sm">
                        Masuk ke Portal Akun
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        
        <!-- ================= HERO SECTION (GROUNDED & DIRECT) ================= -->
        <section class="border-b border-slate-200 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 lg:py-24">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                    
                    <!-- Left: Clear Value Proposition -->
                    <div class="lg:col-span-7 space-y-6">
                        
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-semibold">
                            <span class="w-2 h-2 rounded-full bg-brand-700"></span>
                            Inovasi Transaksi Kampus Polbeng
                        </div>

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                            Kantin kampus bebas antre uang tunai. <br class="hidden sm:inline">
                            <span class="text-brand-800">Cukup satu tap kartu.</span>
                        </h1>

                        <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-2xl">
                            Sistem kasir (POS) bagi pedagang unit usaha inkubator yang terhubung langsung dengan kartu mahasiswa berbasis NFC. Mengeliminasi antrean uang kembalian, merekap penjualan secara otomatis, dan memberikan visibilitas limit belanja bagi wali mahasiswa.
                        </p>

                        <!-- Key Pillars -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 py-2">
                            <div class="border border-slate-200 rounded-lg p-3 bg-slate-50">
                                <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Kecepatan</div>
                                <div class="text-slate-900 font-bold text-sm">Transaksi &lt; 3 Detik</div>
                                <div class="text-slate-500 text-xs mt-0.5">Bebas hitung uang kembalian</div>
                            </div>
                            <div class="border border-slate-200 rounded-lg p-3 bg-slate-50">
                                <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Pedagang</div>
                                <div class="text-slate-900 font-bold text-sm">POS & Rekap Otomatis</div>
                                <div class="text-slate-500 text-xs mt-0.5">Laporan penjualan real-time</div>
                            </div>
                            <div class="border border-slate-200 rounded-lg p-3 bg-slate-50">
                                <div class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-1">Wali Mahasiswa</div>
                                <div class="text-slate-900 font-bold text-sm">Limit & Transparansi</div>
                                <div class="text-slate-500 text-xs mt-0.5">Kontrol batas jajan harian</div>
                            </div>
                        </div>

                        <!-- Direct CTA Buttons -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                            <a href="{{ route('login') }}" class="inline-flex justify-center items-center gap-2 px-6 py-3.5 rounded-xl bg-brand-900 text-white font-bold text-sm hover:bg-brand-800 transition shadow-sm focus:ring-2 focus:ring-brand-700 focus:outline-none">
                                <span>Masuk ke Akun Anda</span>
                                <svg class="w-4 h-4 text-brand-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            <a href="#cara-kerja" class="inline-flex justify-center items-center gap-2 px-6 py-3.5 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-50 hover:border-slate-400 transition">
                                <span>Pelajari Alur Kerja</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </a>
                        </div>

                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span>Terintegrasi 4 hak akses: Pedagang Stan, Mahasiswa, Wali Murid, dan Administrator Kampus.</span>
                        </div>

                    </div>

                    <!-- Right: Tactile POS & NFC Interactive Representation -->
                    <div class="lg:col-span-5">
                        <div class="space-y-4">
                            
                            <!-- Card 1: Real POS Cashier Screen -->
                            <div class="border border-slate-300 rounded-2xl bg-white p-5 shadow-sm">
                                <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Terminal POS Kasir #01</span>
                                    </div>
                                    <span class="text-xs font-mono font-medium text-slate-500">Ibu Kantin A</span>
                                </div>
                                
                                <div class="space-y-2 mb-4 text-xs">
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-800 font-semibold">1x Nasi Goreng Spesial</span>
                                        <span class="font-mono text-slate-700">Rp 15.000</span>
                                    </div>
                                    <div class="flex justify-between items-center py-1 border-b border-slate-100">
                                        <span class="text-slate-800 font-semibold">1x Es Teh Manis Dingin</span>
                                        <span class="font-mono text-slate-700">Rp 4.000</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 font-bold text-sm text-slate-900">
                                        <span>Total Tagihan:</span>
                                        <span class="text-brand-900 font-mono text-base">Rp 19.000</span>
                                    </div>
                                </div>

                                <div class="p-3 bg-brand-50 border border-brand-200 rounded-xl flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-brand-900 text-white flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-brand-900">Siap Terima Pembayaran</div>
                                            <div class="text-[11px] text-brand-700">Tempelkan kartu mahasiswa ke reader</div>
                                        </div>
                                    </div>
                                    <span class="text-[11px] font-mono font-semibold px-2 py-0.5 rounded bg-brand-200 text-brand-900">NFC Ready</span>
                                </div>
                            </div>

                            <!-- Card 2: Student NFC Card Mockup -->
                            <div class="border border-slate-800 bg-slate-900 text-white rounded-2xl p-5 shadow-sm relative overflow-hidden">
                                <div class="flex justify-between items-start mb-6">
                                    <div>
                                        <div class="text-[10px] font-mono text-slate-400 uppercase tracking-widest">POLBENG SMART CARD</div>
                                        <div class="text-sm font-extrabold text-white tracking-tight">KARTU MAHASISWA & KANTIN</div>
                                    </div>
                                    <!-- Contactless Symbol -->
                                    <div class="text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4c4.418 0 8 3.582 8 8m-4 0a4 4 0 00-4-4m-2 4a2 2 0 012-2"/></svg>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-xs font-mono mb-4">
                                    <div>
                                        <div class="text-[10px] text-slate-400">PEMEGANG KARTU</div>
                                        <div class="font-bold text-slate-100">Andi Pratama</div>
                                        <div class="text-[10px] text-slate-400">NIM: 2101092001</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-[10px] text-slate-400">UID CHIP NFC</div>
                                        <div class="font-bold text-emerald-400">NFC-11111</div>
                                        <div class="text-[10px] text-slate-400">Status: Aktif</div>
                                    </div>
                                </div>

                                <div class="pt-3 border-t border-slate-800 flex justify-between items-center text-xs">
                                    <div>
                                        <span class="text-slate-400 text-[11px]">Saldo Kartu: </span>
                                        <span class="font-mono font-bold text-white text-sm">Rp 50.000</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-slate-400 text-[11px]">Limit Sisa: </span>
                                        <span class="font-mono font-semibold text-emerald-400 text-xs">Rp 20.000 / hari</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- ================= MASALAH vs SOLUSI NYATA ================= -->
        <section id="fitur" class="py-16 sm:py-20 border-b border-slate-200 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="max-w-3xl mb-12">
                    <h2 class="text-xs font-bold text-brand-800 uppercase tracking-widest mb-2">Mengapa Sistem Ini Dibangun</h2>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Mengubah kebiasaan transaksi tunai yang lambat menjadi operasional digital yang terukur.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div class="bg-white p-6 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 flex items-center justify-center font-bold text-sm mb-4">
                            01
                        </div>
                        <h3 class="font-bold text-base text-slate-900 mb-2">Eliminasi Antrean Jam Istirahat</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-3">
                            Transaksi tunai memakan waktu untuk mencari uang pas dan kembalian. Dengan tap kartu NFC, proses bayar selesai dalam tempo kurang dari 3 detik.
                        </p>
                        <div class="text-xs font-semibold text-brand-800 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-800"></span>
                            Waktu antrean turun drastis
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg bg-brand-50 border border-brand-200 text-brand-800 flex items-center justify-center font-bold text-sm mb-4">
                            02
                        </div>
                        <h3 class="font-bold text-base text-slate-900 mb-2">Pembukuan Kasir POS Otomatis</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-3">
                            Pedagang tidak perlu lagi mencatat di nota kertas atau khawatir uang hilang. Seluruh pesanan, stok menu, dan rekap pendapatan tersimpan rapi.
                        </p>
                        <div class="text-xs font-semibold text-brand-800 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-800"></span>
                            Rekapitulasi omzet instan
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-xl border border-slate-200">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 flex items-center justify-center font-bold text-sm mb-4">
                            03
                        </div>
                        <h3 class="font-bold text-base text-slate-900 mb-2">Kontrol & Batas Belanja dari Wali</h3>
                        <p class="text-slate-600 text-sm leading-relaxed mb-3">
                            Orang tua dapat menetapkan batas jajan harian anak, memantau riwayat pembelian, dan mentransfer saldo secara aman langsung ke kartu.
                        </p>
                        <div class="text-xs font-semibold text-brand-800 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-800"></span>
                            Transparansi finansial penuh
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- ================= ALUR TRANSAKSI FISIK ================= -->
        <section id="cara-kerja" class="py-16 sm:py-20 border-b border-slate-200 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-14">
                    <h2 class="text-xs font-bold text-brand-800 uppercase tracking-widest mb-2">Alur Pembayaran Fisik</h2>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Bagaimana transaksi berjalan di meja kasir
                    </p>
                    <p class="text-slate-500 text-sm mt-2">
                        Dirancang simpel, minim instruksi teknis, dan langsung familiar bagi pedagang serta mahasiswa.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 relative">
                    
                    <!-- Step 1 -->
                    <div class="border border-slate-200 rounded-xl p-5 bg-slate-50 hover:bg-white hover:border-slate-300 transition">
                        <div class="text-xs font-mono font-bold text-slate-400 mb-3">LANGKAH 01</div>
                        <h3 class="font-bold text-slate-900 text-base mb-2">Input Pesanan di POS</h3>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            Pedagang memilih menu di layar kasir POS. Keranjang belanja menghitung subtotal pesanan secara otomatis.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="border border-slate-200 rounded-xl p-5 bg-slate-50 hover:bg-white hover:border-slate-300 transition">
                        <div class="text-xs font-mono font-bold text-slate-400 mb-3">LANGKAH 02</div>
                        <h3 class="font-bold text-slate-900 text-base mb-2">Tap Kartu Mahasiswa</h3>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            Mahasiswa menempelkan kartu NFC pada USB RFID/NFC reader yang terhubung ke perangkat kasir pedagang.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="border border-slate-200 rounded-xl p-5 bg-slate-50 hover:bg-white hover:border-slate-300 transition">
                        <div class="text-xs font-mono font-bold text-slate-400 mb-3">LANGKAH 03</div>
                        <h3 class="font-bold text-slate-900 text-base mb-2">Validasi Saldo & Limit</h3>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            Sistem secara atomik memverifikasi keaktifan kartu, kecukupan saldo, serta batas limit belanja harian dari wali.
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="border border-slate-200 rounded-xl p-5 bg-slate-50 hover:bg-white hover:border-slate-300 transition">
                        <div class="text-xs font-mono font-bold text-emerald-600 mb-3">SELESAI 04</div>
                        <h3 class="font-bold text-slate-900 text-base mb-2">Struk & Pemotongan Saldo</h3>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            Transaksi tervalidasi seketika. Saldo terpotong, struk transaksi tercetak/tampil, dan rekap masuk ke akun pedagang.
                        </p>
                    </div>

                </div>

                <div class="mt-8 p-4 bg-slate-100 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-600">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-slate-900">Mekanisme Cadangan:</span>
                        <span>Jika kartu rusak atau reader offline, kasir dapat menginput NIM / ID kartu secara manual atau beralih ke mode tunai.</span>
                    </div>
                    <span class="text-slate-500 font-mono">FR-9 & FR-25b Compliance</span>
                </div>

            </div>
        </section>

        <!-- ================= 4 PERAN PENGGUNA ================= -->
        <section id="peran" class="py-16 sm:py-20 border-b border-slate-200 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="max-w-2xl mb-12">
                    <h2 class="text-xs font-bold text-brand-800 uppercase tracking-widest mb-2">Hak Akses & Peran</h2>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Satu platform, melayani empat kebutuhan yang berbeda
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Role: Pedagang -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-7">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amberbrand-100 text-amberbrand-700 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-lg">Pedagang / Tenant Kantin</h3>
                                    <span class="text-xs text-slate-500">Antarmuka Aplikasi Kasir (POS)</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 text-xs font-mono font-medium">POS Terminal</span>
                        </div>
                        <ul class="space-y-2 text-xs sm:text-sm text-slate-600 mb-5">
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Manajemen katalog produk, harga, ketersediaan menu, dan kategori.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Input keranjang belanja cepat dan cetak struk nota belanja.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Pencatatan rekapitulasi penjualan harian, mingguan, dan bulanan.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Role: Mahasiswa -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-7">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-800 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-lg">Mahasiswa Polbeng</h3>
                                    <span class="text-xs text-slate-500">Pengguna Kartu Pintar NFC</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 text-xs font-mono font-medium">NFC Card</span>
                        </div>
                        <ul class="space-y-2 text-xs sm:text-sm text-slate-600 mb-5">
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Pembayaran instan sekali tempel tanpa perlu membawa dompet fisik.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Cek sisa saldo kartu dan histori seluruh transaksi belanja secara transparan.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Top-up saldo mandiri melalui Midtrans (QRIS, GoPay, ShopeePay, Virtual Account).</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Role: Wali Mahasiswa -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-7">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-lg">Wali / Orang Tua</h3>
                                    <span class="text-xs text-slate-500">Monitoring & Pengendalian Pengeluaran</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 text-xs font-mono font-medium">Monitoring</span>
                        </div>
                        <ul class="space-y-2 text-xs sm:text-sm text-slate-600 mb-5">
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Pantau mutasi saldo dan histori jajan anak secara langsung (real-time).</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Atur batas limit belanja harian atau per transaksi untuk mencegah boros.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Dompet digital wali untuk transfer saldo cepat ke kartu anak berdasarkan NIM.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Role: Admin Inkubator -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-7">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-lg">Admin Unit Inkubator</h3>
                                    <span class="text-xs text-slate-500">Otoritas Pengawasan & Approval</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-700 text-xs font-mono font-medium">Administrator</span>
                        </div>
                        <ul class="space-y-2 text-xs sm:text-sm text-slate-600 mb-5">
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Verifikasi dan persetujuan (approval) pendaftaran pedagang dan gerai baru.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Penerbitan, pemetaan UID, aktivasi, dan penggantian kartu NFC yang hilang.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Pemantauan jatuh tempo sewa gerai dan audit perputaran uang di food area.</span>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
        </section>

        <!-- ================= TENANT & KATALOG MENU AKTIF ================= -->
        <section id="menu" class="py-16 sm:py-20 border-b border-slate-200 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
                    <div>
                        <h2 class="text-xs font-bold text-brand-800 uppercase tracking-widest mb-2">Unit Bisnis Kantin</h2>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Pilihan Makanan & Minuman di Polbeng Food Area
                        </p>
                    </div>
                    <span class="text-xs font-mono text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg border border-slate-200 self-start sm:self-auto">
                        Tersedia Pembayaran Tap NFC
                    </span>
                </div>

                <!-- Menu Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse($menus as $menu)
                        <div class="border border-slate-200 rounded-xl overflow-hidden bg-white hover:border-slate-300 transition flex flex-col justify-between">
                            <div>
                                <div class="h-40 bg-slate-100 relative overflow-hidden flex items-center justify-center">
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="text-center p-4">
                                            <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-500 mx-auto flex items-center justify-center font-bold text-sm mb-1">
                                                🍜
                                            </div>
                                            <span class="text-xs text-slate-400">Polbeng Food Area</span>
                                        </div>
                                    @endif
                                    @if($menu->category)
                                        <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded bg-white/90 backdrop-blur-xs text-[11px] font-bold text-slate-700 shadow-2xs border border-slate-200">
                                            {{ ucfirst($menu->category) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <div class="text-[11px] font-bold text-brand-800 uppercase tracking-wider mb-1">
                                        {{ $menu->pedagang->name ?? 'Stan Kantin Polbeng' }}
                                    </div>
                                    <h4 class="font-extrabold text-slate-900 text-base leading-snug mb-1">
                                        {{ $menu->name }}
                                    </h4>
                                    <p class="text-slate-500 text-xs line-clamp-2 leading-relaxed">
                                        {{ $menu->description ?? 'Tersedia di unit food area Polbeng.' }}
                                    </p>
                                </div>
                            </div>
                            <div class="px-4 pb-4 pt-2 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs text-slate-500">Harga:</span>
                                <span class="font-mono font-bold text-base text-slate-900">
                                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-4 border border-dashed border-slate-300 rounded-xl p-8 text-center text-slate-500 text-sm">
                            Belum ada katalog menu yang ditampilkan. Silakan masuk sebagai pedagang untuk menambahkan menu.
                        </div>
                    @endforelse
                </div>

            </div>
        </section>

        <!-- ================= CALL TO ACTION (HONEST & PRACTICAL) ================= -->
        <section class="py-16 sm:py-20 bg-slate-900 text-white">
            <div class="max-w-4xl mx-auto px-4 text-center space-y-6">
                
                <div class="inline-block px-3 py-1 rounded bg-slate-800 border border-slate-700 text-slate-300 text-xs font-mono">
                    PORTAL LOGIN PENGGUNA
                </div>

                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                    Masuk ke Sistem Polbeng Food Area
                </h2>

                <p class="text-slate-400 text-sm sm:text-base leading-relaxed max-w-xl mx-auto">
                    Akses dashboard pedagang kasir, pengecekan saldo mahasiswa, manajemen limit orang tua, atau otorisasi admin melalui satu portal terpadu.
                </p>

                <div class="pt-2">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-brand-700 text-white font-bold text-sm sm:text-base hover:bg-brand-600 transition shadow-sm focus:ring-2 focus:ring-brand-400 focus:outline-none">
                        <span>Buka Halaman Login</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="text-xs text-slate-500 pt-4">
                    Belum memiliki kartu NFC atau belum terdaftar sebagai pedagang? Silakan hubungi pengelola inkubator kampus.
                </div>

            </div>
        </section>

    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-slate-200 text-slate-600 text-xs py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                
                <!-- Col 1 -->
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Polbeng" class="w-7 h-7 object-contain">
                        <span class="font-extrabold text-slate-900 text-sm tracking-tight">POLBENG FOOD AREA</span>
                    </div>
                    <p class="text-slate-500 max-w-sm leading-relaxed">
                        Sistem Point of Sale (POS) & Transaksi Non-Tunai Berbasis NFC pada Unit Usaha Inkubator Bisnis Politeknik Negeri Bengkalis.
                    </p>
                    <div class="text-slate-400 text-[11px]">
                        Jl. Bathin Alam, Sungai Alam, Bengkalis, Riau 28711
                    </div>
                </div>

                <!-- Col 2 -->
                <div>
                    <div class="font-bold text-slate-900 mb-3 uppercase tracking-wider text-[11px]">Navigasi</div>
                    <ul class="space-y-2">
                        <li><a href="#fitur" class="hover:text-brand-800 transition">Fitur Utama</a></li>
                        <li><a href="#cara-kerja" class="hover:text-brand-800 transition">Alur Transaksi NFC</a></li>
                        <li><a href="#peran" class="hover:text-brand-800 transition">4 Peran Pengguna</a></li>
                        <li><a href="#menu" class="hover:text-brand-800 transition">Tenant & Menu</a></li>
                    </ul>
                </div>

                <!-- Col 3 -->
                <div>
                    <div class="font-bold text-slate-900 mb-3 uppercase tracking-wider text-[11px]">Akses Masuk</div>
                    <ul class="space-y-2">
                        <li><a href="{{ route('login') }}" class="hover:text-brand-800 transition">Login Pedagang (POS)</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-brand-800 transition">Login Mahasiswa</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-brand-800 transition">Login Wali Murid</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-brand-800 transition">Login Administrator</a></li>
                    </ul>
                </div>

            </div>

            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2 text-slate-400 text-[11px]">
                <div>
                    &copy; {{ date('Y') }} Unit Usaha Inkubator Bisnis Politeknik Negeri Bengkalis. Seluruh hak cipta dilindungi.
                </div>
                <div class="flex items-center gap-4">
                    <span>Sistem POS & Pembayaran NFC</span>
                    <span>•</span>
                    <span>Metode DevOps</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Simple Mobile Navigation Script -->
    <script>
        document.getElementById('mobile-toggle-btn').addEventListener('click', function() {
            var menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
