<?php

namespace App\Events\FairQueue;

use App\Models\ScheduleJob;

class ScheduleJobSelected
{
    public function __construct(public readonly ScheduleJob $scheduleJob) {}
}
