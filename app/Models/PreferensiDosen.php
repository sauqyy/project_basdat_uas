<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreferensiDosen extends Model
{
    protected $fillable = [
        'dosen_id',
        'mata_kuliah_id',
        'preferensi_hari',
        'preferensi_jam',
        'prioritas',
        'catatan'
    ];

    protected $casts = [
        'preferensi_hari' => 'array',
        'preferensi_jam' => 'array'
    ];

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }
}
