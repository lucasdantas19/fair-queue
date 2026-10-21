<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_jobs', function (Blueprint $table) {
            $table->unsignedInteger('worker_attempts')->default(0)->after('dispatch_attempts');
            $table->timestamp('last_attempt_at')->nullable()->after('worker_attempts');
            $table->timestamp('timed_out_at')->nullable()->after('last_attempt_at');
            $table->unsignedInteger('timeout_seconds')->nullable()->after('timed_out_at');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'worker_attempts',
                'last_attempt_at',
                'timed_out_at',
                'timeout_seconds',
            ]);
        });
    }
};
