<?php

namespace Tests\Feature;

use App\Livewire\RewardSystem\Doorprize;
use App\Models\Coupon;
use App\Models\Participant;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache; // <-- Tambahkan ini
use Livewire\Livewire;
use Tests\TestCase;

class DoorprizeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_sistem_hanya_memilih_peserta_yang_hadir_dan_punya_kupon()
    {
        $pesertaValid = Participant::create([
            'nama' => 'Peserta Valid',
            'unit_kerja' => 'RSUD',
            'status_hadir' => 'Hadir',
            'sudah_menang' => false
        ]);
        Coupon::create(['kode_kupon' => 'V-01', 'participant_id' => $pesertaValid->id, 'status_kupon' => 'Aktif']);

        $pesertaAbsen = Participant::create([
            'nama' => 'Peserta Bolos',
            'unit_kerja' => 'Dinkes',
            'status_hadir' => 'Tidak hadir',
            'sudah_menang' => false
        ]);
        Coupon::create(['kode_kupon' => 'V-02', 'participant_id' => $pesertaAbsen->id, 'status_kupon' => 'Aktif']);

        $pesertaPernahMenang = Participant::create([
            'nama' => 'Peserta Serakah',
            'unit_kerja' => 'Puskesmas',
            'status_hadir' => 'Hadir',
            'sudah_menang' => true
        ]);
        Coupon::create(['kode_kupon' => 'V-03', 'participant_id' => $pesertaPernahMenang->id, 'status_kupon' => 'Aktif']);

        // Eksekusi Livewire dan panggil fungsi pickWinner langsung dari instance class-nya
        $component = Livewire::test(Doorprize::class);
        $winnerData = $component->instance()->pickWinner(); // <-- Diubah di sini

        $this->assertNotNull($winnerData);
        $this->assertEquals($pesertaValid->id, $winnerData['id']);
        $this->assertEquals('Peserta Valid', $winnerData['nama']);
    }

    public function test_menyimpan_pemenang_merubah_status_kupon()
    {
        // Tambahkan unit_kerja agar tidak error NOT NULL constraint
        $peserta = Participant::create([
            'nama' => 'Budi',
            'unit_kerja' => 'Puskesmas A', // <-- Diperbaiki di sini
            'status_hadir' => 'Hadir',
            'sudah_menang' => false
        ]);
        $kupon = Coupon::create(['kode_kupon' => 'K-123', 'participant_id' => $peserta->id, 'status_kupon' => 'Aktif']);

        Livewire::test(Doorprize::class)
            ->call('saveWinner', $peserta->id)
            ->assertDispatched('pemenang-tersimpan');

        $this->assertDatabaseHas('winners', ['participant_id' => $peserta->id]);
        $this->assertDatabaseHas('participants', ['id' => $peserta->id, 'sudah_menang' => 1]);
        $this->assertDatabaseHas('coupons', ['id' => $kupon->id, 'status_kupon' => 'Kadaluarsa']);
    }
}
