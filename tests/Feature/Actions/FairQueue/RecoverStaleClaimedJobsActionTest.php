<?php

namespace Tests\Feature\Actions\FairQueue;

use App\Actions\FairQueue\RecoverStaleClaimedJobsAction;
use App\Jobs\ActionJob;
use App\Models\ScheduleJob;
use App\Models\User;
use App\StatusScheduleJobsEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecoverStaleClaimedJobsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_recovers_claimed_jobs_older_than_the_configured_timeout(): void
    {
        config(['fair_queue.claimed_timeout_seconds' => 300]);
        $user = User::factory()->create();

        $stale = ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'status' => StatusScheduleJobsEnum::CLAIMED,
            'claimed_at' => now()->subMinutes(10),
        ]);
        $recent = ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'status' => StatusScheduleJobsEnum::CLAIMED,
            'claimed_at' => now()->subMinute(),
        ]);

        $recovered = (new RecoverStaleClaimedJobsAction())->execute();

        $this->assertSame(1, $recovered);
        $this->assertSame(StatusScheduleJobsEnum::PENDING, $stale->fresh()->status);
        $this->assertNull($stale->fresh()->claimed_at);
        $this->assertSame(StatusScheduleJobsEnum::CLAIMED, $recent->fresh()->status);
    }

    public function test_can_recover_only_one_queue(): void
    {
        config(['fair_queue.claimed_timeout_seconds' => 300]);
        $user = User::factory()->create();

        ScheduleJob::factory(2)->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'status' => StatusScheduleJobsEnum::CLAIMED,
            'claimed_at' => now()->subMinutes(10),
        ])->each(function (ScheduleJob $job, int $index) {
            $job->update(['queue' => $index === 0 ? 'default' : 'priority']);
        });

        $recovered = (new RecoverStaleClaimedJobsAction())->execute('priority');

        $this->assertSame(1, $recovered);
        $this->assertSame(StatusScheduleJobsEnum::CLAIMED, ScheduleJob::where('queue', 'default')->first()->status);
        $this->assertSame(StatusScheduleJobsEnum::PENDING, ScheduleJob::where('queue', 'priority')->first()->status);
    }
}
