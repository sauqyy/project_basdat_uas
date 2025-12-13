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
        Schema::create('preferensi_dosens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dosen_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliahs')->onDelete('cascade');
            $table->json('preferensi_hari'); // array hari yang dipilih ['Senin', 'Rabu']
            $table->json('preferensi_jam'); // array jam yang dipilih ['08:00-10:00', '10:00-12:00']
            $table->integer('prioritas')->default(1); // 1 = tinggi, 2 = sedang, 3 = rendah
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preferensi_dosens');
    }
};
