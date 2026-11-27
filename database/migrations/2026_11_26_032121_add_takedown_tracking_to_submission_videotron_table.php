<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom tracking penurunan konten fisik per titik videotron pada pivot submission_videotron.
     */
    public function up(): void
    {
        Schema::table('submission_videotron', function (Blueprint $table) {
            $table->timestamp('taken_down_at')->nullable()->after('playback_status');
            $table->foreignId('taken_down_by')
                ->nullable()
                ->after('taken_down_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['videotron_id', 'taken_down_at'], 'idx_sub_vtr_takedown');
            $table->index(['submission_id', 'taken_down_at'], 'idx_sub_vtr_sub_takedown');
        });
    }

    /**
     * Batalkan penambahan kolom tracking penurunan konten fisik.
     */
    public function down(): void
    {
        Schema::table('submission_videotron', function (Blueprint $table) {
            $table->dropIndex('idx_sub_vtr_takedown');
            $table->dropIndex('idx_sub_vtr_sub_takedown');
            $table->dropConstrainedForeignId('taken_down_by');
            $table->dropColumn('taken_down_at');
        });
    }
};
