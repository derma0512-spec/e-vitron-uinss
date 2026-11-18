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
        Schema::create('videotrons', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // misal: Videotron Kampus Utama
            $table->string('location'); // misal: Depan Gedung Rektorat
            $table->string('resolution')->default('1920x1080'); // misal: 1920x1080
            $table->enum('orientation', ['landscape', 'portrait'])->default('landscape');
            $table->enum('condition', ['normal', 'gangguan', 'dalam_perbaikan'])->default('normal');
            $table->text('issue_notes')->nullable(); // Catatan gangguan
            $table->date('estimated_recovery_date')->nullable(); // Perkiraan tanggal normal kembali
            $table->boolean('is_active')->default(true); // Status aktif / nonaktif di sistem
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videotrons');
    }
};
