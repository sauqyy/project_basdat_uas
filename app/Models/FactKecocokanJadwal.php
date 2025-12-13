<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactKecocokanJadwal extends Model
{
    protected $table = 'fact_kecocokan_jadwal';
    
    protected $fillable = [
        'dosen_key',
        'preferensi_key',
        'waktu_key',
        'preferensi_hari_terpenuhi',
        'preferensi_jam_terpenuhi',
        'skor_kecocokan',
        'prioritas_preferensi',
        'jumlah_preferensi_total',
        'jumlah_preferensi_terpenuhi',
        'persentase_kecocokan',
        'catatan_kecocokan',
        'semester',
        'tahun_akademik'
    ];

    protected $casts = [
        'preferensi_hari_terpenuhi' => 'boolean',
        'preferensi_jam_terpenuhi' => 'boolean',
        'persentase_kecocokan' => 'decimal:2'
    ];

    // Relasi ke dimensi
    public function dimDosen(): BelongsTo
    {
        return $this->belongsTo(DimDosen::class, 'dosen_key', 'dosen_key');
    }

    public function dimPreferensi(): BelongsTo
    {
        return $this->belongsTo(DimPreferensi::class, 'preferensi_key', 'preferensi_key');
    }

    public function dimWaktu(): BelongsTo
    {
        return $this->belongsTo(DimWaktu::class, 'waktu_key', 'waktu_key');
    }
}

