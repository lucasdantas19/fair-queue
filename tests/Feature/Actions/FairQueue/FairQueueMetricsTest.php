<?php

namespace Tests\Feature\Actions\FairQueue;

use App\Actions\FairQueue\FairQueueMetrics;
use App\Jobs\ActionJob;
use App\Models\ScheduleJob;
use App\Models\User;
use App\StatusScheduleJobsEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FairQueueMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_reports_state_wait_throughput_and_group_distribution(): void
    {
        config(['fair_queue.observability.window_minutes' => 60]);
        $user = User::factory()->create();
        $this->createJob(StatusScheduleJobsEnum::PENDING, $user);
        $this->createJob(StatusScheduleJobsEnum::RUNNING, $user);
        $finished = $this->createJob(StatusScheduleJobsEnum::FINISHED, $user);
        $finished->update([
            'created_at' => now()->subMinutes(10),
            'started_at' => now()->subMinutes(5),
            'finished_at' => now(),
        ]);

        $summary = (new FairQueueMetrics)->summary();

        $this->assertSame(3, $summary['total']);
        $this->assertSame(1, $summary['pending']);
        $this->assertSame(1, $summary['active']);
        $this->assertSame(1, $summary['finished_in_window']);
        $this->assertSame(1.0, $summary['throughput_per_hour']);
        $this->assertSame(300.0, $summary['average_wait_seconds']);
        $this->assertSame(1, $summary['finished_by_group']["user:{$user->id}"]);
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
