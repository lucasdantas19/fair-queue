<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_jobs', function (Blueprint $table) {
            $table->timestamp('claimed_at')->nullable()->after('group_value');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_jobs', function (Blueprint $table) {
            $table->dropColumn('claimed_at');
        });
    }
};
