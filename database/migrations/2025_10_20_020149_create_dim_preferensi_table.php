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
        Schema::create('dim_preferensi', function (Blueprint $table) {
            $table->id();
            $table->string('preferensi_key')->unique(); // Surrogate key
            $table->string('dosen_id');
            $table->string('mata_kuliah_id')->nullable(); // Nullable untuk support preferensi global
            $table->json('preferensi_hari'); // Array hari yang dipilih
            $table->json('preferensi_jam'); // Array jam yang dipilih
            $table->integer('prioritas'); // 1-5 (1 = paling prioritas)
            $table->text('catatan')->nullable();
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
        Schema::dropIfExists('dim_preferensi');
    }
};
