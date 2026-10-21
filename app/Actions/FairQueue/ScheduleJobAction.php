<?php

namespace App\Actions\FairQueue;

use App\Models\ScheduleJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class ScheduleJobAction
{
    public function execute(ShouldQueue $job, string $groupKey, string $groupValue, string $queue = 'default'): ScheduleJob
    {
        return ScheduleJob::create([
            'payload' => serialize($job),
            'dispatch_token' => (string) Str::uuid(),
            'queue' => $queue,
            'status' => 'pending',
            'group_key' => $groupKey,
            'group_value' => $groupValue,
        ]);
    }
}
