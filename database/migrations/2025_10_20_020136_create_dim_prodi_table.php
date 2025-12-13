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
        Schema::create('dim_prodi', function (Blueprint $table) {
            $table->id();
            $table->string('prodi_key')->unique(); // Surrogate key
            $table->string('kode_prodi')->unique();
            $table->string('nama_prodi');
            $table->string('fakultas');
            $table->text('deskripsi')->nullable();
            $table->string('akreditasi')->nullable();
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
        Schema::dropIfExists('dim_prodi');
    }
};
