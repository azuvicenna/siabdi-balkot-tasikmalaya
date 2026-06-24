<?php

namespace Tests\Feature;

use App\Livewire\Peserta\PresencePage;
use App\Models\Coupon;
use App\Models\Participant;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache; // <-- Tambahkan ini
use Livewire\Livewire;
use Tests\TestCase;

class PresencePageTest extends TestCase
{
    use RefreshDatabase;

    // Tambahkan method ini untuk mereset Cache sebelum tiap pengujian
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_peserta_bisa_absen_dan_dapat_kupon_jika_lokasi_sesuai()
    {
        Storage::fake('public');

        Setting::create([
            'key' => 'config',
            'value' => json_encode([
                'center_lat' => -7.3817108,
                'center_lng' => 108.6547367,
                'radius_meters' => 1000000,
                'max_upload_size' => 10240
            ])
        ]);

        $peserta = Participant::create([
            'nama' => 'Budi Santoso',
            'unit_kerja' => 'Puskesmas A',
            'status_hadir' => 'Tidak hadir'
        ]);

        $kupon = Coupon::create([
            'kode_kupon' => 'HKN-001',
            'status_kupon' => 'Aktif',
            'participant_id' => null
        ]);

        $fakePhoto = UploadedFile::fake()->image('selfie.jpg');

        Livewire::test(PresencePage::class)
            ->set('nama', 'Budi Santoso')
            ->set('unit_kerja', 'Puskesmas A')
            ->set('photo', $fakePhoto)
            ->set('agreement', true)
            ->call('checkLocation', -7.3817108, 108.6547367)
            ->call('klaimKupon')
            ->assertHasNoErrors()
            ->assertRedirect(route('halamanKupon'));

        $this->assertDatabaseHas('participants', [
            'id' => $peserta->id,
            'status_hadir' => 'Hadir'
        ]);

        $this->assertDatabaseHas('coupons', [
            'id' => $kupon->id,
            'participant_id' => $peserta->id
        ]);
    }

    public function test_error_jika_lokasi_di_luar_radius()
    {
        // 1. Bersihkan cache di awal
        Cache::flush();

        // 2. Gunakan updateOrCreate agar dijamin masuk ke database
        Setting::updateOrCreate(
            ['key' => 'geofencing'],
            ['value' => json_encode([
                'center_lat' => -7.3817108,
                'center_lng' => 108.6547367,
                'radius_meters' => 10, // Kita set radius super ketat: cuma 10 METER!
                'max_upload_size' => 10240
            ])]
        );

        // 3. Jaminan mutlak: hapus cache lagi setelah data masuk
        Cache::flush();

        Livewire::test(PresencePage::class)
            // 4. Kita pakai kordinat Monas Jakarta (Jarak 200+ KM dari Tasik)
            ->call('checkLocation', -6.1753924, 106.8271528)
            ->assertSet('locationGranted', false)
            ->assertSet('locationErrorMessage', 'Anda berada di luar area acara. Silakan mendekat.');
    }


    public function test_error_jika_peserta_sudah_pernah_absen_sebelumnya()
    {
        Cache::flush();
        Setting::updateOrCreate(['key' => 'geofencing'], ['value' => json_encode(['center_lat' => -7.3817108, 'center_lng' => 108.6547367, 'radius_meters' => 1000000])]);

        // Buat peserta yang SUDAH HADIR
        Participant::create([
            'nama' => 'Budi',
            'unit_kerja' => 'Puskesmas B',
            'status_hadir' => 'Hadir'
        ]);

        $fakePhoto = UploadedFile::fake()->image('selfie2.jpg');

        Livewire::test(PresencePage::class)
            ->set('nama', 'Budi')
            ->set('unit_kerja', 'Puskesmas B')
            ->set('photo', $fakePhoto)
            ->set('agreement', true)
            ->call('checkLocation', -7.3817108, 108.6547367)
            ->call('klaimKupon')
            ->assertHasErrors(['system']);
    }

    public function test_error_jika_kupon_sudah_habis_terpakai()
    {
        Cache::flush();
        Setting::updateOrCreate(['key' => 'geofencing'], ['value' => json_encode(['center_lat' => -7.3817108, 'center_lng' => 108.6547367, 'radius_meters' => 1000000])]);

        Participant::create(['nama' => 'Peserta Telat', 'unit_kerja' => 'RSUD', 'status_hadir' => 'Tidak hadir']);


        $fakePhoto = UploadedFile::fake()->image('selfie3.jpg');

        Livewire::test(PresencePage::class)
            ->set('nama', 'Peserta Telat')
            ->set('unit_kerja', 'RSUD')
            ->set('photo', $fakePhoto)
            ->set('agreement', true)
            ->call('checkLocation', -7.3817108, 108.6547367) 
            ->call('klaimKupon')
            ->assertHasErrors(['system']);
    }
}
