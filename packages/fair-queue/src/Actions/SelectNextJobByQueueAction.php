<?php

namespace FairQueue\Actions;

use Closure;
use FairQueue\Contracts\QueueFairnessPolicy;
use FairQueue\Contracts\ScheduleJobSelector;
use FairQueue\Events\ScheduleJobSelected;
use FairQueue\Persistence\EloquentScheduleJob;
use FairQueue\StatusScheduleJobsEnum;

class SelectNextJobByQueueAction implements ScheduleJobSelector
{
    /** @param callable(string): QueueFairnessPolicy $policyFactory */
    public function __construct(
        private readonly Closure $policyFactory,
        private readonly string $modelClass = EloquentScheduleJob::class,
    ) {}

    public function execute(string $queue): ?EloquentScheduleJob
    {
        $policy = ($this->policyFactory)($queue);
        $activeStatuses = $policy->activeStatuses();
        $model = $this->modelClass;

        $activeJobsCount = $model::query()
            ->where('queue', $queue)
            ->whereIn('status', $activeStatuses)
            ->count();

        if ($activeJobsCount >= $this->maxActiveJobs($queue)) {
            return null;
        }

        $excludedGroups = [];

        do {
            $selectedGroup = $policy->selectGroup($queue, $excludedGroups);

            if ($selectedGroup === null) {
                return null;
            }

            if ($this->groupHasCapacity($queue, $selectedGroup, $activeStatuses)) {
                break;
            }

            $excludedGroups[] = $selectedGroup;
        } while (true);

        $job = $model::query()
            ->where('queue', $queue)
            ->where('status', StatusScheduleJobsEnum::PENDING->value)
            ->where('group_key', $selectedGroup['group_key'])
            ->where('group_value', $selectedGroup['group_value'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->first();

        if ($job !== null) {
            event($this->selectedEvent($job));
        }

        return $job;
    }

    protected function selectedEvent(EloquentScheduleJob $scheduleJob): object
    {
        return new ScheduleJobSelected($scheduleJob);
    }

    private function maxActiveJobs(string $queue): int
    {
        return (int) config(
            "fair_queue.queues.{$queue}.max_active_jobs",
            config('fair_queue.default_max_active_jobs', 20),
        );
    }

    private function groupHasCapacity(string $queue, array $group, array $activeStatuses): bool
    {
        $limit = config(
            "fair_queue.queues.{$queue}.groups.{$group['group_key']}.max_active_jobs",
        );

        if ($limit === null) {
            return true;
        }

        $activeJobs = ($this->modelClass)::query()
            ->where('queue', $queue)
            ->where('group_key', $group['group_key'])
            ->where('group_value', $group['group_value'])
            ->whereIn('status', $activeStatuses)
            ->count();

        return $activeJobs < (int) $limit;
    }
}
