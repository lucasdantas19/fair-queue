<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_jobs', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('dispatched_at');
            $table->timestamp('failed_at')->nullable()->after('finished_at');
            $table->text('last_error')->nullable()->after('failed_at');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_jobs', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'failed_at', 'last_error']);
        });
    }
};
