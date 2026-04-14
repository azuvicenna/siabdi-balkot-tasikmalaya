<?php

namespace App\Livewire\RewardSystem;

use App\Models\Participant;
use App\Models\Reward;
use App\Models\Winner;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Doorprize extends Component
{
    #[Layout('components.layouts.reward-system')]
    #[Title('Gebyar Undian HKN Day Ke-61')]

    // public function pickWinner()
    // {
    //     return DB::transaction(function () {

    //         // 1. Ambil Kandidat secara Acak
    //         $calon = Participant::query()
    //             ->where('sudah_menang', false)
    //             ->where('status_hadir', 'Hadir') // Pastikan hanya yang hadir
    //             ->whereHas('coupons', function ($q) {
    //                 $q->where('status_kupon', 'Aktif');
    //             })
    //             ->inRandomOrder()
    //             // lockForUpdate mencegah data ini diambil proses lain di detik yang sama
    //             ->lockForUpdate()
    //             ->first();

    //         if (!$calon) {
    //             return null;
    //         }

    //         // Simpan hanya saat tombol "Tetapkan Pemenang" di modal diklik.
    //         // Tapi setidaknya query di atas sudah menjamin pengacakan yang adil.

    //         return [
    //             'id' => $calon->id,
    //             'kode_kupon' => $calon->coupons->first()->kode_kupon ?? '-',
    //             'nama' => $calon->nama,
    //             'unit_kerja' => $calon->unit_kerja,
    //             'foto' => $calon->foto_url,
    //         ];
    //     });
    // }

    public function pickWinner()
    {
        return DB::transaction(function () {
            // 1. AMBIL DAFTAR ID SAJA (lebih ringan dari get() full object)
            $candidateIds = Participant::query()
                ->where('sudah_menang', false)
                ->where('status_hadir', 'Hadir')
                ->whereHas('coupons', function ($q) {
                    $q->where('status_kupon', 'Aktif');
                })
                ->pluck('id'); // <--- Select id doang

            if ($candidateIds->isEmpty()) {
                return null;
            }

            // 2. KOCOK ID-NYA (Sama-sama random, tapi enteng buat RAM)
            // Array integer [1, 55, 302, ...] diacak
            $randomId = $candidateIds->shuffle()->first();

            // 3. AMBIL DATA PEMENANG & LOCK
            $calon = Participant::where('id', $randomId)
                ->lockForUpdate()
                ->first();

            // Memastikan kandidat masih valid saat detik ini juga
            if (!$calon || $calon->sudah_menang || $calon->status_hadir !== 'Hadir') {
                return null;
            }

            return [
                'id' => $calon->id,
                'kode_kupon' => $calon->coupons->first()->kode_kupon ?? '-',
                'nama' => $calon->nama,
                'unit_kerja' => $calon->unit_kerja,
                'foto' => $calon->foto_url,
            ];
        });
    }

    public function saveWinner($participantId)
    {
        $peserta = Participant::find($participantId);

        $kupon = $peserta ? $peserta->coupons()->where('status_kupon', 'Aktif')->first() : null;

        if ($peserta) {

            Winner::create([
                'participant_id' => $peserta->id,
                'reward_id'      => null,
                'coupon_id'      => $kupon ? $kupon->id : null,
            ]);

            $peserta->update(['sudah_menang' => true]);

            if ($kupon) {
                $kupon->update(['status_kupon' => 'Kadaluarsa']);
            }

            $this->dispatch('pemenang-tersimpan');
        }
    }

    public function markAsAbsent($participantId)
    {
        $participant = Participant::find($participantId);
        if ($participant) {
            $participant->update(['status_hadir' => 'Tidak hadir']);
        }
    }

    public function render()
    {
        $rewards = Reward::where('status_hadiah', 'Aktif')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => [
                    'id' => $item->id,
                    'nama_hadiah' => $item->nama_hadiah,
                    'gambar' => $item->gambar ? asset('static/doorprizes/' . $item->gambar) : 'https://placehold.co/300x300/png?text=No+Image',
                    'stok' => $item->stok,
                    'kategori' => $item->category->nama_kategori ?? 'Umum',
                ]];
            });

        return view('livewire.reward-system.doorprize', [
            'jsRewards' => $rewards
        ]);
    }
}
