<?php

namespace App\Events\FairQueue;

use App\Models\ScheduleJob;

class ScheduleJobCompleted
{
    public function __construct(public readonly ScheduleJob $scheduleJob) {}
}
