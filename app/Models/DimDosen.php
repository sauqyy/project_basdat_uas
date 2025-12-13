<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DimDosen extends Model
{
    protected $table = 'dim_dosen';
    
    protected $fillable = [
        'dosen_key',
        'nip',
        'nama_dosen',
        'email',
        'prodi',
        'role',
        'profile_picture',
        'judul_skripsi',
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
        return $this->hasMany(FactJadwal::class, 'dosen_key', 'dosen_key');
    }

    public function factKecocokanJadwal(): HasMany
    {
        return $this->hasMany(FactKecocokanJadwal::class, 'dosen_key', 'dosen_key');
    }
}
