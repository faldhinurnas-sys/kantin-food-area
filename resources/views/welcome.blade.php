<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polbeng Food Area — Kantin & Kuliner Kampus Politeknik Negeri Bengkalis</title>
    <meta name="description" content="Profil resmi dan pusat kuliner kantin mahasiswa Polbeng Food Area di kawasan Politeknik Negeri Bengkalis.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Typography: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS with Custom Theme Tokens -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brandblue: {
                            50: '#eef6ff',
                            100: '#d9eaff',
                            400: '#4da1fc',
                            500: '#207df8',
                            600: '#1b6eed',
                            700: '#1557c0',
                            800: '#16489b',
                            900: '#173f7c',
                        },
                        brandlime: {
                            300: '#dcfc62',
                            400: '#c5f231',
                            500: '#aee31e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
            -webkit-font-smoothing: antialiased;
        }
        .paperclip-shadow {
            filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.18));
        }
    </style>
</head>
<body class="bg-[#f0f4f9] sm:bg-[#207df8] text-slate-900 font-sans min-h-screen py-0 sm:py-6 lg:py-8 px-0 sm:px-4 lg:px-6 antialiased selection:bg-brandlime-400 selection:text-slate-950">

    <!-- ================= MAIN CONTAINER (FRAMED LIKE REFERENCE DESIGN) ================= -->
    <div class="max-w-[1240px] mx-auto bg-white sm:rounded-[36px] lg:rounded-[44px] shadow-2xl overflow-hidden border border-slate-100 flex flex-col min-h-screen sm:min-h-0">

        <!-- ================= TOP HEADER / NAVBAR ================= -->
        <header class="px-5 sm:px-8 lg:px-12 py-5 border-b border-slate-100 bg-white/95 backdrop-blur-md sticky top-0 z-40">
            <div class="flex items-center justify-between gap-4">
                
                <!-- Brand / Logo Pill -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group focus:outline-none">
                    <div class="h-8 sm:h-10 flex items-center shrink-0">
                        <img src="{{ asset('images/polbeng-header.png') }}" alt="Logo Politeknik Negeri Bengkalis" class="h-7 sm:h-9 w-auto object-contain">
                    </div>
                    <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-50 border border-slate-200 p-0.5 flex items-center justify-center shrink-0 shadow-2xs">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Food Area" class="w-full h-full object-contain">
                        </div>
                        <span class="font-extrabold text-xs sm:text-sm text-slate-900 tracking-tight">
                            Food Area
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Friendly & Non-Technical) -->
                <nav class="hidden md:flex items-center gap-6 lg:gap-8 text-xs lg:text-[13px] font-bold text-slate-600">
                    <a href="#profil" class="hover:text-brandblue-600 transition">Tentang Kantin</a>
                    <a href="#layanan" class="hover:text-brandblue-600 transition">Keunggulan</a>
                    <a href="#katalog-menu" class="hover:text-brandblue-600 transition">Menu Favorit</a>
                    <a href="#lokasi" class="hover:text-brandblue-600 transition flex items-center gap-1 text-brandblue-600">
                        <span>📍 Lokasi &amp; Rute</span>
                    </a>
                </nav>

                <!-- Action Button: Pill "Masuk" -->
                <div class="flex items-center gap-2.5">
                    @auth
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2 rounded-full bg-brandblue-600 text-white text-xs font-bold hover:bg-brandblue-700 transition shadow-sm hover:shadow">
                            Buka Akun Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2 rounded-full bg-brandblue-600 text-white text-xs font-bold hover:bg-brandblue-700 transition shadow-sm hover:shadow">
                            Masuk
                        </a>
                    @endauth

                    <!-- Mobile Menu Trigger -->
                    <button id="mobile-nav-toggle" class="md:hidden p-2 rounded-full hover:bg-slate-100 text-slate-700 border border-slate-200" aria-label="Buka Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div id="mobile-nav-menu" class="hidden md:hidden pt-4 pb-2 border-t border-slate-100 mt-3 space-y-2 text-sm font-semibold text-slate-700">
                <a href="#profil" class="block px-3 py-1.5 rounded-lg hover:bg-slate-50">Tentang Kantin</a>
                <a href="#layanan" class="block px-3 py-1.5 rounded-lg hover:bg-slate-50">Keunggulan</a>
                <a href="#katalog-menu" class="block px-3 py-1.5 rounded-lg hover:bg-slate-50">Menu Favorit</a>
                <a href="#lokasi" class="block px-3 py-1.5 rounded-lg hover:bg-slate-50 text-brandblue-600 font-bold">📍 Lokasi &amp; Rute</a>
                <a href="{{ route('login') }}" class="block text-center mt-2 px-4 py-2 bg-brandblue-600 text-white rounded-full font-bold text-xs">
                    Masuk ke Akun
                </a>
            </div>
        </header>

        <main class="flex-grow">

            <!-- ================= SECTION 1: HERO SECTION ================= -->
            <section id="profil" class="px-5 sm:px-8 lg:px-12 py-10 sm:py-14 lg:py-16 bg-white overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
                    
                    <!-- Left Column: Headline & Action Pill -->
                    <div class="lg:col-span-6 space-y-6">
                        
                        <!-- Official Campus Spotlight Chip -->
                        <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-slate-50 border border-slate-200 shadow-2xs">
                            <img src="{{ asset('images/polbeng-header.png') }}" alt="Politeknik Negeri Bengkalis" class="h-4 sm:h-5 w-auto object-contain">
                            <span class="w-px h-3.5 bg-slate-300"></span>
                            <span class="text-[11px] font-extrabold text-slate-800">Unit Usaha Inkubator Kampus</span>
                        </div>

                        <h1 class="text-3xl sm:text-5xl lg:text-[52px] font-black text-slate-900 tracking-tight leading-[1.08]">
                            Kantin Kampus <br class="hidden sm:inline">
                            Polbeng Food Area
                        </h1>

                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-xl">
                            Pusat santap resmi mahasiswa dan warga Politeknik Negeri Bengkalis. Nikmati aneka makanan lezat, minuman segar, dan camilan setiap hari kuliah dengan pembayaran praktis cukup sekali tempel Kartu Mahasiswa.
                        </p>

                        <!-- Rounded Pill CTA Buttons -->
                        <div class="pt-2 flex flex-wrap items-center gap-4">
                            <a href="#katalog-menu" class="group inline-flex items-center gap-3 pl-6 pr-2.5 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-900 font-bold text-xs sm:text-sm transition">
                                <span>Lihat Pilihan Menu</span>
                                <span class="w-8 h-8 rounded-full bg-brandblue-600 text-white flex items-center justify-center group-hover:translate-x-0.5 transition-transform shadow-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </span>
                            </a>
                            <a href="#lokasi" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-brandblue-600 hover:text-brandblue-800 transition">
                                <span>Lihat Petunjuk Rute Lokasi</span>
                                <span>📍</span>
                            </a>
                        </div>

                        <!-- General Public Friendly Highlights -->
                        <div class="grid grid-cols-3 gap-4 pt-3 text-xs text-slate-600 border-t border-slate-100">
                            <div>
                                <span class="font-black text-slate-900 text-sm sm:text-base">Bebas Antre</span>
                                <div class="text-[11px] text-slate-400 mt-0.5">Gak perlu nunggu kembalian</div>
                            </div>
                            <div class="border-l border-slate-200 pl-4">
                                <span class="font-black text-slate-900 text-sm sm:text-base">Menu Lengkap</span>
                                <div class="text-[11px] text-slate-400 mt-0.5">Makanan, jus &amp; camilan</div>
                            </div>
                            <div class="border-l border-slate-200 pl-4">
                                <span class="font-black text-slate-900 text-sm sm:text-base">Mudah &amp; Aman</span>
                                <div class="text-[11px] text-slate-400 mt-0.5">Cukup bawa Kartu Mahasiswa</div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Column: Tilted Photo with Kinetic Lime Accent & Speech Tag -->
                    <div class="lg:col-span-6 relative flex items-center justify-center lg:justify-end py-6">
                        
                        <!-- Kinetic Lime Green Accent Shape Behind Image -->
                        <div class="absolute w-[280px] sm:w-[360px] h-[320px] sm:h-[400px] bg-brandlime-400 rounded-[48px] -rotate-6 lg:-rotate-12 translate-x-3 translate-y-2 opacity-95 pointer-events-none"></div>

                        <!-- Main Photo Card (Tilted with White Border & Shadow) -->
                        <div class="relative z-10 w-[270px] sm:w-[340px] aspect-[4/5] bg-slate-900 rounded-[32px] overflow-hidden shadow-2xl border-[6px] border-white rotate-2 sm:rotate-3 transition hover:rotate-0 duration-300">
                            <img 
                                src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=700&q=80" 
                                alt="Mahasiswa di Kantin Polbeng" 
                                class="w-full h-full object-cover"
                                loading="eager"
                                width="340"
                                height="425"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
                            <div class="absolute bottom-4 left-4 right-4 text-white">
                                <div class="text-[10px] font-mono text-brandlime-300 uppercase tracking-widest">KANTIN POLBENG</div>
                                <div class="font-bold text-xs sm:text-sm">Tempat santai &amp; makan favorit mahasiswa</div>
                            </div>
                        </div>

                        <!-- Floating Speech Bubble Tag -->
                        <div class="absolute -bottom-2 sm:bottom-4 left-0 sm:left-4 z-20 max-w-[250px] sm:max-w-[280px] bg-white/95 backdrop-blur-md rounded-2xl p-3.5 shadow-xl border border-slate-100 flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-brandblue-50 text-brandblue-600 flex items-center justify-center shrink-0 text-sm font-bold">
                                🍽️
                            </div>
                            <div class="text-[11px] leading-snug text-slate-800">
                                <span class="font-extrabold text-slate-900">Jajan Cepat:</span> Tempel kartu mahasiswa di stan kantin, pesanan siap dinikmati.
                            </div>
                        </div>

                        <!-- Indicator Badge on Top Right -->
                        <div class="absolute -top-3 right-0 sm:right-6 z-20 bg-white rounded-full px-3 py-1.5 shadow-md border border-slate-100 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[11px] font-extrabold text-slate-800">Buka Setiap Hari Kuliah</span>
                        </div>

                    </div>

                </div>
            </section>

            <!-- ================= CAMPUS INSTITUTIONAL SPOTLIGHT SECTION ================= -->
            <section class="border-y border-slate-100 bg-slate-50/80 px-5 sm:px-8 lg:px-12 py-7 sm:py-8">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-6 sm:gap-8">
                    
                    <div class="flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
                        <div class="p-3 bg-white rounded-2xl border border-slate-200/90 shadow-sm flex items-center justify-center shrink-0 hover:shadow-md transition">
                            <img src="{{ asset('images/polbeng-header.png') }}" alt="Logo Resmi Politeknik Negeri Bengkalis" class="h-10 sm:h-12 lg:h-14 w-auto object-contain max-w-[240px] sm:max-w-[280px]">
                        </div>

                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                <span class="px-2.5 py-0.5 rounded-full bg-brandblue-50 text-brandblue-700 font-extrabold text-[10px] uppercase tracking-wider border border-brandblue-100">
                                    Pusat Kuliner Kampus
                                </span>
                                <span class="text-xs text-slate-400 hidden sm:inline">•</span>
                                <span class="text-xs font-bold text-slate-700">Politeknik Negeri Bengkalis</span>
                            </div>
                            <h3 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">
                                Bersih, Terjangkau, dan Terpercaya untuk Seluruh Warga Kampus
                            </h3>
                            <p class="text-xs sm:text-[13px] text-slate-500 max-w-2xl leading-relaxed">
                                Dikelola langsung di bawah Unit Usaha Inkubator Bisnis Polbeng untuk memberikan kenyamanan makan bagi mahasiswa, dosen, dan staf kampus setiap hari.
                            </p>
                        </div>
                    </div>

                    <!-- Location Shortcut Pill -->
                    <a href="#lokasi" class="flex items-center gap-3 shrink-0 bg-white px-5 py-3 rounded-2xl border border-slate-200 shadow-xs hover:border-brandblue-400 transition group">
                        <div class="w-9 h-9 rounded-xl bg-brandlime-400 text-slate-900 flex items-center justify-center font-black text-base shrink-0 group-hover:scale-110 transition-transform">
                            📍
                        </div>
                        <div class="text-left font-mono">
                            <div class="text-[10px] text-slate-400 font-bold uppercase">Lokasi Kantin</div>
                            <div class="text-xs sm:text-sm font-extrabold text-slate-900 group-hover:text-brandblue-600 transition">Rusunawa Polbeng →</div>
                        </div>
                    </a>

                </div>
            </section>

            <!-- ================= SECTION 2: VIBRANT BLUE BOX WITH PAPERCLIP CARDS ================= -->
            <section id="layanan" class="px-3 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div class="bg-brandblue-600 rounded-[28px] sm:rounded-[36px] p-6 sm:p-10 lg:p-12 text-white shadow-inner">
                    
                    <!-- Section Header -->
                    <div class="max-w-xl mb-10">
                        <div class="text-xs font-mono font-bold uppercase tracking-widest text-brandblue-200 mb-2">
                            KEUNGGULAN KANTIN KAMPUS
                        </div>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight leading-tight">
                            Kenyamanan untuk Mahasiswa &amp; Pengunjung
                        </h2>
                    </div>

                    <!-- 4 Paperclip White Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-5">
                        
                        <!-- Card 1: Stan Menu Lengkap -->
                        <div class="relative bg-white text-slate-900 rounded-[22px] p-5 pt-7 shadow-lg flex flex-col justify-between min-h-[220px] transition hover:-translate-y-1 duration-200">
                            <div class="absolute -top-3.5 right-6 z-10 pointer-events-none paperclip-shadow">
                                <svg class="w-5 h-9 text-slate-300" viewBox="0 0 24 40" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 14v14a6 6 0 0 0 12 0V8a4 4 0 0 0-8 0v20a2 2 0 0 0 4 0V14" />
                                </svg>
                            </div>
                            
                            <div>
                                <h3 class="font-extrabold text-sm sm:text-base text-slate-900 mb-1 leading-snug">
                                    Pilihan Menu Beragam
                                </h3>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    Mulai dari sarapan, nasi goreng, mie kuah hangat, aneka jus buah segar, hingga camilan favorit.
                                </p>
                            </div>

                            <div class="my-4 flex items-center justify-center text-4xl">
                                🍳
                            </div>

                            <div class="pt-2">
                                <span class="inline-block px-3 py-1 rounded-full bg-slate-900 text-white text-[11px] font-bold font-mono">
                                    Menu Higienis
                                </span>
                            </div>
                        </div>

                        <!-- Card 2: Bayar Tanpa Uang Tunai -->
                        <div class="relative bg-white text-slate-900 rounded-[22px] p-5 pt-7 shadow-lg flex flex-col justify-between min-h-[220px] transition hover:-translate-y-1 duration-200">
                            <div class="absolute -top-3.5 right-6 z-10 pointer-events-none paperclip-shadow">
                                <svg class="w-5 h-9 text-slate-300" viewBox="0 0 24 40" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 14v14a6 6 0 0 0 12 0V8a4 4 0 0 0-8 0v20a2 2 0 0 0 4 0V14" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="font-extrabold text-sm sm:text-base text-slate-900 mb-1 leading-snug">
                                    Cukup Kartu Mahasiswa
                                </h3>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    Gak perlu bawa dompet tebal atau takut uang hilang. Tempel kartu langsung di meja kasir.
                                </p>
                            </div>

                            <div class="my-4 flex items-center justify-center text-4xl">
                                💳
                            </div>

                            <div class="pt-2">
                                <span class="inline-block px-3 py-1 rounded-full bg-slate-900 text-white text-[11px] font-bold font-mono">
                                    Praktis &amp; Aman
                                </span>
                            </div>
                        </div>

                        <!-- Card 3: Mudah untuk Orang Tua -->
                        <div class="relative bg-white text-slate-900 rounded-[22px] p-5 pt-7 shadow-lg flex flex-col justify-between min-h-[220px] transition hover:-translate-y-1 duration-200">
                            <div class="absolute -top-3.5 right-6 z-10 pointer-events-none paperclip-shadow">
                                <svg class="w-5 h-9 text-slate-300" viewBox="0 0 24 40" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 14v14a6 6 0 0 0 12 0V8a4 4 0 0 0-8 0v20a2 2 0 0 0 4 0V14" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="font-extrabold text-sm sm:text-base text-slate-900 mb-1 leading-snug">
                                    Kenyamanan Orang Tua
                                </h3>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    Orang tua bisa mengisi uang saku anak dari HP dan memantau agar jajan tetap hemat serta teratur.
                                </p>
                            </div>

                            <div class="my-4 flex items-center justify-center text-4xl">
                                🛡️
                            </div>

                            <div class="pt-2">
                                <span class="inline-block px-3 py-1 rounded-full bg-slate-900 text-white text-[11px] font-bold font-mono">
                                    Pantau dari Rumah
                                </span>
                            </div>
                        </div>

                        <!-- Card 4: Suasana Nyaman -->
                        <div class="relative bg-white text-slate-900 rounded-[22px] p-5 pt-7 shadow-lg flex flex-col justify-between min-h-[220px] transition hover:-translate-y-1 duration-200">
                            <div class="absolute -top-3.5 right-6 z-10 pointer-events-none paperclip-shadow">
                                <svg class="w-5 h-9 text-slate-300" viewBox="0 0 24 40" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 14v14a6 6 0 0 0 12 0V8a4 4 0 0 0-8 0v20a2 2 0 0 0 4 0V14" />
                                </svg>
                            </div>

                            <div>
                                <h3 class="font-extrabold text-sm sm:text-base text-slate-900 mb-1 leading-snug">
                                    Suasana Bersih &amp; Asri
                                </h3>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    Tempat santai bersama teman sekelas, nugas bareng, atau istirahat sejenak di lingkungan kampus.
                                </p>
                            </div>

                            <div class="my-4 flex items-center justify-center text-4xl">
                                🏫
                            </div>

                            <div class="pt-2">
                                <span class="inline-block px-3 py-1 rounded-full bg-slate-900 text-white text-[11px] font-bold font-mono">
                                    Area Kampus Asri
                                </span>
                            </div>
                        </div>

                    </div>

                </div>
            </section>

            <!-- ================= SECTION 3: MENU & TENANT KATALOG ================= -->
            <section id="katalog-menu" class="px-5 sm:px-8 lg:px-12 py-12 sm:py-16 bg-white">
                
                <div class="flex items-center justify-between mb-8 sm:mb-10">
                    <div>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
                            Pilihan Menu Favorit Mahasiswa
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Hidangan segar dan terlaris dari gerai stan di Polbeng Food Area
                        </p>
                    </div>

                    <a href="#lokasi" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold text-slate-600 hover:text-brandblue-600 transition">
                        <span>📍 Langsung ke Kantin</span>
                    </a>
                </div>

                <!-- 3-Column Grid of Course/Product Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @forelse($menus as $menu)
                        <div class="bg-white border border-slate-200 rounded-[24px] p-4 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                            
                            <div>
                                <div class="relative w-full aspect-[16/10] bg-slate-100 rounded-[18px] overflow-hidden mb-4">
                                    @if($menu->image)
                                        <img 
                                            src="{{ asset('storage/' . $menu->image) }}" 
                                            alt="{{ $menu->name }}" 
                                            class="w-full h-full object-cover"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-slate-50 p-4 text-center">
                                            <span class="text-3xl mb-1">🍲</span>
                                            <span class="text-[11px] font-mono font-semibold text-slate-400">Polbeng Food Area</span>
                                        </div>
                                    @endif

                                    <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5">
                                        <span class="px-2.5 py-0.5 rounded-full bg-white/90 backdrop-blur-xs text-[10px] font-bold text-slate-800 shadow-2xs">
                                            Tersedia
                                        </span>
                                        @if(($menu->transaction_items_sum_quantity ?? 0) > 0)
                                            <span class="px-2.5 py-0.5 rounded-full bg-slate-900/85 backdrop-blur-xs text-[10px] font-bold text-white shadow-2xs">
                                                Terlaris
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full bg-brandblue-600/90 backdrop-blur-xs text-[10px] font-bold text-white shadow-2xs">
                                                Rekomendasi
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <h3 class="font-extrabold text-base sm:text-lg text-slate-900 leading-snug mb-2">
                                    {{ $menu->name }}
                                </h3>

                                <div class="flex flex-wrap items-center gap-1.5 mb-4 text-[10px] font-bold">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        #{{ ucfirst($menu->category ?? 'Makanan') }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md bg-brandblue-50 text-brandblue-700 border border-brandblue-100">
                                        #Segar
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-100">
                                        #Halal
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-2 text-[11px] py-2.5 border-t border-slate-100 mb-4 text-slate-500 font-mono">
                                    <div>
                                        <span class="text-slate-400">Status:</span>
                                        <div class="font-bold text-slate-800 flex items-center gap-1">
                                            @if(($menu->transaction_items_sum_quantity ?? 0) > 0)
                                                <span class="text-emerald-600 font-extrabold">🔥 Terlaris</span>
                                            @else
                                                <span class="text-slate-700 font-bold">Pilihan Stan</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-slate-400">Penjualan:</span>
                                        <div class="font-bold text-slate-900">
                                            {{ $menu->transaction_items_sum_quantity ?? 0 }} porsi terjual
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Stan Pedagang
                                    </div>
                                    <div class="text-xs sm:text-sm font-extrabold text-slate-800 truncate">
                                        {{ $menu->pedagang->name ?? 'Stan Kantin Polbeng' }}
                                    </div>
                                </div>

                                <a href="#lokasi" class="px-4 py-2 rounded-xl bg-brandblue-600 hover:bg-brandblue-700 text-white font-bold text-xs flex items-center gap-1.5 transition shrink-0 shadow-xs">
                                    <span>Kunjungi Stan</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>

                        </div>
                    @empty
                        <div class="col-span-3 border border-dashed border-slate-300 rounded-2xl p-8 text-center text-slate-500 text-sm">
                            Belum ada menu yang ditampilkan. Silakan masuk untuk melihat data selengkapnya.
                        </div>
                    @endforelse
                </div>

            </section>

            <!-- ================= SECTION 4: LOKASI & RUTE GOOGLE MAPS ================= -->
            <section id="lokasi" class="px-5 sm:px-8 lg:px-12 py-12 sm:py-16 bg-slate-50 border-t border-slate-100">
                <div class="bg-white border border-slate-200 rounded-[28px] sm:rounded-[36px] p-6 sm:p-10 lg:p-12 shadow-sm">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        
                        <!-- Left: Info Lokasi & Rute -->
                        <div class="lg:col-span-7 space-y-5">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brandblue-50 text-brandblue-700 text-xs font-bold border border-brandblue-100">
                                <span class="w-2 h-2 rounded-full bg-brandblue-600"></span>
                                Rute &amp; Lokasi Kampus
                            </div>

                            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                                Lokasi &amp; Petunjuk Arah ke Polbeng Food Area
                            </h2>

                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-xl">
                                Kantin Polbeng Food Area terletak strategis di dalam lingkungan kampus Politeknik Negeri Bengkalis, tepatnya di area Rusunawa. Suasana yang asri dan sejuk menjadikannya tempat ideal untuk sarapan, santap siang, maupun bersantai bersama rekan mahasiswa.
                            </p>

                            <!-- Detail Alamat & Jam -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <div class="text-xs font-mono font-bold text-slate-400 uppercase mb-1">📍 Alamat Lengkap</div>
                                    <div class="text-xs sm:text-sm font-bold text-slate-800">
                                        Kawasan Rusunawa Polbeng
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        Jl. Bathin Alam, Sungai Alam, Bengkalis, Riau 28711
                                    </div>
                                </div>

                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                                    <div class="text-xs font-mono font-bold text-slate-400 uppercase mb-1">🕒 Waktu Buka</div>
                                    <div class="text-xs sm:text-sm font-bold text-slate-800">
                                        Senin – Sabtu
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        07.30 – 17.00 WIB (Setiap Hari Kuliah)
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Rute Langsung ke Link Google Maps -->
                            <div class="pt-2">
                                <a 
                                    href="https://share.google/jY8dDF9yt6caXMZql" 
                                    target="_blank" 
                                    rel="noopener noreferrer" 
                                    class="group inline-flex items-center gap-3 px-6 py-3.5 rounded-full bg-brandblue-600 hover:bg-brandblue-700 text-white font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition focus:outline-none"
                                >
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Buka Rute Langsung di Google Maps</span>
                                    <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </span>
                                </a>
                            </div>

                        </div>

                        <!-- Right: Interactive Map Card Visual -->
                        <div class="lg:col-span-5">
                            <div class="relative bg-gradient-to-br from-brandblue-50 to-slate-100 rounded-3xl p-6 border border-slate-200 overflow-hidden text-center sm:text-left">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
                                        <span class="text-xs font-bold text-slate-800">Tujuan Lokasi</span>
                                    </div>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-white text-slate-600 border border-slate-200">Google Maps</span>
                                </div>

                                <div class="my-6 py-6 border-y border-slate-200/80 space-y-2">
                                    <div class="text-4xl sm:text-5xl">📍</div>
                                    <div class="font-black text-lg sm:text-xl text-slate-900">
                                        Polbeng Food Area
                                    </div>
                                    <p class="text-xs text-slate-500">
                                        Kawasan Rusunawa Kampus Politeknik Negeri Bengkalis
                                    </p>
                                </div>

                                <a 
                                    href="https://share.google/jY8dDF9yt6caXMZql" 
                                    target="_blank" 
                                    rel="noopener noreferrer" 
                                    class="block w-full py-2.5 px-4 rounded-xl bg-white hover:bg-slate-50 border border-slate-300 text-slate-800 font-bold text-xs text-center transition shadow-2xs"
                                >
                                    Klik di Sini untuk Navigasi Rute →
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- ================= SECTION 5: CALL TO ACTION FOOTER BANNER ================= -->
            <section class="px-5 sm:px-8 lg:px-12 py-10 bg-slate-50 border-t border-slate-100">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 bg-white border border-slate-200 rounded-3xl p-6 sm:p-8">
                    <div class="space-y-1 text-center sm:text-left">
                        <div class="text-xs font-mono font-bold text-brandblue-600 uppercase tracking-wider">
                            KANTIN POLBENG FOOD AREA
                        </div>
                        <h3 class="font-extrabold text-xl text-slate-900">
                            Ayo Kunjungi &amp; Santap di Kantin Kampus!
                        </h3>
                        <p class="text-xs text-slate-500 max-w-md">
                            Tempat favorit berkumpul, bersantap, dan istirahat mahasiswa di Politeknik Negeri Bengkalis.
                        </p>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        <a href="{{ route('login') }}" class="px-6 py-3 rounded-full bg-brandblue-600 text-white font-bold text-xs sm:text-sm hover:bg-brandblue-700 transition shadow-sm">
                            Masuk ke Akun Portal →
                        </a>
                    </div>
                </div>
            </section>

        </main>

        <!-- ================= FOOTER ================= -->
        <footer class="px-5 sm:px-8 lg:px-12 py-8 bg-white border-t border-slate-100 text-xs text-slate-500">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-left">
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <img src="{{ asset('images/polbeng-header.png') }}" alt="Politeknik Negeri Bengkalis" class="h-8 w-auto object-contain">
                    <div class="h-4 w-px bg-slate-200 hidden sm:block"></div>
                    <span class="font-bold text-slate-700">Kantin Polbeng Food Area</span>
                </div>
                <div class="flex flex-wrap items-center justify-center sm:justify-end gap-3 text-slate-400 text-[11px]">
                    <span>Kawasan Rusunawa Kampus Polbeng</span>
                    <span>•</span>
                    <span>Buka 07.30 - 17.00 WIB</span>
                    <span>•</span>
                    <span>&copy; {{ date('Y') }} Polbeng</span>
                </div>
            </div>
        </footer>

    </div>

    <!-- Minimal JavaScript for Mobile Menu Toggle (0 overhead) -->
    <script>
        document.getElementById('mobile-nav-toggle').addEventListener('click', function() {
            var menu = document.getElementById('mobile-nav-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
