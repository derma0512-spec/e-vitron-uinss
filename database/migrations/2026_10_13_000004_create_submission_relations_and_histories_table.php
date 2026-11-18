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
        // Relasi pengajuan dengan perangkat videotron
        Schema::create('submission_videotron', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('videotron_id')->constrained()->cascadeOnDelete();
            $table->enum('playback_status', ['terjadwal', 'tayang', 'selesai', 'terganggu'])->default('terjadwal');
            $table->timestamps();
        });

        // Riwayat revisi jika admin meminta perbaikan konten
        Schema::create('submission_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->text('admin_feedback'); // Catatan permintaan revisi dari admin
            $table->text('user_reply')->nullable(); // Catatan perbaikan dari pengaju
            $table->text('previous_gdrive_link')->nullable(); // Link sebelum direvisi
            $table->text('updated_gdrive_link')->nullable(); // Link baru setelah direvisi
            $table->timestamp('requested_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // Log riwayat perubahan status pengajuan (audit trail)
        Schema::create('submission_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // Siapa aktornya (Admin / Pengaju)
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submission_status_histories');
        Schema::dropIfExists('submission_revisions');
        Schema::dropIfExists('submission_videotron');
    }
};
