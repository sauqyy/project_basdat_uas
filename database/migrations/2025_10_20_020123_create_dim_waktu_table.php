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
        Schema::create('dim_waktu', function (Blueprint $table) {
            $table->id();
            $table->string('waktu_key')->unique(); // Surrogate key
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('semester');
            $table->integer('tahun_akademik');
            $table->string('periode'); // Ganjil/Genap
            $table->integer('hari_ke'); // 1-5 untuk Senin-Jumat
            $table->string('slot_waktu'); // Pagi/Siang/Sore
            $table->integer('durasi_menit'); // Durasi dalam menit
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dim_waktu');
    }
};
