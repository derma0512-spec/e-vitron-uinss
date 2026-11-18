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
        Schema::table('submissions', function (Blueprint $table) {
            $table->index('status', 'submissions_status_index');
            $table->index('created_at', 'submissions_created_at_index');
            $table->index(['start_date', 'end_date'], 'submissions_dates_index');
        });

        Schema::table('videotrons', function (Blueprint $table) {
            $table->index('condition', 'videotrons_condition_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex('submissions_status_index');
            $table->dropIndex('submissions_created_at_index');
            $table->dropIndex('submissions_dates_index');
        });

        Schema::table('videotrons', function (Blueprint $table) {
            $table->dropIndex('videotrons_condition_index');
        });
    }
};
