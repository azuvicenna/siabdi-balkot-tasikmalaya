<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function closeAndRedirect()
    {
        Setting::updateOrCreate(
            ['key' => 'status_absensi'],
            ['value' => 'tutup']
        );

        return redirect()->route('reward-system.undian');
    }
}
