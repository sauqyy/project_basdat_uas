<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DimMataKuliah extends Model
{
    protected $table = 'dim_mata_kuliah';
    
    protected $fillable = [
        'mata_kuliah_key',
        'kode_mk',
        'nama_mk',
        'sks',
        'semester',
        'prodi',
        'kapasitas',
        'deskripsi',
        'tipe_kelas',
        'menit_per_sks',
        'ada_praktikum',
        'sks_praktikum',
        'sks_materi',
        'is_active',
        'valid_from',
        'valid_to'
    ];

    protected $casts = [
        'ada_praktikum' => 'boolean',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime'
    ];

    public function factJadwals(): HasMany
    {
        return $this->hasMany(FactJadwal::class, 'mata_kuliah_key', 'mata_kuliah_key');
    }
}
