<?php

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WatchSheduleJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_watcher_can_run_a_single_iteration(): void
    {
        $this->artisan('app:watch-shedule-jobs', ['--once' => true])
            ->assertExitCode(0);
    }

    public function test_watcher_can_run_a_configured_number_of_iterations(): void
    {
        config([
            'fair_queue.watcher.max_iterations' => 2,
            'fair_queue.watcher.sleep_seconds' => 0,
        ]);

        $this->artisan('app:watch-shedule-jobs')
            ->assertExitCode(0);
    }
}
