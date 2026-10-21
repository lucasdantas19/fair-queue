<?php

namespace Tests\Feature\Actions\FairQueue;

use App\Actions\FairQueue\DispatchScheduleJobAction;
use App\Jobs\ActionJob;
use App\Jobs\ExecuteScheduledJob;
use App\Models\ScheduleJob;
use App\Models\User;
use App\StatusScheduleJobsEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DispatchScheduleJobActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatches_a_claimed_job_and_updates_its_status(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        $job = new ActionJob($user, 10);

        $scheduleJob = ScheduleJob::factory()->create([
            'payload' => serialize($job),
            'group_key' => 'user',
            'group_value' => $user->id,
        ]);

        $dispatched = (new DispatchScheduleJobAction)->execute('default');

        $this->assertSame($scheduleJob->id, $dispatched?->id);
        $this->assertSame(StatusScheduleJobsEnum::DISPATCHED, $dispatched?->status);
        $this->assertNotNull($dispatched?->claimed_at);
        $this->assertNotNull($dispatched?->dispatched_at);
        $this->assertSame(1, $dispatched?->dispatch_attempts);
        $this->assertNotNull($dispatched?->dispatch_token);
        Queue::assertPushed(ExecuteScheduledJob::class, 1);
    }

    public function test_a_claimed_job_cannot_be_dispatched_twice(): void
    {
        Queue::fake();
        $user = User::factory()->create();

        ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'group_key' => 'user',
            'group_value' => $user->id,
        ]);

        $action = new DispatchScheduleJobAction;

        $this->assertNotNull($action->execute('default'));
        $this->assertNull($action->execute('default'));
        Queue::assertPushed(ExecuteScheduledJob::class, 1);
    }
}
