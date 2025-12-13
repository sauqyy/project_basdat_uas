<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DimProdi extends Model
{
    protected $table = 'dim_prodi';
    
    protected $fillable = [
        'prodi_key',
        'kode_prodi',
        'nama_prodi',
        'fakultas',
        'deskripsi',
        'akreditasi',
        'is_active',
        'valid_from',
        'valid_to'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime'
    ];

    public function factJadwals(): HasMany
    {
        return $this->hasMany(FactJadwal::class, 'prodi_key', 'prodi_key');
    }

    public function factUtilisasiRuangan(): HasMany
    {
        return $this->hasMany(FactUtilisasiRuangan::class, 'prodi_key', 'prodi_key');
    }
}
