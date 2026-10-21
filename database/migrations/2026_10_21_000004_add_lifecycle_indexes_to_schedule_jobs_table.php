<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_jobs', function (Blueprint $table) {
            $table->index(['status', 'claimed_at'], 'schedule_jobs_status_claimed_at_index');
            $table->index(['status', 'finished_at'], 'schedule_jobs_status_finished_at_index');
            $table->index(['status', 'failed_at'], 'schedule_jobs_status_failed_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_jobs', function (Blueprint $table) {
            $table->dropIndex('schedule_jobs_status_claimed_at_index');
            $table->dropIndex('schedule_jobs_status_finished_at_index');
            $table->dropIndex('schedule_jobs_status_failed_at_index');
        });
    }
};
