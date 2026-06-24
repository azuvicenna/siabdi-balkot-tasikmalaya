<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Participant extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'face_descriptor' => 'array',
    ];

    public function coupons()
    {
        return $this->hasMany(Coupon::class, 'participant_id');
    }

    public function winning()
    {
        return $this->hasOne(Winner::class, 'participant_id');
    }

    public function getFotoMasterUrlAttribute()
    {
        if ($this->foto) {
            return Storage::disk('public')->url('photos/' . $this->foto);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama) . '&background=random';
    }

    public function getFotoSelfieUrlAttribute()
    {
        if ($this->foto_kehadiran) {
            return Storage::disk('public')->url('selfies/' . $this->foto_kehadiran);
        }
        return null; 
    }
}
