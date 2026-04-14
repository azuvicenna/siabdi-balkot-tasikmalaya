<?php

namespace App\Livewire\Peserta;

use App\Models\Coupon;
use App\Models\Participant;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Exception;

#[Layout('components.layouts.Pages-peserta.peserta')]
#[Title("Halaman Absensi Peserta")]
class PresencePage extends Component
{
    use WithFileUploads;

    // Konfigurasi Lokasi
    protected const CENTER_LAT = -7.316546993522394;
    protected const CENTER_LNG = 108.19674640365119;
    protected const RADIUS_METERS = 10000;
    protected const EARTH_RADIUS = 6371000;
    protected const MAX_UPLOAD_SIZE = 10240; // 10MB

    // Properties Input
    public string $nama = '';
    public string $unit_kerja = '';
    public $photo;
    public bool $agreement = false;

    // System States
    public bool $isClosedPresence = false;

    // Location States
    public bool $locationGranted = false;
    public string $locationErrorMessage = '';
    public ?float $userLat = null;
    public ?float $userLng = null;

    public function mount()
    {
        $settings = Setting::where('key', 'status_absensi')->first();

        // Cek apakah user sudah punya sesi ID (bukan NIP lagi)
        if (session()->has('current_participant_id')) {
            return redirect()->route('halamanKupon');
        }

        if ($settings && $settings->value == 'tutup') {
            $this->isClosedPresence = true;
        }
    }

    // --- LOGIKA LOKASI (TETAP SAMA) ---
    public function checkLocation($lat, $lng)
    {
        $this->userLat = $lat;
        $this->userLng = $lng;

        $distance = $this->calculateDistance($lat, $lng);

        if ($distance <= self::RADIUS_METERS) {
            $this->locationGranted = true;
            $this->locationErrorMessage = '';
            $this->dispatch('location-granted');
        } else {
            $this->locationGranted = false;
            $this->locationErrorMessage = 'Anda berada di luar area acara. Silakan mendekat.';
            $this->dispatch('location-radius-error');
        }
    }

    public function locationFailed($code)
    {
        $this->locationGranted = false;
        $this->locationErrorMessage = 'Gagal mengambil lokasi. Pastikan GPS aktif.';
        $this->dispatch('location-system-error');
    }

    // --- LOGIKA UTAMA: KLAIM KUPON ---
    public function klaimKupon()
    {
        $this->resetErrorBag();

        // 1. Cek Rate Limit (Anti Spam)
        if ($this->checkRateLimit()) {
            return;
        }

        // 2. Validasi Input
        if (!$this->validateClaimRequest()) {
            return;
        }

        $uploadedPath = null;

        try {
            // 3. Upload Foto & Proses Database
            $uploadedPath = $this->uploadPhoto();
            $couponCode = $this->processTransaction($uploadedPath);

            // 4. Sukses
            $this->handleSuccess($couponCode);
        } catch (Exception $e) {
            $this->handleFailure($e, $uploadedPath);
        }
    }

    private function validateClaimRequest()
    {
        // Validasi Nama, Foto, dan Checkbox
        $this->validate([
            'nama'      => 'required|string|min:3|max:100',
            'unit_kerja'      => 'required|string|min:3|max:100',
            'photo'     => 'required|image|max:' . self::MAX_UPLOAD_SIZE,
            'agreement' => 'accepted'
        ], [
            'nama.required'      => 'Nama wajib diisi.',
            'unit_kerja.required'      => 'Asal Instansi wajib diisi.',
            'photo.required'     => 'Foto selfie wajib diupload.',
            'photo.max'          => 'Ukuran foto terlalu besar (Max 10MB).',
            'agreement.accepted' => 'Anda harus menyetujui pernyataan data.'
        ]);

        // Validasi Lokasi
        if (!$this->locationGranted || is_null($this->userLat) || is_null($this->userLng)) {
            $this->addError('location', 'Posisi belum terverifikasi. Pastikan GPS aktif.');
            return false;
        }

        return true;
    }

    private function uploadPhoto()
    {
        // Generate nama file unik berdasarkan Nama User + Wakt
        $safeName = preg_replace('/[^A-Za-z0-9]/', '', $this->nama);
        $filename = 'selfie_' . $safeName . '_' . time() . '.jpg';

        // return $this->photo->storeAs('photos', $filename, 'public');
        return $this->photo->storeAs('photos', $filename, 's3');
    }

    private function processTransaction($uploadedPath)
    {
        return DB::transaction(function () use ($uploadedPath) {
            // A. Cek Ketersediaan Kupon (Locking)
            $availableCoupon = Coupon::whereNull('participant_id')
                ->lockForUpdate()
                ->first();

            if (!$availableCoupon) {
                throw new Exception('Mohon maaf, kupon undian telah habis!');
            }
            $participant = Participant::create([
                'nama'         => $this->nama,
                'unit_kerja' => $this->unit_kerja,
                'foto'         => basename($uploadedPath),
                'status_hadir' => 'Hadir',
                'latitude'     => $this->userLat,
                'longitude'    => $this->userLng,
            ]);
            $availableCoupon->update([
                'participant_id' => $participant->id
            ]);

            // Simpan ID sementara untuk sesi sukses
            session(['temp_participant_id' => $participant->id]);

            return $availableCoupon->kode_kupon;
        });
    }

    private function handleSuccess($couponCode)
    {
        // Bersihkan limiter
        RateLimiter::clear('claim-coupon:' . request()->ip());

        session()->flash('success', 'Berhasil! Kode Kupon Anda: ' . $couponCode);

        // Pindahkan ID dari temp ke session utama (Login otomatis)
        if (session('temp_participant_id')) {
            session(['current_participant_id' => session('temp_participant_id')]);
            session()->forget('temp_participant_id');
        }

        return redirect()->route('halamanKupon');
    }

    private function handleFailure(Exception $e, $uploadedPath)
    {
        // Hapus foto jika gagal database (Rollback manual file)
        if ($uploadedPath && Storage::disk('public')->exists($uploadedPath)) {
            Storage::disk('public')->delete($uploadedPath);
        }

        Log::error("Gagal Klaim [Nama: {$this->nama}]: " . $e->getMessage());

        $msg = $e->getMessage();
        if (str_contains($msg, 'lock') || str_contains($msg, 'Deadlock')) {
            $msg = "Sistem sibuk, silakan coba tekan tombol lagi.";
        }

        $this->addError('system', $msg);
    }

    private function checkRateLimit()
    {
        $key = 'claim-coupon:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('system', "Mohon tunggu $seconds detik sebelum mencoba lagi.");
            return true;
        }
        RateLimiter::hit($key, 10); // Penalty 10 detik
        return false;
    }

    private function calculateDistance($latUser, $lngUser)
    {
        // Rumus Haversine
        $p = 0.017453292519943295;
        $a = 0.5 - cos(($latUser - self::CENTER_LAT) * $p) / 2 +
            cos(self::CENTER_LAT * $p) * cos($latUser * $p) * (1 - cos(($lngUser - self::CENTER_LNG) * $p)) / 2;

        return 12742 * asin(sqrt($a)) * 1000; // Hasil dalam meter
    }

    public function render()
    {
        return view('livewire.peserta.presence-page');
    }
}
