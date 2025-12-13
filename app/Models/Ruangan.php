<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'kapasitas',
        'tipe_ruangan',
        'fasilitas',
        'status',
        'prodi'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}
