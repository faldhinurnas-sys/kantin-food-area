@extends('layouts.app')
@section('title', 'Admin Dashboard')

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
                <span class="text-[10px] bg-blue-500 text-white font-bold px-1.5 py-0.5 rounded ml-1.5 uppercase">Admin</span>
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
                        <span class="px-2 py-0.5 bg-blue-100 text-primary rounded-full text-[10px] font-extrabold uppercase tracking-wide">ADMIN PANEL</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                </div>
            </div>
            <!-- Tombol Tutup di Mobile -->
            <button type="button" onclick="toggleMobileSidebar()" class="md:hidden text-gray-400 hover:text-gray-600 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- INFO PROFIL ADMIN -->
        <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/80">
            <div class="flex items-center space-x-3">
                @if(Auth::user()->avatar_url)
                    <img id="sidebar-admin-avatar" src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-11 h-11 rounded-full object-cover border border-primary/20 shadow-xs shrink-0">
                @else
                    <div id="sidebar-admin-avatar-fallback" class="w-11 h-11 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary font-bold text-base shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="text-sm font-bold text-gray-900 truncate" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-500 truncate flex items-center mt-0.5">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                        Administrator Sistem
                    </div>
                </div>
            </div>
            <!-- Tombol Edit Profil -->
            <button type="button" onclick="switchAdminTab('profil')" class="mt-3 w-full flex items-center justify-center space-x-1.5 py-1.5 px-3 rounded-lg text-xs font-bold text-primary bg-blue-50 hover:bg-blue-100 border border-blue-200 transition shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                <span>Edit Profil & Keamanan</span>
            </button>
        </div>

        <!-- DAFTAR MENU NAVIGASI (TABS) -->
        <div class="flex-1 overflow-y-auto px-3.5 py-4 space-y-1.5">
            <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">Menu Pengawasan</div>

            <!-- Tab 1: Overview & Approval -->
            <button type="button" onclick="switchAdminTab('overview')" id="btn-admin-overview" class="admin-tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-bold text-sm transition-all text-primary bg-blue-50 border-l-4 border-primary shadow-xs">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 admin-tab-icon text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Overview & Approval</span>
                </div>
                @php $pendingCount = $pendingUsers->count() + $pendingCards->count(); @endphp
                @if($pendingCount > 0)
                    <span class="text-xs font-bold bg-rose-500 text-white px-2 py-0.5 rounded-full animate-pulse">{{ $pendingCount }}</span>
                @else
                    <span class="text-xs text-gray-400 font-medium">0</span>
                @endif
            </button>

            <!-- Tab 2: Manajemen Pengguna -->
            <button type="button" onclick="switchAdminTab('users')" id="btn-admin-users" class="admin-tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 admin-tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Manajemen Pengguna</span>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 font-bold px-2 py-0.5 rounded-full">{{ $users->count() }}</span>
            </button>

            <!-- Tab 3: Manajemen Kartu NFC -->
            <button type="button" onclick="switchAdminTab('cards')" id="btn-admin-cards" class="admin-tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 admin-tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Kartu NFC</span>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 font-bold px-2 py-0.5 rounded-full">{{ $allCards->count() }}</span>
            </button>

            <!-- Tab 4: Pantauan Transaksi -->
            <button type="button" onclick="switchAdminTab('monitor')" id="btn-admin-monitor" class="admin-tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 admin-tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Pantauan Transaksi</span>
                </div>
                <span class="text-xs bg-gray-100 text-gray-600 font-bold px-2 py-0.5 rounded-full">{{ $transactions->count() }}</span>
            </button>

            <!-- Tab 5: Profil Admin -->
            <button type="button" onclick="switchAdminTab('profil')" id="btn-admin-profil" class="admin-tab-btn w-full flex items-center justify-between px-3.5 py-3 rounded-xl font-semibold text-sm transition-all text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 admin-tab-icon text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                    <span>Profil Administrator</span>
                </div>
            </button>
        </div>

        <!-- FOOTER SIDEBAR (LOGOUT) -->
        <div class="p-4 border-t border-gray-100 bg-white">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl font-bold text-sm text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar dari Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA (KANAN) -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- TOPBAR DESKTOP -->
        <header class="hidden md:flex bg-white border-b border-gray-200 px-8 py-4 items-center justify-between sticky top-0 z-30 shadow-2xs">
            <div>
                <h2 id="admin-topbar-title" class="text-xl font-extrabold text-gray-900 tracking-tight">Overview & Approval</h2>
                <p id="admin-topbar-subtitle" class="text-xs text-gray-500 mt-0.5">Persetujuan pendaftaran akun dan aktivasi kartu baru</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2 bg-blue-50 border border-blue-200 px-3.5 py-1.5 rounded-xl text-primary font-bold text-xs shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Total Penjualan: Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
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
            <!-- TAB 1: OVERVIEW & APPROVAL                                     -->
            <!-- ============================================================== -->
            <div id="tab-admin-overview" class="admin-tab-content block space-y-6">
                
                <!-- Quick Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Omzet</span>
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="text-xl font-black text-gray-900 mt-2">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Transaksi</span>
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-primary flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                        </div>
                        <div class="text-xl font-black text-gray-900 mt-2">{{ $transactions->count() }} Transaksi</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Pengguna Menunggu</span>
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                            </div>
                        </div>
                        <div class="text-xl font-black {{ $pendingUsers->count() > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-2">{{ $pendingUsers->count() }} Akun</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Aktivasi Kartu</span>
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                        </div>
                        <div class="text-xl font-black {{ $pendingCards->count() > 0 ? 'text-indigo-600' : 'text-gray-900' }} mt-2">{{ $pendingCards->count() }} Kartu</div>
                    </div>
                </div>

                <!-- 2 Column Section: Pending Users & Pending Cards -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    <!-- PENDING USERS -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-base">Permohonan Akun Pengguna</h3>
                                        <p class="text-xs text-gray-400">Persetujuan registrasi baru</p>
                                    </div>
                                </div>
                                <span class="bg-orange-100 text-orange-800 py-1 px-3 rounded-full text-xs font-bold">{{ $pendingUsers->count() }} Menunggu</span>
                            </div>

                            @if($pendingUsers->isEmpty())
                                <div class="text-center py-12 text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-xs font-semibold">Tidak ada permintaan akun yang tertunda.</p>
                                </div>
                            @else
                                <ul class="divide-y divide-gray-100 max-h-80 overflow-y-auto space-y-1">
                                    @foreach($pendingUsers as $user)
                                        <li class="py-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm">
                                                    {{ $user->name }}
                                                    <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-[10px] ml-1 font-bold uppercase">{{ $user->role }}</span>
                                                </p>
                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                            </div>
                                            <div class="flex gap-2">
                                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-2xs">Setujui</button>
                                                </form>
                                                <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-2xs">Tolak</button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <!-- PENDING CARDS -->
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-primary flex items-center justify-center font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-base">Aktivasi Kartu NFC</h3>
                                        <p class="text-xs text-gray-400">Persetujuan kartu baru mahasiswa</p>
                                    </div>
                                </div>
                                <span class="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-bold">{{ $pendingCards->count() }} Menunggu</span>
                            </div>

                            @if($pendingCards->isEmpty())
                                <div class="text-center py-12 text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-xs font-semibold">Tidak ada permohonan kartu NFC tertunda.</p>
                                </div>
                            @else
                                <ul class="divide-y divide-gray-100 max-h-80 overflow-y-auto space-y-1">
                                    @foreach($pendingCards as $card)
                                        <li class="py-3 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                                            <div>
                                                <p class="font-bold text-gray-800 font-mono text-sm">{{ $card->card_uid }}</p>
                                                <p class="text-xs text-gray-500">Mhs: <span class="font-semibold text-gray-700">{{ $card->user->name ?? 'Tidak ada' }}</span></p>
                                            </div>
                                            <div class="flex gap-2">
                                                <form action="{{ route('admin.cards.approve', $card->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-2xs inline-flex items-center gap-1" onclick="return confirm('ACC dan aktifkan kartu NFC {{ $card->card_uid }} milik {{ addslashes($card->user->name ?? 'Mahasiswa') }}?')">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        <span>ACC / Aktifkan</span>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.cards.reject', $card->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-2xs" onclick="return confirm('Tolak permohonan kartu NFC ini?')">Tolak</button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                </div>

            </div>

            <!-- ============================================================== -->
            <!-- TAB 2: MANAJEMEN AKUN PENGGUNA                                -->
            <!-- ============================================================== -->
            <div id="tab-admin-users" class="admin-tab-content hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Manajemen Pengguna Sistem</h3>
                            <p class="text-xs text-gray-500">Daftar akun pedagang, mahasiswa, dan wali di Kantin Polbeng</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-gray-500">Total Pengguna:</span>
                            <span class="px-2.5 py-1 bg-blue-50 text-primary rounded-xl font-bold text-xs border border-blue-200">
                                {{ $users->count() }} Akun
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto mt-4">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50/80 text-gray-600 rounded-xl text-xs uppercase font-bold">
                                <tr>
                                    <th class="p-3.5 rounded-l-xl">Nama Pengguna</th>
                                    <th class="p-3.5">Email</th>
                                    <th class="p-3.5">Peran</th>
                                    <th class="p-3.5 text-center">Status</th>
                                    <th class="p-3.5 text-right rounded-r-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($users as $u)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-3.5 font-bold text-gray-800">
                                        <div class="flex items-center space-x-2.5">
                                            @if($u->avatar_url)
                                                <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-8 h-8 rounded-full object-cover border border-gray-200 shrink-0">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs shrink-0">
                                                    {{ strtoupper(substr($u->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <span class="truncate font-semibold text-gray-900">{{ $u->name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3.5 text-xs text-gray-600">{{ $u->email }}</td>
                                    <td class="p-3.5">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase
                                            {{ $u->role == 'pedagang' ? 'bg-orange-100 text-orange-700' : ($u->role == 'mahasiswa' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700') }}">
                                            {{ ucfirst($u->role) }}
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-center">
                                        @if($u->status == 'approved')
                                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-extrabold uppercase">Aktif</span>
                                        @elseif($u->status == 'rejected' || $u->status == 'inactive')
                                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-[10px] font-extrabold uppercase">Nonaktif</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-extrabold uppercase">Pending</span>
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-right space-x-1.5 whitespace-nowrap">
                                        <a href="{{ route('admin.users.show', $u->id) }}" class="text-primary hover:text-blue-800 text-xs font-bold bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg border border-blue-200 transition inline-block">Detail</a>
                                        
                                        @if($u->status == 'approved')
                                            <form action="{{ route('admin.users.reject', $u->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-bold bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg border border-rose-200 transition" onclick="return confirm('Yakin ingin memblokir pengguna ini?')">Blokir</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.users.approve', $u->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg border border-emerald-200 transition" onclick="return confirm('Aktifkan kembali pengguna ini?')">Aktifkan</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-white bg-red-50 hover:bg-red-600 border border-red-200 px-2.5 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1 shadow-2xs" onclick="return confirm('Yakin ingin MENGHAPUS PERMANEN akun pengguna {{ addslashes($u->name) }}?')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ============================================================== -->
            <!-- TAB 3: MANAJEMEN KARTU NFC                                     -->
            <!-- ============================================================== -->
            <div id="tab-admin-cards" class="admin-tab-content hidden space-y-6">
                
                <!-- Form Registrasi & Aktivasi Kartu Baru -->
                <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-slate-50 border border-blue-200/80 p-6 rounded-3xl shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900">Daftarkan Kartu NFC Baru (Menunggu Validasi ACC)</h3>
                        </div>
                        <button type="button" id="btn-admin-webnfc" class="hidden text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-3 py-1.5 rounded-xl transition shadow-xs flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            <span id="btn-admin-webnfc-text">Aktifkan Sensor Tap HP</span>
                        </button>
                    </div>

                    <form action="{{ route('admin.cards.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Mahasiswa Pemilik <span class="text-rose-500">*</span></label>
                            <select name="user_id" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl bg-white text-xs font-semibold focus:ring-2 focus:ring-primary focus:border-primary">
                                <option value="">-- Pilih Mahasiswa --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-gray-700 uppercase">UID Kartu NFC <span class="text-rose-500">*</span></label>
                                <span id="admin-nfc-status-badge" class="hidden text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                    Sensor Siap
                                </span>
                            </div>
                            <div class="relative">
                                <input type="text" name="card_uid" id="admin-card-uid-input" placeholder="Tempel kartu / ketik UID..." required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl font-mono text-xs uppercase font-bold focus:ring-2 focus:ring-primary focus:border-primary">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Saldo Awal (Rp)</label>
                            <input type="number" name="balance" min="0" value="0" step="1000" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition shadow-md shadow-blue-500/20 flex items-center justify-center gap-1.5" onclick="return confirm('Daftarkan kartu NFC ini? Kartu akan berstatus Menunggu ACC sebelum aktif digunakan.')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Daftarkan (Menunggu ACC)</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabel Kartu Terdaftar -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Daftar Seluruh Kartu NFC</h3>
                            <p class="text-xs text-gray-500">Kartu fisik yang telah diaktivasi di sistem Kantin Polbeng</p>
                        </div>
                        <span class="px-2.5 py-1 bg-blue-50 text-primary rounded-xl font-bold text-xs border border-blue-200">
                            {{ $allCards->count() }} Kartu
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50/80 text-gray-600 rounded-xl text-xs uppercase font-bold">
                                <tr>
                                    <th class="p-3.5 rounded-l-xl">UID Kartu</th>
                                    <th class="p-3.5">Pemilik Mahasiswa</th>
                                    <th class="p-3.5 text-right">Saldo Saat Ini</th>
                                    <th class="p-3.5 text-center">Status</th>
                                    <th class="p-3.5 text-right rounded-r-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($allCards as $c)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-3.5 font-bold font-mono text-gray-800 text-xs">{{ $c->card_uid }}</td>
                                    <td class="p-3.5 text-xs text-gray-700 font-semibold">{{ $c->user->name ?? 'N/A' }}</td>
                                    <td class="p-3.5 text-right font-black text-sm text-primary">Rp {{ number_format($c->balance, 0, ',', '.') }}</td>
                                    <td class="p-3.5 text-center">
                                        @if($c->status == 'active')
                                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-extrabold uppercase">Aktif</span>
                                        @elseif($c->status == 'pending')
                                            <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-extrabold uppercase animate-pulse">Menunggu ACC</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-[10px] font-extrabold uppercase">Diblokir</span>
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-right space-x-1.5 whitespace-nowrap">
                                        @if($c->status == 'pending')
                                            <!-- Tombol Validasi & ACC Kartu Pending -->
                                            <form action="{{ route('admin.cards.approve', $c->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-white bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1 shadow-2xs" onclick="return confirm('Validasi dan ACC kartu NFC {{ $c->card_uid }} milik {{ addslashes($c->user->name ?? 'Mahasiswa') }}?')">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span>ACC / Aktifkan</span>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.cards.reject', $c->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-500 border border-rose-200 px-2.5 py-1.5 rounded-lg text-xs font-bold transition" onclick="return confirm('Tolak permohonan kartu NFC ini?')">Tolak</button>
                                            </form>
                                        @else
                                            <!-- Tombol Ganti Kartu -->
                                            <button type="button" onclick="openReplaceCardModal('{{ $c->id }}', '{{ $c->card_uid }}', '{{ addslashes($c->user->name ?? 'Mahasiswa') }}', '{{ $c->balance }}')" class="text-amber-700 hover:text-white bg-amber-50 hover:bg-amber-600 border border-amber-200 hover:border-amber-600 px-2.5 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1 shadow-2xs" title="Ganti Kartu Rusak/Hilang & Pindahkan Saldo">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Ganti Kartu</span>
                                            </button>

                                            @if($c->status == 'active')
                                                <form action="{{ route('admin.cards.reject', $c->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-500 border border-rose-200 px-2.5 py-1.5 rounded-lg text-xs font-bold transition" onclick="return confirm('Blokir kartu NFC ini? (Misal: Karena kartu hilang/rusak)')">Blokir</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.cards.approve', $c->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-emerald-600 hover:text-white bg-emerald-50 hover:bg-emerald-600 border border-emerald-200 px-2.5 py-1.5 rounded-lg text-xs font-bold transition" onclick="return confirm('Buka blokir dan aktifkan kembali kartu NFC ini?')">Aktifkan</button>
                                                </form>
                                            @endif
                                        @endif

                                        <!-- Tombol Hapus Kartu -->
                                        <form action="{{ route('admin.cards.destroy', $c->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-white bg-red-50 hover:bg-red-600 border border-red-200 px-2.5 py-1.5 rounded-lg text-xs font-bold transition inline-flex items-center gap-1 shadow-2xs" onclick="return confirm('Apakah Anda yakin ingin MENGHAPUS PERMANEN kartu NFC {{ $c->card_uid }} milik {{ addslashes($c->user->name ?? 'Mahasiswa') }} ini?')">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-500 text-xs">Tidak ada kartu NFC yang terdaftar.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- TAB 4: PANTAUAN TRANSAKSI & LOG LAPORAN                        -->
            <!-- ============================================================== -->
            <div id="tab-admin-monitor" class="admin-tab-content hidden space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-primary via-blue-700 to-indigo-800 rounded-3xl p-6 sm:p-8 shadow-xl text-white flex items-center justify-between border border-white/10">
                        <div>
                            <div class="text-blue-200 text-xs font-bold uppercase tracking-wider mb-1">Total Perputaran Uang Kantin</div>
                            <div class="text-3xl sm:text-4xl font-black">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
                            <p class="text-xs text-blue-200/80 mt-2">Akumulasi seluruh transaksi sukses di sistem POS</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <div class="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Total Transaksi Tercatat</div>
                            <div class="text-3xl sm:text-4xl font-black text-gray-900">{{ $transactions->count() }} <span class="text-sm font-normal text-gray-400">transaksi</span></div>
                            <p class="text-xs text-gray-500 mt-2">Log seluruh pembayaran dan top-up saldo</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">Log Transaksi Seluruh Sistem</h3>
                            <p class="text-xs text-gray-500">Sesuai FR-30: Admin hanya dapat memantau log tanpa mengubah data.</p>
                        </div>
                        <span class="text-xs font-mono bg-white border border-gray-200 px-3 py-1 rounded-xl text-gray-600">
                            Terakhir diupdate: {{ now()->format('H:i:s') }}
                        </span>
                    </div>

                    <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-white text-gray-600 border-b sticky top-0 text-xs uppercase font-bold">
                                <tr>
                                    <th class="p-3.5">Waktu</th>
                                    <th class="p-3.5">Tipe</th>
                                    <th class="p-3.5">Pelaku / Tujuan</th>
                                    <th class="p-3.5 text-right">Nominal</th>
                                    <th class="p-3.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($transactions as $trx)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-3.5 text-xs">
                                        <div class="font-bold text-gray-800">{{ $trx->created_at->format('d M Y') }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $trx->created_at->format('H:i:s') }} WIB</div>
                                    </td>
                                    <td class="p-3.5">
                                        @if($trx->type == 'topup')
                                            <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-extrabold uppercase">TOP-UP</span>
                                            <div class="text-[11px] text-gray-500 mt-0.5">Via {{ ucfirst($trx->payment_method) }}</div>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 rounded-full text-[10px] font-extrabold uppercase">BELANJA</span>
                                            <div class="text-[11px] text-gray-500 mt-0.5">Kantin: {{ $trx->pedagang->name ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-xs">
                                        @if($trx->type == 'topup')
                                            <span class="font-bold text-gray-900">{{ $trx->card->user->name ?? 'Unknown' }}</span>
                                            <div class="text-[11px] text-gray-400 font-mono">{{ $trx->card->card_uid ?? '' }}</div>
                                        @else
                                            <span class="font-bold text-gray-900">Pembeli: {{ $trx->card->user->name ?? 'Pelanggan Umum' }}</span>
                                            <div class="text-[11px] text-gray-400">Metode: {{ ucfirst($trx->payment_method) }}</div>
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-right font-black text-sm {{ $trx->type == 'topup' ? 'text-emerald-600' : 'text-gray-900' }}">
                                        Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3.5 text-center">
                                        @if($trx->status == 'success')
                                            <span class="bg-emerald-500 text-white px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase">Berhasil</span>
                                        @elseif($trx->status == 'pending')
                                            <span class="bg-amber-500 text-white px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase">Pending</span>
                                        @else
                                            <span class="bg-rose-500 text-white px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase">Gagal</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-gray-500 text-xs">Belum ada transaksi di sistem.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- ============================================================== -->
            <!-- TAB 5: PENGATURAN PROFIL ADMIN                                 -->
            <!-- ============================================================== -->
            <div id="tab-admin-profil" class="admin-tab-content hidden space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 max-w-2xl">
                    <div class="pb-5 border-b border-gray-100 mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Pengaturan Akun Administrator</h3>
                        <p class="text-xs text-gray-500 mt-1">Ubah nama, email, foto profil, dan kata sandi akses sistem</p>
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="avatar_cropped">

                        <!-- Foto Profil -->
                        <div class="bg-slate-50 p-4 rounded-2xl border border-gray-200 flex items-center space-x-4">
                            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-primary/20 flex items-center justify-center bg-white shadow-xs shrink-0">
                                @if(Auth::user()->avatar_url)
                                    <img id="preview-admin-tab-avatar" src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <div id="preview-admin-tab-avatar-fallback" class="w-full h-full bg-primary/10 flex items-center justify-center text-primary font-black text-xl">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <label class="cursor-pointer inline-flex items-center space-x-1.5 px-3.5 py-2 bg-white hover:bg-gray-100 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 shadow-2xs transition">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Pilih & Pangkas Foto</span>
                                    <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden" onclick="this.value = null" onchange="previewAdminPhoto(this, 'preview-admin-tab-avatar', 'preview-admin-tab-avatar-fallback')">
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
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Nama Lengkap Admin <span class="text-rose-500">*</span></label>
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
                                <span>Simpan Profil Admin</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>

</div>

<!-- ============================================================== -->
<!-- MODAL GANTI KARTU RUSAK / HILANG                               -->
<!-- ============================================================== -->
<div id="modal-replace-card" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl relative border border-gray-100 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center space-x-2.5 text-amber-600">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-gray-900 leading-tight">Ganti Kartu Rusak / Hilang</h3>
                    <p class="text-[11px] text-gray-500">Pindahkan saldo ke kartu fisik baru</p>
                </div>
            </div>
            <button type="button" onclick="closeReplaceCardModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="form-replace-card" method="POST" class="mt-4 space-y-4">
            @csrf
            <!-- Info Kartu Lama & Saldo -->
            <div class="bg-amber-50/80 border border-amber-200 rounded-2xl p-4 text-xs text-amber-900 space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500 font-medium">Mahasiswa:</span>
                    <span id="replace-student-name" class="font-bold text-gray-900">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 font-medium">Kartu Lama:</span>
                    <span id="replace-old-uid" class="font-mono font-bold text-gray-800">-</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-amber-200/70">
                    <span class="text-gray-700 font-bold">Saldo Dipindahkan:</span>
                    <span id="replace-balance" class="font-black text-base text-primary">Rp 0</span>
                </div>
            </div>

            <!-- Input UID Kartu Baru -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">
                    UID Kartu Baru (Tempel ke HP/Reader) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" name="new_card_uid" id="replace-new-uid-input" required placeholder="Tempel kartu baru..." autocomplete="off"
                        class="w-full px-4 py-2.5 border-2 border-amber-400 rounded-xl font-mono font-bold text-sm uppercase focus:ring-4 focus:ring-amber-100 focus:border-amber-600 text-gray-800">
                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-amber-500 pointer-events-none">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                </div>
                <p class="text-[11px] text-gray-500 mt-1">Tempelkan kartu baru ke belakang HP atau reader RFID. UID otomatis terisi.</p>
            </div>

            <!-- Alasan Penggantian -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Alasan Penggantian</label>
                <select name="reason" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl bg-white text-xs font-semibold text-gray-800 focus:ring-primary focus:border-primary">
                    <option value="Kartu Fisik Rusak/Patah">Kartu Fisik Rusak / Patah</option>
                    <option value="Kartu Hilang">Kartu Hilang</option>
                    <option value="Chip NFC Tidak Terbaca">Chip NFC Tidak Terbaca</option>
                    <option value="Penggantian Rutin">Penggantian Rutin</option>
                </select>
            </div>

            <!-- Tombol Submit -->
            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeReplaceCardModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-extrabold rounded-xl text-xs shadow-md transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Pindahkan Saldo & Aktifkan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const adminTabHeaders = {
        overview: {
            title: 'Overview & Approval',
            subtitle: 'Persetujuan pendaftaran akun baru dan aktivasi kartu mahasiswa'
        },
        users: {
            title: 'Manajemen Pengguna',
            subtitle: 'Kelola data seluruh akun pedagang, mahasiswa, dan wali'
        },
        cards: {
            title: 'Manajemen Kartu NFC',
            subtitle: 'Pendaftaran kartu baru, penggantian kartu rusak, dan pemantauan saldo'
        },
        monitor: {
            title: 'Pantauan Transaksi & Laporan',
            subtitle: 'Log transaksi real-time seluruh sistem kasir dan isi saldo kantin'
        },
        profil: {
            title: 'Profil Administrator',
            subtitle: 'Kelola data diri admin, alamat email login, dan kata sandi akses'
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

    function switchAdminTab(tabId) {
        try {
            localStorage.setItem('adminActiveTab', tabId);
        } catch(e) {}

        // Sembunyikan semua konten tab
        document.querySelectorAll('.admin-tab-content').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('block');
        });
        
        // Tampilkan konten tab yang dipilih
        const activeTab = document.getElementById('tab-admin-' + tabId);
        if (activeTab) {
            activeTab.classList.remove('hidden');
            activeTab.classList.add('block');
        }

        // Reset styling semua tombol di sidebar
        document.querySelectorAll('.admin-tab-btn').forEach(btn => {
            btn.classList.remove('text-primary', 'bg-blue-50', 'border-primary', 'shadow-xs', 'font-bold');
            btn.classList.add('text-gray-600', 'border-transparent', 'font-semibold');
            const icon = btn.querySelector('.admin-tab-icon');
            if (icon) {
                icon.classList.remove('text-primary');
                icon.classList.add('text-gray-400');
            }
        });

        // Set styling tombol yang aktif
        const activeBtn = document.getElementById('btn-admin-' + tabId);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-600', 'border-transparent', 'font-semibold');
            activeBtn.classList.add('text-primary', 'bg-blue-50', 'border-primary', 'shadow-xs', 'font-bold');
            const icon = activeBtn.querySelector('.admin-tab-icon');
            if (icon) {
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-primary');
            }
        }

        // Update judul header desktop
        const headerInfo = adminTabHeaders[tabId];
        if (headerInfo) {
            const titleEl = document.getElementById('admin-topbar-title');
            const subtitleEl = document.getElementById('admin-topbar-subtitle');
            if (titleEl) titleEl.innerText = headerInfo.title;
            if (subtitleEl) subtitleEl.innerText = headerInfo.subtitle;
        }

        // Tutup sidebar jika di layar mobile
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

    // MODAL GANTI KARTU
    function openReplaceCardModal(id, uid, studentName, balance) {
        const modal = document.getElementById('modal-replace-card');
        const form = document.getElementById('form-replace-card');
        form.action = `/admin/cards/${id}/replace`;
        
        document.getElementById('replace-student-name').innerText = studentName;
        document.getElementById('replace-old-uid').innerText = uid;
        document.getElementById('replace-balance').innerText = 'Rp ' + parseInt(balance).toLocaleString('id-ID');
        
        const newUidInput = document.getElementById('replace-new-uid-input');
        newUidInput.value = '';
        modal.classList.remove('hidden');
        setTimeout(() => newUidInput.focus(), 100);
    }

    function closeReplaceCardModal() {
        document.getElementById('modal-replace-card').classList.add('hidden');
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

    // WEB NFC FOR ADMIN (HP & USB Reader)
    async function initAdminWebNfc() {
        if (!('NDEFReader' in window)) return false;
        try {
            const ndef = new NDEFReader();
            await ndef.scan();
            const badge = document.getElementById('admin-nfc-status-badge');
            if (badge) {
                badge.classList.remove('hidden');
                badge.innerHTML = '<span class="w-1.5 h-1.5 mr-1 rounded-full bg-emerald-500 animate-ping inline-block"></span> Web NFC Aktif';
            }
            const btn = document.getElementById('btn-admin-webnfc');
            if (btn) {
                btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700');
                btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                document.getElementById('btn-admin-webnfc-text').innerText = 'Sensor NFC Aktif';
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
                    playBeep(true);
                    const replaceModal = document.getElementById('modal-replace-card');
                    if (replaceModal && !replaceModal.classList.contains('hidden')) {
                        const replaceInput = document.getElementById('replace-new-uid-input');
                        if (replaceInput) {
                            replaceInput.value = uid;
                            replaceInput.focus();
                        }
                    } else {
                        const cardInput = document.getElementById('admin-card-uid-input');
                        if (cardInput) {
                            cardInput.value = uid;
                            cardInput.focus();
                        }
                    }
                }
            };
            return true;
        } catch (err) {
            console.warn("Admin Web NFC error: ", err);
            return false;
        }
    }

    if ('NDEFReader' in window) {
        const btnWebNfc = document.getElementById('btn-admin-webnfc');
        if (btnWebNfc) {
            btnWebNfc.classList.remove('hidden');
            btnWebNfc.addEventListener('click', async () => {
                const ok = await initAdminWebNfc();
                if (ok) {
                    alert('Sensor Web NFC aktif! Tempelkan kartu NFC ke HP.');
                }
            });
        }
    }

    // USB RFID Keyboard-Wedge Fast Reader Listener di Admin
    let adminNfcBuffer = '';
    let adminNfcLastTime = Date.now();
    window.addEventListener('keydown', (e) => {
        if (['input-user-name', 'input-user-email', 'rent_due_date'].includes(document.activeElement?.id)) {
            return;
        }
        const now = Date.now();
        if (now - adminNfcLastTime > 200) {
            adminNfcBuffer = '';
        }
        adminNfcLastTime = now;

        if (e.key === 'Enter') {
            if (adminNfcBuffer.length >= 4) {
                playBeep(true);
                const replaceModal = document.getElementById('modal-replace-card');
                if (replaceModal && !replaceModal.classList.contains('hidden')) {
                    const replaceInput = document.getElementById('replace-new-uid-input');
                    if (replaceInput) {
                        replaceInput.value = adminNfcBuffer.toUpperCase();
                    }
                } else {
                    const cardInput = document.getElementById('admin-card-uid-input');
                    if (cardInput) {
                        cardInput.value = adminNfcBuffer.toUpperCase();
                    }
                }
                adminNfcBuffer = '';
            }
        } else if (e.key.length === 1) {
            adminNfcBuffer += e.key;
        }
    });

    function previewAdminPhoto(input, previewId, fallbackId) {
        if (typeof handleAvatarCropSelect === 'function') {
            handleAvatarCropSelect(input, [previewId, 'sidebar-admin-avatar'], [fallbackId, 'sidebar-admin-avatar-fallback']);
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

    // Restore tab aktif sebelumnya saat halaman dimuat ulang
    document.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.replace('#', '');
        const targetTab = hash || localStorage.getItem('adminActiveTab');
        if (targetTab && targetTab !== 'overview' && document.getElementById('tab-admin-' + targetTab)) {
            switchAdminTab(targetTab);
        }
    });
</script>
@endpush
