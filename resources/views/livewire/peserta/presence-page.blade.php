<div x-data="presencePage(@js($pesertaTerdaftar))" x-init="initLocation()">

    @error('system')
        <div class="fixed top-4 left-0 right-0 z-60 px-4 animate-bounce">
            <div class="bg-red-500 text-white px-6 py-4 rounded-xl shadow-2xl max-w-lg mx-auto flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        <div wire:key="overlay-loading" x-show="showLoading" x-transition
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

        <div wire:key="overlay-denied" x-show="showDenied" x-cloak x-transition
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 backdrop-blur-sm">
            <div class="bg-white p-6 rounded-3xl shadow-xl text-center max-w-sm w-full mx-4 relative overflow-hidden">
                <img src="{{ asset('static/images/deniedLocation.png') }}" alt="Location Denied"
                    class="w-32 mx-auto mb-4 drop-shadow-md"
                    onerror="this.onerror=null; this.src='https://placehold.co/400x400?text=Location+Error';">

                <h3 class="text-xl font-bold text-gray-800 leading-tight mb-2">
                    <span x-text="isPermDenied ? 'Akses Ditolak' : 'Lokasi Tidak Sesuai'"></span>
                </h3>
                <p class="text-gray-600 text-sm mb-4 px-2 leading-relaxed" x-text="deniedMessage"></p>

                <div x-show="isPermDenied"
                    class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5 text-left animate-fade-in-down">
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

                <button @click="retryLocation()"
                    class="w-full text-white font-bold py-3.5 px-4 rounded-xl shadow hover:shadow-lg transform active:scale-95 transition-all flex items-center justify-center gap-2"
                    :class="isPermDenied ? 'bg-blue-600 hover:bg-blue-700' : 'bg-[#5065A4] hover:opacity-90'">
                    <span x-text="isPermDenied ? 'Refresh Halaman' : 'Cek Lokasi Lagi'"></span>
                </button>
            </div>
        </div>
    @else
        <x-peserta.banner />

        @if (!$isClosedPresence)

            <div class="mb-5 mt-2">

                <div x-show="showFaceModal" x-cloak x-transition.opacity
                    class="fixed inset-0 bg-black/70 flex items-center justify-center z-60 backdrop-blur-sm p-4">
                    <div
                        class="bg-white p-6 rounded-3xl shadow-2xl text-center max-w-xs w-full transform transition-all scale-100">
                        <div class="mb-5">
                            <template x-if="faceModalType === 'loading'">
                                <div class="flex justify-center">
                                    <div
                                        class="w-16 h-16 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin">
                                    </div>
                                </div>
                            </template>
                            <template x-if="faceModalType === 'success'">
                                <div
                                    class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto scale-110">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </template>
                            <template x-if="faceModalType === 'not_detected'">
                                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </div>
                            </template>
                            <template x-if="faceModalType === 'too_far'">
                                <div
                                    class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 9v2m0 4h.01M12 4a8 8 0 100 16A8 8 0 0012 4z" />
                                    </svg>
                                </div>
                            </template>
                        </div>

                        <h3 class="font-bold text-gray-900 text-lg mb-2" x-text="faceModalTitle"></h3>
                        <p class="text-gray-500 text-sm mb-6 leading-relaxed px-2" x-text="faceModalMessage"></p>

                        <template x-if="faceModalType !== 'loading'">
                            <button @click="closeFaceModal()"
                                :class="faceModalType === 'success' ? 'bg-green-600 hover:bg-green-700 shadow-green-200' : (
                                    faceModalType === 'too_far' ?
                                    'bg-yellow-500 hover:bg-yellow-600 shadow-yellow-200' :
                                    'bg-red-600 hover:bg-red-700 shadow-red-200')"
                                class="w-full text-white font-bold py-3.5 rounded-xl shadow-lg transition-all active:scale-95 cursor-pointer">
                                <span x-text="faceModalType === 'success' ? 'Lanjut' : 'Coba Lagi'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div x-show="showCameraModal" x-cloak x-transition.opacity
                    class="fixed inset-0 bg-gray-900/95 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
                    <div
                        class="bg-white rounded-3xl shadow-2xl overflow-hidden w-full max-w-lg flex flex-col transform transition-all">
                        <div class="bg-[#5065A4] px-5 py-4 flex items-center justify-between">
                            <span class="text-white font-bold text-sm flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Ambil Foto Selfie
                            </span>
                            <button @click="closeCameraModal()"
                                class="text-white/70 hover:text-white bg-white/10 rounded-full p-1.5 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div
                            class="relative bg-black flex-1 aspect-3/4 sm:aspect-video flex items-center justify-center overflow-hidden">
                            <video id="cameraVideo" autoplay playsinline muted
                                class="absolute inset-0 w-full h-full object-cover -scale-x-100"></video>
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                                <div class="w-48 h-64 border-2 rounded-[100px] shadow-[0_0_0_999px_rgba(0,0,0,0.65)] relative transition-colors duration-300"
                                    :class="liveStatus === 'success' ? 'border-green-500' : 'border-white/60'">
                                    <div class="absolute inset-0 border-4 rounded-[100px] animate-pulse"
                                        :class="liveStatus === 'success' ? 'border-green-400/50' : 'border-white/20'">
                                    </div>
                                </div>
                            </div>
                            <canvas id="cameraCanvas" class="hidden"></canvas>
                        </div>

                        <div class="p-5 bg-gray-50">
                            <div class="text-center mb-4 transition-colors duration-300 font-bold"
                                :class="{
                                    'text-green-600': liveStatus === 'success',
                                    'text-yellow-600': liveStatus === 'too_far',
                                    'text-red-600': liveStatus === 'not_detected' || liveStatus === 'searching'
                                }">
                                <p class="text-sm" x-text="liveMessage"></p>
                                <p class="text-xs text-gray-500 mt-1" x-show="faceRatio > 0">Nilai Wajah: <span
                                        x-text="faceRatio"></span></p>
                            </div>

                            <button @click="capturePhoto()" :disabled="liveStatus !== 'success' || isCameraCapturing"
                                class="w-full text-white font-bold py-4 rounded-xl shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 transition-all active:scale-95"
                                :class="liveStatus === 'success' ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400'">
                                <template x-if="!isCameraCapturing">
                                    <span>Ambil Foto</span>
                                </template>
                                <template x-if="isCameraCapturing">
                                    <span class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4" />
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

                <div @click="openCamera()"
                    class="relative cursor-pointer bg-white border-2 border-dashed border-blue-300 rounded-2xl block text-center hover:bg-blue-50 transition-colors overflow-hidden">

                    <div x-show="isUploading" style="display:none;"
                        class="absolute inset-0 bg-white/95 z-20 flex flex-col items-center justify-center backdrop-blur-sm rounded-2xl">
                        <div
                            class="w-12 h-12 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin mb-3">
                        </div>
                        <span class="text-sm font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-full">Upload <span
                                x-text="progress + '%'"></span></span>
                    </div>

                    @if ($photo)
                        <div class="relative group">
                            <img src="{{ $photo->temporaryUrl() }}" class="w-full object-cover block h-[200px]"
                                alt="Preview">
                            <div class="absolute bottom-3 left-0 right-0 flex justify-center">
                                <span
                                    class="bg-black/70 backdrop-blur-sm text-white text-xs font-bold px-4 py-2 rounded-full shadow-lg flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Ulangi Foto
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="min-h-40 flex flex-col items-center justify-center p-6">
                            <div class="bg-blue-50 p-4 rounded-full mb-3 group-hover:scale-110 transition-transform">
                                <img src="{{ asset('static/images/imgUpload.svg') }}" class="w-10 h-10 opacity-80"
                                    alt="Upload" />
                            </div>
                            <p class="text-gray-800 text-sm font-bold">Ambil Foto Selfie Kehadiran</p>
                            <p class="text-gray-500 text-xs mt-1.5 font-medium">Ketuk di sini untuk buka kamera</p>
                        </div>
                    @endif
                </div>

                @error('photo')
                    <p class="text-red-500 text-xs mt-2 font-bold px-1 text-center">{{ $message }}</p>
                @enderror
            </div>

            <div x-show="$wire.nama" x-cloak x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="mb-5 bg-white border-2 border-green-100 shadow-md rounded-2xl p-5 relative overflow-hidden">

                <div class="absolute top-0 left-0 w-1.5 h-full bg-green-500"></div>

                <div class="flex items-center gap-3 mb-4 border-b border-gray-100 pb-3">
                    <div class="bg-green-100 p-2 rounded-full text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-base">Wajah Dikenali</h3>
                        <p class="text-xs text-gray-500 font-medium">Data di bawah akan disubmit</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Nama Peserta</p>
                        <p class="text-gray-900 font-bold text-[15px]" x-text="$wire.nama"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">NIP</p>
                            <p class="text-gray-900 font-bold text-[15px]" x-text="$wire.nip || '-'"></p>
                        </div>
                        <div>
                            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Instansi</p>
                            <p class="text-gray-900 font-bold text-[15px]" x-text="$wire.unit_kerja"></p>
                        </div>
                    </div>
                </div>
            </div>

            <div wire:key="checkbox-agreement" class="mb-5 bg-gray-50 border border-gray-200 p-4 rounded-xl"
                x-show="$wire.nama" x-transition>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox"
                        class="mt-0.5 w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition-colors"
                        wire:model.live="agreement">
                    <div class="flex flex-col text-gray-600 text-xs leading-relaxed">
                        <span class="font-bold text-gray-800 text-sm mb-0.5">Konfirmasi Data Benar</span>
                        <span>Saya menyatakan data dan foto di atas adalah benar. Data ini akan digunakan untuk undian
                            doorprize.</span>
                    </div>
                </label>
                @error('agreement')
                    <span class="text-red-500 text-xs font-bold block mt-2 ml-8">{{ $message }}</span>
                @enderror
            </div>

            <button wire:click="klaimKupon" wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-wait"
                {{ !$agreement || !$photo ? 'disabled' : '' }}
                class="w-full bg-[#5065A4] text-white font-bold py-4 rounded-xl shadow-lg hover:shadow-xl transform transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="klaimKupon" class="text-base">Klaim Kupon Doorprize</span>
                <span wire:loading wire:target="klaimKupon" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Memproses...
                </span>
            </button>
        @else
            <div class="mb-4 mt-2">
                <p
                    class="text-red-600 font-bold text-center bg-red-50 py-4 px-3 rounded-xl border border-red-200 text-sm flex flex-col items-center justify-center gap-2 shadow-sm">
                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Maaf, pendaftaran kupon doorprize sudah ditutup!
                </p>
            </div>
        @endif
    @endif
