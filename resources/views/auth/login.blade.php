@extends('layouts.app')
@section('title', 'Login - Kantin Polbeng')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] px-4 py-8">
    <div class="bg-white p-7 sm:p-9 rounded-3xl shadow-xl shadow-slate-200/60 w-full max-w-md border border-gray-100 transition-all">
        
        <!-- LOGO & BRANDING -->
        <div class="text-center mb-7">
            <div class="inline-block relative mb-3">
                <div class="w-24 h-24 rounded-3xl bg-slate-50/80 shadow-inner border border-gray-200/80 p-2 flex items-center justify-center mx-auto transition-transform hover:scale-105 duration-200">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Polbeng Food Area" class="w-full h-full object-contain drop-shadow-2xs">
                </div>
            </div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight leading-tight">POLBENG FOOD AREA</h1>
            <div class="flex items-center justify-center gap-1.5 mt-1">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-primary text-[10px] font-extrabold uppercase tracking-wider border border-blue-100">
                    Sistem POS & Pembayaran NFC
                </span>
            </div>
            <p class="text-gray-400 text-xs mt-2">Silakan masuk menggunakan akun Anda</p>
        </div>

        <!-- FORM LOGIN -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            
            <div>
                <label for="email" class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Alamat Email</label>
                <div class="relative">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@kantin.com"
                        class="w-full px-4 py-3 bg-gray-50/80 border border-gray-300 rounded-xl text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary transition duration-150 shadow-2xs">
                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                    </div>
                </div>
                @error('email')
                    <p class="text-rose-600 text-xs font-semibold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-gray-700 uppercase mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required placeholder="••••••••"
                        class="w-full px-4 py-3 bg-gray-50/80 border border-gray-300 rounded-xl text-sm font-medium text-gray-800 placeholder-gray-400 focus:bg-white focus:ring-2 focus:ring-primary focus:border-primary transition duration-150 shadow-2xs">
                    <div class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full flex justify-center items-center space-x-2 py-3 px-4 border border-transparent rounded-xl shadow-md shadow-blue-600/20 text-sm font-bold text-white bg-primary hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition duration-150">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    <span>Masuk ke Akun</span>
                </button>
            </div>
        </form>
        
        <div class="mt-6 pt-4 border-t border-gray-100 text-center text-[11px] text-gray-400">
            Unit Usaha Inkubator Bisnis Politeknik Negeri Bengkalis
        </div>
    </div>
</div>
@endsection
