<?php

namespace Tests\Feature\Actions\FairQueue;

use App\Actions\FairQueue\PruneScheduleJobsAction;
use App\Jobs\ActionJob;
use App\Models\ScheduleJob;
use App\Models\User;
use App\StatusScheduleJobsEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PruneScheduleJobsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_prunes_finished_and_failed_jobs_after_their_retention_period(): void
    {
        config([
            'fair_queue.retention.finished_days' => 30,
            'fair_queue.retention.failed_days' => 90,
        ]);
        $user = User::factory()->create();

        $finished = $this->createJob(StatusScheduleJobsEnum::FINISHED, $user);
        $finished->update(['finished_at' => now()->subDays(31)]);
        $failed = $this->createJob(StatusScheduleJobsEnum::FAILED, $user);
        $failed->update(['failed_at' => now()->subDays(91)]);
        $recent = $this->createJob(StatusScheduleJobsEnum::FINISHED, $user);
        $recent->update(['finished_at' => now()->subDays(2)]);

        $pruned = (new PruneScheduleJobsAction)->execute();

        $this->assertSame(2, $pruned);
        $this->assertDatabaseMissing('schedule_jobs', ['id' => $finished->id]);
        $this->assertDatabaseMissing('schedule_jobs', ['id' => $failed->id]);
        $this->assertDatabaseHas('schedule_jobs', ['id' => $recent->id]);
    }

    public function test_dry_run_counts_without_deleting(): void
    {
        config(['fair_queue.retention.finished_days' => 30]);
        $user = User::factory()->create();
        $job = $this->createJob(StatusScheduleJobsEnum::FINISHED, $user);
        $job->update(['finished_at' => now()->subDays(31)]);

        $count = (new PruneScheduleJobsAction)->execute(dryRun: true);

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('schedule_jobs', ['id' => $job->id]);
    }

    private function createJob(StatusScheduleJobsEnum $status, User $user): ScheduleJob
    {
        return ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'status' => $status,
            'group_key' => 'user',
            'group_value' => $user->id,
        ]);
    }
}
