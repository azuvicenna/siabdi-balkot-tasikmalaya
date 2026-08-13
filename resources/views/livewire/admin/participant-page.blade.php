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
                    <div wire:key="flash-message-{{ time() }}" class="alert alert-success alert-dismissible fade show mb-3" role="alert">
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
            aria-hidden="true" wire:ignore.self @hidden.bs.modal="resetState(); $wire.resetForm()">
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
                                    <label class="form-label required">Upload Foto Wajah</label>

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
            wire:ignore.self x-data="importExcelPhotos" @hidden.bs.modal="resetErrorState(); $refs.excelInput.value = ''; $refs.photosInput.value = '';">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form class="modal-content" @submit.prevent="startImport">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Peserta & Foto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close" :disabled="isLoading"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Initial Upload UI -->
                        <div x-show="!showErrorUI">
                            <div class="mb-3">
                                <label class="form-label required">Pilih File Excel</label>
                                <input type="file" class="form-control" x-ref="excelInput" accept=".xlsx, .xls, .csv" :disabled="isLoading">
                                <small class="form-hint mt-2">
                                    Header Excel wajib: <strong>NIP, NAMA, UNIT KERJA, FOTO</strong>.
                                </small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">Pilih Semua Foto (Multiple)</label>
                                <input type="file" class="form-control" id="import_photos" x-ref="photosInput" wire:model="import_photos" multiple accept="image/*" :disabled="isLoading">
                                <div wire:loading wire:target="import_photos" class="text-primary mt-2 small fw-bold">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Sedang mengunggah foto ke server...
                                </div>
                                <small class="form-hint mt-2 text-primary">
                                    Pilih sekaligus banyak foto. Nama file foto harus persis dengan yang diketik di kolom FOTO pada Excel.
                                </small>
                                @error('import_photos.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('import_data_json')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Error Resolution UI -->
                        <div x-show="showErrorUI" style="display: none;">
                            <div class="alert alert-danger">
                                Ditemukan <strong x-text="errorList.length"></strong> peserta dengan foto bermasalah (foto tidak ada, terlalu besar, atau wajah tidak terdeteksi).
                                <br>Silakan unggah foto baru untuk masing-masing peserta berikut.
                            </div>
                            <div style="max-height: 350px; overflow-y: auto; overflow-x: hidden;" class="pe-2">
                                <template x-for="(err, index) in errorList" :key="index">
                                    <div class="card mb-2 border-danger shadow-sm">
                                        <div class="card-body p-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="fw-bold text-truncate" style="max-width: 60%;" x-text="err.nama"></div>
                                                <div class="badge bg-danger-lt" x-text="err.error_message"></div>
                                            </div>
                                            <div class="text-muted small mb-2" x-text="err.nip ? 'NIP: ' + err.nip : 'NIP Kosong'"></div>
                                            <div class="d-flex gap-2">
                                                <input type="file" class="form-control form-control-sm" accept="image/*" @change="resolveError(index, $event.target)" :disabled="isLoading">
                                                <button type="button" class="btn btn-outline-danger btn-sm btn-icon" @click="removeError(index)" :disabled="isLoading" title="Hapus Data">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash m-0" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M4 7l16 0"></path>
                                                        <path d="M10 11l0 6"></path>
                                                        <path d="M14 11l0 6"></path>
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        
                        <!-- Progress & Status Loading -->
                        <div x-show="isLoading" class="mt-4" style="display: none;">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small font-weight-bold" x-text="statusText"></span>
                                <span class="small font-weight-bold" x-text="progressPercent + '%'"></span>
                            </div>
                            <div class="progress progress-sm">
                                <div class="progress-bar progress-bar-indeterminate bg-primary" :class="{'progress-bar-indeterminate': progressPercent < 10}" :style="'width: ' + progressPercent + '%'"></div>
                            </div>
                        </div>
                        <div wire:loading wire:target="processImportData" class="text-success mt-2 small fw-bold">
                            Sedang menyimpan data ke database...
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary me-auto" x-show="showErrorUI" @click="resetErrorState()" :disabled="isLoading">Batal & Ulangi</button>
                        
                        <button type="submit" class="btn btn-success ms-auto" :disabled="isLoading || showErrorUI" wire:loading.attr="disabled" wire:target="import_photos, processImportData">
                            <span x-show="!isLoading">Mulai Import</span>
                            <span x-show="isLoading">
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
        const cleanupBackdrop = () => {
            setTimeout(() => {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }, 150);
        };

        $wire.on('close-modal', () => {
            const modalCreate = document.getElementById('modal-create-participant');
            const modalDelete = document.getElementById('modal-danger');

            if (modalCreate) bootstrap.Modal.getInstance(modalCreate)?.hide();
            if (modalDelete) bootstrap.Modal.getInstance(modalDelete)?.hide();
            
            cleanupBackdrop();
        });

        $wire.on('close-modal-import', () => {
            const modalImport = document.getElementById('modal-import');
            if (modalImport) bootstrap.Modal.getInstance(modalImport)?.hide();
            
            cleanupBackdrop();
        });

        const modalImport = document.getElementById('modal-import');
        if (modalImport) {
            modalImport.addEventListener('hidden.bs.modal', () => {
                // Reset Livewire properties
                $wire.resetImportData();
                
                // Reset file inputs in DOM
                const excelInput = modalImport.querySelector('input[x-ref="excelInput"]');
                const photosInput = modalImport.querySelector('input[x-ref="photosInput"]');
                if (excelInput) excelInput.value = '';
                if (photosInput) photosInput.value = '';
                
                // Reset Alpine state
                if (window.Alpine) {
                    const alpineData = window.Alpine.$data(modalImport);
                    if (alpineData && typeof alpineData.resetErrorState === 'function') {
                        alpineData.resetErrorState();
                    }
                }
            });
        }

        Alpine.data('adminFaceExtractor', () => ({
            isModelLoaded: false,
            isLoadingModel: false,
            isProcessingFace: false,
            statusType: 'idle', // idle, loading, success, error
            statusMessage: '',
            
            resetState() {
                this.statusType = 'idle';
                this.statusMessage = '';
                this.isProcessingFace = false;
            },

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
                            this.$wire.set('face_descriptor', JSON.stringify(descriptorArray));

                            this.statusType = 'success';
                            this.statusMessage = 'Wajah berhasil dideteksi dan diekstrak! ✅';
                        } else {
                            this.statusType = 'error';
                            this.statusMessage =
                                'GAGAL: Wajah tidak terdeteksi dengan jelas di foto ini! ❌';
                            this.$wire.set('face_descriptor', null);
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

        Alpine.data('importExcelPhotos', () => ({
            isModelLoaded: false,
            isLoading: false,
            statusText: '',
            progressPercent: 0,
            maxSize: 10240 * 1024,
            
            errorList: [],
            validList: [],
            showErrorUI: false,
            
            init() {
                if (this.$wire && this.$wire.maxUploadSize) {
                    this.maxSize = this.$wire.maxUploadSize * 1024;
                }
            },

            async initFaceApi() {
                if (this.isModelLoaded || typeof faceapi === 'undefined') return;
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
                }
            },
            
            resetErrorState() {
                this.errorList = [];
                this.validList = [];
                this.showErrorUI = false;
                this.isLoading = false;
            },

            async startImport() {
                if (this.showErrorUI) return;

                const excelFile = this.$refs.excelInput.files[0];
                const photoFiles = this.$refs.photosInput.files;

                if (!excelFile) {
                    alert('Pilih file Excel terlebih dahulu!');
                    return;
                }

                if (!this.isModelLoaded && typeof faceapi !== 'undefined') {
                    this.statusText = 'Memuat model Face API...';
                    this.isLoading = true;
                    await this.initFaceApi();
                }

                this.isLoading = true;
                this.statusText = 'Membaca file Excel...';
                this.progressPercent = 5;
                this.errorList = [];
                this.validList = [];

                // Pastikan Alpine merender perubahan DOM (loading UI) lalu biarkan browser melakukan paint
                await new Promise(resolve => {
                    this.$nextTick(() => {
                        setTimeout(resolve, 100);
                    });
                });

                try {
                    const data = await this.readExcel(excelFile);
                    if (!data || data.length === 0) throw new Error('Data Excel kosong atau format salah.');
                    
                    const photosMap = {};
                    for (let i = 0; i < photoFiles.length; i++) {
                        photosMap[photoFiles[i].name.trim()] = photoFiles[i];
                    }
                    
                    let total = data.length;
                    
                    for (let i = 0; i < total; i++) {
                        const row = data[i];
                        const nip = this.getCol(row, ['nip']);
                        const nama = this.getCol(row, ['nama']);
                        const unitKerja = this.getCol(row, ['unit kerja', 'unit_kerja']);
                        let fotoName = this.getCol(row, ['foto']);
                        if (fotoName) fotoName = String(fotoName).trim();
                        
                        if (!nip) continue;
                        
                        let participantData = {
                            nip: String(nip),
                            nama: nama,
                            unit_kerja: unitKerja,
                            foto_file: null,
                            descriptor: null,
                            base64_foto: null
                        };

                        if (!fotoName || !photosMap[fotoName]) {
                            participantData.error_message = 'Foto tidak ditemukan di Excel / belum dipilih';
                            this.errorList.push(participantData);
                            continue;
                        }

                        const photoFile = photosMap[fotoName];
                        if (photoFile.size > this.maxSize) {
                            participantData.error_message = `Ukuran melebihi maksimal (${Math.round(this.maxSize/1024)} KB)`;
                            this.errorList.push(participantData);
                            continue;
                        }
                        
                        this.statusText = `Memproses wajah untuk ${nama} (${i+1}/${total})...`;
                        this.progressPercent = 10 + Math.round((i / total) * 80);

                        // Yield ke browser setiap 5 peserta agar UI tidak freeze & progress bar bisa update
                        if (i % 5 === 0) {
                            await new Promise(resolve => setTimeout(resolve, 0));
                        }
                        
                        const descriptor = await this.getFaceDescriptor(photoFile);
                        if (!descriptor) {
                            participantData.error_message = 'Wajah tidak terdeteksi dengan jelas';
                            this.errorList.push(participantData);
                            continue;
                        }
                        
                        participantData.foto_file = fotoName;
                        participantData.descriptor = descriptor;
                        this.validList.push(participantData);
                    }
                    
                    if (this.errorList.length > 0) {
                        this.isLoading = false;
                        this.showErrorUI = true;
                    } else {
                        this.submitToServer();
                    }

                } catch (e) {
                    alert(e.message);
                    this.isLoading = false;
                    this.progressPercent = 0;
                }
            },
            
            async resolveError(index, fileInput) {
                const file = fileInput.files[0];
                if (!file) return;

                this.isLoading = true;
                this.statusText = 'Memvalidasi foto baru...';
                
                if (file.size > this.maxSize) {
                    alert(`Ukuran foto melebihi maksimal (${Math.round(this.maxSize/1024)} KB)`);
                    fileInput.value = '';
                    this.isLoading = false;
                    return;
                }

                const descriptor = await this.getFaceDescriptor(file);
                if (!descriptor) {
                    alert('Wajah tidak terdeteksi pada foto ini. Silakan coba foto lain.');
                    fileInput.value = '';
                    this.isLoading = false;
                    return;
                }

                const base64 = await this.fileToBase64(file);
                let resolvedParticipant = this.errorList[index];
                resolvedParticipant.descriptor = descriptor;
                resolvedParticipant.base64_foto = base64;
                resolvedParticipant.foto_file = file.name;
                
                this.validList.push(resolvedParticipant);
                this.errorList.splice(index, 1);
                
                this.isLoading = false;

                if (this.errorList.length === 0) {
                    this.showErrorUI = false;
                    this.submitToServer();
                }
            },
            
            removeError(index) {
                if (confirm('Yakin ingin menghapus data peserta ini? Data tidak akan dimasukkan ke database.')) {
                    this.errorList.splice(index, 1);
                    if (this.errorList.length === 0) {
                        this.showErrorUI = false;
                        if (this.validList.length > 0) {
                            this.submitToServer();
                        } else {
                            this.resetErrorState();
                            this.$refs.excelInput.value = '';
                            this.$refs.photosInput.value = '';
                        }
                    }
                }
            },

            submitToServer() {
                this.isLoading = true;
                this.statusText = 'Mengirim data ke server... Tunggu sebentar.';
                this.progressPercent = 95;
                
                // Kirim data JSON langsung sebagai argumen method, BUKAN via $wire.set().
                // $wire.set() dulu menyebabkan browser freeze karena harus serialize
                // ratusan array float 128 angka ke Livewire property terlebih dahulu.
                this.$wire.call('processImportData', JSON.stringify(this.validList));
                
                window.addEventListener('close-modal-import', () => {
                    this.resetErrorState();
                    this.$refs.excelInput.value = '';
                    this.$refs.photosInput.value = '';
                }, {once: true});
            },
            
            fileToBase64(file) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = error => reject(error);
                });
            },
            
            getCol(row, possibleNames) {
                const keys = Object.keys(row);
                for (let k of keys) {
                    if (possibleNames.includes(String(k).toLowerCase().trim())) return row[k];
                }
                return null;
            },
            
            readExcel(file) {
                return new Promise((resolve, reject) => {
                    if (typeof XLSX === 'undefined') {
                        reject(new Error("Library XLSX belum dimuat. Periksa koneksi internet."));
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        try {
                            const workbook = XLSX.read(e.target.result, {type: 'binary'});
                            const worksheet = workbook.Sheets[workbook.SheetNames[0]];
                            resolve(XLSX.utils.sheet_to_json(worksheet, { defval: "" }));
                        } catch(err) {
                            reject(err);
                        }
                    };
                    reader.onerror = (err) => reject(err);
                    reader.readAsBinaryString(file);
                });
            },
            
            getFaceDescriptor(file) {
                return new Promise((resolve) => {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.onload = async () => {
                        try {
                            const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
                            resolve(detection ? Array.from(detection.descriptor) : null);
                        } catch(e) {
                            resolve(null);
                        }
                    };
                    img.onerror = () => resolve(null);
                });
            }
        }));
    </script>
@endscript
