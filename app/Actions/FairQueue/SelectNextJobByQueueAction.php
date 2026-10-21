<?php

namespace App\Actions\FairQueue;

use App\Actions\FairQueue\Policies\FairnessPolicy;
use App\Actions\FairQueue\Policies\FairnessPolicyFactory;
use App\Events\FairQueue\ScheduleJobSelected;
use App\Models\ScheduleJob;
use FairQueue\Actions\SelectNextJobByQueueAction as PackageSelectNextJobByQueueAction;
use FairQueue\Persistence\EloquentScheduleJob;

class SelectNextJobByQueueAction extends PackageSelectNextJobByQueueAction
{
    public function __construct(
        ?FairnessPolicy $policy = null,
        FairnessPolicyFactory $policyFactory = new FairnessPolicyFactory,
    ) {
        parent::__construct(
            static fn (string $queue): FairnessPolicy => $policy
                ?? $policyFactory->make($queue),
            ScheduleJob::class,
        );
    }

    protected function selectedEvent(EloquentScheduleJob $scheduleJob): object
    {
        return new ScheduleJobSelected($scheduleJob);
    }
}
