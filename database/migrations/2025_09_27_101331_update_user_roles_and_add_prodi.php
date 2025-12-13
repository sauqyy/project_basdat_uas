<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Update role to support new admin types
            $table->string('role')->default('dosen')->change(); // admin_super, admin_prodi, dosen
            
            // Add prodi column only if it doesn't exist
            if (!Schema::hasColumn('users', 'prodi')) {
                $table->string('prodi')->nullable(); // For admin_prodi to specify which prodi they manage
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('mahasiswa')->change();
            $table->dropColumn('prodi');
        });
    }
};