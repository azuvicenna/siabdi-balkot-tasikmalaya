<?php

namespace App\Livewire\Admin;

use App\Models\Participant;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ParticipantPage extends Component
{
    use WithPagination, WithFileUploads;

    #[Layout('components.layouts.admin')]
    #[Title('Daftar Peserta')]

    protected $paginationTheme = 'bootstrap';
    public $search = '';
    public $deleteId = null;
    public $selectedParticipant = null;

    // Import Properties
    public $import_photos = [];
    public $maxUploadSize = 10240;

    // Form Properties
    #[Rule('required|min:3', as: 'Nama Lengkap')]
    public $nama = '';

    #[Rule('nullable|unique:participants,nip', as: 'NIP')]
    public $nip = '';

    #[Rule('required|min:3', as: 'Unit Kerja')]
    public $unit_kerja = '';

    #[Rule('required|image|max:10240', as: 'Foto Peserta')]
    public $foto;

    public $face_descriptor;

    public function mount()
    {
        $geofencing = Setting::where('key', 'geofencing')->first();
        if ($geofencing) {
            $data = json_decode($geofencing->value, true);
            $this->maxUploadSize = $data['max_upload_size'] ?? 10240;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetForm()
    {
        $this->reset(['nama', 'nip', 'unit_kerja', 'foto', 'face_descriptor', 'selectedParticipant']);
        $this->resetValidation();
    }

    public function resetImportData()
    {
        $this->reset(['import_photos']);
    }

    public function store()
    {
        $this->validate();
        
        if (empty($this->face_descriptor)) {
            $this->addError('face_descriptor', 'Wajah tidak terdeteksi pada foto. Silakan gunakan foto yang jelas.');
            return;
        }

        $fotoMasterPath = null;
        if ($this->foto) {
            $fotoMasterPath = $this->foto->store('photos', 'public');
        }
        Participant::create([
            'nama'            => $this->nama,
            'nip'             => $this->nip,
            'unit_kerja'      => $this->unit_kerja,
            'foto'            => $fotoMasterPath ? basename($fotoMasterPath) : null,
            'face_descriptor' => $this->face_descriptor ? json_decode($this->face_descriptor, true) : null,
        ]);

        $this->resetForm();
        $this->dispatch('close-modal');
        session()->flash('message', 'Data peserta baru beserta Foto Master berhasil ditambahkan!');
    }

    /**
     * Method untuk menampilkan detail peserta ke dalam Modal
     */
    public function showDetail($id)
    {
        // Hindari meload 'face_descriptor' karena ukurannya sangat besar dan membuat Livewire lambat (payload bengkak)
        $this->selectedParticipant = Participant::select(
            'id', 'nama', 'nip', 'unit_kerja', 'foto', 'foto_kehadiran', 'latitude', 'longitude'
        )->find($id);
    }

    public function setDeleteId($id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        if ($this->deleteId) {
            $participant = Participant::find($this->deleteId);

            if ($participant) {
                $participant->delete();
                session()->flash('message', 'Data berhasil dihapus.');
            }

            $this->deleteId = null;
            $this->dispatch('close-modal');
        }
    }

    /**
     * Menerima data JSON langsung sebagai argumen untuk menghindari
     * $wire.set('import_data_json') yang menyebabkan browser freeze
     * akibat payload Livewire yang terlalu besar.
     */
    public function processImportData(string $importDataJson = '[]')
    {
        $this->validate([
            'import_photos.*' => 'image|max:' . $this->maxUploadSize,
        ]);

        $data = json_decode($importDataJson, true);
        if (!is_array($data) || empty($data)) {
            session()->flash('error', 'Data excel kosong atau tidak valid.');
            return;
        }

        $now = Carbon::now();
        $dataToInsert = [];

        // Ambil NIP yang sudah ada di database untuk mencegah duplikasi
        $incomingNips = array_filter(array_column($data, 'nip'));
        $existingNips = Participant::whereIn('nip', $incomingNips)->pluck('nip')->toArray();

        // Map uploaded files by their original filename
        $uploadedFiles = [];
        foreach ($this->import_photos as $photo) {
            $uploadedFiles[$photo->getClientOriginalName()] = $photo;
        }

        try {
            foreach ($data as $row) {
                if (empty($row['nip'])) continue;
                if (in_array($row['nip'], $existingNips)) continue; // Abaikan jika NIP sudah ada

                $fotoMasterPath = null;
                $fileName = $row['foto_file'] ?? null;
                $base64Foto = $row['base64_foto'] ?? null;
                
                if ($base64Foto) {
                    // Decode base64 and save
                    $imageParts = explode(";base64,", $base64Foto);
                    if (count($imageParts) == 2) {
                        $imageTypeAux = explode("image/", $imageParts[0]);
                        $imageExtension = $imageTypeAux[1] ?? 'jpg';
                        $image_base64 = base64_decode($imageParts[1]);
                        $newFileName = uniqid() . '.' . $imageExtension;
                        \Illuminate\Support\Facades\Storage::disk('public')->put('photos/' . $newFileName, $image_base64);
                        $fotoMasterPath = $newFileName;
                    }
                } elseif ($fileName && isset($uploadedFiles[$fileName])) {
                    $file = $uploadedFiles[$fileName];
                    // Save file
                    $path = $file->store('photos', 'public');
                    $fotoMasterPath = basename($path);
                }

                $dataToInsert[] = [
                    'nip'              => $row['nip'],
                    'nama'             => $row['nama'],
                    'unit_kerja'       => $row['unit_kerja'],
                    'foto'             => $fotoMasterPath,
                    'face_descriptor'  => isset($row['descriptor']) ? json_encode($row['descriptor']) : null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            if (!empty($dataToInsert)) {
                Participant::insert($dataToInsert);
                // Invalidate cache face_descriptor agar peserta baru langsung bisa
                // dikenali di halaman absensi tanpa menunggu cache kedaluwarsa
                Cache::forget('face_descriptors');
            }

            $this->reset(['import_photos']);
            $this->dispatch('close-modal-import');

            session()->flash('message', 'Sukses! ' . count($dataToInsert) . ' data peserta baru berhasil diimport beserta fotonya.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $participants = Participant::query()
            ->when($this->search, function ($query) {
                $query->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('unit_kerja', 'like', '%' . $this->search . '%')
                    ->orWhere('nip', 'like', '%' . $this->search . '%');
            })
            ->orderBy('status_hadir', 'asc')
            ->orderBy('nama', 'asc')
            ->paginate(10);

        return view('livewire.admin.participant-page', [
            'participants' => $participants
        ]);
    }
}
