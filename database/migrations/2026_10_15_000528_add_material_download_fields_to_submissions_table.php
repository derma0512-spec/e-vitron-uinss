<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan status persiapan materi (Belum Diunduh / Sudah Diunduh)
     * yang terpisah dari status keputusan pengajuan. Kolom bersifat nullable
     * sehingga seluruh data lama otomatis berstatus "Belum Diunduh".
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->timestamp('material_downloaded_at')->nullable()->after('admin_notes');
            $table->foreignId('material_downloaded_by')->nullable()->after('material_downloaded_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('material_downloaded_by');
            $table->dropColumn('material_downloaded_at');
        });
    }
};
