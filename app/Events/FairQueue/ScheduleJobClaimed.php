<?php

namespace App\Events\FairQueue;

use App\Models\ScheduleJob;

class ScheduleJobClaimed
{
    public function __construct(public readonly ScheduleJob $scheduleJob) {}
}
