<?php

namespace FairQueue\Events;

use FairQueue\Persistence\EloquentScheduleJob;

class ScheduleJobFailed
{
    public function __construct(
        public readonly EloquentScheduleJob $scheduleJob,
        public readonly \Throwable $exception,
    ) {}
}
