<?php

namespace App\Console\Commands;

use App\Actions\FairQueue\DispatchScheduleJobAction;
use App\Models\ScheduleJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Sleep;

#[Signature('app:watch-shedule-jobs {--once : Run one scheduling iteration and exit}')]
#[Description('Dispatch eligible Fair Queue jobs')]
class WatchSheduleJobs extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dispatchScheduleJobAction = new DispatchScheduleJobAction;
        $iterations = $this->option('once')
            ? 1
            : $this->maxIterations();
        $iteration = 0;

        do {
            $queues = ScheduleJob::query()
                ->where('status', 'pending')
                ->groupBy('queue')
                ->selectRaw('queue, COUNT(*) as pending_count')
                ->get();

            foreach ($queues as $queue) {
                $dispatchScheduleJobAction->execute($queue->queue);
            }

            $iteration++;

            if ($iterations === null || $iteration < $iterations) {
                Sleep::for($this->sleepSeconds())->seconds();
            }
        } while ($iterations === null || $iteration < $iterations);

        return self::SUCCESS;
    }

    private function maxIterations(): ?int
    {
        $iterations = config('fair_queue.watcher.max_iterations');

        return $iterations === null || $iterations === ''
            ? null
            : max(1, (int) $iterations);
    }

    private function sleepSeconds(): int
    {
        return max(0, (int) config('fair_queue.watcher.sleep_seconds', 1));
    }
}
