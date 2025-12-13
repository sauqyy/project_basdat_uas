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
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            if (!Schema::hasColumn('mata_kuliahs', 'ada_praktikum')) {
                $table->boolean('ada_praktikum')->default(false)->after('tipe_kelas');
            }
            if (!Schema::hasColumn('mata_kuliahs', 'sks_praktikum')) {
                $table->integer('sks_praktikum')->default(0)->after('ada_praktikum');
            }
            if (!Schema::hasColumn('mata_kuliahs', 'sks_materi')) {
                $table->integer('sks_materi')->default(0)->after('sks_praktikum');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->dropColumn(['ada_praktikum', 'sks_praktikum', 'sks_materi']);
        });
    }
};