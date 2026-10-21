<?php

namespace App\Actions\FairQueue;

use App\Events\FairQueue\ScheduleJobClaimed;
use App\Models\ScheduleJob;
use FairQueue\Actions\ClaimNextScheduleJobAction as PackageClaimNextScheduleJobAction;
use FairQueue\Persistence\EloquentScheduleJob;

class ClaimNextScheduleJobAction extends PackageClaimNextScheduleJobAction
{
    public function __construct(
        SelectNextJobByQueueAction $selector = new SelectNextJobByQueueAction,
    ) {
        parent::__construct($selector, ScheduleJob::class);
    }

    protected function claimedEvent(EloquentScheduleJob $scheduleJob): object
    {
        return new ScheduleJobClaimed($scheduleJob);
    }
}
