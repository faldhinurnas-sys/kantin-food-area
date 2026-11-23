@extends('layouts.app')
@section('title', 'Dashboard Wali / Orang Tua')

@section('content')
<div class="min-h-screen w-full flex flex-col md:flex-row bg-slate-100/70 text-gray-800">

    <!-- MOBILE TOPBAR (Tampil hanya di layar kecil < md) -->
    <header class="md:hidden bg-primary text-white px-4 py-3 flex items-center justify-between sticky top-0 z-40 shadow-md">
        <div class="flex items-center space-x-2.5">
            <div class="w-9 h-9 rounded-xl bg-white p-1 flex items-center justify-center shadow-inner shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold tracking-wide text-base">KANTIN NFC</span>
                <span class="text-[10px] bg-blue-500 text-white font-bold px-1.5 py-0.5 rounded ml-1 uppercase">Wali</span>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button type="button" onclick="openTopupWaliModal()" class="px-2.5 py-1 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}</span>
            </button>
            <button type="button" onclick="toggleMobileSidebar()" class="p-2 rounded-lg bg-blue-800 hover:bg-blue-700 focus:outline-none transition" aria-label="Toggle Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
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
                        <span class="px-2 py-0.5 bg-blue-100 text-primary rounded-full text-[10px] font-extrabold uppercase tracking-wide">WALI / ORANG TUA</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                </div>
            </div>
            <!-- Tombol Tutup di Mobile -->
            <button type="button" onclick="toggleMobileSidebar()" class="md:hidden text-gray-400 hover:text-gray-600 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- INFO PROFIL WALI -->
        <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/80">
            <div class="flex items-center space-x-3">
                @if(Auth::user()->avatar_url)
                    <img id="sidebar-wali-avatar" src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-11 h-11 rounded-full object-cover border border-primary/20 shadow-xs shrink-0">
                @else
                    <div id="sidebar-wali-avatar-fallback" class="w-11 h-11 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-base shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-bold text-gray-900 truncate" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500 truncate flex items-center mt-0.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                        Wali: {{ $mahasiswa->name ?? 'Mahasiswa' }}
                    </div>
                </div>
            </div>

            <!-- Widget Saldo Dompet Digital Wali -->
            <div class="mt-3.5 bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-3 rounded-2xl shadow-xs">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-emerald-100 tracking-wider block">Saldo Dompet Wali</span>
                        <div class="text-base font-black tracking-tight" id="sidebar-balance-text">Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <button type="button" onclick="openTopupWaliModal()" class="px-2.5 py-1 bg-white hover:bg-emerald-50 text-emerald-700 font-extrabold text-xs rounded-xl transition shadow-xs flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        <span>Top Up</span>
                    </button>
                </div>
            </div>

            <!-- Tombol Edit Profil -->
            <button type="button" onclick="switchWaliTab('profil')" class="mt-2.5 w-full flex items-center justify-center space-x-1.5 py-1.5 px-3 rounded-xl text-xs font-bold text-primary bg-blue-50 hover:bg-blue-100 border border-blue-200 transition shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Edit Profil & Keamanan</span>
            </button>
        </div>

        <!-- DAFTAR MENU NAVIGASI (TABS) -->
        <div class="flex-1 overflow-y-auto px-3.5 py-4 space-y-1.5">
            <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Navigasi Wali</div>

            <!-- Tab 1: Ringkasan & Top-Up -->
            <button type="button" onclick="switchWaliTab('ringkasan')" id="btn-wali-ringkasan" class="wali-tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-bold text-sm transition-all text-primary bg-blue-50 border-l-4 border-primary shadow-xs">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 wali-tab-icon text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Saldo & Top-Up</span>
                </div>
                <span class="text-[10px] uppercase font-bold bg-blue-600 text-white px-2 py-0.5 rounded-md">Utama</span>
            </button>

            <!-- Tab 2: Limit Belanja -->
            <button type="button" onclick="switchWaliTab('limit')" id="btn-wali-limit" class="wali-tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 wali-tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Atur Limit Belanja</span>
                </div>
                @if($limit && $limit->daily_limit)
                    <span class="text-[10px] bg-amber-100 text-amber-700 font-bold px-2 py-0.5 rounded-full">Aktif</span>
                @else
                    <span class="text-[10px] text-gray-400 font-medium">Bebas</span>
                @endif
            </button>

            <!-- Tab 3: Riwayat Transaksi -->
            <button type="button" onclick="switchWaliTab('transaksi')" id="btn-wali-transaksi" class="wali-tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 wali-tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Riwayat Transaksi Anak</span>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 font-bold px-2 py-0.5 rounded-full">{{ count($transactions) }}</span>
            </button>

            <!-- Tab 4: Profil Wali -->
            <button type="button" onclick="switchWaliTab('profil')" id="btn-wali-profil" class="wali-tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 wali-tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                    <span>Profil & Keamanan</span>
                </div>
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
                <h2 id="wali-topbar-title" class="text-xl font-extrabold text-gray-900 tracking-tight">Saldo & Top-Up</h2>
                <p id="wali-topbar-subtitle" class="text-xs text-gray-500 mt-0.5">Pantau saldo dompet wali dan kirimkan uang saku ke kartu NFC anak</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Saldo Dompet Wali -->
                <div class="flex items-center space-x-2 bg-emerald-50 border border-emerald-200 px-3.5 py-1.5 rounded-xl text-emerald-800 text-xs shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="font-semibold text-emerald-700">Dompet Wali:</span>
                    <span class="font-black text-emerald-900" id="topbar-balance-text">Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}</span>
                    <button type="button" onclick="openTopupWaliModal()" class="ml-1 px-2 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] rounded-lg transition shadow-2xs">
                        + Top Up
                    </button>
                </div>

                <!-- Saldo Anak -->
                <div class="flex items-center space-x-2 bg-blue-50 border border-blue-200 px-3.5 py-1.5 rounded-xl text-primary font-bold text-xs shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span>Saldo Anak: Rp {{ $card ? number_format($card->balance, 0, ',', '.') : '0' }}</span>
                </div>

                <div class="text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1.5 rounded-xl border border-gray-200">
                    {{ now()->translatedFormat('d M Y') }}
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
            <!-- TAB 1: SALDO & TOP-UP                                          -->
            <!-- ============================================================== -->
            <div id="tab-wali-ringkasan" class="wali-tab-content block space-y-6">

                <!-- PILIH ANAK ASUH / MAHASISWA -->
                <div class="bg-white rounded-2xl p-4 shadow-xs border border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-primary flex items-center justify-center font-bold shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Mahasiswa yang Dipantau</span>
                            <div class="text-sm font-extrabold text-gray-900 flex items-center gap-1.5">
                                <span>{{ $mahasiswa ? $mahasiswa->name : 'Belum Ada Mahasiswa' }}</span>
                                <span class="text-xs text-gray-400 font-normal font-mono">({{ $mahasiswa ? $mahasiswa->email : '-' }})</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 flex-wrap gap-y-2">
                        <button type="button" onclick="quickSendViaNim()" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            <span>Transfer via NIM</span>
                        </button>
                        <button type="button" onclick="quickSendToOtherStudent()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Pilih Mahasiswa Lain</span>
                        </button>
                        @if(isset($allMahasiswa) && count($allMahasiswa) > 1)
                            <div class="flex items-center space-x-1.5">
                                <label class="text-xs font-semibold text-gray-500 whitespace-nowrap">Pantau:</label>
                                <select onchange="window.location.href = '{{ route('wali.dashboard') }}?student_id=' + this.value" class="px-3 py-1.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-800 bg-gray-50 focus:ring-2 focus:ring-primary shadow-2xs">
                                    @foreach($allMahasiswa as $m)
                                        <option value="{{ $m->id }}" {{ $mahasiswa && $mahasiswa->id == $m->id ? 'selected' : '' }}>
                                            {{ $m->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- GRID UTAMA SALDO & FORM TRANSFER -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                    
                    <!-- SISI KIRI: 2 KARTU SALDO & RIWAYAT MUTASI DOMPET (XL: 7 Kolom) -->
                    <div class="xl:col-span-7 space-y-6">
                        
                        <!-- 2 KARTU SALDO BERDAMPINGAN -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            
                            <!-- 1. KARTU DOMPET DIGITAL WALI -->
                            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-700 to-slate-900 text-white p-5 sm:p-6 shadow-xl shadow-emerald-900/15 border border-emerald-400/20 flex flex-col justify-between min-h-[220px]">
                                <!-- Watermark Dompet -->
                                <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none">
                                    <svg class="w-44 h-44" fill="currentColor" viewBox="0 0 24 24"><path d="M21 18v1c0 1.1-.9 2-2 2H5c-1.11 0-2-.9-2-2V5c0-1.1.89-2 2-2h14c1.1 0 2 .9 2 2v1h-9c-1.11 0-2 .9-2 2v8c0 1.1.89 2 2 2h9zm-9-2h10V8H12v8zm4-2.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>
                                </div>

                                <!-- Card Header -->
                                <div class="flex items-center justify-between z-10">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-9 h-7 bg-amber-400/90 rounded-md shadow-xs flex items-center justify-center border border-amber-300">
                                            <div class="w-5 h-4 border border-amber-600/60 rounded-xs flex flex-col justify-around py-0.5 px-0.5">
                                                <div class="h-0.5 bg-amber-700/50"></div>
                                                <div class="h-0.5 bg-amber-700/50"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-[11px] tracking-wider uppercase font-extrabold text-emerald-200">DOMPET DIGITAL WALI</span>
                                            <span class="block text-[9px] text-emerald-300 font-mono">SUMBER DANA KIRIM</span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/80 text-white backdrop-blur-sm border border-emerald-400/30">
                                        <span class="w-1.5 h-1.5 mr-1 rounded-full bg-white animate-pulse"></span>
                                        Aktif
                                    </span>
                                </div>

                                <!-- Saldo Display -->
                                <div class="my-4 z-10">
                                    <div class="text-[11px] uppercase tracking-wider text-emerald-200 font-semibold mb-1">Saldo Tersedia Anda</div>
                                    <div class="text-2xl sm:text-3xl font-black tracking-tight" id="wallet-card-balance">
                                        Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}
                                    </div>
                                </div>

                                <!-- Card Footer & Tombol Top Up -->
                                <div class="flex items-center justify-between z-10 pt-3 border-t border-emerald-400/20">
                                    <div class="min-w-0 pr-2">
                                        <span class="text-[9px] uppercase tracking-wider text-emerald-300 block">Pemilik Dompet</span>
                                        <span class="font-bold text-xs text-white truncate block">
                                            {{ Auth::user()->name }}
                                        </span>
                                    </div>
                                    <button type="button" onclick="openTopupWaliModal()" class="shrink-0 px-3 py-1.5 bg-white hover:bg-emerald-50 text-emerald-800 font-extrabold text-xs rounded-xl shadow-md transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ Top Up</span>
                                    </button>
                                </div>
                            </div>

                            <!-- 2. KARTU NFC MAHASISWA (ANAK ASUH) -->
                            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary via-blue-700 to-indigo-900 text-white p-5 sm:p-6 shadow-xl shadow-blue-900/15 border border-white/10 flex flex-col justify-between min-h-[220px]">
                                <!-- Decorative Watermark -->
                                <div class="absolute -right-6 -bottom-6 opacity-10 pointer-events-none">
                                    <svg class="w-44 h-44" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                </div>

                                <!-- Card Header -->
                                <div class="flex items-center justify-between z-10">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-9 h-7 bg-amber-400/90 rounded-md shadow-xs flex items-center justify-center border border-amber-300">
                                            <div class="w-5 h-4 border border-amber-600/60 rounded-xs flex flex-col justify-around py-0.5 px-0.5">
                                                <div class="h-0.5 bg-amber-700/50"></div>
                                                <div class="h-0.5 bg-amber-700/50"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-[11px] tracking-wider uppercase font-extrabold text-blue-200">KARTU NFC ANAK</span>
                                            <span class="block text-[9px] text-blue-300 font-mono">PENERIMA SALDO</span>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/80 text-white backdrop-blur-sm border border-blue-400/30">
                                        <span class="w-1.5 h-1.5 mr-1 rounded-full bg-white animate-pulse"></span>
                                        {{ $card ? 'Terhubung' : 'Belum Ada' }}
                                    </span>
                                </div>

                                <!-- Saldo Display -->
                                <div class="my-4 z-10">
                                    <div class="text-[11px] uppercase tracking-wider text-blue-200 font-semibold mb-1">Saldo Kartu: {{ $mahasiswa->name ?? '-' }}</div>
                                    <div class="text-2xl sm:text-3xl font-black tracking-tight" id="student-card-balance">
                                        Rp {{ $card ? number_format($card->balance, 0, ',', '.') : '0' }}
                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div class="flex items-center justify-between z-10 pt-3 border-t border-white/15">
                                    <div class="min-w-0 pr-2">
                                        <span class="text-[9px] uppercase tracking-wider text-blue-300 block">UID Kartu</span>
                                        <span class="font-mono text-xs font-bold text-white tracking-wider truncate block">
                                            {{ $card ? $card->card_uid : '-' }}
                                        </span>
                                    </div>
                                    <div class="text-right min-w-0">
                                        <span class="text-[9px] uppercase tracking-wider text-blue-300 block">Mahasiswa</span>
                                        <span class="font-bold text-xs text-white truncate max-w-[110px] block">
                                            {{ $mahasiswa->name ?? '-' }}
                                        </span>
                                        @if($mahasiswa && $mahasiswa->nim)
                                            <span class="text-[9px] font-mono text-amber-300 font-bold block">NIM: {{ $mahasiswa->nim }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- MUTASI DOMPET WALI TERBARU -->
                        <div class="bg-white rounded-3xl p-5 shadow-xs border border-gray-100">
                            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 text-sm">Mutasi Saldo Dompet Wali</h4>
                                        <p class="text-[11px] text-gray-400">Riwayat pengisian dan pengiriman saldo dari dompet Anda</p>
                                    </div>
                                </div>
                                <button type="button" onclick="openTopupWaliModal()" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-xl transition border border-emerald-200">
                                    + Isi Dompet
                                </button>
                            </div>

                            @if(isset($walletTransactions) && count($walletTransactions) > 0)
                                <div class="divide-y divide-gray-100">
                                    @foreach($walletTransactions->take(3) as $wTrx)
                                        <div class="py-2.5 flex items-center justify-between hover:bg-gray-50/80 px-1 rounded-xl transition">
                                            <div class="flex items-center space-x-3 min-w-0">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $wTrx->card_id ? 'bg-blue-50 text-primary' : 'bg-emerald-50 text-emerald-600' }}">
                                                    @if($wTrx->card_id)
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                                    @else
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    @endif
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-xs text-gray-900 truncate">
                                                        {{ $wTrx->card_id ? 'Transfer Saldo ke Anak' : 'Top Up Saldo Dompet' }}
                                                    </div>
                                                    <div class="text-[10px] text-gray-400 truncate">
                                                        {{ $wTrx->created_at->translatedFormat('d M, H:i') }} | {{ $wTrx->payment_method === 'midtrans' ? 'Midtrans' : 'Instan' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0 ml-3">
                                                <div class="font-black text-xs sm:text-sm {{ $wTrx->card_id ? 'text-blue-600' : 'text-emerald-600' }}">
                                                    {{ $wTrx->card_id ? '-' : '+' }} Rp {{ number_format($wTrx->amount, 0, ',', '.') }}
                                                </div>
                                                @if($wTrx->status == 'pending')
                                                    <div class="flex items-center gap-1 justify-end mt-0.5">
                                                        <span class="inline-block px-1.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-700">Pending</span>
                                                        <button type="button" onclick="cancelMidtransTrx('{{ $wTrx->order_id }}')" class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 transition" title="Batalkan transaksi">Batalkan</button>
                                                    </div>
                                                @else
                                                    <span class="inline-block px-1.5 py-0.5 rounded-full text-[9px] font-extrabold {{ $wTrx->status == 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                        {{ $wTrx->status == 'success' ? 'Berhasil' : ($wTrx->order_status == 'batal' ? 'Dibatalkan' : 'Gagal') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6 text-gray-400">
                                    <p class="text-xs font-medium">Belum ada mutasi dompet. Klik "+ Isi Dompet" untuk mulai top up.</p>
                                </div>
                            @endif
                        </div>

                    </div>

                    <!-- SISI KANAN: FORM KIRIM SALDO MAHASISWA (XL: 5 Kolom) -->
                    <div class="xl:col-span-5 bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between" id="container-form-transfer">
                        <div>
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-base">Kirim Saldo Mahasiswa</h3>
                                        <p class="text-xs text-gray-400">Transfer dana ke anak asuh atau mahasiswa lain</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 bg-blue-50 text-primary text-[10px] font-extrabold uppercase rounded-lg border border-blue-200">
                                    Transfer Cepat
                                </span>
                            </div>

                            <!-- BANNER INFO SALDO DOMPET WALI -->
                            <div class="mb-4 p-3 bg-emerald-50/80 border border-emerald-200/80 rounded-2xl flex items-center justify-between">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-2xs shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">Saldo Dompet Anda</span>
                                        <div class="text-sm font-black text-emerald-900" id="form-wali-balance-text">Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                                <button type="button" onclick="openTopupWaliModal()" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl transition shadow-2xs flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Top Up</span>
                                </button>
                            </div>

                            <form id="topup-form" class="space-y-4">
                                @csrf
                                <input type="hidden" name="card_id" id="topup-card-id" value="{{ $card ? $card->id : '' }}">
                                <input type="hidden" name="nim" id="topup-student-nim" value="{{ $mahasiswa && $mahasiswa->nim ? $mahasiswa->nim : '' }}">
                                
                                <!-- PILIH TUJUAN PENERIMA SALDO -->
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-xs font-bold text-gray-700 uppercase">Tujuan Pengiriman</label>
                                        <span class="text-[10px] text-gray-400 font-semibold" id="label-recipient-status">Penerima: {{ $mahasiswa ? $mahasiswa->name : 'Anak Asuh' }}</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-1.5">
                                        <button type="button" onclick="switchRecipientType('anak')" id="btn-recipient-anak" class="p-2.5 rounded-xl border-2 border-primary bg-blue-50/70 font-bold text-xs text-primary flex items-center justify-center space-x-1.5 transition shadow-2xs">
                                            <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                                            <span class="truncate">Anak Asuh</span>
                                        </button>
                                        <button type="button" onclick="switchRecipientType('nim')" id="btn-recipient-nim" class="p-2.5 rounded-xl border-2 border-gray-200 bg-white font-semibold text-xs text-gray-600 hover:border-indigo-300 flex items-center justify-center space-x-1.5 transition">
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                            <span class="truncate font-bold">Lewat NIM</span>
                                        </button>
                                        <button type="button" onclick="switchRecipientType('lain')" id="btn-recipient-lain" class="p-2.5 rounded-xl border-2 border-gray-200 bg-white font-semibold text-xs text-gray-600 hover:border-blue-300 flex items-center justify-center space-x-1.5 transition">
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            <span class="truncate">Daftar Mhs</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- BOX 1: DETAIL PENERIMA: ANAK ASUH -->
                                <div id="box-recipient-anak" class="p-3 bg-blue-50/50 border border-blue-200/80 rounded-2xl flex items-center justify-between">
                                    <div class="flex items-center space-x-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-xs shrink-0">
                                            {{ $mahasiswa ? strtoupper(substr($mahasiswa->name, 0, 2)) : 'M' }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-extrabold text-xs text-gray-900 truncate">{{ $mahasiswa->name ?? 'Mahasiswa Belum Terhubung' }}</div>
                                            <div class="text-[10px] text-gray-500 font-mono">
                                                UID: {{ $card ? $card->card_uid : 'Tidak Ada Kartu' }}
                                                @if($mahasiswa && $mahasiswa->nim) • NIM: {{ $mahasiswa->nim }} @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-[9px] uppercase font-bold text-gray-400 block">Saldo Kartu</span>
                                        <span class="font-black text-xs text-primary">Rp {{ $card ? number_format($card->balance, 0, ',', '.') : '0' }}</span>
                                    </div>
                                </div>

                                <!-- BOX 2: TRANSFER LEWAT NIM MAHASISWA -->
                                <div id="box-recipient-nim" class="hidden space-y-3 bg-gradient-to-br from-indigo-50/80 via-blue-50/50 to-slate-50 p-3.5 rounded-2xl border-2 border-indigo-200">
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <label class="block text-xs font-black text-indigo-950 uppercase">Masukkan NIM Mahasiswa Penerima</label>
                                            <span class="text-[10px] text-indigo-600 font-bold bg-indigo-100/70 px-2 py-0.5 rounded-md">Verifikasi Kampus</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <div class="relative flex-1">
                                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-indigo-400 font-black text-xs">#</span>
                                                <input type="text" id="input-transfer-nim" placeholder="Contoh: 2101092001" onkeydown="if(event.key==='Enter'){event.preventDefault();lookupStudentByNim();}" class="w-full pl-8 pr-3 py-2.5 bg-white border border-indigo-300 rounded-xl text-xs font-bold font-mono text-gray-900 focus:ring-2 focus:ring-indigo-500 shadow-2xs uppercase">
                                            </div>
                                            <button type="button" id="btn-check-nim" onclick="lookupStudentByNim()" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl transition shadow-xs flex items-center space-x-1.5 shrink-0">
                                                <svg id="icon-search-nim" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                                <div id="spinner-search-nim" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin hidden"></div>
                                                <span>Cek NIM</span>
                                            </button>
                                        </div>
                                        <p class="text-[10px] text-gray-500 mt-1">Sistem otomatis mencocokkan NIM dengan kartu NFC aktif mahasiswa.</p>
                                    </div>

                                    <!-- Hasil Verifikasi NIM: Mahasiswa Ditemukan & Kartu Aktif -->
                                    <div id="nim-success-card" class="hidden p-3 bg-white rounded-xl border border-emerald-300 shadow-2xs">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2.5 min-w-0">
                                                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-xs shrink-0 shadow-2xs" id="nim-result-avatar">
                                                    M
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="font-black text-xs text-gray-900 truncate flex items-center gap-1.5">
                                                        <span id="nim-result-name">-</span>
                                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-extrabold bg-emerald-100 text-emerald-800">
                                                            <svg class="w-2.5 h-2.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                            Terverifikasi
                                                        </span>
                                                    </div>
                                                    <div class="text-[10px] text-gray-500 font-mono truncate" id="nim-result-details">NIM: - • UID: -</div>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <span class="text-[9px] uppercase font-bold text-gray-400 block">Saldo Kartu</span>
                                                <span class="font-black text-xs text-emerald-700" id="nim-result-balance">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Error / Peringatan NIM -->
                                    <div id="nim-error-box" class="hidden p-2.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700 font-semibold flex items-center space-x-2">
                                        <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span id="nim-error-text" class="text-[11px] leading-tight">Mahasiswa dengan NIM tersebut tidak ditemukan.</span>
                                    </div>
                                </div>

                                <!-- BOX 3: PILIH MAHASISWA LAIN DARI DAFTAR KAMPUS -->
                                <div id="box-recipient-lain" class="hidden space-y-2.5 bg-slate-50 p-3.5 rounded-2xl border border-gray-200">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Cari / Pilih Mahasiswa Lain</label>
                                        <select id="select-other-student" onchange="onSelectOtherStudent(this)" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl font-bold text-xs text-gray-800 bg-white focus:ring-2 focus:ring-primary shadow-2xs">
                                            <option value="" disabled selected>-- Pilih Mahasiswa Penerima --</option>
                                            @foreach($allMahasiswa as $otherMhs)
                                                @php
                                                    $otherCard = $otherMhs->cards->where('status', 'active')->first();
                                                    $isCurrentAnak = $mahasiswa && $mahasiswa->id == $otherMhs->id;
                                                @endphp
                                                <option value="{{ $otherCard ? $otherCard->id : '' }}"
                                                        data-student-id="{{ $otherMhs->id }}"
                                                        data-name="{{ $otherMhs->name }}"
                                                        data-nim="{{ $otherMhs->nim ?? '' }}"
                                                        data-email="{{ $otherMhs->email }}"
                                                        data-uid="{{ $otherCard ? $otherCard->card_uid : '' }}"
                                                        data-balance="{{ $otherCard ? number_format($otherCard->balance, 0, ',', '.') : '0' }}"
                                                        data-has-card="{{ $otherCard ? '1' : '0' }}">
                                                    {{ $otherMhs->name }} ({{ $otherMhs->nim ? 'NIM: ' . $otherMhs->nim : $otherMhs->email }}) {{ $otherCard ? '• UID: ' . $otherCard->card_uid : '• (Belum ada kartu)' }} {{ $isCurrentAnak ? '★ Anak Anda' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Preview Mahasiswa Lain Terpilih -->
                                    <div id="preview-other-card" class="hidden p-3 bg-white rounded-xl border border-blue-200 flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5 min-w-0">
                                            <div class="w-8 h-8 rounded-xl bg-primary text-white font-black flex items-center justify-center text-xs shrink-0" id="preview-other-avatar">
                                                ML
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-extrabold text-xs text-gray-900 truncate" id="preview-other-name">-</div>
                                                <div class="text-[10px] text-gray-500 font-mono" id="preview-other-uid">UID: -</div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-[9px] uppercase font-bold text-gray-400 block">Saldo Kartu</span>
                                            <span class="font-black text-xs text-emerald-700" id="preview-other-balance">Rp 0</span>
                                        </div>
                                    </div>

                                    <!-- Warning Mahasiswa Belum Punya Kartu -->
                                    <div id="warning-no-card-other" class="hidden p-2.5 bg-rose-50 border border-rose-200 rounded-xl text-[11px] text-rose-700 font-semibold flex items-center space-x-1.5">
                                        <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <span>Mahasiswa ini belum memiliki kartu NFC aktif. Silakan pilih mahasiswa yang memiliki kartu aktif.</span>
                                    </div>
                                </div>

                                <!-- PILIH METODE PENGIRIMAN -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-gray-700 uppercase">Metode Pengiriman</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <label class="flex items-center p-2.5 border-2 rounded-xl cursor-pointer transition border-primary bg-blue-50/60" id="label-method-transfer">
                                            <input type="radio" name="wali_method" value="transfer" class="sr-only" checked onchange="toggleWaliMethod('transfer')">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 rounded-lg bg-primary text-white flex items-center justify-center shrink-0">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                </div>
                                                <div class="text-left">
                                                    <div class="font-bold text-xs text-gray-900 leading-tight">Saldo Dompet</div>
                                                    <span class="text-[10px] text-gray-500">Potong saldo wali</span>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="flex items-center p-2.5 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-300 transition" id="label-method-midtrans">
                                            <input type="radio" name="wali_method" value="midtrans" class="sr-only" onchange="toggleWaliMethod('midtrans')">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 rounded-lg bg-blue-600 text-white flex items-center justify-center shrink-0">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                                </div>
                                                <div class="text-left">
                                                    <div class="font-bold text-xs text-gray-900 leading-tight">Midtrans Online</div>
                                                    <span class="text-[10px] text-gray-500">QRIS / VA Bank</span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <label class="block text-xs font-bold text-gray-700 uppercase">Nominal Kirim Saldo (Rp)</label>
                                        <span id="quick-balance-indicator" class="text-[10px] text-gray-400">Min. Rp 1.000</span>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-gray-400 text-xs">Rp</span>
                                        <input type="number" id="topup-amount" name="amount" required min="1000" step="5000" placeholder="50000" oninput="checkWaliTransferBalance()" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl font-bold text-sm text-gray-800 focus:ring-2 focus:ring-primary shadow-2xs">
                                    </div>
                                </div>

                                <div class="grid grid-cols-4 gap-1.5">
                                    <button type="button" onclick="selectTransferPreset(20000)" class="py-1 px-1.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 transition text-center">20 Rb</button>
                                    <button type="button" onclick="selectTransferPreset(50000)" class="py-1 px-1.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 transition text-center">50 Rb</button>
                                    <button type="button" onclick="selectTransferPreset(100000)" class="py-1 px-1.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 transition text-center">100 Rb</button>
                                    <button type="button" onclick="selectTransferPreset(200000)" class="py-1 px-1.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 transition text-center">200 Rb</button>
                                </div>

                                <!-- PERINGATAN SALDO KURANG (DITAMPILKAN JIKA TRANSFER > SALDO WALI) -->
                                <div id="wali-balance-warning" class="hidden p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 space-y-1.5">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <div class="flex-1">
                                            <span class="font-bold">Saldo Dompet Wali Kurang!</span>
                                            <p class="text-[11px] text-amber-700 mt-0.5" id="wali-shortage-text">Saldo Anda tidak mencukupi untuk nominal transfer ini.</p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="openTopupWaliModal(document.getElementById('topup-amount').value)" class="w-full py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg text-xs transition flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span id="btn-quick-topup-label">Top Up Dompet Sekarang</span>
                                    </button>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Pesan / Catatan (Opsional)</label>
                                    <input type="text" id="topup-notes" name="notes" placeholder="Contoh: Titip uang jajan / bayar kas" class="w-full px-3.5 py-2 border border-gray-300 rounded-xl text-xs font-medium text-gray-800 focus:ring-2 focus:ring-primary shadow-2xs">
                                </div>

                                <button type="button" id="btn-topup" onclick="executeWaliTopup()" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    <span id="btn-topup-label">Kirim Saldo Sekarang (Potong Dompet)</span>
                                </button>
                            </form>
                        </div>
                        <div class="pt-3 border-t border-gray-100 text-[11px] text-gray-400 text-center">
                            Saldo langsung masuk real-time ke kartu NFC mahasiswa penerima
                        </div>
                    </div>
                </div>

                <!-- PREVIEW TRANSAKSI ANAK -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Aktivitas Belanja & Top Up Terkini</h3>
                            <p class="text-xs text-gray-500">Pantauan transaksi yang dilakukan oleh anak Anda</p>
                        </div>
                        <button type="button" onclick="switchWaliTab('transaksi')" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-primary border border-blue-200 rounded-xl text-xs font-bold transition">
                            Lihat Semua ({{ count($transactions) }})
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
                                                {{ $trx->type == 'topup' ? 'Top-Up Saldo' : 'Pengeluaran - ' . ($trx->pedagang->name ?? 'Kantin') }}
                                            </div>
                                            <div class="text-xs text-gray-400 mt-0.5 truncate">
                                                {{ $trx->created_at->translatedFormat('d M Y, H:i') }} | {{ $trx->description ?? 'Transaksi NFC' }}
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
                            <p class="text-sm font-semibold">Belum ada riwayat transaksi anak Anda.</p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- TAB 2: ATUR LIMIT BELANJA                                      -->
            <!-- ============================================================== -->
            <div id="tab-wali-limit" class="wali-tab-content hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 max-w-2xl">
                    <div class="pb-5 border-b border-gray-100 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Atur Batas / Limit Pengeluaran Anak</h3>
                        <p class="text-xs text-gray-500 mt-1">Batasi pengeluaran harian dan kategori menu untuk menjaga penggunaan saldo secara bijak</p>
                    </div>

                    @if($mahasiswa)
                    <form action="{{ route('limit.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $mahasiswa->id }}">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Limit Harian Keseluruhan (Rp)</label>
                            <input type="number" name="daily_limit" value="{{ $limit ? $limit->daily_limit : '' }}" placeholder="Kosongkan jika tanpa batas" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-bold text-gray-800 focus:ring-2 focus:ring-primary shadow-2xs">
                            <p class="text-[11px] text-gray-400 mt-1">Batas total belanja anak dalam 1 hari di semua stan kantin.</p>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <span class="text-xs font-bold text-gray-700 uppercase block mb-3">Limit Per Kategori (Harian)</span>
                            @php
                                $catLimits = $limit && $limit->category_limits ? $limit->category_limits : [];
                            @endphp
                            
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1 font-semibold">Kategori Makanan (Rp)</label>
                                    <input type="number" name="category_limits[Makanan]" value="{{ $catLimits['Makanan'] ?? '' }}" placeholder="Kosong = Tanpa Limit" class="w-full px-3.5 py-2 border border-gray-300 rounded-xl text-xs font-semibold focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1 font-semibold">Kategori Minuman (Rp)</label>
                                    <input type="number" name="category_limits[Minuman]" value="{{ $catLimits['Minuman'] ?? '' }}" placeholder="Kosong = Tanpa Limit" class="w-full px-3.5 py-2 border border-gray-300 rounded-xl text-xs font-semibold focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1 font-semibold">Kategori Jajanan (Rp)</label>
                                    <input type="number" name="category_limits[Jajanan]" value="{{ $catLimits['Jajanan'] ?? '' }}" placeholder="Kosong = Tanpa Limit" class="w-full px-3.5 py-2 border border-gray-300 rounded-xl text-xs font-semibold focus:ring-primary">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl text-xs shadow-md shadow-orange-600/20 transition flex items-center space-x-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan Pengaturan Limit</span>
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="text-center py-8 text-gray-400 text-xs">
                        Akun mahasiswa anak belum terhubung.
                    </div>
                    @endif
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 3: RIWAYAT TRANSAKSI LENGKAP                               -->
            <!-- ============================================================== -->
            <div id="tab-wali-transaksi" class="wali-tab-content hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Riwayat Transaksi & Mutasi</h3>
                            <p class="text-xs text-gray-500">Pantau seluruh aktivitas belanja anak dan mutasi saldo dompet wali</p>
                        </div>
                        <!-- SUB TABS TOGGLE -->
                        <div class="flex items-center space-x-1.5 bg-gray-100 p-1 rounded-2xl">
                            <button type="button" onclick="switchTrxSubTab('anak')" id="subtab-btn-anak" class="px-3.5 py-1.5 rounded-xl text-xs font-extrabold transition bg-white text-primary shadow-xs">
                                Transaksi Anak ({{ count($transactions) }})
                            </button>
                            <button type="button" onclick="switchTrxSubTab('wallet')" id="subtab-btn-wallet" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition text-gray-500 hover:text-gray-800">
                                Mutasi Dompet Wali ({{ isset($walletTransactions) ? count($walletTransactions) : 0 }})
                            </button>
                        </div>
                    </div>

                    <!-- SUB-TAB 1: TRANSAKSI ANAK -->
                    <div id="subtab-content-anak" class="block">
                        @if(count($transactions) > 0)
                            <div class="overflow-x-auto mt-4">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-gray-50/80 text-gray-600 rounded-xl text-xs uppercase font-bold">
                                        <tr>
                                            <th class="p-3.5 rounded-l-xl">Waktu Transaksi</th>
                                            <th class="p-3.5">Jenis / Tempat</th>
                                            <th class="p-3.5">Keterangan</th>
                                            <th class="p-3.5 text-right">Nominal</th>
                                            <th class="p-3.5 text-center rounded-r-xl">Status</th>
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
                                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-extrabold uppercase">Berhasil</span>
                                                @elseif($trx->status == 'pending')
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[10px] font-extrabold uppercase">Pending</span>
                                                        <button type="button" onclick="cancelMidtransTrx('{{ $trx->order_id }}')" class="px-2 py-0.5 bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 rounded-md text-[10px] font-bold transition">Batalkan</button>
                                                    </div>
                                                @else
                                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-[10px] font-extrabold uppercase">{{ $trx->order_status == 'batal' ? 'Dibatalkan' : 'Gagal' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-16 text-gray-400">
                                <p class="text-sm font-semibold">Belum ada riwayat transaksi anak Anda.</p>
                            </div>
                        @endif
                    </div>

                    <!-- SUB-TAB 2: MUTASI DOMPET WALI -->
                    <div id="subtab-content-wallet" class="hidden">
                        <div class="mt-4 flex items-center justify-between p-3.5 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl mb-4">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                </div>
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-emerald-800">Saldo Dompet Saat Ini</span>
                                    <div class="text-base font-black text-emerald-900" id="subtab-wali-balance">Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <button type="button" onclick="openTopupWaliModal()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>+ Top Up Dompet</span>
                            </button>
                        </div>

                        @if(isset($walletTransactions) && count($walletTransactions) > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-gray-50/80 text-gray-600 rounded-xl text-xs uppercase font-bold">
                                        <tr>
                                            <th class="p-3.5 rounded-l-xl">Waktu Transaksi</th>
                                            <th class="p-3.5">Jenis Mutasi</th>
                                            <th class="p-3.5">Keterangan / Order ID</th>
                                            <th class="p-3.5">Metode</th>
                                            <th class="p-3.5 text-right">Nominal</th>
                                            <th class="p-3.5 text-center rounded-r-xl">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($walletTransactions as $wTrx)
                                        <tr class="hover:bg-gray-50/80 transition">
                                            <td class="p-3.5 text-xs">
                                                <div class="font-bold text-gray-800">{{ $wTrx->created_at->translatedFormat('d M Y') }}</div>
                                                <div class="text-gray-400 text-[11px]">{{ $wTrx->created_at->format('H:i') }} WIB</div>
                                            </td>
                                            <td class="p-3.5">
                                                @if($wTrx->card_id)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-primary">
                                                        <span class="w-1.5 h-1.5 bg-primary rounded-full mr-1.5"></span>
                                                        Transfer ke Anak
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>
                                                        Top Up Dompet
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="p-3.5 text-xs text-gray-600 max-w-xs truncate">
                                                <div class="font-medium text-gray-800">{{ $wTrx->description ?? 'Mutasi Dompet Wali' }}</div>
                                                <div class="text-[10px] text-gray-400 font-mono">{{ $wTrx->order_id }}</div>
                                            </td>
                                            <td class="p-3.5 text-xs font-bold text-gray-600 uppercase">
                                                {{ $wTrx->payment_method ?? 'Instan' }}
                                            </td>
                                            <td class="p-3.5 text-right font-black text-sm {{ $wTrx->card_id ? 'text-blue-600' : 'text-emerald-600' }}">
                                                {{ $wTrx->card_id ? '-' : '+' }} Rp {{ number_format($wTrx->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="p-3.5 text-center">
                                                @if($wTrx->status == 'success')
                                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-extrabold uppercase">Berhasil</span>
                                                @elseif($wTrx->status == 'pending')
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[10px] font-extrabold uppercase">Pending</span>
                                                        <button type="button" onclick="cancelMidtransTrx('{{ $wTrx->order_id }}')" class="px-2 py-0.5 bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 rounded-md text-[10px] font-bold transition">Batalkan</button>
                                                    </div>
                                                @else
                                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-[10px] font-extrabold uppercase">{{ $wTrx->order_status == 'batal' ? 'Dibatalkan' : 'Gagal' }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-16 text-gray-400">
                                <p class="text-sm font-semibold">Belum ada riwayat mutasi dompet Anda.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 4: PROFIL WALI                                             -->
            <!-- ============================================================== -->
            <div id="tab-wali-profil" class="wali-tab-content hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 max-w-2xl">
                    <div class="pb-5 border-b border-gray-100 mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Pengaturan Akun Wali / Orang Tua</h3>
                        <p class="text-xs text-gray-500 mt-1">Ubah nama, email, foto profil, dan kata sandi akun Anda</p>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="avatar_cropped">

                        <!-- Foto Profil -->
                        <div class="bg-slate-50 p-4 rounded-2xl border border-gray-200 flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-primary/20 flex items-center justify-center bg-white shadow-xs shrink-0">
                                @if(Auth::user()->avatar_url)
                                    <img id="preview-wali-tab-avatar" src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <div id="preview-wali-tab-avatar-fallback" class="w-full h-full bg-primary/10 flex items-center justify-center text-primary font-black text-xl">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="cursor-pointer inline-flex items-center space-x-1.5 px-3.5 py-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-2xs transition">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Pilih & Pangkas Foto</span>
                                    <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden" onclick="this.value = null" onchange="previewWaliPhoto(this, 'preview-wali-tab-avatar', 'preview-wali-tab-avatar-fallback')">
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
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Nama Lengkap Wali <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-primary focus:border-primary shadow-2xs">
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
                                <span>Simpan Profil Wali</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <!-- ============================================================== -->
    <!-- MODAL TOP UP SALDO DOMPET WALI                                 -->
    <!-- ============================================================== -->
    <div id="modal-topup-wali" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden transition-opacity duration-300">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-gray-100 transform transition-all relative overflow-hidden" onclick="event.stopPropagation()">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shadow-xs">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 leading-tight">Top Up Saldo Dompet Wali</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Isi saldo dompet digital Anda untuk dikirim ke anak</p>
                    </div>
                </div>
                <button type="button" onclick="closeTopupWaliModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition" aria-label="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="space-y-4 pt-4">
                
                <!-- Status Saldo Dompet Saat Ini -->
                <div class="bg-slate-50 border border-gray-200/80 rounded-2xl p-3 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Saldo Dompet Saat Ini</span>
                        <div class="text-sm font-black text-gray-900" id="modal-current-balance">Rp {{ number_format(Auth::user()->balance ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full">
                        Akun Aktif
                    </span>
                </div>

                <!-- Jalur Pembayaran Resmi Midtrans -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Metode Pengisian Saldo</label>
                    <div class="flex items-center justify-between p-3.5 border-2 border-blue-500 bg-blue-50/60 rounded-2xl shadow-2xs">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-xs text-gray-900 block leading-tight">Midtrans Online Gateway</span>
                                <span class="text-[11px] text-gray-500">QRIS (GoPay, ShopeePay, Dana, dll) & Virtual Account Bank</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 shrink-0">
                            Resmi / Otomatis
                        </span>
                    </div>
                    <input type="hidden" name="wallet_topup_method" value="midtrans">
                </div>

                <!-- Input Nominal Top Up -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Nominal Top Up (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-gray-400 text-xs">Rp</span>
                        <input type="number" id="wallet-topup-amount" name="amount" min="1000" step="10000" placeholder="100000" value="100000" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl font-bold text-base text-gray-900 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                    </div>
                </div>

                <!-- Quick Nominal Preset Chips -->
                <div>
                    <span class="text-[11px] font-bold text-gray-500 uppercase block mb-1.5">Pilihan Cepat:</span>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-1.5">
                        <button type="button" onclick="selectWalletTopupPreset(20000)" class="wallet-preset-btn py-1.5 px-2 bg-gray-50 hover:bg-emerald-50 hover:text-emerald-700 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 transition text-center">20 Rb</button>
                        <button type="button" onclick="selectWalletTopupPreset(50000)" class="wallet-preset-btn py-1.5 px-2 bg-gray-50 hover:bg-emerald-50 hover:text-emerald-700 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 transition text-center">50 Rb</button>
                        <button type="button" onclick="selectWalletTopupPreset(100000)" class="wallet-preset-btn py-1.5 px-2 bg-emerald-50 text-emerald-800 border-emerald-300 border rounded-xl text-xs font-bold transition text-center">100 Rb</button>
                        <button type="button" onclick="selectWalletTopupPreset(200000)" class="wallet-preset-btn py-1.5 px-2 bg-gray-50 hover:bg-emerald-50 hover:text-emerald-700 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 transition text-center">200 Rb</button>
                        <button type="button" onclick="selectWalletTopupPreset(500000)" class="wallet-preset-btn py-1.5 px-2 bg-gray-50 hover:bg-emerald-50 hover:text-emerald-700 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 transition text-center">500 Rb</button>
                        <button type="button" onclick="selectWalletTopupPreset(1000000)" class="wallet-preset-btn py-1.5 px-2 bg-gray-50 hover:bg-emerald-50 hover:text-emerald-700 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 transition text-center">1 Jt</button>
                    </div>
                </div>

                <!-- Pesan / Catatan (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                    <input type="text" id="wallet-topup-notes" placeholder="Contoh: Isi saldo dompet bulan ini" class="w-full px-3.5 py-2 border border-gray-300 rounded-xl text-xs font-medium text-gray-800 focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-end space-x-2.5">
                <button type="button" onclick="closeTopupWaliModal()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="button" id="btn-submit-wallet-topup" onclick="submitWaliWalletTopup()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-xl text-xs shadow-md shadow-blue-600/20 transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span id="btn-submit-wallet-label">Bayar via Midtrans (QRIS/VA)</span>
                </button>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Midtrans Snap JS -->
<script src="{{ config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', true)) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY')) }}"></script>
<script>
    const waliTabHeaders = {
        ringkasan: {
            title: 'Saldo & Top-Up',
            subtitle: 'Pantau saldo kartu NFC anak dan lakukan top up saldo secara online'
        },
        limit: {
            title: 'Atur Limit Belanja',
            subtitle: 'Batasi pengeluaran harian dan kategori menu untuk anak asuh Anda'
        },
        transaksi: {
            title: 'Riwayat Transaksi Anak',
            subtitle: 'Pantau log pengeluaran di kantin serta isi saldo kartu'
        },
        profil: {
            title: 'Profil & Keamanan',
            subtitle: 'Kelola data diri wali / orang tua dan kata sandi akun'
        }
    };

    // Variabel saldo dompet wali saat ini untuk validasi instan
    let waliBalance = {{ (float)(Auth::user()->balance ?? 0) }};

    function updateWaliBalanceDisplays(newBalance) {
        waliBalance = parseFloat(newBalance);
        const formatted = 'Rp ' + new Intl.NumberFormat('id-ID').format(waliBalance);
        
        const topbarBalance = document.getElementById('topbar-balance-text');
        if (topbarBalance) topbarBalance.innerText = formatted;

        const sidebarBalance = document.getElementById('sidebar-balance-text');
        if (sidebarBalance) sidebarBalance.innerText = formatted;

        const formWaliBalance = document.getElementById('form-wali-balance-text');
        if (formWaliBalance) formWaliBalance.innerText = formatted;

        const walletCardBalance = document.getElementById('wallet-card-balance');
        if (walletCardBalance) walletCardBalance.innerText = formatted;

        const subtabWaliBalance = document.getElementById('subtab-wali-balance');
        if (subtabWaliBalance) subtabWaliBalance.innerText = formatted;

        const modalCurrentBalance = document.getElementById('modal-current-balance');
        if (modalCurrentBalance) modalCurrentBalance.innerText = formatted;

        checkWaliTransferBalance();
    }

    // Toggle Sub-Tab di Tab 3 (Transaksi Anak vs Mutasi Dompet)
    function switchTrxSubTab(subTabId) {
        const btnAnak = document.getElementById('subtab-btn-anak');
        const btnWallet = document.getElementById('subtab-btn-wallet');
        const contentAnak = document.getElementById('subtab-content-anak');
        const contentWallet = document.getElementById('subtab-content-wallet');

        if (subTabId === 'anak') {
            btnAnak.classList.add('bg-white', 'text-primary', 'shadow-xs', 'font-extrabold');
            btnAnak.classList.remove('text-gray-500');
            btnWallet.classList.remove('bg-white', 'text-primary', 'shadow-xs', 'font-extrabold');
            btnWallet.classList.add('text-gray-500');
            
            contentAnak.classList.remove('hidden');
            contentAnak.classList.add('block');
            contentWallet.classList.add('hidden');
            contentWallet.classList.remove('block');
        } else {
            btnWallet.classList.add('bg-white', 'text-primary', 'shadow-xs', 'font-extrabold');
            btnWallet.classList.remove('text-gray-500');
            btnAnak.classList.remove('bg-white', 'text-primary', 'shadow-xs', 'font-extrabold');
            btnAnak.classList.add('text-gray-500');
            
            contentWallet.classList.remove('hidden');
            contentWallet.classList.add('block');
            contentAnak.classList.add('hidden');
            contentAnak.classList.remove('block');
        }
    }

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

    function switchWaliTab(tabId) {
        document.querySelectorAll('.wali-tab-content').forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('block');
        });

        const activeTab = document.getElementById('tab-wali-' + tabId);
        if (activeTab) {
            activeTab.classList.remove('hidden');
            activeTab.classList.add('block');
        }

        document.querySelectorAll('.wali-tab-btn').forEach(btn => {
            btn.classList.remove('text-primary', 'bg-blue-50', 'border-primary', 'shadow-xs', 'font-bold');
            btn.classList.add('text-gray-600', 'border-transparent', 'font-semibold');
            const icon = btn.querySelector('.wali-tab-icon');
            if (icon) {
                icon.classList.remove('text-primary');
                icon.classList.add('text-gray-400');
            }
        });

        const activeBtn = document.getElementById('btn-wali-' + tabId);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-600', 'border-transparent', 'font-semibold');
            activeBtn.classList.add('text-primary', 'bg-blue-50', 'border-primary', 'shadow-xs', 'font-bold');
            const icon = activeBtn.querySelector('.wali-tab-icon');
            if (icon) {
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-primary');
            }
        }

        const headerInfo = waliTabHeaders[tabId];
        if (headerInfo) {
            const titleEl = document.getElementById('wali-topbar-title');
            const subtitleEl = document.getElementById('wali-topbar-subtitle');
            if (titleEl) titleEl.innerText = headerInfo.title;
            if (subtitleEl) subtitleEl.innerText = headerInfo.subtitle;
        }

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

    // ==========================================
    // LOGIKA MODAL TOP UP DOMPET WALI (MIDTRANS ONLY)
    // ==========================================
    function openTopupWaliModal(prefillAmount = null) {
        const modal = document.getElementById('modal-topup-wali');
        if (!modal) return;
        modal.classList.remove('hidden');

        if (prefillAmount && !isNaN(prefillAmount) && prefillAmount > 0) {
            const amountInput = document.getElementById('wallet-topup-amount');
            if (amountInput) amountInput.value = Math.max(1000, Math.ceil(prefillAmount));
        }
    }

    function closeTopupWaliModal() {
        const modal = document.getElementById('modal-topup-wali');
        if (!modal) return;
        modal.classList.add('hidden');
    }

    function selectWalletTopupPreset(amount) {
        const amountInput = document.getElementById('wallet-topup-amount');
        if (amountInput) amountInput.value = amount;

        document.querySelectorAll('.wallet-preset-btn').forEach(btn => {
            btn.classList.remove('bg-emerald-50', 'text-emerald-800', 'border-emerald-300');
            btn.classList.add('bg-gray-50', 'text-gray-700', 'border-gray-200');
        });

        if (window.event && window.event.currentTarget) {
            window.event.currentTarget.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-300');
            window.event.currentTarget.classList.remove('bg-gray-50', 'text-gray-700', 'border-gray-200');
        }
    }

    async function submitWaliWalletTopup() {
        const amountInput = document.getElementById('wallet-topup-amount');
        const notesInput = document.getElementById('wallet-topup-notes');
        const btn = document.getElementById('btn-submit-wallet-topup');
        const btnLabel = document.getElementById('btn-submit-wallet-label');
        const csrfToken = '{{ csrf_token() }}';

        const amount = amountInput ? amountInput.value : '';
        const notes = notesInput ? notesInput.value : '';

        if (!amount || amount < 1000) {
            alert('Minimal pengisian saldo dompet adalah Rp 1.000');
            if (amountInput) amountInput.focus();
            return;
        }

        btn.disabled = true;
        btnLabel.innerText = 'Menghubungkan ke Midtrans...';

        try {
            const res = await fetch("{{ route('wali.topup_wallet.midtrans') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    amount: amount,
                    notes: notes
                })
            });

            const data = await res.json();
            btn.disabled = false;
            btnLabel.innerText = 'Bayar via Midtrans (QRIS/VA)';

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
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ order_id: result.order_id || data.order_id })
                            });
                        } catch(e) {}
                        alert("Pembayaran berhasil! Saldo dompet wali telah ditambahkan.");
                        window.location.reload();
                    },
                    onPending: function(result){
                        alert("Menunggu pembayaran Anda diselesaikan.");
                        window.location.reload();
                    },
                    onError: async function(result){
                        alert("Pembayaran Midtrans dibatalkan atau gagal.");
                        try {
                            await fetch("{{ route('midtrans.cancel') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ order_id: (result && result.order_id) || data.order_id })
                            });
                        } catch(e) {}
                        window.location.reload();
                    },
                    onClose: async function(){
                        if (confirm("Anda menutup popup pembayaran.\n\nApakah Anda ingin membatalkan transaksi ini?")) {
                            try {
                                await fetch("{{ route('midtrans.cancel') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    body: JSON.stringify({ order_id: data.order_id })
                                });
                            } catch(e) {}
                            window.location.reload();
                        }
                    }
                });
            } else {
                let errMsg = data.error || 'Gagal mengambil token pembayaran Midtrans.';
                alert(errMsg);
            }
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            btnLabel.innerText = 'Bayar via Midtrans (QRIS/VA)';
            alert('Terjadi kesalahan jaringan.');
        }
    }

    // ==========================================
    // LOGIKA KIRIM SALDO WALI KE ANAK / MAHASISWA LAIN / LEWAT NIM
    // ==========================================
    let currentWaliMethod = 'transfer';
    let currentRecipientType = 'anak';
    const anakCardId = "{{ $card ? $card->id : '' }}";
    const anakName = "{{ $mahasiswa ? addslashes($mahasiswa->name) : 'Anak Asuh' }}";
    const anakNim = "{{ $mahasiswa && $mahasiswa->nim ? addslashes($mahasiswa->nim) : '' }}";
    let selectedOtherCardId = '';
    let selectedOtherName = '';
    let selectedOtherNim = '';
    let selectedNimCardId = '';
    let selectedNimStudentName = '';
    let selectedNimValue = '';

    function switchRecipientType(type) {
        currentRecipientType = type;
        const btnAnak = document.getElementById('btn-recipient-anak');
        const btnNim = document.getElementById('btn-recipient-nim');
        const btnLain = document.getElementById('btn-recipient-lain');
        
        const boxAnak = document.getElementById('box-recipient-anak');
        const boxNim = document.getElementById('box-recipient-nim');
        const boxLain = document.getElementById('box-recipient-lain');
        
        const topupCardInput = document.getElementById('topup-card-id');
        const topupNimInput = document.getElementById('topup-student-nim');
        const labelRecipient = document.getElementById('label-recipient-status');

        // Style aktif vs inaktif
        const activeClass = 'p-2.5 rounded-xl border-2 border-primary bg-blue-50/70 font-bold text-xs text-primary flex items-center justify-center space-x-1.5 transition shadow-2xs';
        const inactiveClass = 'p-2.5 rounded-xl border-2 border-gray-200 bg-white font-semibold text-xs text-gray-600 hover:border-blue-300 flex items-center justify-center space-x-1.5 transition';

        if (btnAnak) btnAnak.className = (type === 'anak') ? activeClass : inactiveClass;
        if (btnNim) btnNim.className = (type === 'nim') ? activeClass : inactiveClass;
        if (btnLain) btnLain.className = (type === 'lain') ? activeClass : inactiveClass;

        // Icons
        const anakSvg = btnAnak ? btnAnak.querySelector('svg') : null;
        const nimSvg = btnNim ? btnNim.querySelector('svg') : null;
        const lainSvg = btnLain ? btnLain.querySelector('svg') : null;

        if (anakSvg) anakSvg.setAttribute('class', 'w-4 h-4 ' + (type === 'anak' ? 'text-primary' : 'text-gray-400') + ' shrink-0');
        if (nimSvg) nimSvg.setAttribute('class', 'w-4 h-4 ' + (type === 'nim' ? 'text-primary' : 'text-gray-400') + ' shrink-0');
        if (lainSvg) lainSvg.setAttribute('class', 'w-4 h-4 ' + (type === 'lain' ? 'text-primary' : 'text-gray-400') + ' shrink-0');

        // Toggle boxes
        if (boxAnak) (type === 'anak') ? boxAnak.classList.remove('hidden') : boxAnak.classList.add('hidden');
        if (boxNim) (type === 'nim') ? boxNim.classList.remove('hidden') : boxNim.classList.add('hidden');
        if (boxLain) (type === 'lain') ? boxLain.classList.remove('hidden') : boxLain.classList.add('hidden');

        if (type === 'anak') {
            if (topupCardInput) topupCardInput.value = anakCardId;
            if (topupNimInput) topupNimInput.value = anakNim;
            if (labelRecipient) labelRecipient.innerText = 'Penerima: ' + anakName + (anakNim ? ' [' + anakNim + ']' : '');
        } else if (type === 'nim') {
            if (topupCardInput) topupCardInput.value = selectedNimCardId;
            if (topupNimInput) topupNimInput.value = selectedNimValue;
            if (labelRecipient) {
                labelRecipient.innerText = selectedNimStudentName ? `Penerima: ${selectedNimStudentName} [NIM: ${selectedNimValue}]` : 'Masukkan NIM Mahasiswa';
            }
            const inputNim = document.getElementById('input-transfer-nim');
            if (inputNim && !inputNim.value) {
                setTimeout(() => inputNim.focus(), 150);
            }
        } else {
            if (topupCardInput) topupCardInput.value = selectedOtherCardId;
            if (topupNimInput) topupNimInput.value = selectedOtherNim;
            if (labelRecipient) {
                labelRecipient.innerText = selectedOtherName ? ('Penerima: ' + selectedOtherName) : 'Pilih Mahasiswa';
            }
        }
        updateTransferButtonLabel();
    }

    async function lookupStudentByNim() {
        const input = document.getElementById('input-transfer-nim');
        const btn = document.getElementById('btn-check-nim');
        const icon = document.getElementById('icon-search-nim');
        const spinner = document.getElementById('spinner-search-nim');
        const cardInput = document.getElementById('topup-card-id');
        const nimInput = document.getElementById('topup-student-nim');
        const successCard = document.getElementById('nim-success-card');
        const errorBox = document.getElementById('nim-error-box');
        const errorText = document.getElementById('nim-error-text');
        const nameEl = document.getElementById('nim-result-name');
        const detailsEl = document.getElementById('nim-result-details');
        const balanceEl = document.getElementById('nim-result-balance');
        const avatarEl = document.getElementById('nim-result-avatar');
        const labelRecipient = document.getElementById('label-recipient-status');

        if (!input) return;
        const nim = input.value.trim();
        if (!nim) {
            alert('Masukkan NIM mahasiswa terlebih dahulu.');
            input.focus();
            return;
        }

        btn.disabled = true;
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
        if (successCard) successCard.classList.add('hidden');
        if (errorBox) errorBox.classList.add('hidden');

        try {
            const url = "{{ route('wali.check_nim') }}?nim=" + encodeURIComponent(nim);
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            btn.disabled = false;
            if (icon) icon.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');

            if (res.ok && data.success) {
                if (!data.has_active_card || !data.card) {
                    selectedNimCardId = '';
                    selectedNimStudentName = data.student.name;
                    selectedNimValue = data.student.nim;
                    if (cardInput) cardInput.value = '';
                    if (nimInput) nimInput.value = selectedNimValue;
                    if (errorBox && errorText) {
                        errorText.innerText = `Mahasiswa ${data.student.name} (NIM: ${data.student.nim}) ditemukan, namun belum memiliki kartu NFC aktif. Hubungi mahasiswa untuk aktivasi kartu di Admin.`;
                        errorBox.classList.remove('hidden');
                    }
                    if (labelRecipient) labelRecipient.innerText = 'Penerima: ' + data.student.name + ' (Kartu Belum Aktif)';
                    updateTransferButtonLabel();
                    return;
                }

                selectedNimCardId = data.card.id;
                selectedNimStudentName = data.student.name;
                selectedNimValue = data.student.nim;

                if (cardInput) cardInput.value = selectedNimCardId;
                if (nimInput) nimInput.value = selectedNimValue;

                if (nameEl) nameEl.innerText = data.student.name;
                if (detailsEl) detailsEl.innerText = `NIM: ${data.student.nim} • UID: ${data.card.card_uid}`;
                if (balanceEl) balanceEl.innerText = data.card.balance_formatted;

                if (avatarEl) {
                    const initials = data.student.name
                        .trim()
                        .split(/\s+/)
                        .map(w => w[0])
                        .slice(0, 2)
                        .join('')
                        .toUpperCase();
                    avatarEl.innerText = initials || 'M';
                }

                if (successCard) successCard.classList.remove('hidden');
                if (labelRecipient) labelRecipient.innerText = `Penerima: ${data.student.name} [NIM: ${data.student.nim}]`;
                updateTransferButtonLabel();
            } else {
                selectedNimCardId = '';
                selectedNimStudentName = '';
                selectedNimValue = '';
                if (cardInput) cardInput.value = '';
                if (nimInput) nimInput.value = '';

                if (errorBox && errorText) {
                    errorText.innerText = data.message || 'Mahasiswa dengan NIM tersebut tidak ditemukan.';
                    errorBox.classList.remove('hidden');
                }
                if (labelRecipient) labelRecipient.innerText = 'NIM Tidak Ditemukan';
                updateTransferButtonLabel();
            }
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            if (icon) icon.classList.remove('hidden');
            if (spinner) spinner.classList.add('hidden');
            if (errorBox && errorText) {
                errorText.innerText = 'Terjadi kesalahan saat memeriksa NIM. Pastikan koneksi stabil.';
                errorBox.classList.remove('hidden');
            }
        }
    }

    function quickSendViaNim() {
        switchWaliTab('overview');
        switchRecipientType('nim');
        const container = document.getElementById('container-form-transfer');
        if (container) {
            container.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        const input = document.getElementById('input-transfer-nim');
        if (input) {
            setTimeout(() => input.focus(), 300);
        }
    }

    function onSelectOtherStudent(selectEl) {
        if (!selectEl) return;
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt || (!opt.value && opt.dataset.hasCard !== '1')) {
            const hasCard = opt ? opt.dataset.hasCard === '1' : false;
            if (!hasCard) {
                selectedOtherCardId = '';
                selectedOtherName = opt ? (opt.dataset.name || '') : '';
                selectedOtherNim = opt ? (opt.dataset.nim || '') : '';
                const cardInput = document.getElementById('topup-card-id');
                const nimInput = document.getElementById('topup-student-nim');
                if (cardInput && currentRecipientType === 'lain') cardInput.value = '';
                if (nimInput && currentRecipientType === 'lain') nimInput.value = selectedOtherNim;
                const preview = document.getElementById('preview-other-card');
                const warning = document.getElementById('warning-no-card-other');
                if (preview) preview.classList.add('hidden');
                if (warning) warning.classList.remove('hidden');
                const labelRecipient = document.getElementById('label-recipient-status');
                if (labelRecipient) labelRecipient.innerText = 'Penerima: ' + (selectedOtherName || 'Tanpa Kartu');
                updateTransferButtonLabel();
            }
            return;
        }

        selectedOtherCardId = opt.value;
        selectedOtherName = opt.dataset.name || '';
        selectedOtherNim = opt.dataset.nim || '';
        const email = opt.dataset.email || '';
        const uid = opt.dataset.uid || '';
        const balance = opt.dataset.balance || '0';

        const cardInput = document.getElementById('topup-card-id');
        const nimInput = document.getElementById('topup-student-nim');
        if (cardInput && currentRecipientType === 'lain') {
            cardInput.value = selectedOtherCardId;
        }
        if (nimInput && currentRecipientType === 'lain') {
            nimInput.value = selectedOtherNim;
        }

        const preview = document.getElementById('preview-other-card');
        const warning = document.getElementById('warning-no-card-other');
        const nameEl = document.getElementById('preview-other-name');
        const uidEl = document.getElementById('preview-other-uid');
        const balanceEl = document.getElementById('preview-other-balance');
        const avatarEl = document.getElementById('preview-other-avatar');
        const labelRecipient = document.getElementById('label-recipient-status');

        if (warning) warning.classList.add('hidden');
        if (preview) preview.classList.remove('hidden');

        const nimLabel = selectedOtherNim ? ` [NIM: ${selectedOtherNim}]` : '';
        if (nameEl) nameEl.innerText = selectedOtherName + nimLabel + (email ? ' (' + email + ')' : '');
        if (uidEl) uidEl.innerText = 'UID: ' + uid;
        if (balanceEl) balanceEl.innerText = 'Rp ' + balance;
        if (labelRecipient) labelRecipient.innerText = 'Penerima: ' + selectedOtherName + nimLabel;

        if (avatarEl && selectedOtherName) {
            const initials = selectedOtherName
                .trim()
                .split(/\s+/)
                .map(word => word[0])
                .slice(0, 2)
                .join('')
                .toUpperCase();
            avatarEl.innerText = initials || 'M';
        }

        updateTransferButtonLabel();
    }

    function quickSendToOtherStudent() {
        switchWaliTab('overview');
        switchRecipientType('lain');
        const container = document.getElementById('container-form-transfer');
        if (container) {
            container.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        const select = document.getElementById('select-other-student');
        if (select) {
            setTimeout(() => select.focus(), 300);
        }
    }

    function updateTransferButtonLabel() {
        const btnLabel = document.getElementById('btn-topup-label');
        if (!btnLabel) return;

        let targetLabel = 'Anak';
        if (currentRecipientType === 'anak') {
            targetLabel = anakName ? anakName.split(' ')[0] : 'Anak';
        } else if (currentRecipientType === 'nim') {
            targetLabel = selectedNimStudentName ? selectedNimStudentName.split(' ')[0] : 'Mahasiswa (NIM)';
        } else {
            targetLabel = selectedOtherName ? selectedOtherName.split(' ')[0] : 'Mahasiswa Lain';
        }

        if (currentWaliMethod === 'transfer') {
            btnLabel.innerText = `Kirim Saldo ke ${targetLabel} (Potong Dompet)`;
        } else {
            btnLabel.innerText = `Bayar Saldo ${targetLabel} via Midtrans`;
        }
    }

    function toggleWaliMethod(method) {
        currentWaliMethod = method;
        const lblTransfer = document.getElementById('label-method-transfer');
        const lblMidtrans = document.getElementById('label-method-midtrans');
        
        if (method === 'transfer') {
            lblTransfer.classList.add('border-primary', 'bg-blue-50/60');
            lblTransfer.classList.remove('border-gray-200');
            lblMidtrans.classList.remove('border-primary', 'bg-blue-50/60');
            lblMidtrans.classList.add('border-gray-200');
        } else {
            lblMidtrans.classList.add('border-primary', 'bg-blue-50/60');
            lblMidtrans.classList.remove('border-gray-200');
            lblTransfer.classList.remove('border-primary', 'bg-blue-50/60');
            lblTransfer.classList.add('border-gray-200');
        }
        updateTransferButtonLabel();
        checkWaliTransferBalance();
    }

    function selectTransferPreset(amount) {
        const amountInput = document.getElementById('topup-amount');
        if (amountInput) {
            amountInput.value = amount;
            checkWaliTransferBalance();
        }
    }

    function checkWaliTransferBalance() {
        const amountInput = document.getElementById('topup-amount');
        const warningBox = document.getElementById('wali-balance-warning');
        const shortageText = document.getElementById('wali-shortage-text');
        const quickTopupBtn = document.getElementById('btn-quick-topup-label');

        if (!amountInput || !warningBox) return;

        const amount = parseFloat(amountInput.value) || 0;

        if (currentWaliMethod === 'transfer' && amount > waliBalance) {
            const shortage = amount - waliBalance;
            const formattedShortage = 'Rp ' + new Intl.NumberFormat('id-ID').format(shortage);
            const formattedBalance = 'Rp ' + new Intl.NumberFormat('id-ID').format(waliBalance);

            if (shortageText) {
                shortageText.innerText = `Saldo Anda saat ini ${formattedBalance}. Anda kekurangan ${formattedShortage} untuk melakukan transfer ini.`;
            }
            if (quickTopupBtn) {
                quickTopupBtn.innerText = `Top Up ${formattedShortage} Sekarang`;
            }
            warningBox.classList.remove('hidden');
        } else {
            warningBox.classList.add('hidden');
        }
    }

    async function executeWaliTopup() {
        const form = document.getElementById('topup-form');
        const amountInput = document.getElementById('topup-amount');
        const notesInput = document.getElementById('topup-notes');
        const cardIdInput = form ? form.querySelector('input[name="card_id"]') : null;
        const nimInput = form ? form.querySelector('input[name="nim"]') : null;
        const btn = document.getElementById('btn-topup');
        const btnLabel = document.getElementById('btn-topup-label');

        const amount = parseFloat(amountInput ? amountInput.value : 0);
        const notes = notesInput ? notesInput.value : '';
        const cardId = cardIdInput ? cardIdInput.value : '';
        const nim = nimInput ? nimInput.value.trim() : '';
        const csrfToken = '{{ csrf_token() }}';

        if (!cardId && !nim) {
            if (currentRecipientType === 'anak') {
                alert('Mahasiswa anak asuh belum terhubung atau belum memiliki kartu NFC aktif.');
            } else if (currentRecipientType === 'nim') {
                alert('Silakan masukkan NIM mahasiswa dan klik tombol "Cek NIM" terlebih dahulu.');
                const inNim = document.getElementById('input-transfer-nim');
                if (inNim) inNim.focus();
            } else {
                alert('Silakan pilih mahasiswa lain yang memiliki kartu NFC aktif terlebih dahulu.');
                const sel = document.getElementById('select-other-student');
                if (sel) sel.focus();
            }
            return;
        }

        if (!amount || amount < 1000) {
            alert('Minimal pengiriman saldo adalah Rp 1.000');
            if (amountInput) amountInput.focus();
            return;
        }

        if (currentWaliMethod === 'transfer') {
            if (amount > waliBalance) {
                const shortage = amount - waliBalance;
                const formattedShortage = 'Rp ' + new Intl.NumberFormat('id-ID').format(shortage);
                if (confirm(`Saldo Dompet Wali Anda tidak mencukupi (Kurang ${formattedShortage}).\n\nApakah Anda ingin mengisi saldo dompet sekarang?`)) {
                    openTopupWaliModal(shortage);
                }
                return;
            }

            btn.disabled = true;
            btnLabel.innerText = 'Mengirim Saldo...';

            try {
                const res = await fetch("{{ route('wali.transfer') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        card_id: cardId || null,
                        nim: nim || null,
                        amount: amount,
                        notes: notes,
                        payment_method: 'transfer_wali'
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    alert(data.message || (currentRecipientType === 'anak' ? 'Berhasil mengirim saldo ke anak!' : 'Berhasil mengirim saldo ke mahasiswa!'));
                    window.location.reload();
                } else {
                    if (data.insufficient_balance) {
                        if (confirm((data.message || 'Saldo dompet tidak mencukupi') + "\n\nBuka modal Top Up Dompet sekarang?")) {
                            openTopupWaliModal(data.shortage || amount);
                        }
                    } else {
                        alert(data.message || 'Gagal mengirim saldo.');
                    }
                    btn.disabled = false;
                    updateTransferButtonLabel();
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
                btn.disabled = false;
                updateTransferButtonLabel();
            }
            return;
        }

        // Jalur Midtrans
        btn.disabled = true;
        btnLabel.innerText = 'Menghubungkan ke Midtrans...';

        try {
            const res = await fetch("{{ route('topup.midtrans') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    card_id: cardId || null,
                    nim: nim || null,
                    amount: amount,
                    notes: notes
                })
            });

            const data = await res.json();
            btn.disabled = false;
            updateTransferButtonLabel();

            if (res.ok && data.snap_token) {
                if (typeof window.snap === 'undefined') {
                    alert('Midtrans Snap JS belum termuat. Anda dapat menggunakan opsi Saldo Dompet.');
                    return;
                }
                window.snap.pay(data.snap_token, {
                    onSuccess: async function(result){
                        try {
                            await fetch("{{ route('midtrans.finish') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ order_id: result.order_id || data.order_id })
                            });
                        } catch(e) {}
                        alert("Pembayaran berhasil! Saldo kartu mahasiswa penerima telah ditambahkan.");
                        window.location.reload();
                    },
                    onPending: function(result){
                        alert("Menunggu pembayaran Anda diselesaikan.");
                        window.location.reload();
                    },
                    onError: async function(result){
                        alert("Pembayaran Midtrans dibatalkan atau gagal.");
                        try {
                            await fetch("{{ route('midtrans.cancel') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ order_id: (result && result.order_id) || data.order_id })
                            });
                        } catch(e) {}
                        window.location.reload();
                    },
                    onClose: async function(){
                        if (confirm("Anda menutup popup pembayaran.\n\nApakah Anda ingin membatalkan transaksi ini?")) {
                            try {
                                await fetch("{{ route('midtrans.cancel') }}", {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    body: JSON.stringify({ order_id: data.order_id })
                                });
                            } catch(e) {}
                            window.location.reload();
                        }
                    }
                });
            } else {
                let errMsg = data.error || 'Gagal mengambil token pembayaran Midtrans.';
                alert(errMsg);
            }
        } catch (err) {
            console.error(err);
            btn.disabled = false;
            updateTransferButtonLabel();
            alert('Terjadi kesalahan jaringan.');
        }
    }

    function previewWaliPhoto(input, previewId, fallbackId) {
        if (typeof handleAvatarCropSelect === 'function') {
            handleAvatarCropSelect(input, [previewId, 'sidebar-wali-avatar'], [fallbackId, 'sidebar-wali-avatar-fallback']);
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
