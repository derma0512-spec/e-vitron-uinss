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
        Schema::table('submission_revisions', function (Blueprint $table) {
            $table->string('revision_type', 20)->default('materi')->after('submission_id');
            $table->string('previous_surat_peminjaman')->nullable()->after('previous_gdrive_link');
            $table->string('updated_surat_peminjaman')->nullable()->after('updated_gdrive_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submission_revisions', function (Blueprint $table) {
            $table->dropColumn(['revision_type', 'previous_surat_peminjaman', 'updated_surat_peminjaman']);
        });
    }
};
