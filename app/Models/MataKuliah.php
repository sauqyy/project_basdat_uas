<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'sks',
        'semester',
        'dosen_id',
        'kapasitas',
        'deskripsi',
        'tipe_kelas',
        'menit_per_sks',
        'prodi',
        'ada_praktikum',
        'sks_praktikum',
        'sks_materi'
    ];

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function jadwals(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }

    public function preferensiDosen(): HasMany
    {
        return $this->hasMany(PreferensiDosen::class);
    }
}
