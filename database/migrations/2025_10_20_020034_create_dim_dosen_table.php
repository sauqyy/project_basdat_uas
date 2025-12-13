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
        Schema::create('dim_dosen', function (Blueprint $table) {
            $table->id();
            $table->string('dosen_key')->unique(); // Surrogate key
            $table->string('nip')->unique();
            $table->string('nama_dosen');
            $table->string('email');
            $table->string('prodi');
            $table->string('role');
            $table->text('profile_picture')->nullable();
            $table->text('judul_skripsi')->nullable();
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
        Schema::dropIfExists('dim_dosen');
    }
};
