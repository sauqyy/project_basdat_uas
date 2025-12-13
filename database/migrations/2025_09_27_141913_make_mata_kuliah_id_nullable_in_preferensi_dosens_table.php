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
        Schema::table('preferensi_dosens', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['mata_kuliah_id']);
            
            // Make mata_kuliah_id nullable
            $table->foreignId('mata_kuliah_id')->nullable()->change();
            
            // Re-add the foreign key constraint with nullable
            $table->foreign('mata_kuliah_id')->references('id')->on('mata_kuliahs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preferensi_dosens', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['mata_kuliah_id']);
            
            // Make mata_kuliah_id not nullable again
            $table->foreignId('mata_kuliah_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('mata_kuliah_id')->references('id')->on('mata_kuliahs')->onDelete('cascade');
        });
    }
};