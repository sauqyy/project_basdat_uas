<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DimPreferensi extends Model
{
    protected $table = 'dim_preferensi';
    
    protected $fillable = [
        'preferensi_key',
        'dosen_id',
        'mata_kuliah_id',
        'preferensi_hari',
        'preferensi_jam',
        'prioritas',
        'catatan',
        'is_active',
        'valid_from',
        'valid_to'
    ];

    protected $casts = [
        'preferensi_hari' => 'array',
        'preferensi_jam' => 'array',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime'
    ];

    public function factJadwals(): HasMany
    {
        return $this->hasMany(FactJadwal::class, 'preferensi_key', 'preferensi_key');
    }

    public function factKecocokanJadwal(): HasMany
    {
        return $this->hasMany(FactKecocokanJadwal::class, 'preferensi_key', 'preferensi_key');
    }
}
