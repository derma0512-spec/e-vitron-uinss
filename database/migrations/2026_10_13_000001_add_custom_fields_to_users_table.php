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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('mahasiswa')->after('email'); // admin, mahasiswa, dosen
            $table->string('identifier')->nullable()->unique()->after('role'); // NIM atau NIP
            $table->string('phone')->nullable()->after('identifier'); // No WhatsApp
            $table->string('unit_or_prodi')->nullable()->after('phone'); // Prodi / Fakultas / Unit Kerja
            $table->string('auth_type')->default('local')->after('unit_or_prodi'); // local, campus_sso
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'identifier', 'phone', 'unit_or_prodi', 'auth_type']);
        });
    }
};
