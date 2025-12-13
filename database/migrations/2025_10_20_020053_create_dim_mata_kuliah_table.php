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
        Schema::create('dim_mata_kuliah', function (Blueprint $table) {
            $table->id();
            $table->string('mata_kuliah_key')->unique(); // Surrogate key
            $table->string('kode_mk')->unique();
            $table->string('nama_mk');
            $table->integer('sks');
            $table->string('semester');
            $table->string('prodi');
            $table->integer('kapasitas');
            $table->text('deskripsi')->nullable();
            $table->string('tipe_kelas');
            $table->integer('menit_per_sks')->nullable();
            $table->boolean('ada_praktikum')->default(false);
            $table->integer('sks_praktikum')->nullable();
            $table->integer('sks_materi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dim_mata_kuliah');
    }
};
