<?php

namespace FairQueue\Jobs;

use FairQueue\Persistence\EloquentScheduleJob;
use FairQueue\StatusScheduleJobsEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExecuteScheduledJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $scheduleJobId,
        public readonly string $dispatchToken,
        public readonly string $payload,
    ) {}

    public function handle(): void
    {
        $scheduleJob = EloquentScheduleJob::query()
            ->whereKey($this->scheduleJobId)
            ->where('dispatch_token', $this->dispatchToken)
            ->where('status', StatusScheduleJobsEnum::RUNNING->value)
            ->first();

        if ($scheduleJob === null) {
            return;
        }

        dispatch_sync(unserialize($this->payload));
    }

    public function uniqueId(): string
    {
        return $this->dispatchToken;
    }

    public function uniqueFor(): int
    {
        return (int) config('fair_queue.dispatch_unique_for_seconds', 86400);
    }

    public function tags(): array
    {
        return [
            'fair-queue',
            "schedule-job:{$this->scheduleJobId}",
            "dispatch-token:{$this->dispatchToken}",
        ];
    }
}
