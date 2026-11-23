@extends('layouts.app')
@section('title', 'Detail Pengguna')

@section('content')
<div class="min-h-screen bg-slate-100/70 p-4 sm:p-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.dashboard') }}" class="text-primary hover:underline font-bold text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard Admin
            </a>
        </div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-primary to-blue-600 p-6 text-white flex justify-between items-center">
        <div class="flex items-center gap-4">
            <div class="relative shrink-0">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-white/40 shadow-md">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-white/20 border-2 border-white/30 flex items-center justify-center font-black text-2xl text-white shadow-inner">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-extrabold">{{ $user->name }}</h1>
                    <button type="button" onclick="document.getElementById('modal-edit-user-admin').classList.remove('hidden')" class="px-2.5 py-1 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-lg border border-white/30 transition flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        <span>Edit Profil & Foto</span>
                    </button>
                </div>
                <p class="opacity-80 text-sm mt-1 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    {{ $user->email }}
                </p>
            </div>
        </div>
        <div class="text-right">
            <span class="px-4 py-1.5 rounded-full text-sm font-bold uppercase tracking-wider
                {{ $user->role == 'pedagang' ? 'bg-orange-400 text-white' : ($user->role == 'mahasiswa' ? 'bg-blue-400 text-white' : 'bg-purple-400 text-white') }}">
                {{ $user->role }}
            </span>
            <div class="mt-2 text-xs font-bold opacity-80">Bergabung: {{ $user->created_at->format('d M Y') }}</div>
        </div>
    </div>
    
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Info Dasar -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 mb-3 border-b pb-2">Informasi Akun</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Status Akun:</span>
                    <span class="font-bold {{ $user->status == 'approved' ? 'text-green-600' : 'text-red-600' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
                
                @if($user->role == 'pedagang')
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Jatuh Tempo Sewa Lapak:</span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-800 {{ $user->rent_due_date && \Carbon\Carbon::parse($user->rent_due_date)->isPast() ? 'text-red-500' : '' }}">
                                {{ $user->rent_due_date ? \Carbon\Carbon::parse($user->rent_due_date)->format('d M Y') : 'Belum diatur' }}
                            </span>
                            <button onclick="document.getElementById('modal-rent').classList.remove('hidden')" class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 px-2 py-1 rounded">Ubah</button>
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Penjualan:</span>
                        <span class="font-bold text-primary text-lg">Rp {{ number_format($totalSalesOrSpent, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if($user->role == 'mahasiswa')
                    <div class="flex justify-between">
                        <span class="text-gray-500">NIM Mahasiswa:</span>
                        <span class="font-mono font-bold text-primary bg-blue-50 px-2 py-0.5 rounded">{{ $user->nim ?? 'Belum diatur' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Pengeluaran:</span>
                        <span class="font-bold text-red-500">Rp {{ number_format($totalSalesOrSpent, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-4">
                        <span class="text-gray-500 block mb-2 font-bold">Kartu NFC Dimiliki:</span>
                        @forelse($cards as $card)
                            <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-200 text-xs flex justify-between items-center mb-1.5 hover:bg-gray-100/70 transition">
                                <div>
                                    <span class="font-mono font-bold">{{ $card->card_uid }}</span> 
                                    <span class="{{ $card->status == 'active' ? 'text-green-600' : 'text-red-500' }} ml-1">({{ ucfirst($card->status) }})</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-primary">Saldo: Rp {{ number_format($card->balance, 0, ',', '.') }}</span>
                                    <form action="{{ route('admin.cards.destroy', $card->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin MENGHAPUS kartu NFC {{ $card->card_uid }} ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 p-1.5 rounded-lg transition" title="Hapus Kartu">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-gray-400 italic">Belum ada kartu NFC.</div>
                        @endforelse
                    </div>
                @endif
                
                @if($user->role == 'wali')
                    <div class="mt-4">
                        <span class="text-gray-500 block mb-2 font-bold">Tanggungan Mahasiswa:</span>
                        @forelse($children as $child)
                            <div class="bg-gray-50 p-2 rounded border text-xs flex justify-between items-center mb-1 hover:bg-blue-50 transition cursor-pointer" onclick="window.location='{{ route('admin.users.show', $child->id) }}'">
                                <span class="font-bold">{{ $child->name }}</span>
                                <span class="text-blue-500 underline">Lihat Detail</span>
                            </div>
                        @empty
                            <div class="text-xs text-gray-400 italic">Belum menautkan akun mahasiswa manapun.</div>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Transaksi / Aktivitas Terbaru -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Aktivitas Transaksi Terbaru
    </h3>
    
    <div class="overflow-x-auto max-h-96">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 sticky top-0 text-gray-600">
                <tr>
                    <th class="p-3">Waktu</th>
                    <th class="p-3">Keterangan</th>
                    <th class="p-3 text-right">Nominal</th>
                    <th class="p-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $trx)
                <tr class="hover:bg-gray-50">
                    <td class="p-3">
                        <div class="font-bold text-gray-700">{{ $trx->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $trx->created_at->format('H:i:s') }}</div>
                    </td>
                    <td class="p-3 text-gray-600">
                        @if($trx->type == 'topup')
                            Top-Up via {{ ucfirst($trx->payment_method) }} <br>
                            <span class="text-xs">Kartu: {{ $trx->card->card_uid ?? '-' }}</span>
                        @else
                            @if($user->role == 'pedagang')
                                Pembelian dari: {{ $trx->card->user->name ?? 'Pelanggan Umum' }}
                            @else
                                Pembelian di: {{ $trx->pedagang->name ?? 'Kantin' }}
                            @endif
                        @endif
                    </td>
                    <td class="p-3 text-right font-bold {{ $trx->type == 'topup' ? 'text-green-600' : ($user->role == 'pedagang' ? 'text-green-600' : 'text-red-500') }}">
                        {{ $trx->type == 'topup' ? '+' : ($user->role == 'pedagang' ? '+' : '-') }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </td>
                    <td class="p-3 text-center">
                        @if($trx->status == 'success')
                            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded">Sukses</span>
                        @else
                            <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded">Gagal</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-6 text-center text-gray-500 italic">Tidak ada catatan transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Edit Tempo Sewa Lapak -->
<div id="modal-rent" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 w-full max-w-sm shadow-xl">
        <h3 class="font-bold text-lg mb-4">Ubah Jatuh Tempo Sewa</h3>
        <form action="{{ route('admin.users.rent', $user->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                <input type="date" name="rent_due_date" value="{{ $user->rent_due_date }}" required class="w-full border-gray-300 rounded-md focus:ring-primary px-3 py-2 border">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-rent').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>
<!-- Modal Edit Pengguna (Admin) -->
<div id="modal-edit-user-admin" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex justify-between items-center mb-4 border-b pb-3">
            <h3 class="font-bold text-lg text-gray-800">Edit Data Pengguna ({{ ucfirst($user->role) }})</h3>
            <button type="button" onclick="document.getElementById('modal-edit-user-admin').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="avatar_cropped">

            <!-- Avatar Upload & Preview -->
            <div class="flex items-center space-x-4 pb-3 border-b border-gray-100">
                <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-primary/20 flex items-center justify-center bg-gray-50 shadow-inner shrink-0">
                    @if($user->avatar_url)
                        <img id="preview-admin-user-avatar" src="{{ $user->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <div id="preview-admin-user-avatar-fallback" class="w-full h-full bg-primary/10 flex items-center justify-center text-primary font-black text-xl">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <label class="cursor-pointer inline-flex items-center space-x-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-bold transition">
                        <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Unggah Foto Profil</span>
                        <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden" onclick="this.value = null" onchange="previewAdminUserPhoto(this)">
                    </label>
                    @if($user->avatar)
                        <label class="block mt-1 text-[11px] text-red-600 font-semibold cursor-pointer">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-red-300 text-red-600 mr-1">
                            Hapus foto saat ini
                        </label>
                    @endif
                    <div class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, WEBP (maks 3MB)</div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap / Nama Stan</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border-gray-300 rounded-lg px-3 py-2 border text-sm font-semibold">
            </div>
            @if($user->role === 'mahasiswa')
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">NIM (Nomor Induk Mahasiswa)</label>
                <input type="text" name="nim" value="{{ old('nim', $user->nim) }}" placeholder="Contoh: 2101092001" class="w-full border-gray-300 rounded-lg px-3 py-2 border text-sm font-semibold font-mono">
            </div>
            @endif
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border-gray-300 rounded-lg px-3 py-2 border text-sm">
            </div>
            <div class="border-t pt-3">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reset Password (Opsional)</label>
                <input type="password" name="password" autocomplete="new-password" placeholder="Kosongkan jika tidak ingin mereset password" class="w-full border-gray-300 rounded-lg px-3 py-2 border text-sm">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-edit-user-admin').classList.add('hidden')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-bold text-sm">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-blue-700 text-white rounded-lg font-bold text-sm shadow">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewAdminUserPhoto(input) {
    if (typeof handleAvatarCropSelect === 'function') {
        handleAvatarCropSelect(input, ['preview-admin-user-avatar'], ['preview-admin-user-avatar-fallback']);
        return;
    }
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let img = document.getElementById('preview-admin-user-avatar');
            const fallback = document.getElementById('preview-admin-user-avatar-fallback');
            if (!img && fallback && fallback.parentElement) {
                img = document.createElement('img');
                img.id = 'preview-admin-user-avatar';
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
</script>
@endpush
