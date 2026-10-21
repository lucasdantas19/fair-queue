<?php

namespace FairQueue\Actions;

use FairQueue\Events\ScheduleJobDispatched;
use FairQueue\Jobs\ExecuteScheduledJob;
use FairQueue\Persistence\EloquentScheduleJob;
use FairQueue\StatusScheduleJobsEnum;
use Illuminate\Support\Str;

class DispatchScheduleJobAction
{
    /** @param class-string<ExecuteScheduledJob> $jobClass */
    public function __construct(
        private readonly ClaimNextScheduleJobAction $claimer,
        private readonly string $jobClass = ExecuteScheduledJob::class,
    ) {}

    public function execute(string $queue): ?EloquentScheduleJob
    {
        $scheduleJob = $this->claimer->execute($queue);

        if ($scheduleJob === null) {
            return null;
        }

        try {
            if ($scheduleJob->dispatch_token === null) {
                $scheduleJob->update([
                    'dispatch_token' => (string) Str::uuid(),
                ]);
                $scheduleJob->refresh();
            }

            $scheduleJob->increment('dispatch_attempts');
            $scheduleJob->update([
                'last_dispatch_at' => now(),
                'last_dispatch_error' => null,
            ]);

            $this->beforeDispatch($scheduleJob);

            dispatch(new ($this->jobClass)(
                $scheduleJob->id,
                $scheduleJob->dispatch_token,
                $scheduleJob->payload,
            ))->onQueue($scheduleJob->queue);

            $scheduleJob->update([
                'status' => StatusScheduleJobsEnum::DISPATCHED,
                'dispatched_at' => now(),
            ]);

            $scheduleJob = $scheduleJob->fresh();
            event($this->dispatchedEvent($scheduleJob));

            return $scheduleJob;
        } catch (\Throwable $exception) {
            $scheduleJob->update([
                'status' => StatusScheduleJobsEnum::PENDING,
                'claimed_at' => null,
                'last_dispatch_error' => $exception->getMessage(),
            ]);

            throw $exception;
        } finally {
            $this->afterDispatch($scheduleJob);
        }
    }

    protected function beforeDispatch(EloquentScheduleJob $scheduleJob): void {}

    protected function afterDispatch(EloquentScheduleJob $scheduleJob): void {}

    protected function dispatchedEvent(EloquentScheduleJob $scheduleJob): object
    {
        return new ScheduleJobDispatched($scheduleJob);
    }
}
