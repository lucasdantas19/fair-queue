<?php

namespace App\Actions\FairQueue;

use App\Models\ScheduleJob;
use App\StatusScheduleJobsEnum;

class FairQueueMetrics
{
    public function summary(?string $queue = null, ?int $windowMinutes = null): array
    {
        $windowMinutes ??= (int) config('fair_queue.observability.window_minutes', 60);
        $query = ScheduleJob::query();

        if ($queue !== null) {
            $query->where('queue', $queue);
        }

        $jobs = $query->get([
            'queue',
            'status',
            'group_key',
            'group_value',
            'created_at',
            'started_at',
            'finished_at',
        ]);

        $finishedSince = now()->subMinutes($windowMinutes);
        $finished = $jobs->filter(
            fn (ScheduleJob $job): bool => $job->status === StatusScheduleJobsEnum::FINISHED
                && $job->finished_at?->greaterThanOrEqualTo($finishedSince),
        );
        $waits = $jobs
            ->filter(fn (ScheduleJob $job): bool => $job->started_at !== null)
            ->map(fn (ScheduleJob $job): int => abs($job->started_at->diffInSeconds($job->created_at)));
        $byStatus = $jobs->countBy(fn (ScheduleJob $job): string => $job->status->value);
        $byGroup = $finished->countBy(
            fn (ScheduleJob $job): string => "{$job->group_key}:{$job->group_value}",
        );

        return [
            'queue' => $queue,
            'window_minutes' => $windowMinutes,
            'total' => $jobs->count(),
            'by_status' => $byStatus->all(),
            'pending' => $byStatus->get(StatusScheduleJobsEnum::PENDING->value, 0),
            'active' => $jobs->whereIn('status', [
                StatusScheduleJobsEnum::CLAIMED,
                StatusScheduleJobsEnum::DISPATCHED,
                StatusScheduleJobsEnum::RUNNING,
            ])->count(),
            'finished_in_window' => $finished->count(),
            'failed' => $byStatus->get(StatusScheduleJobsEnum::FAILED->value, 0),
            'throughput_per_hour' => $windowMinutes > 0
                ? round($finished->count() * 60 / $windowMinutes, 2)
                : 0,
            'average_wait_seconds' => $waits->isNotEmpty()
                ? round($waits->average(), 2)
                : 0,
            'finished_by_group' => $byGroup->all(),
        ];
    }
}
