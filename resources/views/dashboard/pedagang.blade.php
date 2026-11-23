@extends('layouts.app')
@section('title', 'Sistem Kasir (POS) - Pedagang')

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
                <span class="text-[10px] bg-blue-500 text-white font-bold px-1.5 py-0.5 rounded ml-1.5 uppercase">Pedagang</span>
            </div>
        </div>
        <button type="button" onclick="toggleMobileSidebar()" class="p-2 rounded-lg bg-blue-800 hover:bg-blue-700 focus:outline-none transition" aria-label="Toggle Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </header>

    <!-- BACKDROP MOBILE -->
    <div id="sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden transition-opacity"></div>

    <!-- SIDEBAR VERTIKAL KIRI -->
    <aside id="pedagang-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-gray-200 flex flex-col transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:static md:h-screen md:sticky md:top-0 shrink-0 shadow-sm">
        
        <!-- SIDEBAR BRAND & LOGO -->
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-gray-200 p-1 flex items-center justify-center shadow-xs shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-wider text-gray-900 leading-none">KANTIN NFC</h1>
                    <div class="flex items-center mt-1 space-x-1.5">
                        <span class="px-2 py-0.5 bg-blue-100 text-primary rounded-full text-[10px] font-extrabold uppercase tracking-wide">PEDAGANG</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                </div>
            </div>
            <!-- Tombol Tutup di Mobile -->
            <button type="button" onclick="toggleMobileSidebar()" class="md:hidden text-gray-400 hover:text-gray-600 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- INFO PROFIL PEDAGANG -->
        <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/80">
            <div class="flex items-center space-x-3">
                @if(Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-11 h-11 rounded-full object-cover border border-primary/20 shadow-xs shrink-0">
                @else
                    <div class="w-11 h-11 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-base shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-bold text-gray-900 truncate" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500 truncate flex items-center mt-0.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                        Stan Aktif / Buka
                    </div>
                </div>
            </div>
            <!-- Tombol Edit Profil Pedagang -->
            <button type="button" onclick="switchTab('profil')" class="mt-3 w-full flex items-center justify-center space-x-1.5 py-1.5 px-3 rounded-lg text-xs font-bold text-primary bg-blue-50 hover:bg-blue-100 border border-blue-200 transition shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Edit Profil & Nama</span>
            </button>
        </div>

        <!-- DAFTAR MENU NAVIGASI (TABS) -->
        <div class="flex-1 overflow-y-auto px-3.5 py-4 space-y-1.5">
            <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Menu Utama</div>

            <!-- Tab 1: Mesin Kasir -->
            <button type="button" onclick="switchTab('kasir')" id="btn-tab-kasir" class="tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-bold text-sm transition-all text-primary bg-blue-50 border-l-4 border-primary shadow-xs">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 tab-icon text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Mesin Kasir (POS)</span>
                </div>
                <span class="text-[10px] uppercase font-bold bg-blue-600 text-white px-2 py-0.5 rounded-md">Kasir</span>
            </button>

            <!-- Tab 2: Pesanan Aktif -->
            <button type="button" onclick="switchTab('pesanan')" id="btn-tab-pesanan" class="tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pesanan Aktif</span>
                </div>
                @if($activeOrders->count() > 0)
                    <span class="text-xs font-bold bg-orange-500 text-white px-2 py-0.5 rounded-full animate-pulse">{{ $activeOrders->count() }}</span>
                @else
                    <span class="text-xs text-gray-400 font-medium">0</span>
                @endif
            </button>

            <!-- Tab 3: Manajemen Menu -->
            <button type="button" onclick="switchTab('manajemen')" id="btn-tab-manajemen" class="tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Manajemen Menu</span>
                </div>
                <span class="text-xs text-gray-400 font-medium">{{ $menus->count() }}</span>
            </button>

            <!-- Tab 4: Laporan & Riwayat -->
            <button type="button" onclick="switchTab('laporan')" id="btn-tab-laporan" class="tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Laporan & Riwayat</span>
                </div>
            </button>

            <!-- Tab 5: Profil & Edit Nama -->
            <button type="button" onclick="switchTab('profil')" id="btn-tab-profil" class="tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                    <span>Profil & Edit Nama</span>
                </div>
                <span class="text-[10px] uppercase font-bold bg-blue-100 text-primary px-2 py-0.5 rounded-md">Profil</span>
            </button>

            <!-- Tombol Cek Saldo NFC Pelanggan -->
            <button type="button" onclick="openCekSaldoModal()" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl font-bold text-xs text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition shadow-2xs mt-2">
                <div class="flex items-center space-x-2.5">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cek Saldo Kartu NFC</span>
                </div>
                <span class="text-[10px] uppercase font-bold bg-indigo-600 text-white px-2 py-0.5 rounded-md">Tap Kartu</span>
            </button>

            <!-- WIDGET RINGKASAN PENDAPATAN -->
            <div class="pt-4">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl p-4 text-gray-800 shadow-xs">
                    <div class="flex items-center justify-between text-xs text-gray-500 font-bold mb-1">
                        <span>Total Pendapatan</span>
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div class="text-lg font-black text-primary tracking-tight">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] text-gray-500 mt-1">Transaksi Berhasil</div>
                </div>
            </div>
        </div>

        <!-- FOOTER SIDEBAR (LOGOUT) -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 hover:text-red-700 transition border border-red-200 shadow-2xs">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar (Logout)</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- AREA KONTEN UTAMA (SISI KANAN) -->
    <div class="flex-1 flex flex-col min-w-0 bg-slate-50 min-h-screen">
        
        <!-- TOPBAR HEADER KONTEN -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 sticky top-0 z-20 shadow-xs">
            <div>
                <h2 id="topbar-title" class="text-xl font-black text-gray-800 flex items-center">
                    <span id="topbar-title-text">Mesin Kasir (POS)</span>
                </h2>
                <p id="topbar-subtitle" class="text-xs text-gray-500 mt-0.5">Pilih katalog menu dan proses transaksi pembayaran tunai atau kartu NFC</p>
            </div>
            
            <div class="flex items-center space-x-3 shrink-0">
                <!-- Tombol Cek Saldo NFC -->
                <button type="button" onclick="openCekSaldoModal()" class="flex items-center space-x-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 rounded-lg text-xs font-bold transition shadow-xs">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cek Saldo NFC</span>
                </button>

                <!-- Status Reader NFC -->
                <div class="hidden sm:flex items-center space-x-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-lg text-xs font-semibold shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>RFID / NFC Reader Siap</span>
                </div>

                <!-- Info Hari / Tanggal -->
                <div class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg border border-gray-200">
                    📅 {{ date('d M Y') }}
                </div>
            </div>
        </header>

        <!-- MAIN SCROLLABLE CONTENT -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            
            <!-- FLASH MESSAGES -->
            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 mb-6 shadow-sm rounded-r-lg flex items-center justify-between" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-emerald-500 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 mb-6 shadow-sm rounded-r-lg flex items-center justify-between" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span class="font-medium text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 mb-6 shadow-sm rounded-r-lg" role="alert">
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

