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
        Schema::create('fact_utilisasi_ruangan', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys ke dimensi
            $table->string('ruangan_key');
            $table->string('waktu_key');
            $table->string('prodi_key');
            
            // Measures (Fakta)
            $table->integer('total_jam_penggunaan')->default(0); // dalam menit
            $table->integer('total_jam_tersedia')->default(0); // dalam menit
            $table->decimal('persentase_utilisasi', 5, 2)->nullable(); // Persentase
            $table->integer('jumlah_kelas')->default(0);
            $table->integer('jumlah_mahasiswa_total')->default(0);
            $table->decimal('rata_rata_kapasitas', 5, 2)->nullable();
            $table->decimal('peak_hour_utilisasi', 5, 2)->nullable();
            
            // Metadata
            $table->string('periode_semester')->nullable();
            $table->string('tahun_akademik')->nullable();
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index(['ruangan_key', 'waktu_key']);
            $table->index(['prodi_key', 'waktu_key']);
            $table->index('tahun_akademik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_utilisasi_ruangan');
    }
};
