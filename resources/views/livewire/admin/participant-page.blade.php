<div x-data="adminFaceExtractor()">
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Daftar Peserta
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Flash Message -->
            <div>
                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check"
                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l5 5l10 -10" />
                        </svg>
                        <span class="ms-2">{{ session('message') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <!-- Main Card -->
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header d-flex justify-content-between align-items-center py-3">
                            <h3 class="card-title mb-0 text-dark">Data Peserta</h3>
                            <div class="card-actions d-flex gap-2">
                                <div class="text-muted">
                                    <div class="d-inline-block">
                                        <input type="text" class="form-control" placeholder="Cari NIP, Nama, Unit..."
                                            wire:model.live.debounce.500ms="search">
                                    </div>
                                </div>
                                <button class="btn btn-success d-none d-sm-inline-block" data-bs-toggle="modal"
                                    data-bs-target="#modal-import">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-file-import" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path
                                            d="M5 13v-8a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2h-5.5m-9.5 -2h7m-3 -3l3 3l-3 3" />
                                    </svg>
                                    Import Data
                                </button>
                                <button class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                                    data-bs-target="#modal-create-participant" @click="initFaceApi()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus"
                                        width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" fill="none" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg>
                                    Tambah Peserta
                                </button>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table
                                class="table card-table table-vcenter table-hover text-nowrap datatable align-middle">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th class="w-1 text-muted">No.</th>
                                        <th class="text-muted">Nama</th>
                                        <th class="text-muted">Asal Instansi</th>
                                        <th class="text-muted">Status Kehadiran</th>
                                        <th class="text-muted">Status Menang</th>
                                        <th class="text-end text-muted">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($participants as $index => $item)
                                        <tr wire:key="participant-{{ $item->id }}">
                                            <td><span
                                                    class="text-muted">{{ $participants->firstItem() + $index }}</span>
                                            </td>
                                            <td><span class="text-dark fw-medium">{{ $item->nama }}</span></td>
                                            <td><span class="text-dark">{{ $item->unit_kerja }}</span></td>
                                            <td>
                                                @if ($item->status_hadir == 'Hadir')
                                                    <span class="badge bg-success me-1"></span> Hadir
                                                @else
                                                    <span class="badge bg-danger me-1"></span> Tidak Hadir
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->sudah_menang)
                                                    <span class="badge bg-yellow text-yellow-fg">Menang</span>
                                                @else
                                                    <span class="text-secondary">Belum</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-list flex-nowrap justify-content-end">
                                                    <a href="#" class="btn btn-white btn-icon text-primary"
                                                        data-bs-toggle="modal" data-bs-target="#modal-detail"
                                                        wire:click="showDetail({{ $item->id }})"
                                                        title="Lihat Detail">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-eye" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                            <path
                                                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                        </svg>
                                                    </a>
                                                    <a href="#" class="btn btn-white btn-icon text-danger"
                                                        data-bs-toggle="modal" data-bs-target="#modal-danger"
                                                        wire:click="setDeleteId({{ $item->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-trash" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M4 7l16 0" />
                                                            <path d="M10 11l0 6" />
                                                            <path d="M14 11l0 6" />
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="empty">
                                                    <div class="empty-title">Data tidak ditemukan</div>
                                                    <p class="empty-subtitle text-muted">
                                                        Coba ubah kata kunci pencarian Anda.
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="card-footer d-flex align-items-center py-3 bg-white">
                            @if ($participants->total() > 0)
                                <p class="m-0 text-muted small">
                                    Showing <span>{{ $participants->firstItem() }}</span> to
                                    <span>{{ $participants->lastItem() }}</span> of
                                    <span>{{ $participants->total() }}</span> entries
                                </p>
                            @else
                                <p class="m-0 text-muted small">Showing 0 entries</p>
                            @endif

                            <div class="ms-auto">
                                {{ $participants->links() }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Tambah Peserta (Terintegrasi Face API) -->
        <div class="modal modal-blur fade" id="modal-create-participant" tabindex="-1" role="dialog"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <form class="modal-content" wire:submit="store">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Peserta Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            wire:click="resetForm"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label required">Nama Lengkap</label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                        wire:model.blur="nama" placeholder="Masukkan nama lengkap">
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">NIP (Opsional)</label>
                                    <input type="text" class="form-control @error('nip') is-invalid @enderror"
                                        wire:model="nip" placeholder="Masukkan 18 digit NIP" maxlength="18"
                                        inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    <small class="text-muted">Harus berisi tepat 18 angka.</small>
                                    @error('nip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label required">Asal Instansi / Unit Kerja</label>
                                    <input type="text"
                                        class="form-control @error('unit_kerja') is-invalid @enderror"
                                        wire:model="unit_kerja" placeholder="Contoh: Dinas Pendidikan">
                                    @error('unit_kerja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Face API File Upload Area with Improved UX -->
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <label class="form-label">Upload Foto Wajah <span
                                            class="text-muted font-weight-normal">(Opsional - Untuk Face
                                            Recognition)</span></label>

                                    <!-- State: Sedang Load Model AI -->
                                    <template x-if="isLoadingModel && !isModelLoaded">
                                        <div
                                            class="alert alert-info border-0 bg-info-lt d-flex align-items-center mb-2">
                                            <div class="spinner-border spinner-border-sm text-info me-3"
                                                role="status"></div>
                                            <div>
                                                <h4 class="alert-title mb-0">Menyiapkan AI Model...</h4>
                                                <div class="text-muted small">Harap tunggu sebentar, sistem deteksi
                                                    wajah sedang dipersiapkan.</div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- State: Ready / Uploaded -->
                                    <template x-if="isModelLoaded">
                                        <div>
                                            <input type="file"
                                                class="form-control @error('foto') is-invalid @enderror"
                                                wire:model="foto" accept="image/*" :disabled="isProcessingFace"
                                                @change="extractFaceData($event)">

                                            <!-- Dynamic Feedback Box -->
                                            <div class="mt-2 p-2 rounded d-flex align-items-center"
                                                :class="bgClass" x-show="statusType !== 'idle'">
                                                <div x-show="statusType === 'loading'"
                                                    class="spinner-grow spinner-grow-sm me-2" :class="textClass"
                                                    role="status"></div>

                                                <svg x-show="statusType === 'success'"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-check text-success me-2"
                                                    width="20" height="20" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M5 12l5 5l10 -10" />
                                                </svg>

                                                <svg x-show="statusType === 'error'"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-x text-danger me-2"
                                                    width="20" height="20" viewBox="0 0 24 24"
                                                    stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M18 6l-12 12" />
                                                    <path d="M6 6l12 12" />
                                                </svg>

                                                <span class="small fw-medium" :class="textClass"
                                                    x-text="statusMessage"></span>
                                            </div>
                                        </div>
                                    </template>

                                    @error('foto')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer d-flex">
                        <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal"
                            wire:click="resetForm">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="isProcessingFace || isLoadingModel"
                            wire:loading.attr="disabled">
                            <div wire:loading wire:target="store" class="spinner-border spinner-border-sm me-2"></div>
                            <svg wire:loading.remove wire:target="store" xmlns="http://www.w3.org/2000/svg"
                                class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Import -->
        <div class="modal modal-blur fade" id="modal-import" tabindex="-1" role="dialog" aria-hidden="true"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form class="modal-content" wire:submit="import">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Peserta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Pilih File Excel</label>
                            <input type="file" class="form-control @error('file_import') is-invalid @enderror"
                                wire:model="file_import" accept=".xlsx, .xls, .csv">
                            @error('file_import')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div wire:loading wire:target="file_import" class="text-primary mt-2 small">
                                Sedang mengunggah file ke server... Tunggu sebentar.
                            </div>
                            <small class="form-hint mt-2">
                                Header Excel wajib: <strong>NIP, NAMA, UNIT KERJA</strong>.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-success ms-auto" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="import">Mulai Import</span>
                            <span wire:loading wire:target="import">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Detail Kehadiran -->
        <div class="modal modal-blur fade" id="modal-detail" tabindex="-1" role="dialog" aria-hidden="true"
            wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Kehadiran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div wire:loading wire:target="showDetail" class="w-100 text-center py-4">
                            <div class="spinner-border text-primary" role="status"></div>
                            <div class="mt-2 text-muted">Memuat data...</div>
                        </div>

                        @if ($selectedParticipant)
                            <div wire:loading.remove wire:target="showDetail">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Nama Peserta</label>
                                            <p class="form-control-static mb-0">{{ $selectedParticipant->nama }}</p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-primary">Foto Master
                                                (Registrasi)</label>

                                            <img src="{{ $selectedParticipant->foto_master_url }}"
                                                class="img-fluid rounded border shadow-sm w-100 object-cover"
                                                style="max-height: 200px;" alt="Foto Master Data">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-success">Foto Selfie
                                                (Kehadiran)</label>

                                            @if ($selectedParticipant->foto_selfie_url)
                                                <img src="{{ $selectedParticipant->foto_selfie_url }}"
                                                    class="img-fluid rounded border shadow-sm w-100 object-cover"
                                                    style="max-height: 200px;" alt="Foto Selfie Kehadiran">
                                            @else
                                                <div class="alert alert-secondary text-center small p-3 mb-0">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="icon icon-tabler icon-tabler-camera-off mb-1 d-block mx-auto"
                                                        width="24" height="24" viewBox="0 0 24 24"
                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M3 3l18 18" />
                                                        <path
                                                            d="M5 5h-2v11a2 2 0 0 0 2 2h14m2 -2v-9a2 2 0 0 0 -2 -2h-1" />
                                                        <path d="M14.948 10.959a3 3 0 1 0 -4.007 -3.919" />
                                                    </svg>
                                                    Belum ada foto selfie.
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="mb-3 h-100 d-flex flex-column">
                                            <label class="form-label fw-bold">Lokasi Presensi</label>
                                            <div class="d-flex gap-2 mb-2 text-muted small">
                                                <span>Lat: {{ $selectedParticipant->latitude ?? '-' }}</span>
                                                <span>Long: {{ $selectedParticipant->longitude ?? '-' }}</span>
                                            </div>

                                            @if ($selectedParticipant->latitude && $selectedParticipant->longitude)
                                                <div class="ratio ratio-1x1 grow"
                                                    style="max-height: 480px; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                                    <iframe width="100%" height="100%" frameborder="0"
                                                        scrolling="no" marginheight="0" marginwidth="0"
                                                        src="https://maps.google.com/maps?q={{ $selectedParticipant->latitude }},{{ $selectedParticipant->longitude }}&hl=id&z=17&output=embed">
                                                    </iframe>
                                                </div>
                                            @else
                                                <div class="alert alert-warning mt-2">Data lokasi tidak ditemukan.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Hapus Data (Danger) -->
        <div class="modal modal-blur fade" id="modal-danger" tabindex="-1" role="dialog" aria-hidden="true"
            wire:ignore.self>
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-status bg-danger"></div>
                    <div class="modal-body text-center py-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 9v2m0 4v.01" />
                            <path
                                d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                        </svg>
                        <h3>Apakah Anda yakin?</h3>
                        <div class="text-muted">Data yang dihapus tidak dapat dikembalikan lagi.</div>
                    </div>
                    <div class="modal-footer border-0">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <a href="#" class="btn w-100" data-bs-dismiss="modal">Batal</a>
                                </div>
                                <div class="col">
                                    <a href="#" class="btn btn-danger w-100" wire:click="delete"
                                        data-bs-dismiss="modal">Ya, Hapus</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-modal', (event) => {
                const modalCreate = document.getElementById('modal-create-participant');
                const modalDelete = document.getElementById('modal-danger');

                if (modalCreate) bootstrap.Modal.getOrCreateInstance(modalCreate).hide();
                if (modalDelete) bootstrap.Modal.getOrCreateInstance(modalDelete).hide();
            });

            Livewire.on('close-modal-import', () => {
                const modalImport = document.getElementById('modal-import');
                if (modalImport) bootstrap.Modal.getOrCreateInstance(modalImport).hide();
            });
        });

        Alpine.data('adminFaceExtractor', () => ({
            isModelLoaded: false,
            isLoadingModel: false,
            isProcessingFace: false,
            statusType: 'idle', // idle, loading, success, error
            statusMessage: '',

            // Dynamic styling untuk visual feedback UI
            get bgClass() {
                if (this.statusType === 'success')
                    return 'bg-success-lt border border-success border-opacity-25';
                if (this.statusType === 'error')
                    return 'bg-danger-lt border border-danger border-opacity-25';
                if (this.statusType === 'loading' || this.isProcessingFace)
                    return 'bg-warning-lt border border-warning border-opacity-25';
                return '';
            },
            get textClass() {
                if (this.statusType === 'success') return 'text-success';
                if (this.statusType === 'error') return 'text-danger';
                if (this.statusType === 'loading' || this.isProcessingFace) return 'text-warning';
                return '';
            },

            async initFaceApi() {
                if (this.isModelLoaded || typeof faceapi === 'undefined') return;

                this.isLoadingModel = true;
                this.statusType = 'idle';

                const MODEL_URL = '/models';
                try {
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                    ]);

                    this.isModelLoaded = true;
                } catch (error) {
                    console.error("Gagal memuat Face API Model", error);
                } finally {
                    this.isLoadingModel = false;
                }
            },

            async extractFaceData(event) {
                const file = event.target.files[0];
                if (!file) return;

                if (!this.isModelLoaded) {
                    alert('Model AI belum selesai dimuat, harap tunggu sebentar.');
                    event.target.value = '';
                    return;
                }

                this.isProcessingFace = true;
                this.statusType = 'loading';
                this.statusMessage = 'Mengekstrak fitur wajah, mohon tunggu...';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);

                img.onload = async () => {
                    try {
                        const detection = await faceapi.detectSingleFace(img, new faceapi
                                .TinyFaceDetectorOptions())
                            .withFaceLandmarks()
                            .withFaceDescriptor();

                        if (detection) {
                            const descriptorArray = Array.from(detection.descriptor);
                            @this.set('face_descriptor', JSON.stringify(descriptorArray));

                            this.statusType = 'success';
                            this.statusMessage = 'Wajah berhasil dideteksi dan diekstrak! ✅';
                        } else {
                            this.statusType = 'error';
                            this.statusMessage =
                                'GAGAL: Wajah tidak terdeteksi dengan jelas di foto ini! ❌';
                            @this.set('face_descriptor', null);
                        }
                    } catch (err) {
                        this.statusType = 'error';
                        this.statusMessage = 'Terjadi kesalahan sistem saat memproses wajah.';
                    } finally {
                        this.isProcessingFace = false;
                    }
                };
            }
        }));
    </script>
@endscript
