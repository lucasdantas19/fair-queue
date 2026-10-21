<?php

namespace FairQueue\Events;

use FairQueue\Persistence\EloquentScheduleJob;

class ScheduleJobClaimed
{
    public function __construct(public readonly EloquentScheduleJob $scheduleJob) {}
}
