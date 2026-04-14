<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Participant;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Pest\Support\Str;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public static function run(): void
    {
        $faker = Faker::create('id_ID');

        // --- BAGIAN PERBAIKAN ---
        // 1. Matikan Cek Foreign Key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Kosongkan Tabel
        Participant::truncate();

        // 3. Nyalakan Lagi Cek Foreign Key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // ------------------------

        foreach (range(1, 3) as $i) {
            Participant::create([
                'nama' => $faker->name,
                'foto' => null,
                'status_hadir' => 'Tidak hadir',
                'latitude' => null,
                'longitude' => null,
                'sudah_menang' => false,
            ]);
        }

        // foreach (range(1, 3) as $i) {
        //     $participant = Participant::create([
        //         'nama' => $faker->name,
        //         'unit_kerja' => 'UPTD Puskesmas Urug',
        //         'foto' => null,
        //         'status_hadir' => 'Hadir',
        //         'latitude' => '-7.230316356025819',
        //         'longitude' => '108.1546012707976',
        //         'sudah_menang' => false,
        //     ]);

        //     Coupon::create([
        //         'participant_id' => $participant->id,
        //         'kode_kupon' => mt_rand(100000, 999999),
        //         'status_kupon' => 'Aktif',
        //     ]);
        // }
    }
}
