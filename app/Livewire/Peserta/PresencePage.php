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

    // Properties Input
    public string $nama = '';
    public string $unit_kerja = '';
    public ?string $nip = null;
    public $photo;
    public bool $agreement = false;

    // System States
    public bool $isClosedPresence = false;

    // Location States
    public bool $locationGranted = false;
    public string $locationErrorMessage = '';
    public ?float $userLat = null;
    public ?float $userLng = null;

    // --- HELPER UNTUK MENGAMBIL SETTING DARI JSON ---
    private function getConfig($configKey, $default = null)
    {
        $setting = Setting::where('key', 'geofencing')->first();

        if ($setting && $setting->value) {
            $configs = json_decode($setting->value, true);

            if (is_array($configs) && array_key_exists($configKey, $configs)) {
                return $configs[$configKey];
            }
        }

        return $default; 
    }

    public function mount()
    {
        $settings = Setting::where('key', 'status_absensi')->first();

        if (session()->has('current_participant_id')) {
            return redirect()->route('halamanKupon');
        }

        if ($settings && $settings->value == 'tutup') {
            $this->isClosedPresence = true;
        }
    }

    // --- LOGIKA LOKASI ---
    public function checkLocation($lat, $lng)
    {
        $this->userLat = $lat;
        $this->userLng = $lng;

        $distance = $this->calculateDistance($lat, $lng);

        // Ambil radius dari JSON
        $radiusMeters = (float) $this->getConfig('radius_meters', 1000000);

        if ($distance <= $radiusMeters) {
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

        if ($this->checkRateLimit()) {
            return;
        }

        if (!$this->validateClaimRequest()) {
            return;
        }

        $uploadedPath = null;

        try {
            $uploadedPath = $this->uploadPhoto();
            $couponCode = $this->processTransaction($uploadedPath);
            $this->handleSuccess($couponCode);
        } catch (Exception $e) {
            $this->handleFailure($e, $uploadedPath);
        }
    }

    private function validateClaimRequest()
    {
        $maxSize = (int) $this->getConfig('max_upload_size', 10240);
        $maxSizeMB = round($maxSize / 1024, 1);

        $this->validate([
            'nama'       => 'required|string',
            'unit_kerja' => 'required|string',
            'photo'      => 'required|image|max:' . $maxSize,
            'agreement'  => 'accepted'
        ], [
            'nama.required'      => 'Wajah belum terdeteksi. Silakan ambil foto ulang.',
            'photo.required'     => 'Foto selfie wajib diupload.',
            'photo.max'          => 'Ukuran foto terlalu besar (Max ' . $maxSizeMB . 'MB).',
            'agreement.accepted' => 'Anda harus menyetujui pernyataan data.'
        ]);

        if (!$this->locationGranted || is_null($this->userLat) || is_null($this->userLng)) {
            $this->addError('location', 'Posisi belum terverifikasi. Pastikan GPS aktif.');
            return false;
        }

        return true;
    }

    private function uploadPhoto()
    {
        $safeName = preg_replace('/[^A-Za-z0-9]/', '', $this->nama);
        $filename = 'selfie_' . $safeName . '_' . time() . '.jpg';
        return $this->photo->storeAs('selfies', $filename, 'public');
    }

    private function processTransaction($uploadedPath)
    {
        return DB::transaction(function () use ($uploadedPath) {
            $participantQuery = Participant::where('nama', $this->nama)
                ->where('unit_kerja', $this->unit_kerja);

            if ($this->nip && $this->nip !== '-') {
                $participantQuery->where('nip', $this->nip);
            }

            $participant = $participantQuery->lockForUpdate()->first();

            if (!$participant) {
                throw new Exception('Data peserta tidak valid di database.');
            }

            if ($participant->status_hadir === 'Hadir') {
                throw new Exception('Anda sudah melakukan absensi dan mengklaim kupon sebelumnya!');
            }

            $availableCoupon = Coupon::whereNull('participant_id')
                ->lockForUpdate()
                ->first();

            if (!$availableCoupon) {
                throw new Exception('Mohon maaf, kupon undian telah habis!');
            }

            $participant->update([
                'foto_kehadiran' => basename($uploadedPath),
                'status_hadir'   => 'Hadir',
                'latitude'       => $this->userLat,
                'longitude'      => $this->userLng,
            ]);

            $availableCoupon->update([
                'participant_id' => $participant->id
            ]);

            session(['temp_participant_id' => $participant->id]);

            return $availableCoupon->kode_kupon;
        });
    }

    private function handleSuccess($couponCode)
    {
        RateLimiter::clear('claim-coupon:' . request()->ip());
        session()->flash('success', 'Berhasil! Kode Kupon Anda: ' . $couponCode);

        if (session('temp_participant_id')) {
            session(['current_participant_id' => session('temp_participant_id')]);
            session()->forget('temp_participant_id');
        }

        return redirect()->route('halamanKupon');
    }

    private function handleFailure(Exception $e, $uploadedPath)
    {
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
        RateLimiter::hit($key, 10);
        return false;
    }

    private function calculateDistance($latUser, $lngUser)
    {
        $centerLat = (float) $this->getConfig('center_lat', -7.3164413671413895);
        $centerLng = (float) $this->getConfig('center_lng', 108.19693890463489);

        $p = 0.017453292519943295;
        $a = 0.5 - cos(($latUser - $centerLat) * $p) / 2 +
            cos($centerLat * $p) * cos($latUser * $p) * (1 - cos(($lngUser - $centerLng) * $p)) / 2;

        return 12742 * asin(sqrt($a)) * 1000;
    }

    public function render()
    {
        $pesertaTerdaftar = Participant::whereNotNull('face_descriptor')
            ->get(['nama', 'nip', 'unit_kerja', 'face_descriptor']);

        return view('livewire.peserta.presence-page', [
            'pesertaTerdaftar' => $pesertaTerdaftar
        ]);
    }
}
