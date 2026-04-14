<div x-data="presencePage()" x-init="initLocation()">

    @error('system')
    <div class="fixed top-4 left-0 right-0 z-60 px-4 animate-bounce">
        <div class="bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl max-w-lg mx-auto flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <h4 class="font-bold text-lg">Gagal Memproses</h4>
                <p class="text-sm font-medium">{{ $message }}</p>
            </div>
            <button type="button" class="ml-auto bg-white/20 p-1 rounded-full hover:bg-white/30"
                onclick="this.parentElement.parentElement.remove()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </div>
    @enderror

    @if (!$locationGranted)
    <!-- ================= OVERLAY LOADING (Meminta Akses Lokasi) ================= -->
    <div x-show="showLoading" x-transition
        class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white p-8 rounded-2xl shadow-2xl text-center max-w-xs w-full mx-4">
            <div class="flex justify-center mb-5">
                <div class="relative">
                    <div class="w-12 h-12 border-4 border-blue-200 rounded-full"></div>
                    <div
                        class="w-12 h-12 border-4 border-blue-600 rounded-full animate-spin absolute top-0 left-0 border-t-transparent">
                    </div>
                </div>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Melacak Lokasi...</h3>
            <p class="text-gray-500 text-sm">Mohon izinkan akses lokasi.</p>
        </div>
    </div>

    {{-- ================== OVERLAY LOKASI DITOLAK / ERROR ================== --}}
    <div x-show="showDenied" x-cloak x-transition
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white p-6 rounded-2xl shadow-xl text-center max-w-sm w-full mx-4 relative overflow-hidden">

            <img src="{{ asset('static/images/deniedLocation.png') }}" alt="Location Denied"
                class="w-32 mx-auto mb-4 drop-shadow-md"
                onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=Location+Error';">

            <h3 class="text-xl font-bold text-gray-800 leading-tight mb-2">
                <span x-text="isPermDenied ? 'Akses Ditolak' : 'Lokasi Tidak Sesuai'"></span>
            </h3>
            <p class="text-gray-600 text-sm mb-4 px-2 leading-relaxed" x-text="deniedMessage"></p>

            {{-- PANDUAN KHUSUS (Blocked) --}}
            <div x-show="isPermDenied"
                class="bg-blue-50 border border-blue-100 rounded-lg p-3 mb-5 text-left animate-fade-in-down">
                <p class="text-xs font-bold text-blue-800 mb-2 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    Cara Mengaktifkan Izin:
                </p>
                <ol class="list-decimal ml-4 text-[11px] text-blue-700 space-y-1.5 font-medium">
                    <li>Klik ikon <strong>Gembok (🔒)</strong> di bar alamat.</li>
                    <li>Klik menu <strong>Izin / Permissions</strong>.</li>
                    <li>Aktifkan <strong>Lokasi / Location</strong>.</li>
                    <li>Klik tombol <strong>Refresh Halaman</strong>.</li>
                </ol>
            </div>

            {{-- TOMBOL AKSI --}}
            <button @click="retryLocation()"
                class="w-full text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform active:scale-95 transition flex items-center justify-center gap-2"
                :class="isPermDenied ? 'bg-blue-600 hover:bg-blue-700' : 'bg-[#5065A4] hover:opacity-90'">
                <span x-text="isPermDenied ? 'Refresh Halaman' : 'Cek Lokasi Lagi'"></span>
            </button>
        </div>
    </div>
    @else
    {{-- ================== FORM MUNCUL KALAU DALAM RADIUS ================== --}}

    <x-peserta.banner />
    @if (!$isClosedPresence)
    <div class="mb-4">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600 text-sm font-semibold">Nama Lengkap</span>
        </div>

        <input type="text" wire:model="nama" placeholder="Masukkan Nama Lengkap Anda"
            class="w-full border-2 border-gray-300 rounded-lg p-3 text-sm font-semibold focus:border-blue-500 focus:outline-none transition-colors @error('nama') @enderror">

        @error('nama')
        <p class="text-red-500 text-xs mt-1 font-bold">
            {{ $message }}
        </p>
        @enderror
    </div>

    <div class="mb-4">
        <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600 text-sm font-semibold">Asal Instansi</span>
        </div>

        <input type="text" wire:model="unit_kerja" placeholder="Masukkan Asal Instansi Anda"
            class="w-full border-2 border-gray-300 rounded-lg p-3 text-sm font-semibold focus:border-blue-500 focus:outline-none transition-colors @error('unit_kerja') @enderror">

        @error('unit_kerja')
        <p class="text-red-500 text-xs mt-1 font-bold">
            {{ $message }}
        </p>
        @enderror
    </div>

    {{-- 3. Upload Selfie Section --}}
    <div class="mb-4">

        {{-- Pop-up Modal Validasi Wajah --}}
        <div x-show="showFaceModal" x-cloak x-transition
            class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 backdrop-blur-sm">
            <div class="bg-white p-6 rounded-2xl shadow-2xl text-center max-w-xs w-full mx-4">

                {{-- Icon berdasarkan tipe --}}
                <div class="mb-4">
                    <template x-if="faceModalType === 'loading'">
                        <div class="flex justify-center">
                            <div class="w-14 h-14 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin">
                            </div>
                        </div>
                    </template>
                    <template x-if="faceModalType === 'success'">
                        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </template>
                    <template x-if="faceModalType === 'not_detected'">
                        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </template>
                    <template x-if="faceModalType === 'too_far'">
                        <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01M12 4a8 8 0 100 16A8 8 0 0012 4z" />
                            </svg>
                        </div>
                    </template>
                </div>

                <h3 class="font-bold text-gray-800 text-base mb-1" x-text="faceModalTitle"></h3>
                <p class="text-gray-500 text-sm mb-5 leading-relaxed" x-text="faceModalMessage"></p>

                <template x-if="faceModalType !== 'loading'">
                    <button @click="closeFaceModal()"
                        :class="faceModalType === 'success' ? 'bg-green-600 hover:bg-green-700' : (faceModalType === 'too_far' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-red-600 hover:bg-red-700')"
                        class="w-full text-white font-bold py-2.5 rounded-xl transition">
                        <span x-text="faceModalType === 'success' ? 'Lanjut ✓' : 'Coba Lagi'"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Modal Kamera Live --}}
        <div x-show="showCameraModal" x-cloak x-transition
            class="fixed inset-0 bg-black/80 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-sm mx-4">
                <div class="bg-[#5065A4] px-4 py-3 flex items-center justify-between">
                    <span class="text-white font-bold text-sm">📸 Ambil Foto Selfie</span>
                    <button @click="closeCameraModal()" class="text-white/70 hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="relative bg-black">
                    <video id="cameraVideo" autoplay playsinline muted class="w-full block"
                        style="max-height: 320px; object-fit: cover;"></video>
                    {{-- Overlay panduan frame wajah --}}
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-40 h-48 border-2 border-white/60 rounded-[50%] opacity-60"></div>
                    </div>
                    <canvas id="cameraCanvas" class="hidden"></canvas>
                </div>
                <div class="p-4">
                    <p class="text-gray-500 text-xs text-center mb-3">Pastikan wajah berada di dalam lingkaran</p>
                    <button @click="capturePhoto()" :disabled="isCameraCapturing"
                        class="w-full bg-[#5065A4] text-white font-bold py-3 rounded-xl disabled:opacity-60 disabled:cursor-wait flex items-center justify-center gap-2 transition">
                        <template x-if="!isCameraCapturing">
                            <span>📸 Ambil Foto & Verifikasi</span>
                        </template>
                        <template x-if="isCameraCapturing">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                Memproses...
                            </span>
                        </template>
                    </button>
                </div>
            </div>
        </div>

        {{-- Trigger Button / Preview --}}
        <div @click="openCamera()"
            class="relative cursor-pointer bg-white border-2 border-dashed border-blue-300 rounded-xl block text-center hover:bg-blue-50 transition overflow-hidden group">

            <div x-show="isUploading" style="display:none;"
                class="absolute inset-0 bg-white/90 z-20 flex flex-col items-center justify-center backdrop-blur-sm">
                <div class="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-2">
                </div>
                <span class="text-xs font-bold text-blue-600">Upload <span x-text="progress + '%'"></span></span>
            </div>

            @if ($photo)
            <div class="relative">
                <img src="{{ $photo->temporaryUrl() }}" class="w-full object-cover block rounded-lg" alt="Preview">
                <div
                    class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <span class="text-white text-sm font-bold bg-black/50 px-3 py-1 rounded-full">Ganti Foto</span>
                </div>
            </div>
            @else
            <div class="min-h-[150px] flex flex-col items-center justify-center p-6">
                <img src="{{ asset('static/images/imgUpload.svg') }}" class="mb-3 w-16 h-16 opacity-80" alt="Upload" />
                <p class="text-gray-800 text-sm font-bold">Ambil Foto Selfie Untuk Kehadiran</p>
                <p class="text-gray-500 text-xs mt-1">Ketuk di sini untuk membuka kamera</p>
            </div>
            @endif
        </div>

        @error('photo')
        <p class="text-red-500 text-sm mt-2 text-center font-semibold">{{ $message }}</p>
        @enderror
    </div>

    <!-- Checkbox Agreement -->
    <div class="mb-4">
        <label class="flex items-start gap-2 cursor-pointer p-2 rounded hover:bg-gray-50">
            <input type="checkbox" class="mt-1 w-4 h-4 text-blue-600 rounded focus:ring-blue-500"
                wire:model.live="agreement">
            <div class="flex flex-col text-gray-600 text-xs leading-relaxed">
                <span class="font-semibold text-gray-800">Saya menyatakan data di atas benar.</span>
                <span>
                    Data ini akan digunakan untuk undian doorprize.
                </span>
            </div>
        </label>
        @error('agreement')
        <span class="text-red-500 text-xs ml-6">{{ $message }}</span>
        @enderror
    </div>

    <button wire:click="klaimKupon" wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait" {{
        (!$agreement || !$photo) ? 'disabled' : '' }}
        class="w-full bg-[#5065A4] text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl transform transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">

        <span wire:loading.remove wire:target="klaimKupon">Klaim Kupon</span>

        <span wire:loading wire:target="klaimKupon" class="flex items-center gap-1">
            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </span>
    </button>
    @else
    <div class="mb-4">
        <p
            class="text-red-600 font-semibold text-center bg-red-50 p-2 rounded border border-red-200 text-sm flex items-center justify-center gap-2">
            Maaf Pendaftaran kupon doorprize sudah ditutup!
        </p>
    </div>
    @endif
    @endif
