<?php

namespace FairQueue\Events;

use FairQueue\Persistence\EloquentScheduleJob;

class ScheduleJobCompleted
{
    public function __construct(public readonly EloquentScheduleJob $scheduleJob) {}
}
