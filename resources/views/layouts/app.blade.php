<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kantin NFC - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E40AF', // Blue 800
                        secondary: '#3B82F6', // Blue 500
                        accent: '#DBEAFE', // Blue 100
                        bg_light: '#F8FAFC',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <style>
        body { background-color: theme('colors.bg_light'); }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        /* Custom Styling for Cropper Modal */
        .cropper-view-box {
            outline: 2.5px solid #2563eb !important;
            outline-color: #2563eb !important;
            border-radius: 16px;
        }
        .cropper-face {
            background-color: transparent !important;
        }
        .cropper-line, .cropper-point {
            background-color: #2563eb !important;
        }
        .cropper-point.point-se {
            width: 10px !important;
            height: 10px !important;
        }
        .cropper-dashed {
            border-color: rgba(255, 255, 255, 0.6) !important;
        }
        .cropper-modal {
            background-color: rgba(15, 23, 42, 0.75) !important;
        }
    </style>
</head>
<body class="text-gray-800 font-sans antialiased min-h-screen flex flex-col">
    @if(Auth::check())
        @yield('content')
    @else
        <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm rounded" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm rounded" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="bg-white border-t py-4 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Sistem Pembayaran Kantin Berbasis NFC. All rights reserved.
        </footer>
    @endif

    @if(Auth::check() && Auth::user()->role !== 'pedagang')
    <!-- MODAL EDIT PROFIL PENGGUNA (NAVBAR) -->
    <div id="modal-global-profile" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-primary to-blue-600 px-6 py-4 flex justify-between items-center text-white">
                <div class="flex items-center space-x-2">
                    <div class="p-1.5 bg-white/20 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7 7z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-lg">Edit Profil & Foto Akun</h3>
                </div>
                <button type="button" onclick="document.getElementById('modal-global-profile').classList.add('hidden')" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="avatar_cropped">

                <!-- Avatar Preview & Upload -->
                <div class="flex items-center space-x-4 pb-3 border-b border-gray-100">
                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-primary/20 flex items-center justify-center bg-gray-50 shadow-inner shrink-0">
                        @if(Auth::user()->avatar_url)
                            <img id="preview-global-avatar" src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <div id="preview-global-avatar-fallback" class="w-full h-full bg-primary/10 flex items-center justify-center text-primary font-black text-xl">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <label class="cursor-pointer inline-flex items-center space-x-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-bold transition">
                            <svg class="w-3.5 h-3.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Ganti Foto</span>
                            <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" class="hidden" onclick="this.value = null" onchange="previewGlobalPhoto(this)">
                        </label>
                        @if(Auth::user()->avatar)
                            <label class="block mt-1 text-[11px] text-red-600 font-semibold cursor-pointer">
                                <input type="checkbox" name="remove_avatar" value="1" class="rounded border-red-300 text-red-600 mr-1">
                                Hapus foto
                            </label>
                        @endif
                        <div class="text-[11px] text-gray-400 mt-0.5">JPG, PNG, WEBP (maks 3MB)</div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary text-sm font-semibold text-gray-800 shadow-2xs">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary text-sm font-medium text-gray-800 shadow-2xs">
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
                    <button type="button" onclick="document.getElementById('modal-global-profile').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 font-bold text-sm transition">
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
    @endif

    <!-- ========================================== -->
    <!-- MODAL SESUAIKAN & PANGKAS FOTO (CROPPER)   -->
    <!-- ========================================== -->
    <div id="modal-avatar-cropper" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden z-[999] flex items-center justify-center p-3 sm:p-4">
        <div class="bg-white rounded-3xl w-full max-w-xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col max-h-[92vh] animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Header Modal -->
            <div class="bg-gradient-to-r from-primary via-blue-700 to-blue-600 px-6 py-4 flex items-center justify-between text-white shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center shadow-inner">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-base sm:text-lg leading-tight">Sesuaikan Posisi & Pangkas Foto</h3>
                        <p class="text-xs text-blue-100 font-medium">Geser dan perbesar foto agar pas di bingkai profil</p>
                    </div>
                </div>
                <button type="button" onclick="cancelAvatarCropper()" class="text-white/80 hover:text-white p-2 rounded-xl hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Body Cropper Canvas Area -->
            <div class="p-4 sm:p-5 flex-1 overflow-y-auto space-y-4">
                
                <!-- Cropper Viewport Container -->
                <div class="relative w-full h-72 sm:h-80 bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 shadow-inner flex items-center justify-center">
                    <img id="cropper-image-target" src="" alt="Crop Preview" class="max-w-full block" style="display:none;">
                    
                    <!-- Petunjuk Geser -->
                    <div class="absolute top-2.5 left-2.5 bg-black/60 backdrop-blur-md text-white/90 px-3 py-1 rounded-lg text-[11px] font-semibold flex items-center space-x-1.5 pointer-events-none z-10 border border-white/10">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/></svg>
                        <span>Klik & Tahan untuk menggeser gambar</span>
                    </div>
                </div>

                <!-- Toolbar Kontrol: Zoom Slider, Rotate, Reset -->
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 space-y-3">
                    <!-- Zoom Slider -->
                    <div class="flex items-center space-x-3">
                        <button type="button" onclick="cropperZoomStep(-0.1)" title="Perkecil" class="p-2 rounded-xl bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 hover:text-primary transition shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        
                        <div class="flex-1 flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/></svg>
                            <input type="range" id="cropper-zoom-range" min="0.1" max="3" step="0.05" value="1" oninput="cropperZoomTo(this.value)" class="w-full accent-primary h-2 bg-gray-200 rounded-lg cursor-pointer">
                        </div>

                        <button type="button" onclick="cropperZoomStep(0.1)" title="Perbesar" class="p-2 rounded-xl bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 hover:text-primary transition shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>

                    <!-- Quick Action Tools: Rotate, Reset -->
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-200/60 text-xs">
                        <div class="flex items-center space-x-1.5">
                            <button type="button" onclick="cropperRotate(-90)" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold transition flex items-center space-x-1 shadow-2xs" title="Putar Kiri 90 Derajat">
                                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                <span>Putar Kiri</span>
                            </button>
                            <button type="button" onclick="cropperRotate(90)" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold transition flex items-center space-x-1 shadow-2xs" title="Putar Kanan 90 Derajat">
                                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a8 8 0 00-8 8v2m18-10l-6 6m6-6l-6-6"/></svg>
                                <span>Putar Kanan</span>
                            </button>
                        </div>

                        <div class="flex items-center space-x-1.5">
                            <button type="button" onclick="cropperResetPosition()" class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold transition flex items-center space-x-1 shadow-2xs" title="Kembalikan Posisi Semula">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Reset Posisi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between shrink-0">
                <button type="button" onclick="cancelAvatarCropper()" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 font-bold text-sm transition">
                    Batal
                </button>
                <button type="button" onclick="applyAvatarCropped()" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-primary to-blue-600 hover:from-blue-700 hover:to-blue-700 text-white font-bold text-sm shadow-md shadow-blue-500/25 hover:shadow-lg transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Terapkan & Pasang Foto</span>
                </button>
            </div>

        </div>
    </div>

    <!-- CROPPER SCRIPT & UNIVERSAL AVATAR LOGIC -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script>
    let currentCropper = null;
    let currentCropFileInput = null;
    let currentCropPreviewIds = [];
    let currentCropFallbackIds = [];

    function handleAvatarCropSelect(input, previewIds = [], fallbackIds = []) {
        if (!input.files || !input.files[0]) return;
        
        const file = input.files[0];
        if (!file.type.match(/^image\//)) {
            alert('File yang dipilih harus berupa format gambar (JPG, PNG, WEBP).');
            input.value = '';
            return;
        }

        currentCropFileInput = input;
        currentCropPreviewIds = Array.isArray(previewIds) ? previewIds : [previewIds];
        currentCropFallbackIds = Array.isArray(fallbackIds) ? fallbackIds : [fallbackIds];

        const reader = new FileReader();
        reader.onload = function(e) {
            const modal = document.getElementById('modal-avatar-cropper');
            const imgTarget = document.getElementById('cropper-image-target');
            if (!modal || !imgTarget) return;

            if (currentCropper) {
                currentCropper.destroy();
                currentCropper = null;
            }

            imgTarget.src = e.target.result;
            imgTarget.style.display = 'block';
            modal.classList.remove('hidden');

            // Inisialisasi Cropper.js dengan dragMode: 'move' agar langsung bisa digeser!
            currentCropper = new Cropper(imgTarget, {
                aspectRatio: 1, // 1:1 Persegi sempurna untuk foto profil
                viewMode: 1, // Batasi crop box di dalam canvas
                dragMode: 'move', // Menggeser gambar langsung dengan mouse/touch
                autoCropArea: 0.88,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                ready() {
                    const slider = document.getElementById('cropper-zoom-range');
                    if (slider) slider.value = 1;
                },
                zoom(e) {
                    const slider = document.getElementById('cropper-zoom-range');
                    if (slider && e.detail && e.detail.ratio) {
                        slider.value = Math.max(0.1, Math.min(3, e.detail.ratio));
                    }
                }
            });
        };
        reader.readAsDataURL(file);
    }

    function cropperZoomStep(delta) {
        if (currentCropper) {
            currentCropper.zoom(delta);
        }
    }

    function cropperZoomTo(val) {
        if (currentCropper) {
            currentCropper.zoomTo(parseFloat(val));
        }
    }

    function cropperRotate(deg) {
        if (currentCropper) {
            currentCropper.rotate(deg);
        }
    }

    function cropperResetPosition() {
        if (currentCropper) {
            currentCropper.reset();
            const slider = document.getElementById('cropper-zoom-range');
            if (slider) slider.value = 1;
        }
    }

    function cancelAvatarCropper() {
        const modal = document.getElementById('modal-avatar-cropper');
        if (modal) modal.classList.add('hidden');
        if (currentCropper) {
            currentCropper.destroy();
            currentCropper = null;
        }
        if (currentCropFileInput) {
            const form = currentCropFileInput.form;
            const hiddenCrop = form ? form.querySelector('input[name="avatar_cropped"]') : null;
            if (!hiddenCrop || !hiddenCrop.value) {
                currentCropFileInput.value = '';
            }
        }
    }

    function applyAvatarCropped() {
        if (!currentCropper) return;

        const canvas = currentCropper.getCroppedCanvas({
            width: 500,
            height: 500,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!canvas) {
            alert('Gagal memproses pangkas foto. Silakan pilih foto lain.');
            return;
        }

        const croppedBase64 = canvas.toDataURL('image/jpeg', 0.92);

        // Update semua target preview gambar
        currentCropPreviewIds.forEach(id => {
            if (!id) return;
            let img = document.getElementById(id);
            if (!img) {
                const fallback = document.getElementById(id + '-fallback');
                if (fallback && fallback.parentElement) {
                    img = document.createElement('img');
                    img.id = id;
                    img.className = 'w-full h-full object-cover';
                    fallback.parentElement.appendChild(img);
                }
            }
            if (img) {
                img.src = croppedBase64;
                img.classList.remove('hidden');
                img.style.display = 'block';
            }
        });

        // Sembunyikan fallback avatar huruf
        currentCropFallbackIds.forEach(id => {
            if (!id) return;
            const fallback = document.getElementById(id);
            if (fallback) fallback.classList.add('hidden');
        });

        // Selaraskan preview jika ada di halaman yang sama
        ['preview-avatar-tab', 'preview-avatar-modal', 'preview-global-avatar', 'preview-mhs-inline-avatar', 'preview-mhs-modal-avatar', 'preview-admin-user-avatar'].forEach(id => {
            const img = document.getElementById(id);
            if (img && currentCropPreviewIds.includes(id)) {
                img.src = croppedBase64;
                img.classList.remove('hidden');
                img.style.display = 'block';
            }
        });

        // Simpan ke form: avatar_cropped hidden input & File via DataTransfer
        if (currentCropFileInput) {
            const form = currentCropFileInput.form;
            if (form) {
                let hiddenInput = form.querySelector('input[name="avatar_cropped"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'avatar_cropped';
                    form.appendChild(hiddenInput);
                }
                hiddenInput.value = croppedBase64;

                // Batalkan centang hapus foto jika sebelumnya dicentang
                const removeCheckbox = form.querySelector('input[name="remove_avatar"]');
                if (removeCheckbox) removeCheckbox.checked = false;
            }

            try {
                canvas.toBlob(function(blob) {
                    if (blob && window.DataTransfer) {
                        const dt = new DataTransfer();
                        const file = new File([blob], 'avatar_cropped.jpg', { type: 'image/jpeg' });
                        dt.items.add(file);
                        currentCropFileInput.files = dt.files;
                    }
                }, 'image/jpeg', 0.92);
            } catch (e) {
                console.warn('DataTransfer not available, relying on avatar_cropped base64', e);
            }
        }

        // Tutup modal
        const modal = document.getElementById('modal-avatar-cropper');
        if (modal) modal.classList.add('hidden');
        if (currentCropper) {
            currentCropper.destroy();
            currentCropper = null;
        }
    }

    // Fungsi wrapper untuk navbar profile modal
    function previewGlobalPhoto(input) {
        handleAvatarCropSelect(input, ['preview-global-avatar'], ['preview-global-avatar-fallback']);
    }
    </script>

    @stack('scripts')
</body>
</html>