</div>

<script>
    function presencePage(pesertaDariBackend) {
        return {
            showLoading: true,
            showDenied: false,
            isPermDenied: false,
            deniedMessage: '',
            isUploading: false,
            progress: 0,
            showCameraModal: false,
            isCameraCapturing: false,
            cameraStream: null,

            liveStatus: 'loading',
            liveMessage: '',
            faceRatio: 0,
            detectionInterval: null,

            showFaceModal: false,
            faceModalType: 'loading',
            faceModalTitle: '',
            faceModalMessage: '',

            faceMatcher: null,
            participantsData: pesertaDariBackend || [],

            async initLocation() {
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
                    navigator.permissions.query({
                        name: 'geolocation'
                    }).then((result) => {
                        if (result.state === 'denied') {
                            this.handlePermissionDenied();
                        } else {
                            this.requestLocation();
                        }
                    });
                } else {
                    this.requestLocation();
                }

                if (typeof faceapi !== 'undefined') {
                    const MODEL_URL = '/models';
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                    ]).then(() => {
                        console.log('Semua AI Model siap!');
                        this.initFaceMatcher();
                    }).catch(console.error);
                }
            },

            initFaceMatcher() {
                console.log("Memproses data wajah dari DB. Jumlah:", this.participantsData.length);

                if (this.participantsData.length === 0) {
                    console.warn("Data peserta kosong. Recognition dinonaktifkan.");
                    return;
                }

                try {
                    const labeledDescriptors = [];

                    for (const p of this.participantsData) {
                        let arr = p.face_descriptor;

                        if (!arr) continue;

                        if (typeof arr === 'string') {
                            try {
                                arr = JSON.parse(arr);
                            } catch (e) {
                                continue;
                            }
                        }

                        if (!Array.isArray(arr)) {
                            arr = Object.values(arr);
                        }

                        if (arr.length > 0) {
                            const descriptorArray = new Float32Array(arr);

                            // PERBAIKAN: Menyelipkan NIP ke dalam label
                            const nipSafe = p.nip || '';
                            labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(
                                `${p.nama}|${p.unit_kerja}|${nipSafe}`, [
                                    descriptorArray
                                ]));
                        }
                    }

                    if (labeledDescriptors.length > 0) {
                        this.faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.55);
                        console.log('Face Matcher berhasil diinisialisasi!');
                    } else {
                        console.warn("Tidak ada data descriptor yang valid untuk diproses.");
                    }

                } catch (error) {
                    console.error("Gagal membuat Face Matcher:", error);
                }
            },

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
                            if (error.code === 1) {
                                this.handlePermissionDenied();
                                @this.call('locationFailed', 1);
                            } else {
                                @this.call('locationFailed', error.code);
                            }
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
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

            async openCamera() {
                this.showCameraModal = true;
                this.liveStatus = 'searching';
                this.liveMessage = 'Menyiapkan kamera...';

                await this.$nextTick();
                const video = document.getElementById('cameraVideo');

                try {
                    this.cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: {
                                ideal: 640
                            },
                            height: {
                                ideal: 480
                            }
                        },
                        audio: false
                    });
                    video.srcObject = this.cameraStream;

                    video.onloadedmetadata = () => {
                        this.startLiveValidation(video);
                    };
                } catch (err) {
                    this.showCameraModal = false;
                    this.showFacePopup('not_detected', 'Kamera Error', 'Pastikan izin kamera sudah diberikan.');
                }
            },

            startLiveValidation(video) {
                // Hapus setInterval lama jika ada
                if (this.detectionInterval) clearTimeout(this.detectionInterval);

                // Buat fungsi async yang akan memanggil dirinya sendiri
                const runDetection = async () => {
                    if (!this.showCameraModal || video.readyState !== 4 || this.isCameraCapturing) return;

                    try {
                        const detection = await faceapi.detectSingleFace(video, new faceapi
                                .TinyFaceDetectorOptions({
                                    inputSize: 224,
                                    scoreThreshold: 0.5
                                }))
                            .withFaceLandmarks()
                            .withFaceDescriptor();

                        if (detection) {
                            this.faceRatio = (detection.box.width / video.videoWidth).toFixed(2);

                            if (this.faceRatio < 0.25) {
                                this.liveStatus = 'too_far';
                                this.liveMessage = 'Wajah terlalu jauh. Maju sedikit.';
                            } else if (this.faceRatio > 0.55) {
                                this.liveStatus = 'too_close';
                                this.liveMessage = 'Wajah terlalu dekat. Mundur sedikit.';
                            } else {
                                if (!this.faceMatcher) {
                                    this.liveStatus = 'success';
                                    this.liveMessage = 'Wajah di posisi pas. Klik ambil foto.';
                                } else {
                                    const bestMatch = this.faceMatcher.findBestMatch(detection.descriptor);

                                    if (bestMatch.label !== 'unknown') {
                                        const [nama, instansi, nip] = bestMatch.label.split('|');

                                        this.liveStatus = 'success';
                                        this.liveMessage = `Dikenali: ${nama}. Klik ambil foto.`;

                                        this.$wire.set('nama', nama);
                                        this.$wire.set('unit_kerja', instansi);
                                        this.$wire.set('nip', nip);
                                    } else {
                                        this.liveStatus = 'not_detected';
                                        this.liveMessage = 'Wajah tidak dikenali di sistem.';
                                    }
                                }
                            }
                        } else {
                            this.faceRatio = 0;
                            this.liveStatus = 'not_detected';
                            this.liveMessage = 'Posisikan wajah ke area terang';
                        }
                    } catch (e) {
                        this.liveStatus = 'not_detected';
                        this.liveMessage = 'Mencari wajah...';
                    }

                    if (!this.isCameraCapturing) {
                        this.detectionInterval = setTimeout(runDetection, 500);
                    }
                };
                runDetection();
            },

            closeCameraModal() {
                this.showCameraModal = false;
                this.stopCamera();
            },

            stopCamera() {
                if (this.detectionInterval) clearInterval(this.detectionInterval);
                if (this.cameraStream) {
                    this.cameraStream.getTracks().forEach(t => t.stop());
                    this.cameraStream = null;
                }
            },

            capturePhoto() {
                if (this.isCameraCapturing || this.liveStatus !== 'success') return;

                this.isCameraCapturing = true;
                if (this.detectionInterval) clearInterval(this.detectionInterval);

                const video = document.getElementById('cameraVideo');
                const canvas = document.getElementById('cameraCanvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;

                const ctx = canvas.getContext('2d');
                ctx.translate(canvas.width, 0);
                ctx.scale(-1, 1);
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                canvas.toBlob((blob) => {
                    this.isCameraCapturing = false;
                    this.closeCameraModal();
                    this.uploadBlob(blob);
                }, 'image/jpeg', 0.8);
            },

            showFacePopup(type, title, message) {
                this.faceModalType = type;
                this.faceModalTitle = title;
                this.faceModalMessage = message;
                this.showFaceModal = true;
            },

            closeFaceModal() {
                this.showFaceModal = false;
            },

            uploadBlob(blob) {
                this.isUploading = true;
                this.progress = 0;
                const fileToUpload = new File([blob], 'selfie_live.jpg', {
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
                        this.showFacePopup('not_detected', 'Upload Gagal',
                            'Gagal mengupload gambar. Silakan coba lagi.');
                    },
                    (event) => {
                        this.progress = event.detail.progress;
                    }
                );
            }
        }
    }
</script>
