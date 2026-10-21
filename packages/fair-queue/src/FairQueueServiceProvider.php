<?php

namespace FairQueue;

use Illuminate\Support\ServiceProvider;

class FairQueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fair_queue.php', 'fair_queue');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/fair_queue.php' => config_path('fair_queue.php'),
        ], 'fair-queue-config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_schedule_jobs_table.php' => database_path('migrations/'.date('Y_m_d_His').'_create_schedule_jobs_table.php'),
        ], 'fair-queue-migrations');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
