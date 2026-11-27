<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_super_admin')->default(false)->after('role');
        });

        // Promosikan admin aktif pertama yang sudah ada agar instalasi eksisting memiliki Super Admin
        $hasSuperAdmin = DB::table('users')
            ->where('role', 'admin')
            ->where('is_super_admin', true)
            ->exists();

        if (! $hasSuperAdmin) {
            $firstAdminId = DB::table('users')
                ->where('role', 'admin')
                ->where(function ($query) {
                    $query->whereNull('account_status')
                        ->orWhere('account_status', 'approved');
                })
                ->orderBy('id')
                ->value('id');

            if ($firstAdminId !== null) {
                DB::table('users')
                    ->where('id', $firstAdminId)
                    ->update(['is_super_admin' => true]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });
    }
};
