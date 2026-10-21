<?php

namespace App\Actions\FairQueue;

use App\Models\ScheduleJob;
use App\StatusScheduleJobsEnum;

class RecoverStaleClaimedJobsAction
{
    public function execute(?string $queue = null): int
    {
        $query = ScheduleJob::query()
            ->where('status', StatusScheduleJobsEnum::CLAIMED->value)
            ->whereNotNull('claimed_at')
            ->where('claimed_at', '<=', now()->subSeconds($this->timeout()));

        if ($queue !== null) {
            $query->where('queue', $queue);
        }

        return $query->update([
            'status' => StatusScheduleJobsEnum::PENDING,
            'claimed_at' => null,
        ]);
    }

    private function timeout(): int
    {
        return (int) config('fair_queue.claimed_timeout_seconds', 300);
    }
}
