<?php

namespace FairQueue\Listeners;

use FairQueue\Events\ScheduleJobCompleted;
use FairQueue\Events\ScheduleJobFailed;
use FairQueue\Persistence\EloquentScheduleJob;
use FairQueue\StatusScheduleJobsEnum;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobTimedOut;

class ScheduleJobLifecycleListener
{
    /** @param class-string<EloquentScheduleJob> $modelClass */
    public function __construct(protected string $modelClass = EloquentScheduleJob::class) {}

    public function processing(JobProcessing $event): void
    {
        $this->update($event->job->payload(), [
            'status' => StatusScheduleJobsEnum::RUNNING,
            'started_at' => now(),
            'worker_attempts' => $this->attempts($event->job),
            'last_attempt_at' => now(),
        ], [
            StatusScheduleJobsEnum::DISPATCHED,
            StatusScheduleJobsEnum::CLAIMED,
            StatusScheduleJobsEnum::RUNNING,
        ]);
    }

    public function processed(JobProcessed $event): void
    {
        $updated = $this->update($event->job->payload(), [
            'status' => StatusScheduleJobsEnum::FINISHED,
            'finished_at' => now(),
        ], [StatusScheduleJobsEnum::RUNNING]);

        if ($updated !== null) {
            event($this->completedEvent($updated));
        }
    }

    public function failed(JobFailed $event): void
    {
        $updated = $this->update($event->job->payload(), [
            'status' => StatusScheduleJobsEnum::FAILED,
            'failed_at' => now(),
            'last_error' => $event->exception->getMessage(),
        ], [
            StatusScheduleJobsEnum::CLAIMED,
            StatusScheduleJobsEnum::DISPATCHED,
            StatusScheduleJobsEnum::RUNNING,
        ]);

        if ($updated !== null) {
            event($this->failedEvent($updated, $event->exception));
        }
    }

    public function exceptionOccurred(JobExceptionOccurred $event): void
    {
        $this->update($event->job->payload(), [
            'worker_attempts' => $this->attempts($event->job),
            'last_attempt_at' => now(),
            'last_error' => $event->exception->getMessage(),
        ], [StatusScheduleJobsEnum::RUNNING]);
    }

    public function timedOut(JobTimedOut $event): void
    {
        $message = 'Worker timeout exceeded';

        if ($event->timeout !== null) {
            $message .= " after {$event->timeout} seconds";
        }

        $this->update($event->job->payload(), [
            'worker_attempts' => $this->attempts($event->job),
            'last_attempt_at' => now(),
            'timed_out_at' => now(),
            'timeout_seconds' => $event->timeout,
            'last_error' => $message,
        ], [StatusScheduleJobsEnum::RUNNING]);
    }

    protected function completedEvent(EloquentScheduleJob $scheduleJob): object
    {
        return new ScheduleJobCompleted($scheduleJob);
    }

    protected function failedEvent(EloquentScheduleJob $scheduleJob, \Throwable $exception): object
    {
        return new ScheduleJobFailed($scheduleJob, $exception);
    }

    protected function update(array $payload, array $attributes, array $allowedStatuses = []): ?EloquentScheduleJob
    {
        $scheduleJobId = $payload['fair_queue']['schedule_job_id'] ?? null;

        if ($scheduleJobId === null) {
            return null;
        }

        $query = ($this->modelClass)::query()
            ->whereKey($scheduleJobId)
            ->where(function ($query) use ($payload): void {
                if (isset($payload['fair_queue']['dispatch_token'])) {
                    $query->where(
                        'dispatch_token',
                        $payload['fair_queue']['dispatch_token'],
                    );
                }
            });

        if ($allowedStatuses !== []) {
            $query->whereIn(
                'status',
                array_map(
                    static fn (StatusScheduleJobsEnum $status): string => $status->value,
                    $allowedStatuses,
                ),
            );
        }

        if ($query->update($attributes) === 0) {
            return null;
        }

        return ($this->modelClass)::query()->find($scheduleJobId);
    }

    private function attempts(object $job): int
    {
        return method_exists($job, 'attempts')
            ? (int) $job->attempts()
            : 0;
    }
}
