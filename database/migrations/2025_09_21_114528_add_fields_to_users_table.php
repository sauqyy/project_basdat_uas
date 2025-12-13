<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('mahasiswa'); // admin, dosen, mahasiswa
            $table->string('nim')->nullable();
            $table->string('nip')->nullable();
            $table->string('judul_skripsi')->nullable();
            $table->string('profile_picture')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role','nim','nip','judul_skripsi','profile_picture']);
        });
    }
};
