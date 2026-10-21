<?php

namespace FairQueue\Events;

use FairQueue\Persistence\EloquentScheduleJob;

class ScheduleJobSelected
{
    public function __construct(public readonly EloquentScheduleJob $scheduleJob) {}
}
