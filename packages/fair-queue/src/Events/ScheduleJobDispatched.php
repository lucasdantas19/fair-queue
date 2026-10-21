<?php

namespace FairQueue\Events;

use FairQueue\Persistence\EloquentScheduleJob;

class ScheduleJobDispatched
{
    public function __construct(public readonly EloquentScheduleJob $scheduleJob) {}
}
