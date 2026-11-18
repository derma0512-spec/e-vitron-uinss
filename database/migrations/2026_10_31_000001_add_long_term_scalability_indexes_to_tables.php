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
            $table->index(['user_id', 'status', 'created_at'], 'submissions_user_status_created_idx');
            $table->index(['status', 'start_date', 'end_date'], 'submissions_status_schedule_idx');
            $table->index('updated_at', 'submissions_updated_at_index');
        });

        Schema::table('submission_videotron', function (Blueprint $table) {
            $table->index(['videotron_id', 'submission_id'], 'submission_videotron_vid_sub_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id', 'read_at', 'created_at'], 'notifications_notifiable_read_created_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'users_role_index');
        });

        Schema::table('videotrons', function (Blueprint $table) {
            $table->index(['is_active', 'condition'], 'videotrons_active_condition_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex('submissions_user_status_created_idx');
            $table->dropIndex('submissions_status_schedule_idx');
            $table->dropIndex('submissions_updated_at_index');
        });

        Schema::table('submission_videotron', function (Blueprint $table) {
            $table->dropIndex('submission_videotron_vid_sub_idx');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_notifiable_read_created_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
        });

        Schema::table('videotrons', function (Blueprint $table) {
            $table->dropIndex('videotrons_active_condition_idx');
        });
    }
};
