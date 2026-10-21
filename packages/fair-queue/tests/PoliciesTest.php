<?php

namespace FairQueue\Tests;

use DateTimeImmutable;
use FairQueue\Contracts\CursorStore;
use FairQueue\Jobs\ExecuteScheduledJob;
use FairQueue\Policies\LeastActivePolicy;
use FairQueue\Policies\RoundRobinPolicy;
use FairQueue\StatusScheduleJobsEnum;
use FairQueue\ValueObjects\GroupSnapshot;
use PHPUnit\Framework\TestCase;

class PoliciesTest extends TestCase
{
    public function test_status_values_are_part_of_the_package_domain(): void
    {
        $this->assertSame('pending', StatusScheduleJobsEnum::PENDING->value);
        $this->assertSame('finished', StatusScheduleJobsEnum::FINISHED->value);
    }

    public function test_least_active_selects_the_group_with_fewer_active_jobs(): void
    {
        $groups = [
            $this->group('user', '1', 4, 1),
            $this->group('user', '2', 1, 2),
        ];

        $selected = (new LeastActivePolicy)->select($groups);

        $this->assertSame('2', $selected?->groupValue);
    }

    public function test_round_robin_advances_after_the_current_group(): void
    {
        $cursor = new class implements CursorStore
        {
            private ?string $value = null;

            public function get(string $key): ?string
            {
                return $this->value;
            }

            public function put(string $key, string $value): void
            {
                $this->value = $value;
            }
        };
        $policy = new RoundRobinPolicy($cursor, 'queue:default');
        $groups = [
            $this->group('user', '1', 0, 1),
            $this->group('user', '2', 0, 2),
        ];

        $this->assertSame('1', $policy->select($groups)?->groupValue);
        $this->assertSame('2', $policy->select($groups)?->groupValue);
    }

    public function test_execution_job_exposes_stable_identity_and_tags(): void
    {
        $job = new ExecuteScheduledJob(42, 'dispatch-token-42', 'serialized-payload');

        $this->assertSame('dispatch-token-42', $job->uniqueId());
        $this->assertSame([
            'fair-queue',
            'schedule-job:42',
            'dispatch-token:dispatch-token-42',
        ], $job->tags());
    }

    private function group(string $key, string $value, int $active, int $id): GroupSnapshot
    {
        return new GroupSnapshot(
            $key,
            $value,
            $active,
            new DateTimeImmutable("2026-10-21 12:{$id}:00"),
            $id,
        );
    }
}
