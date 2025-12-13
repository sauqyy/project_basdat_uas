<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fact_jadwal', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys ke dimensi
            $table->string('dosen_key');
            $table->string('mata_kuliah_key');
            $table->string('ruangan_key');
            $table->string('waktu_key');
            $table->string('prodi_key');
            $table->string('preferensi_key')->nullable();
            
            // Measures (Fakta)
            $table->integer('jumlah_sks');
            $table->integer('durasi_menit');
            $table->integer('kapasitas_kelas');
            $table->integer('jumlah_mahasiswa');
            $table->decimal('utilisasi_ruangan', 5, 2)->nullable(); // Persentase
            $table->integer('prioritas_preferensi')->nullable();
            $table->boolean('konflik_jadwal')->default(false);
            $table->integer('tingkat_konflik')->nullable(); // 0-5
            
            // Status dan metadata
            $table->boolean('status_aktif')->default(true);
            $table->timestamp('created_at_jadwal');
            $table->timestamp('updated_at_jadwal');
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index(['dosen_key', 'waktu_key']);
            $table->index(['ruangan_key', 'waktu_key']);
            $table->index(['mata_kuliah_key', 'prodi_key']);
            $table->index('status_aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_jadwal');
    }
};
