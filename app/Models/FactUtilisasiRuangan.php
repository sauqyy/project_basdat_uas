<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactUtilisasiRuangan extends Model
{
    protected $table = 'fact_utilisasi_ruangan';
    
    protected $fillable = [
        'ruangan_key',
        'waktu_key',
        'prodi_key',
        'total_jam_penggunaan',
        'total_jam_tersedia',
        'persentase_utilisasi',
        'jumlah_kelas',
        'jumlah_mahasiswa_total',
        'rata_rata_kapasitas',
        'peak_hour_utilisasi',
        'periode_semester',
        'tahun_akademik'
    ];

    protected $casts = [
        'persentase_utilisasi' => 'decimal:2',
        'rata_rata_kapasitas' => 'decimal:2',
        'peak_hour_utilisasi' => 'decimal:2'
    ];

    // Relasi ke dimensi
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
}

