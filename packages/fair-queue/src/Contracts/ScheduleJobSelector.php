<?php

namespace FairQueue\Contracts;

use FairQueue\Persistence\EloquentScheduleJob;

interface ScheduleJobSelector
{
    public function execute(string $queue): ?EloquentScheduleJob;
}
