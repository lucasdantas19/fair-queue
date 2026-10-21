<?php

namespace App\Listeners;

use App\Events\FairQueue\ScheduleJobCompleted;
use App\Events\FairQueue\ScheduleJobFailed;
use App\Models\ScheduleJob;
use FairQueue\Listeners\ScheduleJobLifecycleListener as PackageScheduleJobLifecycleListener;
use FairQueue\Persistence\EloquentScheduleJob;

class ScheduleJobLifecycleListener extends PackageScheduleJobLifecycleListener
{
    public function __construct()
    {
        parent::__construct(ScheduleJob::class);
    }

    protected function completedEvent(EloquentScheduleJob $scheduleJob): object
    {
        return new ScheduleJobCompleted($scheduleJob);
    }

    protected function failedEvent(EloquentScheduleJob $scheduleJob, \Throwable $exception): object
    {
        return new ScheduleJobFailed($scheduleJob, $exception);
    }
}
