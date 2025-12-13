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
        Schema::create('fact_kecocokan_jadwal', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys ke dimensi
            $table->string('dosen_key');
            $table->string('preferensi_key');
            $table->string('waktu_key');
            
            // Measures (Fakta)
            $table->boolean('preferensi_hari_terpenuhi')->default(false);
            $table->boolean('preferensi_jam_terpenuhi')->default(false);
            $table->integer('skor_kecocokan')->default(0); // 0-100
            $table->integer('prioritas_preferensi')->nullable(); // 1-5
            $table->integer('jumlah_preferensi_total')->default(0);
            $table->integer('jumlah_preferensi_terpenuhi')->default(0);
            $table->decimal('persentase_kecocokan', 5, 2)->nullable(); // Persentase
            $table->text('catatan_kecocokan')->nullable();
            
            // Metadata
            $table->string('semester')->nullable();
            $table->string('tahun_akademik')->nullable();
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index(['dosen_key', 'waktu_key']);
            $table->index(['preferensi_key', 'waktu_key']);
            $table->index('skor_kecocokan');
            $table->index('tahun_akademik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fact_kecocokan_jadwal');
    }
};
