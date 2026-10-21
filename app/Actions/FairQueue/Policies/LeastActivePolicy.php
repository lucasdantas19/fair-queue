<?php

namespace App\Actions\FairQueue\Policies;

use App\Models\ScheduleJob;
use App\StatusScheduleJobsEnum;

class LeastActivePolicy implements FairnessPolicy
{
    /**
     * Select the group with the fewest active jobs.
     *
     * Ties are broken by the oldest pending job and then by its id so the
     * result remains deterministic when several jobs share a timestamp.
     *
     * @return array{group_key: string, group_value: string}|null
     */
    public function selectGroup(string $queue, array $excludedGroups = []): ?array
    {
        $activeCounts = ScheduleJob::query()
            ->where('queue', $queue)
            ->whereIn('status', $this->activeStatuses())
            ->groupBy('group_key', 'group_value')
            ->select('group_key', 'group_value')
            ->selectRaw('COUNT(*) as active_jobs_count');

        $groups = ScheduleJob::query()
            ->from('schedule_jobs as pending')
            ->leftJoinSub($activeCounts, 'active', function ($join): void {
                $join->on('active.group_key', '=', 'pending.group_key')
                    ->on('active.group_value', '=', 'pending.group_value');
            })
            ->where('pending.queue', $queue)
            ->where('pending.status', StatusScheduleJobsEnum::PENDING->value)
            ->groupBy('pending.group_key', 'pending.group_value')
            ->select([
                'pending.group_key',
                'pending.group_value',
            ])
            ->selectRaw('MIN(pending.created_at) as oldest_pending_at')
            ->selectRaw('MIN(pending.id) as oldest_pending_id')
            ->selectRaw('COALESCE(MAX(active.active_jobs_count), 0) as active_jobs_count')
            ->get()
            ->reject(fn ($group): bool => in_array([
                'group_key' => $group->group_key,
                'group_value' => $group->group_value,
            ], $excludedGroups, true));

        if ($groups->isEmpty()) {
            return null;
        }

        $selectedGroup = $groups
            ->map(function ($group) {
                $group->fairness_score = $group->active_jobs_count - $this->agingBonus($group);

                return $group;
            })
            ->sortBy([
                ['fairness_score', 'asc'],
                ['active_jobs_count', 'asc'],
                ['oldest_pending_at', 'asc'],
                ['oldest_pending_id', 'asc'],
            ])
            ->first();

        return [
            'group_key' => $selectedGroup->group_key,
            'group_value' => $selectedGroup->group_value,
        ];
    }

    /**
     * @return list<string>
     */
    public function activeStatuses(): array
    {
        return [
            StatusScheduleJobsEnum::DISPATCHED->value,
            StatusScheduleJobsEnum::RUNNING->value,
            StatusScheduleJobsEnum::CLAIMED->value,
        ];
    }

    private function agingBonus(object $group): int
    {
        $agingSeconds = (int) config('fair_queue.aging_seconds', 0);

        if ($agingSeconds <= 0) {
            return 0;
        }

        return (int) now()->diffInSeconds($group->oldest_pending_at) / $agingSeconds;
    }
}
