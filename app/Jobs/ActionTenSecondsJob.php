<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ActionTenSecondsJob implements ShouldQueue
{
    use Queueable;

    const DELAY = 10;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("ActionTenSecondsJob started. This job will take " . self::DELAY . " seconds to complete.");

        sleep(self::DELAY);

        Log::info("ActionTenSecondsJob completed after " . self::DELAY . " seconds.");
    }
}
