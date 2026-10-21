<?php

namespace Tests\Feature\Actions\FairQueue;

use App\Actions\FairQueue\ClaimNextScheduleJobAction;
use App\Jobs\ActionJob;
use App\Models\ScheduleJob;
use App\Models\User;
use App\StatusScheduleJobsEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClaimNextScheduleJobActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_claim_rechecks_the_pending_status_inside_the_transaction(): void
    {
        $user = User::factory()->create();
        $job = ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'group_key' => 'user',
            'group_value' => $user->id,
        ]);

        $claimed = (new ClaimNextScheduleJobAction)->execute('default');

        $this->assertSame($job->id, $claimed?->id);
        $this->assertSame(StatusScheduleJobsEnum::CLAIMED, $claimed?->status);
        $this->assertNotNull($claimed?->claimed_at);
    }

    public function test_a_claimed_job_is_not_available_to_a_second_scheduler(): void
    {
        $user = User::factory()->create();
        $job = ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'group_key' => 'user',
            'group_value' => $user->id,
        ]);
        $action = new ClaimNextScheduleJobAction;

        $this->assertSame($job->id, $action->execute('default')?->id);
        $this->assertNull($action->execute('default'));
    }
}
