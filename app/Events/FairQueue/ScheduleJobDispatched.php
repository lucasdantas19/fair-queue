<?php

namespace App\Events\FairQueue;

use App\Models\ScheduleJob;

class ScheduleJobDispatched
{
    public function __construct(public readonly ScheduleJob $scheduleJob) {}
}
