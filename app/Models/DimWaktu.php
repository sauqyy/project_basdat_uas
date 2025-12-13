<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DimWaktu extends Model
{
    protected $table = 'dim_waktu';
    
    protected $fillable = [
        'waktu_key',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'semester',
        'tahun_akademik',
        'periode',
        'hari_ke',
        'slot_waktu',
        'durasi_menit',
        'is_active'
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'is_active' => 'boolean'
    ];

    public function factJadwals(): HasMany
    {
        return $this->hasMany(FactJadwal::class, 'waktu_key', 'waktu_key');
    }

    public function factUtilisasiRuangan(): HasMany
    {
        return $this->hasMany(FactUtilisasiRuangan::class, 'waktu_key', 'waktu_key');
    }

    public function factKecocokanJadwal(): HasMany
    {
        return $this->hasMany(FactKecocokanJadwal::class, 'waktu_key', 'waktu_key');
    }
}
