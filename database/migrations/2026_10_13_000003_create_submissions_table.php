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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique(); // misal: TIK-202610-0001
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Informasi Konten
            $table->string('title'); // Judul kegiatan / konten
            $table->enum('content_type', ['video', 'poster']);
            $table->text('description'); // Deskripsi konten
            $table->string('pic_name'); // Nama Penanggung Jawab
            $table->string('organization')->nullable(); // Organisasi / Unit / Lembaga
            $table->string('whatsapp'); // No WhatsApp penanggung jawab
            $table->text('gdrive_link'); // Tautan Google Drive materi
            $table->integer('video_duration_seconds')->nullable(); // Durasi video dalam detik jika ada

            // Jadwal Penayangan
            $table->date('start_date');
            $table->date('end_date');

            // Status Pengajuan
            // Menunggu, Revisi, Disetujui, Ditolak, Dibatalkan, Selesai
            $table->enum('status', ['menunggu', 'revisi', 'disetujui', 'ditolak', 'dibatalkan', 'selesai'])
                ->default('menunggu');

            $table->text('admin_notes')->nullable(); // Catatan admin saat revisi / tolak / acc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