<!-- TAB 1: MESIN KASIR -->
<div id="tab-kasir" class="tab-content block">
    <div class="flex flex-col lg:flex-row gap-6 items-start pb-12">
        
        <!-- Area Kiri: Katalog Menu (Grid Besar) -->
        <div class="lg:w-7/12 xl:w-2/3 w-full pr-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">Pilih Menu</h3>
                <span class="text-xs text-gray-500 font-medium">Klik menu untuk menambahkan ke keranjang</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($menus as $menu)
                <div class="cursor-pointer bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg hover:border-primary transition transform hover:-translate-y-1 overflow-hidden"
                     onclick="addToCart('{{ $menu->id }}', '{{ $menu->name }}', {{ $menu->price }})">
                    
                    <div class="h-32 bg-gray-100 w-full relative">
                        @if($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        <span class="absolute top-2 right-2 bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-1 rounded-full">{{ $menu->category }}</span>
                    </div>
                    
                    <div class="p-3 text-center">
                        <h4 class="font-bold text-gray-800 text-sm mb-1 truncate" title="{{ $menu->name }}">{{ $menu->name }}</h4>
                        <div class="text-primary font-extrabold text-sm">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Area Kanan: Keranjang Belanja (Struk) -->
        <div class="lg:w-5/12 xl:w-1/3 w-full flex flex-col bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden lg:sticky lg:top-24">
            <!-- Header Keranjang -->
            <div class="bg-gray-800 text-white px-5 py-3.5 flex items-center justify-between shrink-0">
                <h3 class="font-bold flex items-center text-base">
                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Pesanan Saat Ini
                </h3>
                <span id="cart-item-count-badge" class="text-xs bg-blue-600 text-white font-bold px-2.5 py-0.5 rounded-full">0 Menu</span>
            </div>
            
            <!-- Daftar Item Pesanan (Diberikan min-height agar tidak tersepit) -->
            <div class="min-h-[160px] max-h-[260px] overflow-y-auto p-4 space-y-2.5 bg-gray-50 border-b border-gray-200">
                <div class="text-gray-400 text-center py-8" id="empty-cart-msg">
                    <svg class="w-12 h-12 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <p class="text-sm font-medium">Belum ada menu yang dipilih.</p>
                    <p class="text-xs text-gray-400 mt-1">Pilih menu di sebelah kiri</p>
                </div>
                <!-- Cart items dinamis via JS -->
                <div id="cart-items-list" class="space-y-2.5"></div>
            </div>

            <!-- Bagian Pembayaran & Checkout -->
            <div class="p-4 bg-white space-y-3">
                <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                    <span class="font-bold text-gray-600 text-base">Total Tagihan</span>
                    <span id="cart-total-display" class="font-extrabold text-primary text-2xl">Rp 0</span>
                </div>

                <!-- FORM 1: BAYAR LANGSUNG NFC -->
                <form action="{{ route('transaction.nfc') }}" method="POST" id="nfc-checkout-form">
                    @csrf
                    <div id="nfc-hidden-items-container"></div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 p-3.5 rounded-xl shadow-xs relative overflow-hidden transition-all duration-300" id="nfc-card-box">
                        <!-- NFC Header & Indicator -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-1.5 text-xs font-bold text-blue-700 uppercase tracking-wide">
                                <svg class="w-4 h-4 text-primary animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span>Bayar Cepat NFC</span>
                            </div>
                            <span id="nfc-sensor-badge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-300">
                                <span class="w-2 h-2 mr-1 rounded-full bg-green-500 animate-ping"></span>
                                Sensor Aktif
                            </span>
                        </div>

                        <!-- Web NFC Status / Trigger Button -->
                        <div id="web-nfc-banner" class="hidden mb-2">
                            <button type="button" id="btn-activate-webnfc" class="w-full text-xs font-semibold py-1.5 px-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center justify-center gap-1.5 transition shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <span>Aktifkan Sensor Tap Web NFC</span>
                            </button>
                        </div>

                        <!-- NFC Input Field -->
                        <div class="relative">
                            <input type="text" name="card_uid" id="kasir-nfc-input" placeholder="Tempelkan kartu NFC..." autocomplete="off" disabled 
                                class="w-full px-3 py-2.5 pl-9 border border-blue-400 rounded-lg focus:ring-4 focus:ring-blue-100 focus:border-blue-600 text-center font-mono font-bold text-base tracking-wider text-gray-800 transition disabled:bg-gray-100 disabled:border-gray-200 disabled:opacity-50">
                            <div class="absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-blue-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                        </div>

                        <!-- Manual Submit Button (FR-9 Fallback) -->
                        <button type="submit" id="btn-nfc-submit" disabled
                            class="w-full mt-2 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-bold text-sm transition shadow flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>BAYAR SEKARANG (NFC)</span>
                        </button>
                    </div>
                </form>

                <!-- FORM 2: SIMPAN PESANAN (TUNAI/QRIS) -->
                <form action="{{ route('transaction.store_order') }}" method="POST" id="checkout-form">
                    @csrf
                    <div id="hidden-items-container"></div>
                    
                    <button type="submit" id="btn-checkout" class="w-full py-2.5 bg-gray-800 text-white rounded-lg font-bold text-sm hover:bg-gray-900 transition shadow opacity-50 cursor-not-allowed" disabled>
                        SIMPAN & BAYAR NANTI (TUNAI/QRIS)
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- TAB PESANAN AKTIF -->
<div id="tab-pesanan" class="tab-content hidden">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 min-h-[70vh]">
        <h3 class="text-xl font-bold text-gray-800 mb-6 border-b pb-4">Pesanan Belum Lunas & Sedang Diproses</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($activeOrders as $order)
                <div class="border rounded-xl p-4 shadow-sm relative {{ $order->status == 'pending' ? 'bg-orange-50 border-orange-200' : 'bg-yellow-50 border-yellow-200' }}">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="text-xs font-bold text-gray-500">ID: #{{ $order->id }}</span>
                            <div class="text-sm text-gray-600">{{ $order->created_at->format('H:i') }}</div>
                        </div>
                        @if($order->status == 'pending')
                            <span class="bg-orange-100 text-orange-700 text-[10px] font-bold px-2 py-1 rounded">BELUM DIBAYAR</span>
                        @else
                            <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-1 rounded">DIPROSES (LUNAS)</span>
                        @endif
                    </div>
                    
                    <ul class="text-sm space-y-1 mb-4 border-b border-gray-200/50 pb-3">
                        @foreach($order->items as $item)
                            <li class="flex justify-between">
                                <span>{{ $item->quantity }}x {{ $item->menu->name ?? 'Menu' }}</span>
                                <span class="text-gray-500">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    
                    <div class="flex justify-between items-center font-bold text-lg mb-4">
                        <span>Total:</span>
                        <span class="text-primary">Rp {{ number_format($order->amount, 0, ',', '.') }}</span>
                    </div>

                    @if($order->status == 'pending')
                        <button onclick="openPaymentModal({{ $order->id }}, {{ $order->amount }})" class="w-full bg-green-500 text-white font-bold py-2 rounded shadow hover:bg-green-600 transition">
                            PROSES BAYAR
                        </button>
                    @elseif($order->order_status == 'diproses')
                        <form action="{{ route('transaction.status', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="order_status" value="selesai">
                            <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 rounded shadow hover:bg-blue-600 transition" onclick="return confirm('Tandai pesanan ini sudah diambil pelanggan?')">
                                TANDAI SELESAI
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Belum ada pesanan aktif saat ini.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- MODAL PEMBAYARAN -->
<div id="payment-modal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl w-full max-w-md mx-4 shadow-2xl overflow-hidden transform scale-100 transition-all">
        <div class="bg-gray-800 px-6 py-4 flex justify-between items-center text-white">
            <h3 class="font-bold text-lg">Proses Pembayaran</h3>
            <button onclick="closePaymentModal()" class="text-gray-300 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6">
            <div class="text-center mb-6">
                <div class="text-sm text-gray-500 mb-1">Total Tagihan</div>
                <div class="text-4xl font-extrabold text-primary" id="modal-amount-display">Rp 0</div>
            </div>
            
            <form id="form-pay" action="" method="POST">
                @csrf
                <div class="space-y-3 mb-6">
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_method" value="tunai" class="peer hidden" checked onchange="toggleNfcInput()">
                        <div class="p-4 border-2 rounded-xl peer-checked:border-primary peer-checked:bg-blue-50 hover:bg-gray-50 transition flex items-center">
                            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <div class="font-bold text-gray-800 text-lg">Tunai / Cash</div>
                        </div>
                    </label>
                    
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_method" value="nfc" class="peer hidden" onchange="toggleNfcInput()">
                        <div class="p-4 border-2 rounded-xl peer-checked:border-primary peer-checked:bg-blue-50 hover:bg-gray-50 transition flex items-center">
                            <div class="w-10 h-10 bg-blue-100 text-primary rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <div class="font-bold text-gray-800 text-lg">Kartu Kantin (NFC)</div>
                        </div>
                    </label>
                    
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment_method" value="midtrans" class="peer hidden" onchange="toggleNfcInput()">
                        <div class="p-4 border-2 rounded-xl peer-checked:border-primary peer-checked:bg-blue-50 hover:bg-gray-50 transition flex items-center">
                            <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            <div class="font-bold text-gray-800 text-lg">QRIS / Online (Midtrans)</div>
                        </div>
                    </label>
                </div>
                
                <div id="nfc-input-container" class="hidden mb-6 bg-blue-50 border border-blue-200 p-4 rounded-xl">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-primary uppercase tracking-wide">Tempelkan Kartu (NFC)</label>
                        <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">Siap Tap</span>
                    </div>
                    <input type="text" name="card_uid" id="modal-card-uid" placeholder="Tempelkan kartu NFC ke scanner..." class="w-full px-4 py-3 border-2 border-blue-300 rounded-lg focus:ring-primary focus:border-primary text-lg font-mono text-center">
                </div>
                
                <button type="submit" id="btn-submit-payment" class="w-full bg-green-500 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-green-600 transition text-lg">
                    KONFIRMASI PEMBAYARAN
                </button>
            </form>
        </div>
    </div>
</div>

<!-- TAB 2: MANAJEMEN MENU -->
<div id="tab-manajemen" class="tab-content hidden">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Form Tambah/Edit Menu -->
        <div class="lg:col-span-1">
            <div class="glass p-6 rounded-xl shadow-md bg-white border border-gray-200 sticky top-24" id="menu-form-container">
                <h3 class="font-bold text-gray-800 mb-4 flex justify-between items-center text-lg border-b pb-2">
                    <span id="menu-form-title">Tambah Menu Baru</span>
                    <button type="button" id="btn-cancel-edit" class="hidden text-xs px-3 py-1 bg-gray-200 rounded text-gray-600 hover:bg-gray-300 font-bold transition">BATAL</button>
                </h3>
                <form action="{{ route('menu.store') }}" method="POST" enctype="multipart/form-data" id="form-menu">
                    @csrf
                    <div id="method-container"></div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu</label>
                        <input type="text" name="name" id="input-menu-name" required class="w-full px-3 py-2 border rounded-md focus:ring-primary">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category" id="input-menu-category" required class="w-full px-3 py-2 border rounded-md focus:ring-primary">
                            <option value="Makanan">Makanan</option>
                            <option value="Minuman">Minuman</option>
                            <option value="Jajanan">Jajanan</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                        <input type="number" name="price" id="input-menu-price" required class="w-full px-3 py-2 border rounded-md focus:ring-primary">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Produk <span class="text-xs text-gray-400 font-normal" id="image-help-text">(Opsional)</span></label>
                        <input type="file" name="image" accept="image/*" class="w-full px-3 py-1 border rounded-md text-sm">
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                        <input type="text" name="description" id="input-menu-desc" class="w-full px-3 py-2 border rounded-md focus:ring-primary">
                    </div>
                    <button type="submit" id="btn-submit-menu" class="w-full py-3 bg-secondary text-white rounded-md font-bold hover:bg-blue-600 transition shadow-md">Simpan Menu</button>
                </form>
            </div>
        </div>

        <!-- Daftar Menu Lengkap -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="font-bold text-gray-800 text-lg mb-2">Daftar Menu Tersedia</h3>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-gray-600 border-b">
                        <tr>
                            <th class="p-4 w-16">Foto</th>
                            <th class="p-4">Info Menu</th>
                            <th class="p-4 text-right">Harga</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $menu)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4">
                                @if($menu->image)
                                    <img src="{{ asset('storage/' . $menu->image) }}" class="w-12 h-12 object-cover rounded shadow-sm">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center text-gray-400 shadow-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800 text-base">{{ $menu->name }}</div>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">{{ $menu->category }}</span>
                                    <span class="text-xs text-gray-500 truncate max-w-[200px]" title="{{ $menu->description }}">{{ $menu->description }}</span>
                                </div>
                            </td>
                            <td class="p-4 text-right font-extrabold text-gray-800">
                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button type="button" class="btn-edit-menu px-3 py-1.5 bg-yellow-100 text-yellow-700 rounded font-bold hover:bg-yellow-200 transition"
                                            data-id="{{ $menu->id }}" data-name="{{ $menu->name }}" data-category="{{ $menu->category }}" 
                                            data-price="{{ $menu->price }}" data-description="{{ $menu->description }}">
                                        Edit
                                    </button>
                                    <form action="{{ route('menu.destroy', $menu->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-100 text-red-600 font-bold rounded hover:bg-red-200 transition" onclick="return confirm('Hapus menu ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">Belum ada menu. Silakan tambahkan menu baru.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- TAB 3: LAPORAN & RIWAYAT -->
