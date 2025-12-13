<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DimRuangan extends Model
{
    protected $table = 'dim_ruangan';
    
    protected $fillable = [
        'ruangan_key',
        'kode_ruangan',
        'nama_ruangan',
        'kapasitas',
        'tipe_ruangan',
        'fasilitas',
        'prodi',
        'status',
        'is_active',
        'valid_from',
        'valid_to'
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime'
    ];

    public function factJadwals(): HasMany
    {
        return $this->hasMany(FactJadwal::class, 'ruangan_key', 'ruangan_key');
    }

    public function factUtilisasiRuangan(): HasMany
    {
        return $this->hasMany(FactUtilisasiRuangan::class, 'ruangan_key', 'ruangan_key');
    }
}
