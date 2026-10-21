<?php

namespace FairQueue;

enum StatusScheduleJobsEnum: string
{
    case PENDING = 'pending';
    case CLAIMED = 'claimed';
    case DISPATCHED = 'dispatched';
    case RUNNING = 'running';
    case FINISHED = 'finished';
    case FAILED = 'failed';
}
