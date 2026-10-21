<?php

namespace App\Events\FairQueue;

use App\Models\ScheduleJob;

class ScheduleJobFailed
{
    public function __construct(
        public readonly ScheduleJob $scheduleJob,
        public readonly \Throwable $exception,
    ) {}
}
