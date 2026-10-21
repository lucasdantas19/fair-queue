<?php

namespace FairQueue\Persistence;

use FairQueue\StatusScheduleJobsEnum;
use Illuminate\Database\Eloquent\Model;

class EloquentScheduleJob extends Model
{
    protected $table = 'schedule_jobs';

    protected $guarded = [];

    protected $casts = [
        'status' => StatusScheduleJobsEnum::class,
        'claimed_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'last_dispatch_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'timed_out_at' => 'datetime',
        'timeout_seconds' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'failed_at' => 'datetime',
        'dispatch_attempts' => 'integer',
        'worker_attempts' => 'integer',
    ];
}