<div id="tab-laporan" class="tab-content hidden">
    <div class="space-y-6">
        
        <!-- Pendapatan -->
        <div class="glass p-8 rounded-2xl shadow-md bg-gradient-to-r from-blue-700 to-blue-500 text-white relative overflow-hidden flex items-center justify-between">
            <div>
                <h2 class="text-sm uppercase tracking-widest font-semibold mb-2 opacity-80 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Total Pendapatan Hari Ini
                </h2>
                <div class="text-5xl font-extrabold tracking-tight">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </div>
            </div>
            <div class="opacity-20 hidden md:block">
                <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>

        <!-- Riwayat Transaksi Lengkap -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                Riwayat Transaksi Terbaru
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-4 rounded-tl-lg">Waktu</th>
                            <th class="p-4">Kartu Pelanggan</th>
                            <th class="p-4">Item Terjual</th>
                            <th class="p-4 text-right">Nominal</th>
                            <th class="p-4 text-center">Status Bayar</th>
                            <th class="p-4 text-center rounded-tr-lg">Status Pesanan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-gray-600 font-medium">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                            <td class="p-4">
                                @if($trx->card)
                                    <div class="font-bold text-gray-800">{{ $trx->card->card_uid }}</div>
                                    <div class="text-xs text-gray-500 font-normal">Mhs: {{ $trx->card->user->name ?? 'Unknown' }}</div>
                                @else
                                    <div class="font-bold text-gray-800">Pelanggan Umum</div>
                                    <div class="text-xs text-gray-500 font-normal">Via {{ ucfirst($trx->payment_method) }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-gray-600">
                                @if($trx->items && $trx->items->count() > 0)
                                    <ul class="list-disc pl-4 text-xs space-y-1">
                                    @foreach($trx->items as $item)
                                        <li>{{ $item->menu->name ?? 'Menu' }} (x{{ $item->quantity }})</li>
                                    @endforeach
                                    </ul>
                                @else
                                    <span class="text-xs italic text-gray-400">Tidak ada detail item</span>
                                @endif
                            </td>
                            <td class="p-4 text-right font-extrabold text-gray-800 text-base">
                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                @if($trx->status == 'success')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-bold text-xs shadow-sm">SUKSES</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full font-bold text-xs shadow-sm">GAGAL</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($trx->status == 'success' && $trx->order_status == 'diproses')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold block mb-1">DIPROSES</span>
                                    <form action="{{ route('transaction.status', $trx->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="order_status" value="selesai">
                                        <button type="submit" class="text-[10px] bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded shadow" onclick="return confirm('Tandai pesanan selesai dan sudah diambil?')">
                                            Tandai Selesai
                                        </button>
                                    </form>
                                @elseif($trx->status == 'success' && $trx->order_status == 'selesai')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-bold text-xs shadow-sm">SELESAI</span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">Belum ada transaksi di toko Anda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- TAB 5: PENGATURAN PROFIL & NAMA STAN -->
<div id="tab-profil" class="tab-content hidden">
    <div class="max-w-3xl bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Banner Profil -->
        <div class="bg-gradient-to-r from-primary to-blue-600 p-6 sm:p-8 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                @if(Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-white/40 shadow-md">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-white/10 border-2 border-white/30 backdrop-blur-sm flex items-center justify-center font-black text-2xl text-white shadow-inner">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <h3 class="text-2xl font-black leading-tight">{{ Auth::user()->name }}</h3>
                    <p class="text-white/80 text-sm mt-0.5 flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ Auth::user()->email }}
                    </p>
                    <span class="inline-block mt-2 px-2.5 py-0.5 bg-white/20 rounded-full text-xs font-bold uppercase tracking-wider text-white">
                        Pedagang / Mitra Stan Kantin Polbeng
                    </span>
                </div>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-xl px-4 py-2.5 text-xs text-right">
                <div class="text-white/70">Jatuh Tempo Sewa:</div>
                <div class="font-bold text-white text-sm">
                    {{ Auth::user()->rent_due_date ? \Carbon\Carbon::parse(Auth::user()->rent_due_date)->format('d M Y') : 'Aktif' }}
                </div>
            </div>
        </div>

        <!-- Form Edit Profil Langsung di Tab -->
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" onsubmit="localStorage.setItem('pedagang_active_tab', 'profil')" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="avatar_cropped">

            <div class="border-b border-gray-100 pb-4">
                <h4 class="text-base font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                    Informasi Data Stan & Pedagang
                </h4>
                <p class="text-xs text-gray-500 mt-1">Ubah nama stan pedagang, email login, dan pasang foto profil stan Anda.</p>
            </div>

            <!-- Upload Foto Profil -->
            <div class="bg-slate-50 border border-gray-200 rounded-2xl p-4 sm:p-5">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-3">Foto Profil Stan / Pedagang</label>
                <div class="flex flex-col sm:flex-row items-center gap-5">
                    <div class="relative shrink-0">
                        <div id="preview-avatar-tab-container" class="w-20 h-20 rounded-2xl overflow-hidden border-2 border-dashed border-gray-300 flex items-center justify-center bg-white shadow-sm">
                            @if(Auth::user()->avatar_url)
                                <img id="preview-avatar-tab" src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <div id="preview-avatar-tab-fallback" class="w-full h-full bg-primary/10 flex items-center justify-center text-primary font-black text-2xl">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 space-y-2 text-center sm:text-left">
                        <div class="flex flex-wrap items-center gap-2 justify-center sm:justify-start">
                            <label class="cursor-pointer px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-2xs hover:border-gray-400 transition inline-flex items-center space-x-1.5">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Pilih Foto Baru</span>
                                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden" onclick="this.value = null" onchange="previewProfileImage(this, 'preview-avatar-tab', 'preview-avatar-tab-fallback')">
                            </label>
                            @if(Auth::user()->avatar)
                                <label class="cursor-pointer px-3 py-2 bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl text-xs font-semibold text-red-600 transition inline-flex items-center space-x-1">
                                    <input type="checkbox" name="remove_avatar" value="1" class="rounded border-red-300 text-red-600 mr-1.5">
                                    <span>Hapus Foto Profil</span>
                                </label>
                            @endif
                        </div>
                        <p class="text-[11px] text-gray-400">Format yang didukung: JPG, PNG, WEBP. Ukuran maksimal 3MB.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Nama Stan / Pedagang <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary text-sm font-semibold text-gray-800 shadow-2xs">
                    <p class="text-[11px] text-gray-400 mt-1.5">Contoh: "Stan Makanan Mbak Sri", "Kantin Juice Segar"</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Alamat Email Login <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary text-sm font-medium text-gray-800 shadow-2xs">
                    <p class="text-[11px] text-gray-400 mt-1.5">Digunakan untuk login ke sistem.</p>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6">
                <h4 class="text-base font-bold text-gray-800 flex items-center mb-1">
                    <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Ganti Password (Opsional)
                </h4>
                <p class="text-xs text-gray-500 mb-4">Kosongkan jika tidak berniat mengganti password lama.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password Baru</label>
                        <input type="password" name="password" autocomplete="new-password" placeholder="Minimal 6 karakter" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi password baru" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary text-sm">
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-6 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-primary hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan Profil</span>
                </button>
            </div>
        </form>
    </div>
</div>

        </main>
    </div>
</div>

<!-- MODAL EDIT PROFIL PEDAGANG -->
<div id="modal-edit-profile" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden transform transition-all animate-in fade-in zoom-in duration-200">
        <!-- Header Modal -->
        <div class="bg-gradient-to-r from-primary to-blue-600 px-6 py-4 flex justify-between items-center text-white">
            <div class="flex items-center space-x-2">
                <div class="p-1.5 bg-white/20 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                </div>
                <h3 class="font-extrabold text-lg">Edit Profil & Foto Stan</h3>
            </div>
            <button type="button" onclick="closeProfileModal()" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Form Edit Profil -->
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" onsubmit="localStorage.setItem('pedagang_active_tab', 'profil')" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="avatar_cropped">

            <!-- Avatar Preview & Upload di Modal -->
            <div class="flex items-center space-x-4 pb-3 border-b border-gray-100">
                <div class="relative shrink-0">
                    <div id="preview-avatar-modal-container" class="w-16 h-16 rounded-full overflow-hidden border-2 border-primary/30 flex items-center justify-center bg-gray-50 shadow-inner">
                        @if(Auth::user()->avatar_url)
                            <img id="preview-avatar-modal" src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <div id="preview-avatar-modal-fallback" class="w-full h-full bg-primary/10 flex items-center justify-center text-primary font-black text-xl">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <label class="cursor-pointer inline-flex items-center space-x-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-bold transition">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Ganti Foto</span>
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden" onclick="this.value = null" onchange="previewProfileImage(this, 'preview-avatar-modal', 'preview-avatar-modal-fallback')">
                    </label>
                    @if(Auth::user()->avatar)
                        <label class="block mt-1 text-[11px] text-red-600 font-semibold cursor-pointer">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-red-300 text-red-600 mr-1">
                            Hapus foto profil
                        </label>
                    @endif
                    <div class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, WEBP (maks 3MB)</div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1.5">Nama Stan / Pedagang <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary text-sm font-semibold text-gray-800 placeholder-gray-400 shadow-2xs">
                <p class="text-[11px] text-gray-400 mt-1">Nama ini akan tampil di kasir dan struk transaksi.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary text-sm font-medium text-gray-800 placeholder-gray-400 shadow-2xs">
            </div>

            <div class="pt-2 border-t border-gray-100">
                <span class="text-xs font-bold text-gray-500 block mb-2">Ganti Password (Opsional)</span>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Password Baru</label>
                        <input type="password" name="password" autocomplete="new-password" placeholder="Kosongkan jika tidak ingin mengganti" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg focus:ring-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-600 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi password baru" class="w-full px-3.5 py-2 border border-gray-300 rounded-lg focus:ring-primary text-sm">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-2.5">
                <button type="button" onclick="closeProfileModal()" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 font-bold text-sm transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary hover:bg-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/20 transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL CEK SALDO KARTU NFC                 -->
<!-- ========================================== -->
<div id="modal-cek-saldo" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl relative border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-3.5 border-b border-gray-100">
            <div class="flex items-center space-x-2.5">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shadow-inner">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-gray-900 leading-tight">Cek Saldo Kartu NFC</h3>
                    <p class="text-[11px] text-gray-500">Pengecekan saldo tanpa memotong biaya</p>
                </div>
            </div>
            <button type="button" onclick="closeCekSaldoModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="mt-4 space-y-4">
            
            <!-- Sensor Status & Visual Indicator -->
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-200/80 rounded-2xl p-6 text-center shadow-2xs">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-white text-indigo-600 flex items-center justify-center shadow-sm border border-indigo-100 mb-3">
                    <svg class="w-8 h-8 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h4 class="font-extrabold text-base text-gray-900">Tempelkan Kartu NFC Sekarang</h4>
                <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">Dekatkan kartu ke alat USB RFID Reader untuk memeriksa saldo secara otomatis</p>
                <div class="mt-4 inline-flex items-center space-x-2 bg-emerald-50 text-emerald-700 px-3.5 py-1.5 rounded-full text-xs font-bold border border-emerald-200 shadow-2xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Sensor Reader Siap Membaca</span>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="cek-saldo-loading" class="hidden text-center py-6">
                <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin mx-auto mb-2"></div>
                <span class="text-xs font-bold text-gray-500">Membaca data kartu NFC...</span>
            </div>

            <!-- Error Message Box -->
            <div id="cek-saldo-error" class="hidden bg-rose-50 border border-rose-200 text-rose-700 p-3.5 rounded-2xl text-xs flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span id="cek-saldo-error-text" class="font-semibold"></span>
            </div>

            <!-- Result Card -->
            <div id="cek-saldo-result" class="hidden space-y-3 pt-2">
                
                <!-- Pemilik Kartu -->
                <div class="flex items-center space-x-3 bg-slate-50 p-3 rounded-2xl border border-gray-100">
                    <div id="cek-saldo-avatar-box" class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0 border border-gray-200 overflow-hidden">
                        <span id="cek-saldo-avatar-fallback">M</span>
                        <img id="cek-saldo-avatar-img" src="" alt="Avatar" class="w-full h-full object-cover hidden">
                    </div>
                    <div class="min-w-0 flex-1">
                        <div id="cek-saldo-nama" class="font-bold text-sm text-gray-900 truncate">Nama Mahasiswa</div>
                        <div class="text-[11px] text-gray-500 font-mono flex items-center space-x-2">
                            <span id="cek-saldo-uid" class="font-bold text-gray-700">UID: -</span>
                            <span id="cek-saldo-status" class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-800">Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Saldo Card -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-700 text-white p-5 rounded-2xl shadow-lg shadow-emerald-500/15 text-center">
                    <span class="text-[11px] uppercase tracking-wider text-emerald-100 font-bold block">Saldo Kartu Saat Ini</span>
                    <div id="cek-saldo-amount" class="text-3xl font-black mt-1">Rp 0</div>
                    <p class="text-[11px] text-emerald-100/90 mt-1">Saldo utuh dan tidak terpotong</p>
                </div>

                <!-- Limit Info -->
                <div class="grid grid-cols-2 gap-2 text-center text-xs">
                    <div class="bg-slate-50 border border-gray-100 p-2.5 rounded-xl">
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Limit Harian</span>
                        <span id="cek-saldo-limit-daily" class="font-bold text-gray-800 text-xs block mt-0.5">Tanpa Limit</span>
                    </div>
                    <div class="bg-slate-50 border border-gray-100 p-2.5 rounded-xl">
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Sisa Limit Hari Ini</span>
                        <span id="cek-saldo-limit-remaining" class="font-bold text-emerald-600 text-xs block mt-0.5">Bebas</span>
                    </div>
                </div>

            </div>

        </div>

        <div class="mt-5 pt-3 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closeCekSaldoModal()" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition">
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
    // HEADER DESKRIPSI UNTUK TIAP TAB
    const tabHeaders = {
        kasir: {
            title: 'Mesin Kasir (POS)',
            subtitle: 'Pilih katalog menu dan proses transaksi pembayaran tunai atau kartu NFC'
        },
        pesanan: {
            title: 'Pesanan Aktif',
            subtitle: 'Kelola pesanan yang menunggu pembayaran atau sedang diproses'
        },
        manajemen: {
            title: 'Manajemen Menu',
            subtitle: 'Tambah, edit, dan kelola daftar harga serta foto menu makanan/minuman'
        },
        laporan: {
            title: 'Laporan & Riwayat Penjualan',
            subtitle: 'Rekapitulasi total pendapatan dan riwayat transaksi pedagang'
        },
        profil: {
            title: 'Pengaturan Profil & Nama Stan',
            subtitle: 'Kelola nama stan pedagang, email login, dan kata sandi akun'
        }
    };

    // TOGGLE SIDEBAR MOBILE
    function toggleMobileSidebar() {
        const sidebar = document.getElementById('pedagang-sidebar');
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

    // FUNGSI MODAL EDIT PROFIL
    function openProfileModal() {
        const modal = document.getElementById('modal-edit-profile');
        if (modal) modal.classList.remove('hidden');
    }

    function closeProfileModal() {
        const modal = document.getElementById('modal-edit-profile');
        if (modal) modal.classList.add('hidden');
    }

    function previewProfileImage(input, previewId, fallbackId) {
        if (typeof handleAvatarCropSelect === 'function') {
            handleAvatarCropSelect(input, [previewId, 'preview-avatar-tab', 'preview-avatar-modal'], [fallbackId, 'preview-avatar-tab-fallback', 'preview-avatar-modal-fallback']);
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

    // FUNGSI TAB NAVIGASI
    function switchTab(tabId) {
        if (!tabHeaders[tabId]) tabId = 'kasir';

        // Simpan preferensi tab
        try {
            localStorage.setItem('pedagang_active_tab', tabId);
        } catch (e) {}

        // Sembunyikan semua tab content
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('block');
        });

        // Tampilkan tab yang dipilih
        const targetTab = document.getElementById('tab-' + tabId);
        if (targetTab) {
            targetTab.classList.remove('hidden');
            targetTab.classList.add('block');
        }

        // Reset styling semua tombol navigasi tab
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('text-primary', 'bg-blue-50', 'border-primary', 'shadow-xs', 'font-bold');
            btn.classList.add('text-gray-600', 'border-transparent', 'font-semibold');
            const icon = btn.querySelector('.tab-icon');
            if (icon) {
                icon.classList.remove('text-primary');
                icon.classList.add('text-gray-400');
            }
        });

        // Highlight tombol aktif
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

        // Update teks judul & deskripsi di topbar header
        const titleEl = document.getElementById('topbar-title-text');
        const subtitleEl = document.getElementById('topbar-subtitle');
        if (titleEl && tabHeaders[tabId]) titleEl.innerText = tabHeaders[tabId].title;
        if (subtitleEl && tabHeaders[tabId]) subtitleEl.innerText = tabHeaders[tabId].subtitle;

        // Tutup sidebar drawer di mobile setelah memilih tab
        const sidebar = document.getElementById('pedagang-sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        if (sidebar && !sidebar.classList.contains('-translate-x-full') && window.innerWidth < 768) {
            sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
        }
    }

    // Auto restore tab saat reload
    document.addEventListener('DOMContentLoaded', () => {
        let initialTab = 'kasir';
        try {
            const saved = localStorage.getItem('pedagang_active_tab');
            if (saved && tabHeaders[saved]) initialTab = saved;
        } catch (e) {}
        switchTab(initialTab);
    });

    // FUNGSI KERANJANG KASIR
    let cart = {};

    function addToCart(id, name, price) {
        if (cart[id]) {
            cart[id].quantity++;
        } else {
            cart[id] = { name: name, price: parseInt(price), quantity: 1 };
        }
        updateCartUI();
    }

    function updateCartUI() {
        const container = document.getElementById('cart-items-list');
        const emptyMsg = document.getElementById('empty-cart-msg');
        const totalDisplay = document.getElementById('cart-total-display');
        const hiddenInputs = document.getElementById('hidden-items-container');
        const nfcHiddenInputs = document.getElementById('nfc-hidden-items-container');
        const btnCheckout = document.getElementById('btn-checkout');
        const nfcInput = document.getElementById('kasir-nfc-input');
        
        let htmlStr = '';
        let hiddenStr = '';
        
        let total = 0;
        let index = 0;
        let itemCount = 0;

        for (const id in cart) {
            const item = cart[id];
            const subtotal = parseInt(item.price) * parseInt(item.quantity);
            total += subtotal;
            itemCount++;
            
            // UI Item
            htmlStr += `
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm flex flex-col space-y-2">
                    <div class="flex justify-between items-start">
                        <div class="font-bold text-gray-800 text-sm leading-tight">${item.name}</div>
                        <button type="button" class="text-red-400 hover:text-red-600 ml-2" onclick="removeItem('${id}')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="text-primary font-bold text-sm">Rp ${subtotal.toLocaleString('id-ID')}</div>
                        <div class="flex items-center bg-gray-100 rounded-md">
                            <button type="button" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-l-md" onclick="minItem('${id}')">-</button>
                            <span class="w-8 text-center font-bold text-sm">${item.quantity}</span>
                            <button type="button" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-r-md" onclick="plusItem('${id}')">+</button>
                        </div>
                    </div>
                </div>
            `;

            // Hidden Inputs for Form Submit
            hiddenStr += `
                <input type="hidden" name="items[${index}][menu_id]" value="${id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            `;
            index++;
        }

        container.innerHTML = htmlStr;
        hiddenInputs.innerHTML = hiddenStr;
        nfcHiddenInputs.innerHTML = hiddenStr;

        const btnNfcSubmit = document.getElementById('btn-nfc-submit');
        if (itemCount === 0) {
            emptyMsg.style.display = 'block';
            btnCheckout.disabled = true;
            btnCheckout.classList.add('opacity-50', 'cursor-not-allowed');
            
            nfcInput.disabled = true;
            nfcInput.value = '';
            if (btnNfcSubmit) btnNfcSubmit.disabled = true;
        } else {
            emptyMsg.style.display = 'none';
            btnCheckout.disabled = false;
            btnCheckout.classList.remove('opacity-50', 'cursor-not-allowed');
            
            nfcInput.disabled = false;
            if (btnNfcSubmit) btnNfcSubmit.disabled = false;
            // Auto focus input NFC agar siap di-tap oleh reader/keyboard
            setTimeout(() => {
                if (document.activeElement !== nfcInput && !document.getElementById('payment-modal')?.classList.contains('hidden')) {
                    // Jangan ganggu jika modal terbuka
                } else {
                    nfcInput.focus();
                }
            }, 100);
        }

        totalDisplay.innerHTML = 'Rp ' + total.toLocaleString('id-ID');
        const countBadge = document.getElementById('cart-item-count-badge');
        if (countBadge) countBadge.innerText = itemCount + ' Menu';
    }

    // Aksi kontrol keranjang
    window.plusItem = function(id) { cart[id].quantity++; updateCartUI(); }
    window.minItem = function(id) { 
        cart[id].quantity--; 
        if(cart[id].quantity <= 0) delete cart[id]; 
        updateCartUI(); 
    }
    window.removeItem = function(id) { delete cart[id]; updateCartUI(); }

    // FUNGSI EDIT MENU
    const formMenu = document.getElementById('form-menu');
    const methodContainer = document.getElementById('method-container');
    const inputName = document.getElementById('input-menu-name');
    const inputCategory = document.getElementById('input-menu-category');
    const inputPrice = document.getElementById('input-menu-price');
    const inputDesc = document.getElementById('input-menu-desc');
    const formTitle = document.getElementById('menu-form-title');
    const btnSubmit = document.getElementById('btn-submit-menu');
    const btnCancel = document.getElementById('btn-cancel-edit');
    const imageHelpText = document.getElementById('image-help-text');
    const originalAction = "{{ route('menu.store') }}";

    document.querySelectorAll('.btn-edit-menu').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = e.target.getAttribute('data-id');
            inputName.value = e.target.getAttribute('data-name');
            inputCategory.value = e.target.getAttribute('data-category');
            inputPrice.value = e.target.getAttribute('data-price');
            inputDesc.value = e.target.getAttribute('data-description');
            
            formMenu.action = `/pedagang/menu/${id}`;
            methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            formTitle.innerText = "Edit Menu";
            btnSubmit.innerText = "SIMPAN PERUBAHAN";
            btnSubmit.classList.replace('bg-secondary', 'bg-yellow-500');
            btnSubmit.classList.replace('hover:bg-blue-600', 'hover:bg-yellow-600');
            imageHelpText.innerText = "(Biarkan kosong jika tak diganti)";
            
            btnCancel.classList.remove('hidden');
            
            // Pindah form ke tampilan yang terlihat
            document.getElementById('menu-form-container').scrollIntoView({ behavior: 'smooth' });
        });
    });

    btnCancel.addEventListener('click', () => {
        formMenu.action = originalAction;
        methodContainer.innerHTML = '';
        formMenu.reset();
        
        formTitle.innerText = "Tambah Menu Baru";
        btnSubmit.innerText = "Simpan Menu";
        btnSubmit.classList.replace('bg-yellow-500', 'bg-secondary');
        btnSubmit.classList.replace('hover:bg-yellow-600', 'hover:bg-blue-600');
        imageHelpText.innerText = "(Opsional)";
        
        btnCancel.classList.add('hidden');
    });

    // FUNGSI MODAL PEMBAYARAN
    function openPaymentModal(id, amount) {
        document.getElementById('payment-modal').classList.remove('hidden');
        document.getElementById('modal-amount-display').innerText = 'Rp ' + parseInt(amount).toLocaleString('id-ID');
        
        // Setup Form
        const formPay = document.getElementById('form-pay');
        formPay.action = `/pedagang/transaction/${id}/pay`;
        
        // Reset state
        document.querySelector('input[name="payment_method"][value="tunai"]').checked = true;
        toggleNfcInput();
    }

    function closePaymentModal() {
        document.getElementById('payment-modal').classList.add('hidden');
    }

    function toggleNfcInput() {
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const nfcContainer = document.getElementById('nfc-input-container');
        const nfcInput = document.getElementById('modal-card-uid');
        
        if (method === 'nfc') {
            nfcContainer.classList.remove('hidden');
            nfcInput.required = true;
            setTimeout(() => nfcInput.focus(), 100);
        } else {
            nfcContainer.classList.add('hidden');
            nfcInput.required = false;
        }
    }

    // Ajax submit for payment
    document.getElementById('form-pay').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        const form = this;
        const btnSubmit = document.getElementById('btn-submit-payment');
        
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'MEMPROSES...';
        
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (method === 'midtrans' && data.snap_token) {
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result){
                            alert("Pembayaran online berhasil!");
                            window.location.reload();
                        },
                        onPending: function(result){
                            alert("Menunggu pembayaran Anda!");
                            window.location.reload();
                        },
                        onError: function(result){
                            alert("Pembayaran gagal!");
                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML = 'KONFIRMASI PEMBAYARAN';
                        },
                        onClose: function(){
                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML = 'KONFIRMASI PEMBAYARAN';
                        }
                    });
                } else {
                    alert(data.message || 'Pembayaran berhasil!');
                    window.location.reload();
                }
            } else {
                alert('Gagal: ' + data.message);
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = 'KONFIRMASI PEMBAYARAN';
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan jaringan.');
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'KONFIRMASI PEMBAYARAN';
        });
    });

    // ================= FITUR NFC (WEB NFC, AUDIO FEEDBACK, KEYBOARD BUFFER & SIMULATOR) =================
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
            } else {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(220, ctx.currentTime);
                gain.gain.setValueAtTime(0.25, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
                osc.start(ctx.currentTime);
                osc.stop(ctx.currentTime + 0.3);
            }
        } catch(e) {}
    }

    // Web NFC API Integration (Untuk Smartphone / Tablet dengan NFC Hardware)
    async function initWebNfc() {
        if (!('NDEFReader' in window)) return;
        try {
            const ndef = new NDEFReader();
            await ndef.scan();
            const badge = document.getElementById('nfc-sensor-badge');
            if (badge) {
                badge.innerHTML = '<span class="w-2 h-2 mr-1 rounded-full bg-green-500 animate-ping"></span> Web NFC Aktif';
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
                    const cekSaldoModal = document.getElementById('modal-cek-saldo');
                    if (cekSaldoModal && !cekSaldoModal.classList.contains('hidden')) {
                        doCheckBalance(uid);
                        return;
                    }

                    playBeep(true);
                    const modal = document.getElementById('payment-modal');
                    if (modal && !modal.classList.contains('hidden')) {
                        const modalInput = document.getElementById('modal-card-uid');
                        if (modalInput) {
                            modalInput.value = uid;
                            document.getElementById('form-pay').requestSubmit();
                        }
                    } else {
                        const cartInput = document.getElementById('kasir-nfc-input');
                        if (cartInput && !cartInput.disabled) {
                            cartInput.value = uid;
                            document.getElementById('nfc-checkout-form').submit();
                        }
                    }
                }
            };
            return true;
        } catch (err) {
            console.warn("Web NFC scan error: ", err);
            return false;
        }
    }

    if ('NDEFReader' in window) {
        const banner = document.getElementById('web-nfc-banner');
        if (banner) banner.classList.remove('hidden');
        document.getElementById('btn-activate-webnfc')?.addEventListener('click', async () => {
            const ok = await initWebNfc();
            if (ok) {
                alert('Sensor Web NFC aktif! Dekatkan kartu NFC ke smartphone.');
                banner.classList.add('hidden');
            }
        });
    }

    // USB RFID Keyboard-Wedge Fast Reader Listener
    let nfcKeyBuffer = '';
    let nfcLastKeyTime = Date.now();
    window.addEventListener('keydown', (e) => {
        if (['input-menu-name', 'input-menu-category', 'input-menu-price', 'input-menu-desc'].includes(e.target.id)) {
            return;
        }
        const now = Date.now();
        if (now - nfcLastKeyTime > 200) {
            nfcKeyBuffer = '';
        }
        nfcLastKeyTime = now;

        if (e.key === 'Enter') {
            if (nfcKeyBuffer.length >= 4) {
                const cekSaldoModal = document.getElementById('modal-cek-saldo');
                if (cekSaldoModal && !cekSaldoModal.classList.contains('hidden')) {
                    doCheckBalance(nfcKeyBuffer);
                    nfcKeyBuffer = '';
                    return;
                }

                const modal = document.getElementById('payment-modal');
                if (modal && !modal.classList.contains('hidden')) {
                    const modalInput = document.getElementById('modal-card-uid');
                    if (modalInput) {
                        modalInput.value = nfcKeyBuffer;
                        playBeep(true);
                    }
                } else {
                    const cartInput = document.getElementById('kasir-nfc-input');
                    if (cartInput && !cartInput.disabled) {
                        cartInput.value = nfcKeyBuffer;
                        playBeep(true);
                        document.getElementById('nfc-checkout-form').submit();
                    }
                }
                nfcKeyBuffer = '';
            }
        } else if (e.key.length === 1) {
            nfcKeyBuffer += e.key;
        }
    });

    // FUNGSI CEK SALDO KARTU NFC
    async function doCheckBalance(uid) {
        if (!uid) return;
        const modal = document.getElementById('modal-cek-saldo');
        if (modal && modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        }

        const inputUid = document.getElementById('input-cek-saldo-uid');
        if (inputUid) inputUid.value = uid;

        const loading = document.getElementById('cek-saldo-loading');
        const result = document.getElementById('cek-saldo-result');
        const errBox = document.getElementById('cek-saldo-error');
        const errText = document.getElementById('cek-saldo-error-text');

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
                document.getElementById('cek-saldo-nama').innerText = data.user_name || 'Mahasiswa';
                document.getElementById('cek-saldo-uid').innerText = 'UID: ' + data.card_uid;
                
                const statusEl = document.getElementById('cek-saldo-status');
                if (statusEl) {
                    statusEl.innerText = data.status === 'active' ? 'Aktif' : 'Diblokir';
                    statusEl.className = data.status === 'active' 
                        ? 'px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-800' 
                        : 'px-1.5 py-0.2 rounded text-[9px] font-bold uppercase bg-rose-100 text-rose-800';
                }

                document.getElementById('cek-saldo-amount').innerText = data.balance_formatted;
                document.getElementById('cek-saldo-limit-daily').innerText = data.daily_limit;
                document.getElementById('cek-saldo-limit-remaining').innerText = data.remaining_daily_limit;

                const avatarImg = document.getElementById('cek-saldo-avatar-img');
                const avatarFallback = document.getElementById('cek-saldo-avatar-fallback');
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

    function openCekSaldoModal() {
        const modal = document.getElementById('modal-cek-saldo');
        if (modal) {
            modal.classList.remove('hidden');
            document.getElementById('cek-saldo-result')?.classList.add('hidden');
            document.getElementById('cek-saldo-error')?.classList.add('hidden');
            document.getElementById('cek-saldo-loading')?.classList.add('hidden');
        }
    }

    function closeCekSaldoModal() {
        document.getElementById('modal-cek-saldo')?.classList.add('hidden');
    }
</script>
@endpush