</div>


{{-- ================= SCRIPT ALPINE.JS (FIXED) ================= --}}
<script>
    function presencePage() {
        return {
            // State Lokasi
            showLoading: true,
            showDenied: false,
            isPermDenied: false,
            deniedMessage: '',

            // State Upload
            isUploading: false,
            progress: 0,

            // State Kamera
            showCameraModal: false,
            isCameraCapturing: false,
            cameraStream: null,

            // State Pop-up Face Modal
            showFaceModal: false,
            faceModalType: 'loading', // 'loading' | 'success' | 'not_detected' | 'too_far'
            faceModalTitle: '',
            faceModalMessage: '',
            _pendingBlob: null, // simpan sementara blob hasil foto jika sukses

            initLocation() {
                window.addEventListener('location-radius-error', () => {
                    this.showDeniedState('Kamu berada di luar radius lokasi acara.');
                });
                window.addEventListener('permission-blocked', () => {
                    this.handlePermissionDenied();
                });
                window.addEventListener('location-system-error', () => {
                    this.showDeniedState('Gagal mendapatkan posisi. Pastikan GPS aktif.');
                });

                if (navigator.permissions && navigator.permissions.query) {
                    navigator.permissions.query({ name: 'geolocation' }).then((result) => {
                        if (result.state === 'denied') {
                            this.handlePermissionDenied();
                        } else {
                            this.requestLocation();
                        }
                    });
                } else {
                    this.requestLocation();
                }
            },

            // --- HELPER LOKASI ---
            showDeniedState(msg) {
                this.showLoading = false;
                this.showDenied = true;
                this.isPermDenied = false;
                this.deniedMessage = msg;
            },

            retryLocation() {
                if (this.isPermDenied) window.location.reload();
                else this.requestLocation();
            },

            requestLocation() {
                this.showDenied = false;
                this.showLoading = true;
                this.isPermDenied = false;

                if ("geolocation" in navigator) {
                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            @this.call('checkLocation', pos.coords.latitude, pos.coords.longitude);
                        },
                        (error) => {
                            console.error('Geolocation error:', error);
                            if (error.code === 1) {
                                this.handlePermissionDenied();
                                @this.call('locationFailed', 1);
                            } else {
                                @this.call('locationFailed', error.code);
                            }
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                } else {
                    this.showDeniedState("Browser tidak mendukung GPS");
                }
            },

            handlePermissionDenied() {
                this.showLoading = false;
                this.showDenied = true;
                this.isPermDenied = true;
                this.deniedMessage = "Akses lokasi diblokir browser. Anda perlu mengizinkannya secara manual.";
            },

            // --- KAMERA ---
            async openCamera() {
                this.showCameraModal = true;
                // Preload model face-api saat kamera dibuka
                if (typeof FaceRecognition !== 'undefined') {
                    FaceRecognition.loadModels().catch(console.error);
                }
                await this.$nextTick();
                const video = document.getElementById('cameraVideo');
                try {
                    this.cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
                        audio: false
                    });
                    video.srcObject = this.cameraStream;
                } catch (err) {
                    console.error('Kamera error:', err);
                    this.showCameraModal = false;
                    this.showFacePopup('not_detected', 'Kamera Tidak Bisa Dibuka', 'Pastikan izin kamera sudah diberikan pada browser.');
                }
            },

            closeCameraModal() {
                this.showCameraModal = false;
                this.stopCamera();
            },

            stopCamera() {
                if (this.cameraStream) {
                    this.cameraStream.getTracks().forEach(t => t.stop());
                    this.cameraStream = null;
                }
            },

            async capturePhoto() {
                if (this.isCameraCapturing) return;
                this.isCameraCapturing = true;

                const video = document.getElementById('cameraVideo');
                const canvas = document.getElementById('cameraCanvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0);

                // Tampilkan loading modal face detection
                this.showFacePopup('loading', 'Mendeteksi Wajah...', 'Harap tunggu, sistem sedang memverifikasi wajah Anda.');

                try {
                    // Pastikan model sudah loaded
                    const loaded = await FaceRecognition.loadModels();
                    if (!loaded) throw new Error('Model gagal dimuat');

                    const detection = await FaceRecognition.detectFace(canvas);
                    const result = FaceRecognition.validate(detection);

                    if (result.valid) {
                        // Simpan blob dulu, upload setelah user konfirmasi
                        canvas.toBlob((blob) => {
                            this._pendingBlob = blob;
                            this.isCameraCapturing = false;
                            this.showFacePopup('success', 'Verifikasi Berhasil! ✅', 'Wajah berhasil terdeteksi. Klik Lanjut untuk menggunakan foto ini.');
                        }, 'image/jpeg', 0.7);
                    } else {
                        this.isCameraCapturing = false;
                        this.showFacePopup(result.type, result.type === 'too_far' ? '⚠️ Wajah Terlalu Jauh' : '❌ Wajah Tidak Terdeteksi', result.message);
                    }
                } catch (err) {
                    console.error('Face detection error:', err);
                    this.isCameraCapturing = false;
                    this.showFacePopup('not_detected', 'Terjadi Kesalahan', 'Gagal memproses deteksi wajah. Silakan coba lagi.');
                }
            },

            // --- FACE MODAL ---
            showFacePopup(type, title, message) {
                this.faceModalType = type;
                this.faceModalTitle = title;
                this.faceModalMessage = message;
                this.showFaceModal = true;
            },

            closeFaceModal() {
                this.showFaceModal = false;
                if (this.faceModalType === 'success' && this._pendingBlob) {
                    // Upload ke Livewire dan tutup kamera
                    this.closeCameraModal();
                    this.uploadBlob(this._pendingBlob);
                    this._pendingBlob = null;
                }
                // Jika gagal, biarkan kamera tetap terbuka
            },

            uploadBlob(blob) {
                this.isUploading = true;
                this.progress = 0;
                const fileToUpload = new File([blob], 'selfie_compressed.jpg', {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });

                @this.upload('photo', fileToUpload,
                    () => {
                        this.isUploading = false;
                        this.progress = 100;
                    },
                    () => {
                        this.isUploading = false;
                        this.showFacePopup('not_detected', 'Upload Gagal', 'Gagal mengupload gambar. Silakan coba lagi.');
                    },
                    (event) => {
                        this.progress = event.detail.progress;
                    }
                );
            }
        }
    }
</script>