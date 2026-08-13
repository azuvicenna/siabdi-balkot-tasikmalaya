<?php

use App\Livewire\Peserta\PresencePage;
use App\Livewire\Peserta\RaffleTicketPage;
use App\Models\Participant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', PresencePage::class)->name('HalamanPresensi');
Route::get('/halaman-kupon', RaffleTicketPage::class)->name('halamanKupon');

// Endpoint ringan untuk face-descriptor — tidak lewat Livewire payload
// Data di-cache 5 menit agar tidak query DB setiap kali halaman dibuka
Route::get('/api/face-descriptors', function () {
    $data = Cache::remember('face_descriptors', 300, function () {
        return Participant::whereNotNull('face_descriptor')
            ->get(['nama', 'nip', 'unit_kerja', 'face_descriptor'])
            ->toArray();
    });

    return response()->json($data);
})->name('api.faceDescriptors');

require __DIR__ . '/admin.php';
require __DIR__ . '/reward-system.php';
