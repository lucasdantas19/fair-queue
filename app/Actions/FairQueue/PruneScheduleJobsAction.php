<?php

namespace App\Actions\FairQueue;

use App\Models\ScheduleJob;
use App\StatusScheduleJobsEnum;

class PruneScheduleJobsAction
{
    public function execute(?string $queue = null, ?int $batchSize = null, bool $dryRun = false): int
    {
        $retention = config('fair_queue.retention');
        $query = ScheduleJob::query()
            ->where(function ($query) use ($retention): void {
                $query
                    ->where(function ($query) use ($retention): void {
                        $query->where('status', StatusScheduleJobsEnum::FINISHED->value)
                            ->where('finished_at', '<=', now()->subDays((int) $retention['finished_days']));
                    })
                    ->orWhere(function ($query) use ($retention): void {
                        $query->where('status', StatusScheduleJobsEnum::FAILED->value)
                            ->where('failed_at', '<=', now()->subDays((int) $retention['failed_days']));
                    });
            });

        if ($queue !== null) {
            $query->where('queue', $queue);
        }

        if ($dryRun) {
            return $query->count();
        }

        $deleted = 0;
        $limit = $batchSize ?? (int) $retention['batch_size'];

        $query->select('id')->chunkById($limit, function ($jobs) use (&$deleted): void {
            $deleted += ScheduleJob::query()
                ->whereKey($jobs->modelKeys())
                ->delete();
        });

        return $deleted;
    }
}
