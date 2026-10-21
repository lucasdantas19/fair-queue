<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_jobs', function (Blueprint $table) {
            $table->uuid('dispatch_token')->nullable()->unique()->after('id');
            $table->unsignedInteger('dispatch_attempts')->default(0)->after('dispatched_at');
            $table->timestamp('last_dispatch_at')->nullable()->after('dispatch_attempts');
            $table->text('last_dispatch_error')->nullable()->after('last_dispatch_at');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_jobs', function (Blueprint $table) {
            $table->dropUnique(['dispatch_token']);
            $table->dropColumn([
                'dispatch_token',
                'dispatch_attempts',
                'last_dispatch_at',
                'last_dispatch_error',
            ]);
        });
    }
};
