<?php

namespace Tests\Feature\Actions\FairQueue;

use App\Actions\FairQueue\ScheduleJobAction;
use App\Jobs\ActionJob;
use App\Models\ScheduleJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleJobActionTest extends TestCase
{
    use RefreshDatabase;

    protected ScheduleJobAction $scheduleJobAction;
    protected User $user1;
    protected User $user2;

    public function setUp(): void
    {
        parent::setUp();

        $this->scheduleJobAction = new ScheduleJobAction();

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
    }

    public function test_action_schedule_job()
    {
        $jobAction = new ActionJob($this->user1, 60);
        $groupKey = 'user';

        $scheduleJobAction = $this->scheduleJobAction->execute(
            $jobAction,
            $groupKey,
            $this->user1->id
        );

        $scheduleJobActionFromDB = ScheduleJob::find($scheduleJobAction->id);

        $this->assertEquals(serialize($jobAction), $scheduleJobActionFromDB->payload);
        $this->assertEquals($groupKey, $scheduleJobActionFromDB->group_key);
        $this->assertEquals($this->user1->id, $scheduleJobActionFromDB->group_value);
    }
}
