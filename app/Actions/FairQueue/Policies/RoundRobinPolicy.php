<?php

namespace App\Actions\FairQueue\Policies;

use App\Models\ScheduleJob;
use App\StatusScheduleJobsEnum;
use Illuminate\Support\Facades\Cache;

class RoundRobinPolicy implements FairnessPolicy
{
    public function selectGroup(string $queue, array $excludedGroups = []): ?array
    {
        $groups = ScheduleJob::query()
            ->where('queue', $queue)
            ->where('status', StatusScheduleJobsEnum::PENDING->value)
            ->groupBy('group_key', 'group_value')
            ->selectRaw('group_key, group_value, MIN(created_at) as oldest_pending_at, MIN(id) as oldest_pending_id')
            ->orderBy('oldest_pending_at')
            ->orderBy('oldest_pending_id')
            ->get()
            ->reject(fn ($group): bool => $this->isExcluded($group, $excludedGroups))
            ->values();

        if ($groups->isEmpty()) {
            return null;
        }

        $cursor = Cache::get($this->cacheKey($queue));
        $selected = $groups->first();

        if ($cursor !== null) {
            $cursorIndex = $groups->search(
                fn ($group): bool => $this->groupId($group->group_key, $group->group_value) === $cursor,
            );

            if ($cursorIndex !== false) {
                $selected = $groups->get(($cursorIndex + 1) % $groups->count());
            }
        }

        $result = [
            'group_key' => $selected->group_key,
            'group_value' => $selected->group_value,
        ];

        Cache::put(
            $this->cacheKey($queue),
            $this->groupId($result['group_key'], $result['group_value']),
        );

        return $result;
    }

    public function activeStatuses(): array
    {
        return [
            StatusScheduleJobsEnum::DISPATCHED->value,
            StatusScheduleJobsEnum::RUNNING->value,
            StatusScheduleJobsEnum::CLAIMED->value,
        ];
    }

    private function cacheKey(string $queue): string
    {
        return "fair_queue:round_robin:{$queue}";
    }

    private function groupId(string $key, string $value): string
    {
        return "{$key}:{$value}";
    }

    private function isExcluded(object $group, array $excludedGroups): bool
    {
        return in_array([
            'group_key' => $group->group_key,
            'group_value' => $group->group_value,
        ], $excludedGroups, true);
    }
}
