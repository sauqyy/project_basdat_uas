<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            if (!Schema::hasColumn('mata_kuliahs', 'tipe_kelas')) {
                $table->string('tipe_kelas')->default('teori'); // teori, praktikum
            }
            if (!Schema::hasColumn('mata_kuliahs', 'menit_per_sks')) {
                $table->integer('menit_per_sks')->default(50); // Custom minutes per SKS
            }
            if (!Schema::hasColumn('mata_kuliahs', 'prodi')) {
                $table->string('prodi')->default('Teknik Informatika'); // Prodi assignment
            }
        });
    }

    public function down(): void
    {
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->dropColumn(['tipe_kelas', 'menit_per_sks', 'prodi']);
        });
    }
};