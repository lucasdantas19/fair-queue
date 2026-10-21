<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_jobs', function (Blueprint $table): void {
            $table->id();
            $table->uuid('dispatch_token')->nullable()->unique();
            $table->longText('payload');
            $table->string('queue')->default('default');
            $table->string('status')->default('pending');
            $table->string('group_key');
            $table->string('group_value');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();
            $table->unsignedInteger('dispatch_attempts')->default(0);
            $table->timestamp('last_dispatch_at')->nullable();
            $table->text('last_dispatch_error')->nullable();
            $table->unsignedInteger('worker_attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('timed_out_at')->nullable();
            $table->unsignedInteger('timeout_seconds')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['queue', 'status', 'created_at']);
            $table->index(['queue', 'group_key', 'group_value', 'status']);
            $table->index(['status', 'claimed_at']);
            $table->index(['status', 'finished_at']);
            $table->index(['status', 'failed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_jobs');
    }
};
