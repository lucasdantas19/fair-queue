<?php

namespace Tests\Feature\Actions\FairQueue;

use App\Actions\FairQueue\SelectNextJobByQueueAction;
use App\Jobs\ActionJob;
use App\Models\ScheduleJob;
use App\Models\User;
use App\StatusScheduleJobsEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SelectNextJobActionTest extends TestCase
{
    use RefreshDatabase;

    const DEFAULT_QUEUE = 'default';

    const DEFAULT_GROUP_KEY = 'user';

    protected User $user1;

    protected User $user2;

    protected User $user3;

    protected SelectNextJobByQueueAction $selectNextJobByQueueAction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->selectNextJobByQueueAction = new SelectNextJobByQueueAction;

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
        $this->user3 = User::factory()->create();
    }

    protected function createScheduleJobsForUser(User $user, int $count, StatusScheduleJobsEnum $status = StatusScheduleJobsEnum::PENDING)
    {
        ScheduleJob::factory($count)->create([
            'payload' => serialize(new ActionJob($user, 60)),
            'status' => $status,
            'group_key' => self::DEFAULT_GROUP_KEY,
            'group_value' => $user->id,
        ]);
    }

    public function test_select_correct_next_job_by_queue()
    {
        $this->createScheduleJobsForUser($this->user2, 1);
        $this->createScheduleJobsForUser($this->user3, 9);
        $this->createScheduleJobsForUser($this->user1, 5);

        $nextJob = $this->selectNextJobByQueueAction->execute(self::DEFAULT_QUEUE);

        $this->assertEquals($nextJob->group_key, self::DEFAULT_GROUP_KEY);
        $this->assertEquals($nextJob->group_value, $this->user2->id);
    }

    public function test_select_correct_next_job_when_the_most_group_has_processing()
    {
        $this->createScheduleJobsForUser($this->user1, 5);
        $this->createScheduleJobsForUser($this->user2, 1);
        $this->createScheduleJobsForUser($this->user3, 19, StatusScheduleJobsEnum::DISPATCHED);

        $nextJob = $this->selectNextJobByQueueAction->execute(self::DEFAULT_QUEUE);

        $this->assertEquals($nextJob->group_key, self::DEFAULT_GROUP_KEY);
        $this->assertEquals($nextJob->group_value, $this->user1->id);
    }

    public function test_select_correct_next_when_many_jobs_of_many_groups_dispatched()
    {
        // User 1 - 5 Actions | 2 Dispatched and 3 pending
        $this->createScheduleJobsForUser($this->user1, 3);
        $this->createScheduleJobsForUser($this->user1, 2, StatusScheduleJobsEnum::DISPATCHED);

        // User 2 - 7 Actions | 2 Dispatched and 5 pending
        $this->createScheduleJobsForUser($this->user2, 5);
        $this->createScheduleJobsForUser($this->user2, 2, StatusScheduleJobsEnum::DISPATCHED);

        // User 3 - 1 Action | 1 pending
        $this->createScheduleJobsForUser($this->user3, 1);
        // $this->createScheduleJobsForUser($this->user1, 2, StatusScheduleJobsEnum::DISPATCHED);

        $nextJob = $this->selectNextJobByQueueAction->execute(self::DEFAULT_QUEUE);

        $this->assertEquals($nextJob->group_key, self::DEFAULT_GROUP_KEY);
        $this->assertEquals($nextJob->group_value, $this->user3->id);
    }

    public function test_returns_null_when_queue_has_no_pending_jobs(): void
    {
        $this->assertNull(
            $this->selectNextJobByQueueAction->execute(self::DEFAULT_QUEUE)
        );
    }

    public function test_counts_running_jobs_against_queue_capacity(): void
    {
        $this->createScheduleJobsForUser(
            $this->user1,
            20,
            StatusScheduleJobsEnum::RUNNING,
        );
        $this->createScheduleJobsForUser($this->user2, 1);

        $this->assertNull(
            $this->selectNextJobByQueueAction->execute(self::DEFAULT_QUEUE)
        );
    }

    public function test_can_select_groups_with_round_robin_policy(): void
    {
        config(['fair_queue.queues.default.policy' => 'round_robin']);
        Cache::forget('fair_queue:round_robin:default');

        $this->createScheduleJobsForUser($this->user1, 1);
        $this->createScheduleJobsForUser($this->user2, 1);

        $first = $this->selectNextJobByQueueAction->execute(self::DEFAULT_QUEUE);
        $second = $this->selectNextJobByQueueAction->execute(self::DEFAULT_QUEUE);

        $this->assertSame((string) $this->user1->id, $first?->group_value);
        $this->assertSame((string) $this->user2->id, $second?->group_value);
    }

    public function test_respects_the_configured_limit_for_a_group(): void
    {
        config([
            'fair_queue.queues.default.groups.user.max_active_jobs' => 1,
        ]);

        $this->createScheduleJobsForUser($this->user1, 1, StatusScheduleJobsEnum::RUNNING);
        $this->createScheduleJobsForUser($this->user1, 1);
        $this->createScheduleJobsForUser($this->user2, 1);

        $nextJob = $this->selectNextJobByQueueAction->execute(self::DEFAULT_QUEUE);

        $this->assertSame((string) $this->user2->id, $nextJob?->group_value);
    }

    public function test_aging_can_raise_an_old_group_above_a_group_with_more_active_jobs(): void
    {
        config(['fair_queue.aging_seconds' => 60]);

        $this->createScheduleJobsForUser($this->user1, 1);
        ScheduleJob::query()
            ->where('group_value', $this->user1->id)
            ->first()
            ?->update([
                'created_at' => now()->subMinutes(5),
            ]);
        $this->createScheduleJobsForUser($this->user2, 1, StatusScheduleJobsEnum::RUNNING);

        $nextJob = $this->selectNextJobByQueueAction->execute(self::DEFAULT_QUEUE);

        $this->assertSame((string) $this->user1->id, $nextJob?->group_value);
    }

    public function test_least_active_uses_one_group_aggregation_query(): void
    {
        $this->createScheduleJobsForUser($this->user1, 2);
        $this->createScheduleJobsForUser($this->user2, 1, StatusScheduleJobsEnum::RUNNING);
        $queries = [];

        DB::listen(function ($query) use (&$queries): void {
            $queries[] = $query->sql;
        });

        $this->selectNextJobByQueueAction->execute(self::DEFAULT_QUEUE);

        $this->assertCount(3, $queries);
        $this->assertStringContainsString('left join', strtolower($queries[1]));
    }
}
