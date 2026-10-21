<?php

namespace App\Actions\FairQueue;

use App\Events\FairQueue\ScheduleJobDispatched;
use App\Jobs\ExecuteScheduledJob;
use FairQueue\Actions\DispatchScheduleJobAction as PackageDispatchScheduleJobAction;
use FairQueue\Persistence\EloquentScheduleJob;

class DispatchScheduleJobAction extends PackageDispatchScheduleJobAction
{
    public function __construct(
        ClaimNextScheduleJobAction $claimer = new ClaimNextScheduleJobAction,
    ) {
        parent::__construct($claimer, ExecuteScheduledJob::class);
    }

    protected function beforeDispatch(EloquentScheduleJob $scheduleJob): void
    {
        FairQueuePayloadContext::set(
            $scheduleJob->id,
            $scheduleJob->dispatch_token,
        );
    }

    protected function afterDispatch(EloquentScheduleJob $scheduleJob): void
    {
        FairQueuePayloadContext::clear();
    }

    protected function dispatchedEvent(EloquentScheduleJob $scheduleJob): object
    {
        return new ScheduleJobDispatched($scheduleJob);
    }
}
