<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ActionJob implements ShouldQueue
{
    use Queueable;

    protected User $user;

    protected int $time;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, int $time)
    {
        $this->user = $user;
        $this->time = $time;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Action started for user: {$this->user->id} with a delay of {$this->time} seconds.");

        // Simulate a long-running task
        sleep($this->time);
        dump("Action completed for user: {$this->user->id} after {$this->time} seconds.");

        // Perform the action for the user
        // For example, you can log the action or update the user's status
        Log::info("Action completed for user: {$this->user->id} after {$this->time} seconds.");
    }
}
