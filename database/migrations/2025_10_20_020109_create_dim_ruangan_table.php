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
        Schema::create('dim_ruangan', function (Blueprint $table) {
            $table->id();
            $table->string('ruangan_key')->unique(); // Surrogate key
            $table->string('kode_ruangan')->unique();
            $table->string('nama_ruangan');
            $table->integer('kapasitas');
            $table->string('tipe_ruangan'); // kelas, lab, auditorium
            $table->text('fasilitas')->nullable();
            $table->string('prodi');
            $table->boolean('status')->default(true); // true = tersedia, false = tidak tersedia
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
        Schema::dropIfExists('dim_ruangan');
    }
};
