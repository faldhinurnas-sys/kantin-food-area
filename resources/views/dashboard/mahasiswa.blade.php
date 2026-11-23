@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="min-h-screen w-full flex flex-col md:flex-row bg-slate-100/70 text-gray-800">

    <!-- MOBILE TOPBAR (Tampil hanya di layar kecil < md) -->
    <header class="md:hidden bg-primary text-white px-4 py-3.5 flex items-center justify-between sticky top-0 z-40 shadow-md">
        <div class="flex items-center space-x-2.5">
            <div class="w-9 h-9 rounded-xl bg-white p-1 flex items-center justify-center shadow-inner shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold tracking-wide text-base">KANTIN NFC</span>
                <span class="text-[10px] bg-blue-500 text-white font-bold px-1.5 py-0.5 rounded ml-1.5 uppercase">Mahasiswa</span>
            </div>
        </div>
        <button type="button" onclick="toggleMobileSidebar()" class="p-2 rounded-lg bg-blue-800 hover:bg-blue-700 focus:outline-none transition" aria-label="Toggle Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </header>

    <!-- BACKDROP MOBILE -->
    <div id="sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity"></div>

    <!-- SIDEBAR VERTIKAL KIRI -->
    <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:static md:h-screen md:sticky md:top-0 shrink-0 shadow-sm">
        
        <!-- SIDEBAR BRAND & LOGO -->
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-gray-200 p-1 flex items-center justify-center shadow-xs shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-wider text-gray-900 leading-none">KANTIN NFC</h1>
                    <div class="flex items-center mt-1 space-x-1.5">
                        <span class="px-2 py-0.5 bg-blue-100 text-primary rounded-full text-[10px] font-extrabold uppercase tracking-wide">MAHASISWA</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                </div>
            </div>
            <!-- Tombol Tutup di Mobile -->
            <button type="button" onclick="toggleMobileSidebar()" class="md:hidden text-gray-400 hover:text-gray-600 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- INFO PROFIL MAHASISWA -->
        <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/80">
            <div class="flex items-center space-x-3">
                @if(Auth::user()->avatar_url)
                    <img id="sidebar-avatar-img" src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-11 h-11 rounded-full object-cover border border-primary/20 shadow-xs shrink-0">
                @else
                    <div id="sidebar-avatar-fallback" class="w-11 h-11 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-base shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-bold text-gray-900 truncate" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500 truncate flex items-center mt-0.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                        Akun Mahasiswa Aktif
                    </div>
                </div>
            </div>
            <!-- Tombol Edit Profil -->
            <button type="button" onclick="switchTab('profil')" class="mt-3 w-full flex items-center justify-center space-x-1.5 py-1.5 px-3 rounded-lg text-xs font-bold text-primary bg-blue-50 hover:bg-blue-100 border border-blue-200 transition shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Pengaturan Profil & Sandi</span>
            </button>
        </div>

        <!-- DAFTAR MENU NAVIGASI (TABS) -->
        <div class="flex-1 overflow-y-auto px-3.5 py-4 space-y-1.5">
            <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Navigasi Utama</div>

            <!-- Tab 1: Ringkasan & Saldo -->
            <button type="button" onclick="switchTab('ringkasan')" id="btn-tab-ringkasan" class="tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-bold text-sm transition-all text-primary bg-blue-50 border-l-4 border-primary shadow-xs">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 tab-icon text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Saldo & Kartu NFC</span>
                </div>
                <span class="text-[10px] uppercase font-bold bg-blue-600 text-white px-2 py-0.5 rounded-md">Utama</span>
            </button>

            <!-- Tab 2: Top-Up Saldo -->
            <button type="button" onclick="switchTab('topup')" id="btn-tab-topup" class="tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <span>Top Up Saldo</span>
                </div>
                <span class="text-[10px] uppercase font-bold bg-emerald-500 text-white px-2 py-0.5 rounded-md">Isi Saldo</span>
            </button>

            <!-- Tab 3: Riwayat Transaksi -->
            <button type="button" onclick="switchTab('transaksi')" id="btn-tab-transaksi" class="tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Riwayat Transaksi</span>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 font-bold px-2 py-0.5 rounded-full">{{ count($transactions) }}</span>
            </button>

            <!-- Tab 3: Limit Belanja -->
            <button type="button" onclick="switchTab('limit')" id="btn-tab-limit" class="tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Batas / Limit Belanja</span>
                </div>
                @if($limit && $limit->daily_limit)
                    <span class="text-[10px] bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full">Aktif</span>
                @else
                    <span class="text-[10px] text-gray-400 font-medium">Bebas</span>
                @endif
            </button>

            <!-- Tab 4: Pengaturan Profil -->
            <button type="button" onclick="switchTab('profil')" id="btn-tab-profil" class="tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                    <span>Profil & Keamanan</span>
                </div>
            </button>

            <!-- Tombol Cek Saldo NFC Mahasiswa -->
            <button type="button" onclick="openCekSaldoModalMhs()" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-xs text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition shadow-2xs mt-2">
                <div class="flex items-center space-x-2.5">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Tap Kartu Cek Saldo</span>
                </div>
                <span class="text-[10px] uppercase font-bold bg-indigo-600 text-white px-2 py-0.5 rounded-md">Tap Kartu</span>
            </button>
        </div>

        <!-- FOOTER SIDEBAR (LOGOUT) -->
        <div class="p-4 border-t border-gray-100 bg-white">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl font-bold text-sm text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar dari Akun</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA (KANAN) -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- TOPBAR DESKTOP -->
        <header class="hidden md:flex bg-white border-b border-gray-200 px-8 py-4 items-center justify-between sticky top-0 z-30 shadow-2xs">
            <div>
                <h2 id="topbar-title" class="text-xl font-extrabold text-gray-900 tracking-tight">Saldo & Kartu NFC</h2>
                <p id="topbar-subtitle" class="text-xs text-gray-500 mt-0.5">Pantau saldo kartu NFC, informasi kartu, dan batas pengeluaran</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Tombol Cek Saldo NFC -->
                <button type="button" onclick="openCekSaldoModalMhs()" class="flex items-center space-x-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 rounded-xl text-xs font-bold transition shadow-2xs">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cek Saldo Kartu</span>
                </button>

                <div class="flex items-center space-x-2 bg-blue-50 border border-blue-200 px-3.5 py-1.5 rounded-xl text-primary font-bold text-xs shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Saldo: Rp {{ $card ? number_format($card->balance, 0, ',', '.') : '0' }}</span>
                </div>
                <div class="text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1.5 rounded-xl border border-gray-200">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
        </header>

        <!-- FLASH NOTIFIKASI -->
        <div class="px-4 sm:px-8 pt-4">
            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl shadow-xs mb-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="text-sm font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl shadow-xs mb-4 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span class="text-sm font-semibold">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-xl shadow-xs mb-4">
                    <div class="font-bold text-sm mb-1">Perhatian:</div>
                    <ul class="list-disc pl-5 text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- ISI KONTEN TAB -->
        <div class="flex-1 p-4 sm:p-8">

            <!-- ============================================================== -->
            <!-- TAB 1: SALDO & KARTU NFC (RINGKASAN)                           -->
            <!-- ============================================================== -->
            <div id="tab-ringkasan" class="tab-content block space-y-6">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- KARTU FISIK / SALDO NFC DIGITAL -->
                    <div class="lg:col-span-2 relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-blue-700 to-indigo-900 text-white p-6 sm:p-8 shadow-xl shadow-blue-900/15 border border-white/10 flex flex-col justify-between min-h-[220px]">
                        <!-- Decorative Watermark -->
                        <div class="absolute -right-8 -bottom-8 opacity-10 pointer-events-none">
                            <svg class="w-56 h-56" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                        </div>

                        <!-- Card Header -->
                        <div class="flex items-center justify-between z-10">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-7 bg-amber-400/90 rounded-md shadow-xs flex items-center justify-center border border-amber-300">
                                    <div class="w-6 h-4 border border-amber-600/60 rounded-xs flex flex-col justify-around py-0.5 px-1">
                                        <div class="h-0.5 bg-amber-700/50"></div>
                                        <div class="h-0.5 bg-amber-700/50"></div>
                                    </div>
                                </div>
                                <div>
                                    <span class="text-xs tracking-wider uppercase font-bold text-blue-200">KARTU KANTIN POLBENG</span>
                                    <span class="block text-[10px] text-blue-300 font-mono">NFC SMARTPASS</span>
                                </div>
                            </div>
                            @if($card && $card->status === 'active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/90 text-white backdrop-blur-sm shadow-xs border border-emerald-400/30">
                                    <span class="w-2 h-2 mr-1.5 rounded-full bg-white animate-pulse"></span>
                                    Aktif Siap Pakai
                                </span>
                            @elseif($card)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-500/90 text-white backdrop-blur-sm shadow-xs">
                                    Diblokir / Nonaktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-500/90 text-white backdrop-blur-sm shadow-xs">
                                    Belum Terdaftar
                                </span>
                            @endif
                        </div>

                        <!-- Saldo Display -->
                        <div class="my-6 z-10">
                            <div class="text-xs uppercase tracking-widest text-blue-200 font-semibold mb-1">Saldo Tersedia</div>
                            <div class="text-3xl sm:text-5xl font-black tracking-tight flex items-baseline space-x-2">
                                <span>Rp {{ $card ? number_format($card->balance, 0, ',', '.') : '0' }}</span>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="flex items-center justify-between z-10 pt-4 border-t border-white/15">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-blue-300 block">UID Kartu NFC</span>
                                <span class="font-mono text-xs sm:text-sm font-bold text-white tracking-widest">
                                    {{ $card ? $card->card_uid : 'TIDAK TERSEDIA' }}
                                </span>
                            </div>
                            <div class="text-center px-1">
                                <span class="text-[10px] uppercase tracking-wider text-blue-300 block">NIM Mahasiswa</span>
                                <span class="font-mono text-xs sm:text-sm font-bold text-amber-300 tracking-wider">
                                    {{ Auth::user()->nim ?? '-' }}
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] uppercase tracking-wider text-blue-300 block">Pemegang Kartu</span>
                                <span class="font-bold text-xs sm:text-sm text-white truncate max-w-[130px] block">
                                    {{ Auth::user()->name }}
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Cek Saldo Real-Time via Tap & Top Up -->
                        <div class="mt-4 pt-3 border-t border-white/15 flex flex-wrap items-center justify-between gap-2 z-10">
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" onclick="openCekSaldoModalMhs()" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-xs font-bold transition backdrop-blur-xs border border-white/25 shadow-2xs">
                                    <svg class="w-4 h-4 text-emerald-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>Tap Cek Saldo</span>
                                </button>
                                <button type="button" onclick="switchTab('topup')" class="inline-flex items-center space-x-2 px-3.5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    <span>+ Top Up Saldo</span>
                                </button>
                            </div>
                            <span class="text-[11px] text-blue-200">Online via Midtrans (QRIS & VA Bank)</span>
                        </div>
                    </div>

                    <!-- RINGKASAN LIMIT & ATURAN WALI -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                                <h3 class="text-base font-bold text-gray-900 flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <span>Batas Pengeluaran</span>
                                </h3>
                                <button type="button" onclick="switchTab('limit')" class="text-xs text-primary font-bold hover:underline">
                                    Detail
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div class="bg-slate-50 p-4 rounded-2xl border border-gray-100">
                                    <span class="text-xs text-gray-500 font-semibold uppercase block">Limit Harian (Keseluruhan)</span>
                                    <div class="text-xl font-black text-gray-900 mt-1">
                                        {{ $limit && $limit->daily_limit ? 'Rp ' . number_format($limit->daily_limit, 0, ',', '.') : 'Tanpa Batasan Limit' }}
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1">Ditentukan langsung oleh akun wali / orang tua.</p>
                                </div>

                                @php
                                    $catLimits = $limit && $limit->category_limits ? $limit->category_limits : [];
                                @endphp
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div class="bg-blue-50/60 p-2.5 rounded-xl border border-blue-100">
                                        <span class="text-[10px] font-bold text-gray-500 block uppercase">Makanan</span>
                                        <span class="text-xs font-black text-primary block mt-0.5">
                                            {{ !empty($catLimits['Makanan']) ? 'Rp ' . number_format($catLimits['Makanan'], 0, ',', '.') : 'Bebas' }}
                                        </span>
                                    </div>
                                    <div class="bg-emerald-50/60 p-2.5 rounded-xl border border-emerald-100">
                                        <span class="text-[10px] font-bold text-gray-500 block uppercase">Minuman</span>
                                        <span class="text-xs font-black text-emerald-700 block mt-0.5">
                                            {{ !empty($catLimits['Minuman']) ? 'Rp ' . number_format($catLimits['Minuman'], 0, ',', '.') : 'Bebas' }}
                                        </span>
                                    </div>
                                    <div class="bg-purple-50/60 p-2.5 rounded-xl border border-purple-100">
                                        <span class="text-[10px] font-bold text-gray-500 block uppercase">Jajanan</span>
                                        <span class="text-xs font-black text-purple-700 block mt-0.5">
                                            {{ !empty($catLimits['Jajanan']) ? 'Rp ' . number_format($catLimits['Jajanan'], 0, ',', '.') : 'Bebas' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 mt-4 flex items-center justify-between">
                            <span class="text-xs text-gray-500">Butuh isi ulang saldo kartu?</span>
                            <button type="button" onclick="switchTab('topup')" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 hover:underline flex items-center gap-1">
                                <span>Isi Saldo Mandiri</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- TRANSAKSI TERAKHIR (PREVIEW 4 TRANSAKSI) -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Transaksi Terkini</h3>
                            <p class="text-xs text-gray-500">Aktivitas pembayaran kantin dan top-up saldo terbaru Anda</p>
                        </div>
                        <button type="button" onclick="switchTab('transaksi')" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-primary border border-blue-200 rounded-xl text-xs font-bold transition">
                            Lihat Semua Riwayat ({{ count($transactions) }})
                        </button>
                    </div>

                    @if(count($transactions) > 0)
                        <div class="divide-y divide-gray-100">
                            @foreach($transactions->take(4) as $trx)
                                <div class="py-3.5 flex items-center justify-between hover:bg-gray-50/80 px-2 rounded-xl transition">
                                    <div class="flex items-center space-x-3.5 min-w-0">
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $trx->type == 'topup' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-primary' }}">
                                            @if($trx->type == 'topup')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-sm text-gray-900 truncate">
                                                {{ $trx->type == 'topup' ? 'Top-Up Saldo' : ($trx->pedagang->name ?? 'Kantin Polbeng') }}
                                            </div>
                                            <div class="text-xs text-gray-400 mt-0.5 truncate">
                                                {{ $trx->created_at->translatedFormat('d M Y, H:i') }} | {{ $trx->description ?? 'Pembayaran NFC' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0 ml-4">
                                        <div class="font-extrabold text-sm sm:text-base {{ $trx->type == 'topup' ? 'text-emerald-600' : 'text-gray-900' }}">
                                            {{ $trx->type == 'topup' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                        </div>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $trx->status == 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ ucfirst($trx->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-sm font-semibold">Belum ada aktivitas transaksi.</p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- TAB 2: RIWAYAT TRANSAKSI LENGKAP                               -->
            <!-- ============================================================== -->
            <div id="tab-transaksi" class="tab-content hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Seluruh Riwayat Transaksi</h3>
                            <p class="text-xs text-gray-500">Catatan pengeluaran di kantin serta riwayat isi saldo kartu NFC</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-gray-500">Total:</span>
                            <span class="px-2.5 py-1 bg-blue-50 text-primary rounded-xl font-bold text-xs border border-blue-200">
                                {{ count($transactions) }} Transaksi
                            </span>
                        </div>
                    </div>

                    @if(count($transactions) > 0)
                        <div class="overflow-x-auto mt-4">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50/80 text-gray-600 rounded-xl text-xs uppercase font-bold">
                                    <tr>
                                        <th class="p-3.5 rounded-l-xl">Waktu Transaksi</th>
                                        <th class="p-3.5">Jenis / Tempat</th>
                                        <th class="p-3.5">Keterangan</th>
                                        <th class="p-3.5 text-right">Nominal</th>
                                        <th class="p-3.5 text-center rounded-r-xl">Status Pesanan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($transactions as $trx)
                                    <tr class="hover:bg-gray-50/80 transition">
                                        <td class="p-3.5 text-xs">
                                            <div class="font-bold text-gray-800">{{ $trx->created_at->translatedFormat('d M Y') }}</div>
                                            <div class="text-gray-400 text-[11px]">{{ $trx->created_at->format('H:i') }} WIB</div>
                                        </td>
                                        <td class="p-3.5">
                                            @if($trx->type == 'topup')
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>
                                                    Top-Up Saldo
                                                </span>
                                            @else
                                                <div class="font-bold text-gray-900">{{ $trx->pedagang->name ?? 'Kantin Polbeng' }}</div>
                                                <div class="text-xs text-gray-400">Pembayaran Kasir POS</div>
                                            @endif
                                        </td>
                                        <td class="p-3.5 text-xs text-gray-600 max-w-xs truncate">
                                            {{ $trx->description ?? 'Transaksi via NFC' }}
                                        </td>
                                        <td class="p-3.5 text-right font-black text-sm {{ $trx->type == 'topup' ? 'text-emerald-600' : 'text-gray-900' }}">
                                            {{ $trx->type == 'topup' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="p-3.5 text-center">
                                            @if($trx->status == 'success')
                                                @if($trx->type == 'payment')
                                                    @if($trx->order_status == 'diproses')
                                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-extrabold uppercase">Diproses</span>
                                                    @elseif($trx->order_status == 'selesai')
                                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-extrabold uppercase">Selesai</span>
                                                    @else
                                                        <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-[10px] font-extrabold uppercase">Berhasil</span>
                                                    @endif
                                                @else
                                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-extrabold uppercase">Berhasil</span>
                                                @endif
                                            @elseif($trx->status == 'pending')
                                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-extrabold uppercase">Pending</span>
                                            @else
                                                <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-[10px] font-extrabold uppercase">Gagal</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-16 text-gray-400">
                            <p class="text-sm font-semibold">Belum ada riwayat transaksi yang tercatat.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 3: LIMIT & BATAS BELANJA                                   -->
            <!-- ============================================================== -->
            <div id="tab-limit" class="tab-content hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100">
                    <div class="max-w-2xl">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Batas Pengeluaran Harian Anda</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Fitur limit pengeluaran diatur secara langsung oleh wali / orang tua untuk memastikan keuangan Anda tetap terkontrol saat bertransaksi di unit usaha inkubator / kantin Polbeng.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white p-6 rounded-3xl shadow-lg shadow-orange-500/20">
                            <span class="text-xs uppercase tracking-wider text-orange-100 font-bold block">Limit Belanja Harian (Total)</span>
                            <div class="text-3xl font-black mt-2">
                                {{ $limit && $limit->daily_limit ? 'Rp ' . number_format($limit->daily_limit, 0, ',', '.') : 'Tanpa Batasan' }}
                            </div>
                            <p class="text-xs text-orange-100/90 mt-2">
                                {{ $limit && $limit->daily_limit ? 'Transaksi Anda dalam sehari tidak dapat melebihi jumlah ini.' : 'Wali Anda tidak menetapkan batas maksimal pengeluaran harian.' }}
                            </p>
                        </div>

                        <div class="bg-slate-50 border border-gray-200 p-6 rounded-3xl">
                            <span class="text-xs uppercase tracking-wider text-gray-500 font-bold block">Status Limit Per Kategori</span>
                            <div class="space-y-3 mt-4">
                                <div class="flex items-center justify-between pb-2 border-b border-gray-200/60">
                                    <span class="text-xs font-bold text-gray-700">Makanan</span>
                                    <span class="text-xs font-black text-gray-900">
                                        {{ !empty($catLimits['Makanan']) ? 'Rp ' . number_format($catLimits['Makanan'], 0, ',', '.') . ' / hari' : 'Tanpa Batas' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between pb-2 border-b border-gray-200/60">
                                    <span class="text-xs font-bold text-gray-700">Minuman</span>
                                    <span class="text-xs font-black text-gray-900">
                                        {{ !empty($catLimits['Minuman']) ? 'Rp ' . number_format($catLimits['Minuman'], 0, ',', '.') . ' / hari' : 'Tanpa Batas' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-700">Jajanan & Cemilan</span>
                                    <span class="text-xs font-black text-gray-900">
                                        {{ !empty($catLimits['Jajanan']) ? 'Rp ' . number_format($catLimits['Jajanan'], 0, ',', '.') . ' / hari' : 'Tanpa Batas' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 4: PENGATURAN PROFIL & AKUN                                -->
            <!-- ============================================================== -->
            <div id="tab-profil" class="tab-content hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 max-w-2xl">
                    <div class="pb-5 border-b border-gray-100 mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Pengaturan Akun Mahasiswa</h3>
                        <p class="text-xs text-gray-500 mt-1">Ubah nama lengkap, email, foto profil, dan kata sandi akun Anda</p>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="avatar_cropped">

                        <!-- Foto Profil -->
                        <div class="bg-slate-50 p-4 rounded-2xl border border-gray-200 flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-primary/20 flex items-center justify-center bg-white shadow-xs shrink-0">
                                @if(Auth::user()->avatar_url)
                                    <img id="preview-tab-avatar" src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <div id="preview-tab-avatar-fallback" class="w-full h-full bg-primary/10 flex items-center justify-center text-primary font-black text-xl">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="cursor-pointer inline-flex items-center space-x-1.5 px-3.5 py-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-2xs transition">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Pilih & Pangkas Foto</span>
                                    <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden" onclick="this.value = null" onchange="previewMhsPhoto(this, 'preview-tab-avatar', 'preview-tab-avatar-fallback')">
                                </label>
                                @if(Auth::user()->avatar)
                                    <label class="block mt-1.5 text-xs text-rose-600 font-semibold cursor-pointer">
                                        <input type="checkbox" name="remove_avatar" value="1" class="rounded border-rose-300 text-rose-600 mr-1">
                                        Hapus foto saat disimpan
                                    </label>
                                @endif
                                <div class="text-[11px] text-gray-400 mt-1">Dukung format JPG, PNG, WEBP (maks. 3MB)</div>
                            </div>
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Nama Lengkap Mahasiswa <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-primary focus:border-primary shadow-2xs">
                        </div>

                        <!-- NIM Mahasiswa -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Nomor Induk Mahasiswa (NIM)</label>
                            <input type="text" name="nim" value="{{ old('nim', Auth::user()->nim) }}" placeholder="Contoh: 2101092001" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-mono font-bold text-gray-800 focus:ring-2 focus:ring-primary focus:border-primary shadow-2xs">
                            <p class="text-[11px] text-gray-400 mt-1">NIM ini dapat diberikan ke orang tua/wali untuk pengiriman saldo atau uang saku.</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Alamat Email Login <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-800 focus:ring-2 focus:ring-primary focus:border-primary shadow-2xs">
                        </div>

                        <!-- Ganti Password -->
                        <div class="pt-4 border-t border-gray-100 space-y-3">
                            <span class="text-xs font-bold text-gray-600 uppercase block">Ganti Kata Sandi (Opsional)</span>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Kata Sandi Baru</label>
                                <input type="password" name="password" autocomplete="new-password" placeholder="Kosongkan jika tidak ingin mengubah" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi kata sandi baru" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary">
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-md shadow-blue-500/20 transition flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan Perubahan Profil</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TAB: TOP UP SALDO MANDIRI MAHASISWA                            -->
            <!-- ============================================================== -->
            <div id="tab-topup" class="tab-content hidden space-y-6">

                <!-- KARTU INFORMASI SALDO & STATUS -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-1 bg-gradient-to-br from-emerald-600 via-teal-700 to-cyan-800 text-white rounded-3xl p-6 shadow-xl shadow-teal-900/15 border border-white/10 flex flex-col justify-between min-h-[220px] relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none">
                            <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs uppercase tracking-wider font-bold text-teal-200">Kartu NFC Mahasiswa</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/20 text-white backdrop-blur-xs">
                                    {{ $card && $card->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <div class="mt-4">
                                <span class="text-[11px] uppercase tracking-widest text-teal-200 font-semibold block">Saldo Saat Ini</span>
                                <div class="text-3xl sm:text-4xl font-black mt-1">
                                    Rp {{ $card ? number_format($card->balance, 0, ',', '.') : '0' }}
                                </div>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-white/15 flex items-center justify-between text-xs">
                            <div>
                                <span class="text-[10px] text-teal-200 uppercase block">UID Kartu</span>
                                <span class="font-mono font-bold">{{ $card ? $card->card_uid : '-' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] text-teal-200 uppercase block">Pemegang</span>
                                <span class="font-bold">{{ Auth::user()->name }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- FORM PENGISIAN SALDO -->
                    <div class="lg:col-span-2 bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-gray-100">
                        @if($card && $card->status === 'active')
                            <div class="flex items-center space-x-3 pb-4 border-b border-gray-100 mb-5">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-base text-gray-900">Form Isi Ulang Saldo Kartu NFC</h3>
                                    <p class="text-xs text-gray-500">Pilih nominal dan metode pembayaran yang Anda inginkan</p>
                                </div>
                            </div>

                            <form id="form-topup-mhs" action="{{ route('mahasiswa.topup') }}" method="POST" class="space-y-5">
                                @csrf
                                <input type="hidden" name="card_id" value="{{ $card->id }}">

                                <!-- PILIHAN METODE PEMBAYARAN -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">1. Metode Pembayaran</label>
                                    <div class="flex items-center justify-between p-4 border-2 border-primary bg-blue-50/70 rounded-2xl shadow-2xs">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-sm text-gray-900">Midtrans Online Gateway</div>
                                                <p class="text-xs text-gray-500">QRIS (GoPay, ShopeePay, Dana, OVO) & Virtual Account Bank</p>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 shrink-0">Resmi / Otomatis</span>
                                    </div>
                                    <input type="hidden" name="payment_method" value="midtrans">
                                </div>

                                <!-- PILIHAN NOMINAL -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">2. Pilih / Masukkan Nominal Saldo</label>
                                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 mb-3">
                                        <button type="button" onclick="setMhsNominal(10000)" class="btn-nominal py-2 px-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-blue-50 hover:border-primary text-xs font-bold text-gray-800 transition">10 Rb</button>
                                        <button type="button" onclick="setMhsNominal(20000)" class="btn-nominal py-2 px-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-blue-50 hover:border-primary text-xs font-bold text-gray-800 transition">20 Rb</button>
                                        <button type="button" onclick="setMhsNominal(50000)" class="btn-nominal py-2 px-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-blue-50 hover:border-primary text-xs font-bold text-gray-800 transition">50 Rb</button>
                                        <button type="button" onclick="setMhsNominal(100000)" class="btn-nominal py-2 px-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-blue-50 hover:border-primary text-xs font-bold text-gray-800 transition">100 Rb</button>
                                        <button type="button" onclick="setMhsNominal(200000)" class="btn-nominal py-2 px-3 rounded-xl border border-gray-200 bg-gray-50 hover:bg-blue-50 hover:border-primary text-xs font-bold text-gray-800 transition">200 Rb</button>
                                    </div>

                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-500 text-sm">Rp</span>
                                        <input type="number" id="mhs-topup-amount" name="amount" required min="1000" step="1000" placeholder="50000" class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl font-bold text-base text-gray-900 focus:ring-2 focus:ring-primary focus:border-primary shadow-2xs">
                                    </div>
                                    <span class="text-[11px] text-gray-400 mt-1 block">Minimal pengisian saldo adalah Rp 1.000</span>
                                </div>

                                <!-- CATATAN OPSIONAL -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">3. Catatan / Keterangan (Opsional)</label>
                                    <input type="text" id="mhs-topup-notes" name="notes" placeholder="Contoh: Uang makan siang, persiapan praktikum..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-xs font-medium text-gray-800 focus:ring-2 focus:ring-primary shadow-2xs">
                                </div>

                                <!-- TOMBOL SUBMIT -->
                                <div>
                                    <button type="button" id="btn-submit-mhs-topup" onclick="submitMhsTopup()" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-sm shadow-lg shadow-emerald-600/20 transition flex items-center justify-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <span id="btn-submit-mhs-topup-text">Bayar Sekarang (Midtrans)</span>
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="text-center py-12 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <p class="text-sm font-bold text-gray-600">Kartu NFC Tidak Aktif / Belum Terdaftar</p>
                                <p class="text-xs text-gray-400 mt-1">Silakan hubungi administrator kampus untuk aktivasi kartu NFC Anda sebelum melakukan top up saldo.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- PANDUAN CARA TOP UP -->
                <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-slate-50 rounded-3xl p-6 border border-blue-100">
                    <h3 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Panduan Cara Top Up Saldo Kartu NFC</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-primary font-bold flex items-center justify-center text-xs mb-2">1</span>
                            <span class="font-bold text-gray-900 block mb-0.5">Tentukan Nominal</span>
                            <p class="text-gray-500 text-[11px]">Pilih nominal cepat atau masukkan jumlah sesuai kebutuhan Anda.</p>
                        </div>
                        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-primary font-bold flex items-center justify-center text-xs mb-2">2</span>
                            <span class="font-bold text-gray-900 block mb-0.5">Metode Midtrans</span>
                            <p class="text-gray-500 text-[11px]">Pembayaran resmi Midtrans (QRIS GoPay, Dana, OVO, ShopeePay & VA Bank).</p>
                        </div>
                        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-primary font-bold flex items-center justify-center text-xs mb-2">3</span>
                            <span class="font-bold text-gray-900 block mb-0.5">Selesaikan Pembayaran</span>
                            <p class="text-gray-500 text-[11px]">Scan QRIS pada popup Midtrans atau transfer ke nomor Virtual Account.</p>
                        </div>
                        <div class="bg-white p-3.5 rounded-2xl border border-gray-100 shadow-2xs">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs mb-2">4</span>
                            <span class="font-bold text-gray-900 block mb-0.5">Saldo Siap Dipakai</span>
                            <p class="text-gray-500 text-[11px]">Saldo langsung terupdate otomatis dan kartu siap di-tap di stan kantin.</p>
                        </div>
                    </div>
                </div>

                <!-- RIWAYAT TOP UP KHUSUS MAHASISWA -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Riwayat Pengisian Saldo (Top Up & Transfer)</h3>
                            <p class="text-xs text-gray-500">Daftar saldo masuk dari top up mandiri maupun transfer dari wali</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ isset($topupHistory) ? count($topupHistory) : 0 }} Kali Top Up
                        </span>
                    </div>

                    @if(isset($topupHistory) && count($topupHistory) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="text-gray-400 font-bold border-b border-gray-100 uppercase tracking-wider text-[10px]">
                                        <th class="py-3 px-3">Tanggal & Waktu</th>
                                        <th class="py-3 px-3">Keterangan / Sumber</th>
                                        <th class="py-3 px-3">Metode</th>
                                        <th class="py-3 px-3">Nominal Masuk</th>
                                        <th class="py-3 px-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($topupHistory as $topup)
                                        <tr class="hover:bg-gray-50/80 transition">
                                            <td class="py-3 px-3 font-mono text-gray-600">
                                                {{ $topup->created_at->format('d M Y, H:i') }}
                                            </td>
                                            <td class="py-3 px-3">
                                                <span class="font-bold text-gray-900 block">{{ $topup->description ?? 'Top Up Saldo' }}</span>
                                                @if($topup->order_id)
                                                    <span class="text-[10px] text-gray-400 font-mono">{{ $topup->order_id }}</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $topup->payment_method === 'midtrans' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                                    {{ $topup->payment_method ?? 'Midtrans' }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 font-bold text-emerald-600 text-sm">
                                                + Rp {{ number_format($topup->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-3 text-right">
                                                @if($topup->status === 'success')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                        Berhasil
                                                    </span>
                                                @elseif($topup->status === 'pending')
                                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                            Menunggu
                                                        </span>
                                                        <button type="button" onclick="cancelMidtransTrx('{{ $topup->order_id }}')" class="px-2 py-0.5 bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 rounded-md text-[10px] font-bold transition">
                                                            Batalkan
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                                        {{ $topup->order_status === 'batal' ? 'Dibatalkan' : 'Gagal' }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <p class="text-xs font-semibold">Belum ada riwayat top up saldo.</p>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </main>

</div>

<!-- ========================================== -->
<!-- MODAL CEK SALDO KARTU NFC MAHASISWA       -->
<!-- ========================================== -->
<div id="modal-cek-saldo-mhs" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl relative border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-3.5 border-b border-gray-100">
            <div class="flex items-center space-x-2.5">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-gray-900 leading-tight">Cek Saldo Kartu NFC</h3>
                    <p class="text-[11px] text-gray-500">Pengecekan real-time tanpa memotong saldo</p>
                </div>
            </div>
            <button type="button" onclick="closeCekSaldoModalMhs()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="mt-4 space-y-4">
            
            <!-- Sensor Status & Visual Indicator -->
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-200/80 rounded-2xl p-4 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-white text-indigo-600 flex items-center justify-center shadow-xs border border-indigo-100 mb-2">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h4 class="font-bold text-sm text-gray-900">Tempelkan Kartu ke Sensor HP</h4>
                <p class="text-xs text-gray-500 mt-0.5">Dekatkan kartu NFC fisik ke belakang bodi smartphone atau reader</p>
                <div class="mt-2.5 inline-flex items-center space-x-1.5 bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full text-[10px] font-bold border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span id="mhs-nfc-status-badge">Sensor NFC Siap</span>
                </div>
            </div>

            <!-- Manual Input Fallback -->
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Atau Ketik UID Kartu</label>
                <div class="flex gap-2">
                    <input type="text" id="input-cek-saldo-mhs-uid" value="{{ $card ? $card->card_uid : '' }}" placeholder="Ketik UID kartu..." class="flex-1 px-3.5 py-2 border border-gray-300 rounded-xl font-mono text-xs uppercase font-bold focus:ring-2 focus:ring-indigo-500">
                    <button type="button" onclick="handleManualCheckBalanceMhs()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow-xs">
                        Periksa
                    </button>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="cek-saldo-mhs-loading" class="hidden text-center py-6">
                <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-2"></div>
                <span class="text-xs font-bold text-gray-500">Membaca saldo kartu NFC...</span>
            </div>

            <!-- Error Message Box -->
            <div id="cek-saldo-mhs-error" class="hidden bg-rose-50 border border-rose-200 text-rose-700 p-3.5 rounded-2xl text-xs flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span id="cek-saldo-mhs-error-text" class="font-semibold"></span>
            </div>

            <!-- Result Card -->
            <div id="cek-saldo-mhs-result" class="hidden space-y-3 pt-2">
                
                <!-- Pemilik Kartu -->
                <div class="flex items-center space-x-3 bg-slate-50 p-3 rounded-2xl border border-gray-100">
                    <div id="cek-saldo-mhs-avatar-box" class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0 border border-gray-200 overflow-hidden">
                        <span id="cek-saldo-mhs-avatar-fallback">M</span>
                        <img id="cek-saldo-mhs-avatar-img" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div id="cek-saldo-mhs-nama" class="font-bold text-sm text-gray-900 truncate">Nama Mahasiswa</div>
                        <div class="text-[11px] text-gray-500 font-mono flex items-center space-x-2">
                            <span id="cek-saldo-mhs-uid" class="font-bold text-gray-700">UID: -</span>
                            <span id="cek-saldo-mhs-status" class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-800">Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Saldo Card -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-700 text-white p-5 rounded-2xl shadow-lg shadow-emerald-500/15 text-center">
                    <span class="text-[11px] uppercase tracking-wider text-emerald-100 font-bold block">Saldo Kartu Terverifikasi</span>
                    <div id="cek-saldo-mhs-amount" class="text-3xl font-black mt-1">Rp 0</div>
                    <p class="text-[11px] text-emerald-100/90 mt-1">Saldo utuh dan tidak terpotong</p>
                </div>

                <!-- Limit Info -->
                <div class="grid grid-cols-2 gap-2 text-center text-xs">
                    <div class="bg-slate-50 border border-gray-100 p-2.5 rounded-xl">
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Limit Harian</span>
                        <span id="cek-saldo-mhs-limit-daily" class="font-bold text-gray-800 text-xs block mt-0.5">Tanpa Limit</span>
                    </div>
                    <div class="bg-slate-50 border border-gray-100 p-2.5 rounded-xl">
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Sisa Kuota Hari Ini</span>
                        <span id="cek-saldo-mhs-limit-remaining" class="font-bold text-emerald-600 text-xs block mt-0.5">Bebas</span>
                    </div>
                </div>

            </div>

        </div>

        <div class="mt-5 pt-3 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closeCekSaldoModalMhs()" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
                Tutup Pengecekan
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Midtrans Snap JS -->
<script src="{{ config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', true)) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY')) }}"></script>
<script>
    const tabHeaders = {
        ringkasan: {
            title: 'Saldo & Kartu NFC',
            subtitle: 'Pantau saldo kartu NFC, informasi kartu, dan batas pengeluaran'
        },
        topup: {
            title: 'Top Up Saldo Mandiri',
            subtitle: 'Isi ulang saldo kartu NFC Anda melalui Midtrans (QRIS & Virtual Account)'
        },
        transaksi: {
            title: 'Riwayat Transaksi',
            subtitle: 'Catatan seluruh transaksi pembayaran di kantin dan riwayat top-up'
        },
        limit: {
            title: 'Batas / Limit Belanja',
            subtitle: 'Informasi batasan harian dan per kategori yang diatur oleh wali'
        },
        profil: {
            title: 'Pengaturan Profil & Akun',
            subtitle: 'Kelola data diri mahasiswa, alamat email, dan kata sandi akun'
        }
    };

    function toggleMobileSidebar() {
        const sidebar = document.getElementById('app-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (!sidebar) return;
        const isHidden = sidebar.classList.contains('-translate-x-full');
        if (isHidden) {
            sidebar.classList.remove('-translate-x-full');
            if (backdrop) backdrop.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
        }
    }

    function switchTab(tabId) {
        // Sembunyikan semua tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('block');
        });

        // Tampilkan tab yang dipilih
        const activeTab = document.getElementById('tab-' + tabId);
        if (activeTab) {
            activeTab.classList.remove('hidden');
            activeTab.classList.add('block');
        }

        // Reset semua tombol tab di sidebar
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('text-primary', 'bg-blue-50', 'border-primary', 'shadow-xs', 'font-bold');
            btn.classList.add('text-gray-600', 'border-transparent', 'font-semibold');
            const icon = btn.querySelector('.tab-icon');
            if (icon) {
                icon.classList.remove('text-primary');
                icon.classList.add('text-gray-400');
            }
        });

        // Set tombol aktif
        const activeBtn = document.getElementById('btn-tab-' + tabId);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-600', 'border-transparent', 'font-semibold');
            activeBtn.classList.add('text-primary', 'bg-blue-50', 'border-primary', 'shadow-xs', 'font-bold');
            const icon = activeBtn.querySelector('.tab-icon');
            if (icon) {
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-primary');
            }
        }

        // Update header judul di desktop
        const headerInfo = tabHeaders[tabId];
        if (headerInfo) {
            const titleEl = document.getElementById('topbar-title');
            const subtitleEl = document.getElementById('topbar-subtitle');
            if (titleEl) titleEl.innerText = headerInfo.title;
            if (subtitleEl) subtitleEl.innerText = headerInfo.subtitle;
        }

        // Tutup sidebar jika di mobile
        if (window.innerWidth < 768) {
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
            }
            if (backdrop && !backdrop.classList.contains('hidden')) {
                backdrop.classList.add('hidden');
            }
        }
    }

    function previewMhsPhoto(input, previewId, fallbackId) {
        if (typeof handleAvatarCropSelect === 'function') {
            handleAvatarCropSelect(input, [previewId, 'sidebar-avatar-img'], [fallbackId, 'sidebar-avatar-fallback']);
            return;
        }
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let img = document.getElementById(previewId);
                const fallback = document.getElementById(fallbackId);
                if (!img && fallback && fallback.parentElement) {
                    img = document.createElement('img');
                    img.id = previewId;
                    img.className = 'w-full h-full object-cover';
                    fallback.parentElement.appendChild(img);
                }
                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    img.style.display = 'block';
                }
                if (fallback) fallback.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // AUDIO BEEP
    function playBeep(isSuccess = true) {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            if (isSuccess) {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                osc.frequency.setValueAtTime(1320, ctx.currentTime + 0.08);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.25);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.25);
            }
        } catch(e) {}
    }

    // FUNGSI CEK SALDO KARTU NFC MAHASISWA
    async function doCheckBalanceMhs(uid) {
        if (!uid) return;
        const modal = document.getElementById('modal-cek-saldo-mhs');
        if (modal && modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        }

        const inputUid = document.getElementById('input-cek-saldo-mhs-uid');
        if (inputUid) inputUid.value = uid;

        const loading = document.getElementById('cek-saldo-mhs-loading');
        const result = document.getElementById('cek-saldo-mhs-result');
        const errBox = document.getElementById('cek-saldo-mhs-error');
        const errText = document.getElementById('cek-saldo-mhs-error-text');

        if (loading) loading.classList.remove('hidden');
        if (result) result.classList.add('hidden');
        if (errBox) errBox.classList.add('hidden');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || '{{ csrf_token() }}';

            const res = await fetch("{{ route('nfc.check_balance') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ card_uid: uid })
            });

            const data = await res.json();
            if (loading) loading.classList.add('hidden');

            if (res.ok && data.success) {
                playBeep(true);
                document.getElementById('cek-saldo-mhs-nama').innerText = data.user_name || 'Mahasiswa';
                document.getElementById('cek-saldo-mhs-uid').innerText = 'UID: ' + data.card_uid;
                
                const statusEl = document.getElementById('cek-saldo-mhs-status');
                if (statusEl) {
                    statusEl.innerText = data.status === 'active' ? 'Aktif' : 'Diblokir';
                    statusEl.className = data.status === 'active' 
                        ? 'px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-800' 
                        : 'px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-rose-100 text-rose-800';
                }

                document.getElementById('cek-saldo-mhs-amount').innerText = data.balance_formatted;
                document.getElementById('cek-saldo-mhs-limit-daily').innerText = data.daily_limit;
                document.getElementById('cek-saldo-mhs-limit-remaining').innerText = data.remaining_daily_limit;

                const avatarImg = document.getElementById('cek-saldo-mhs-avatar-img');
                const avatarFallback = document.getElementById('cek-saldo-mhs-avatar-fallback');
                if (data.user_avatar && avatarImg) {
                    avatarImg.src = data.user_avatar;
                    avatarImg.classList.remove('hidden');
                    if (avatarFallback) avatarFallback.classList.add('hidden');
                } else {
                    if (avatarImg) avatarImg.classList.add('hidden');
                    if (avatarFallback) {
                        avatarFallback.classList.remove('hidden');
                        avatarFallback.innerText = (data.user_name || 'M').substring(0, 2).toUpperCase();
                    }
                }

                if (result) result.classList.remove('hidden');
            } else {
                playBeep(false);
                if (errBox && errText) {
                    errText.innerText = data.message || 'Kartu tidak ditemukan.';
                    errBox.classList.remove('hidden');
                }
            }
        } catch (e) {
            if (loading) loading.classList.add('hidden');
            if (errBox && errText) {
                errText.innerText = 'Terjadi kesalahan jaringan saat memeriksa kartu.';
                errBox.classList.remove('hidden');
            }
        }
    }

    function openCekSaldoModalMhs() {
        const modal = document.getElementById('modal-cek-saldo-mhs');
        if (modal) {
            modal.classList.remove('hidden');
            document.getElementById('cek-saldo-mhs-result')?.classList.add('hidden');
            document.getElementById('cek-saldo-mhs-error')?.classList.add('hidden');
            document.getElementById('cek-saldo-mhs-loading')?.classList.add('hidden');
            initMhsWebNfc();
        }
    }

    function closeCekSaldoModalMhs() {
        document.getElementById('modal-cek-saldo-mhs')?.classList.add('hidden');
    }

    function handleManualCheckBalanceMhs() {
        const input = document.getElementById('input-cek-saldo-mhs-uid');
        if (input && input.value.trim()) {
            doCheckBalanceMhs(input.value.trim());
        }
    }

    // WEB NFC SENSOR UNTUK MAHASISWA (Android Chrome)
    let mhsNfcActive = false;
    async function initMhsWebNfc() {
        if (!('NDEFReader' in window)) return false;
        if (mhsNfcActive) return true;
        try {
            const ndef = new NDEFReader();
            await ndef.scan();
            mhsNfcActive = true;
            const badge = document.getElementById('mhs-nfc-status-badge');
            if (badge) {
                badge.innerText = 'Sensor HP Aktif - Tempelkan Kartu';
            }
            ndef.onreading = (event) => {
                let uid = event.serialNumber ? event.serialNumber.replace(/:/g, '').toUpperCase() : '';
                if (!uid && event.message && event.message.records) {
                    for (const record of event.message.records) {
                        if (record.recordType === 'text') {
                            const decoder = new TextDecoder(record.encoding);
                            uid = decoder.decode(record.data).trim();
                        }
                    }
                }
                if (uid) {
                    doCheckBalanceMhs(uid);
                }
            };
            return true;
        } catch (err) {
            console.warn("Mhs Web NFC error: ", err);
            return false;
        }
    }

    if ('NDEFReader' in window) {
        initMhsWebNfc();
    }

    // USB RFID Listener untuk Mahasiswa
    let mhsKeyBuffer = '';
    let mhsLastKeyTime = Date.now();
    window.addEventListener('keydown', (e) => {
        if (['input-cek-saldo-mhs-uid', 'input'].includes(e.target.id)) {
            if (e.target.id === 'input-cek-saldo-mhs-uid' && e.key === 'Enter') {
                handleManualCheckBalanceMhs();
            }
            return;
        }
        const now = Date.now();
        if (now - mhsLastKeyTime > 200) {
            mhsKeyBuffer = '';
        }
        mhsLastKeyTime = now;

        if (e.key === 'Enter') {
            if (mhsKeyBuffer.length >= 4) {
                doCheckBalanceMhs(mhsKeyBuffer);
                mhsKeyBuffer = '';
            }
        } else if (e.key.length === 1) {
            mhsKeyBuffer += e.key;
        }
    });

    // ==========================================
    // LOGIKA TOP UP SALDO MAHASISWA
    // ==========================================
    function setMhsNominal(amount) {
        const input = document.getElementById('mhs-topup-amount');
        if (input) {
            input.value = amount;
            input.focus();
        }
    }

    async function submitMhsTopup() {
        const form = document.getElementById('form-topup-mhs');
        const amountInput = document.getElementById('mhs-topup-amount');
        const notesInput = document.getElementById('mhs-topup-notes');
        const cardIdInput = form.querySelector('input[name="card_id"]');
        const btn = document.getElementById('btn-submit-mhs-topup');
        const btnText = document.getElementById('btn-submit-mhs-topup-text');

        const amount = amountInput.value;
        const notes = notesInput ? notesInput.value : '';
        const cardId = cardIdInput ? cardIdInput.value : '';

        if (!amount || amount < 1000) {
            alert('Silakan masukkan nominal top up minimal Rp 1.000');
            amountInput.focus();
            return;
        }

        // Mode Midtrans Gateway
        btn.disabled = true;
        btnText.innerText = 'Menghubungkan ke Midtrans...';

        try {
            const res = await fetch("{{ route('topup.midtrans') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    card_id: cardId,
                    amount: amount,
                    notes: notes
                })
            });

            const data = await res.json();
            btn.disabled = false;
            btnText.innerText = 'Bayar Sekarang (Midtrans)';

            if (res.ok && data.snap_token) {
                if (typeof window.snap === 'undefined') {
                    alert('Midtrans Snap JS belum termuat. Periksa koneksi internet Anda.');
                    return;
                }
                window.snap.pay(data.snap_token, {
                    onSuccess: async function(result){
                        try {
                            await fetch("{{ route('midtrans.finish') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ order_id: result.order_id || data.order_id })
                            });
                        } catch(e) {}
                        alert("Pembayaran berhasil! Saldo kartu Anda telah ditambahkan.");
                        window.location.reload();
                    },
                    onPending: function(result){
                        alert("Menunggu pembayaran Anda diselesaikan.");
                        window.location.reload();
                    },
                    onError: async function(result){
                        alert("Pembayaran Midtrans gagal atau dibatalkan.");
                        try {
                            await fetch("{{ route('midtrans.cancel') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ order_id: (result && result.order_id) || data.order_id })
                            });
                        } catch(e) {}
                        window.location.reload();
                    },
                    onClose: async function(){
                        if (confirm("Anda menutup popup pembayaran Midtrans.\n\nApakah Anda ingin membatalkan transaksi ini sekarang?")) {
                            try {
                                await fetch("{{ route('midtrans.cancel') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({ order_id: data.order_id })
                                });
                            } catch(e) {}
                            window.location.reload();
                        }
                    }
                });
            } else {
                let errMsg = data.error || 'Gagal mendapatkan token pembayaran Midtrans.';
                alert(errMsg);
            }
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btnText.innerText = 'Bayar Sekarang (Midtrans)';
            alert('Terjadi kesalahan koneksi ke server.');
        }
    }
    async function cancelMidtransTrx(orderId) {
        if (!confirm('Apakah Anda yakin ingin membatalkan transaksi pembayaran ini (' + orderId + ')?')) {
            return;
        }
        try {
            const res = await fetch("{{ route('midtrans.cancel') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order_id: orderId })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message || 'Transaksi berhasil dibatalkan.');
                window.location.reload();
            } else {
                alert(data.message || 'Gagal membatalkan transaksi.');
            }
        } catch(err) {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        }
    }
</script>
@endpush
