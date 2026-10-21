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
        Schema::create('schedule_jobs', function (Blueprint $table) {
            $table->id();

            $table->longText('payload');

            $table->string('queue')->default('default');

            $table->string('status')->default('pending');

            $table->string('group_key');
            $table->string('group_value');

            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();

            $table->index(['queue', 'status', 'created_at']);
            $table->index(['queue', 'group_key', 'group_value', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_jobs');
    }
};
