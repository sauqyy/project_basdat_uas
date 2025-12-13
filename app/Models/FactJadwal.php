<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactJadwal extends Model
{
    protected $table = 'fact_jadwal';
    
    protected $fillable = [
        'dosen_key',
        'mata_kuliah_key',
        'ruangan_key',
        'waktu_key',
        'prodi_key',
        'preferensi_key',
        'jumlah_sks',
        'durasi_menit',
        'kapasitas_kelas',
        'jumlah_mahasiswa',
        'utilisasi_ruangan',
        'prioritas_preferensi',
        'konflik_jadwal',
        'tingkat_konflik',
        'status_aktif',
        'created_at_jadwal',
        'updated_at_jadwal'
    ];

    protected $casts = [
        'utilisasi_ruangan' => 'decimal:2',
        'konflik_jadwal' => 'boolean',
        'status_aktif' => 'boolean',
        'created_at_jadwal' => 'datetime',
        'updated_at_jadwal' => 'datetime'
    ];

    // Relasi ke dimensi
    public function dimDosen(): BelongsTo
    {
        return $this->belongsTo(DimDosen::class, 'dosen_key', 'dosen_key');
    }

    public function dimMataKuliah(): BelongsTo
    {
        return $this->belongsTo(DimMataKuliah::class, 'mata_kuliah_key', 'mata_kuliah_key');
    }

    public function dimRuangan(): BelongsTo
    {
        return $this->belongsTo(DimRuangan::class, 'ruangan_key', 'ruangan_key');
    }

    public function dimWaktu(): BelongsTo
    {
        return $this->belongsTo(DimWaktu::class, 'waktu_key', 'waktu_key');
    }

    public function dimProdi(): BelongsTo
    {
        return $this->belongsTo(DimProdi::class, 'prodi_key', 'prodi_key');
    }

    public function dimPreferensi(): BelongsTo
    {
        return $this->belongsTo(DimPreferensi::class, 'preferensi_key', 'preferensi_key');
    }
}
