<?php

namespace Tests\Feature\Actions\FairQueue;

use App\Jobs\ActionJob;
use App\Listeners\ScheduleJobLifecycleListener;
use App\Models\ScheduleJob;
use App\Models\User;
use App\StatusScheduleJobsEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobTimedOut;
use Tests\TestCase;

class ScheduleJobLifecycleListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_the_schedule_job_when_the_worker_processes_it(): void
    {
        $scheduleJob = $this->createScheduleJob();
        $queueJob = $this->queueJob($scheduleJob->id);
        $listener = new ScheduleJobLifecycleListener;

        $listener->processing(new JobProcessing('sync', $queueJob));
        $this->assertSame(StatusScheduleJobsEnum::RUNNING, $scheduleJob->fresh()->status);
        $this->assertNotNull($scheduleJob->fresh()->started_at);
        $this->assertSame(2, $scheduleJob->fresh()->worker_attempts);

        $listener->processed(new JobProcessed('sync', $queueJob));
        $this->assertSame(StatusScheduleJobsEnum::FINISHED, $scheduleJob->fresh()->status);
        $this->assertNotNull($scheduleJob->fresh()->finished_at);
    }

    public function test_records_the_error_when_the_worker_fails_the_job(): void
    {
        $scheduleJob = $this->createScheduleJob();
        $queueJob = $this->queueJob($scheduleJob->id);
        $listener = new ScheduleJobLifecycleListener;

        $listener->failed(new JobFailed('sync', $queueJob, new \RuntimeException('Something failed')));

        $fresh = $scheduleJob->fresh();
        $this->assertSame(StatusScheduleJobsEnum::FAILED, $fresh->status);
        $this->assertNotNull($fresh->failed_at);
        $this->assertSame('Something failed', $fresh->last_error);
    }

    public function test_records_an_exception_that_will_be_retried_without_releasing_capacity(): void
    {
        $scheduleJob = $this->createScheduleJob();
        $queueJob = $this->queueJob($scheduleJob->id);
        $listener = new ScheduleJobLifecycleListener;

        $listener->processing(new JobProcessing('sync', $queueJob));
        $listener->exceptionOccurred(new JobExceptionOccurred(
            'sync',
            $queueJob,
            new \RuntimeException('Retry me'),
        ));

        $fresh = $scheduleJob->fresh();
        $this->assertSame(StatusScheduleJobsEnum::RUNNING, $fresh->status);
        $this->assertSame(2, $fresh->worker_attempts);
        $this->assertSame('Retry me', $fresh->last_error);
    }

    public function test_records_a_worker_timeout(): void
    {
        $scheduleJob = $this->createScheduleJob();
        $queueJob = $this->queueJob($scheduleJob->id);
        $listener = new ScheduleJobLifecycleListener;

        $listener->processing(new JobProcessing('sync', $queueJob));
        $listener->timedOut(new JobTimedOut('sync', $queueJob, 30));

        $fresh = $scheduleJob->fresh();
        $this->assertSame(StatusScheduleJobsEnum::RUNNING, $fresh->status);
        $this->assertSame(30, $fresh->timeout_seconds);
        $this->assertNotNull($fresh->timed_out_at);
        $this->assertSame('Worker timeout exceeded after 30 seconds', $fresh->last_error);
    }

    private function createScheduleJob(): ScheduleJob
    {
        $user = User::factory()->create();

        return ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'status' => StatusScheduleJobsEnum::DISPATCHED,
            'group_key' => 'user',
            'group_value' => $user->id,
        ]);
    }

    private function queueJob(int $scheduleJobId): object
    {
        return new class($scheduleJobId)
        {
            public function __construct(private readonly int $scheduleJobId) {}

            public function payload(): array
            {
                return ['fair_queue' => ['schedule_job_id' => $this->scheduleJobId]];
            }

            public function attempts(): int
            {
                return 2;
            }
        };
    }
}
