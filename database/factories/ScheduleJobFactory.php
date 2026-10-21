<?php

namespace Database\Factories;

use App\Jobs\ActionJob;
use App\Models\ScheduleJob;
use App\StatusScheduleJobsEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ScheduleJob>
 */
class ScheduleJobFactory extends Factory
{
    const DEFAULT_QUEUE = 'default';
    const DEFAULT_GROUP_KEY = 'user_id';
    const DEFAULT_ACTION_TIME = 60;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'payload' => serialize(new ActionJob($user, self::DEFAULT_ACTION_TIME)),
            'dispatch_token' => (string) Str::uuid(),
            'queue' => self::DEFAULT_QUEUE,
            'status' => StatusScheduleJobsEnum::PENDING->value,
            'group_key' => self::DEFAULT_GROUP_KEY,
            'group_value' => 0,
            'dispatched_at' => null,
            'finished_at' => null,
        ];
    }
}
