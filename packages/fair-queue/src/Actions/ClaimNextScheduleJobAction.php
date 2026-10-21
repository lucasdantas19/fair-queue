<?php

namespace FairQueue\Actions;

use FairQueue\Contracts\ScheduleJobSelector;
use FairQueue\Events\ScheduleJobClaimed;
use FairQueue\Persistence\EloquentScheduleJob;
use FairQueue\StatusScheduleJobsEnum;
use Illuminate\Support\Facades\DB;

class ClaimNextScheduleJobAction
{
    public function __construct(
        private readonly ScheduleJobSelector $selector,
        private readonly string $modelClass = EloquentScheduleJob::class,
    ) {}

    public function execute(string $queue): ?EloquentScheduleJob
    {
        return DB::transaction(function () use ($queue): ?EloquentScheduleJob {
            ($this->modelClass)::query()
                ->where('queue', $queue)
                ->lockForUpdate()
                ->get();

            $selected = $this->selector->execute($queue);

            if ($selected === null) {
                return null;
            }

            $claimed = ($this->modelClass)::query()
                ->whereKey($selected->getKey())
                ->where('queue', $queue)
                ->where('status', StatusScheduleJobsEnum::PENDING->value)
                ->first();

            if ($claimed === null) {
                return null;
            }

            $claimed->update([
                'status' => StatusScheduleJobsEnum::CLAIMED,
                'claimed_at' => now(),
            ]);

            $claimed = $claimed->fresh();
            event($this->claimedEvent($claimed));

            return $claimed;
        });
    }

    protected function claimedEvent(EloquentScheduleJob $scheduleJob): object
    {
        return new ScheduleJobClaimed($scheduleJob);
    }
}
