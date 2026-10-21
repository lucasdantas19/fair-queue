<?php

use App\Actions\FairQueue\Policies\LeastActivePolicy;
use App\Actions\FairQueue\Policies\RoundRobinPolicy;

return [
    /*
    |--------------------------------------------------------------------------
    | Default capacity
    |--------------------------------------------------------------------------
    */
    'default_max_active_jobs' => 20,
    'default_policy' => 'least_active',
    'aging_seconds' => 0,

    'policies' => [
        'least_active' => LeastActivePolicy::class,
        'round_robin' => RoundRobinPolicy::class,
    ],

    'claimed_timeout_seconds' => env('FAIR_QUEUE_CLAIMED_TIMEOUT_SECONDS', 300),
    'dispatch_unique_for_seconds' => env('FAIR_QUEUE_DISPATCH_UNIQUE_FOR_SECONDS', 86400),

    'retention' => [
        'finished_days' => (int) env('FAIR_QUEUE_FINISHED_RETENTION_DAYS', 30),
        'failed_days' => (int) env('FAIR_QUEUE_FAILED_RETENTION_DAYS', 90),
        'batch_size' => (int) env('FAIR_QUEUE_PRUNE_BATCH_SIZE', 500),
    ],

    'observability' => [
        'window_minutes' => (int) env('FAIR_QUEUE_METRICS_WINDOW_MINUTES', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue-specific capacity
    |--------------------------------------------------------------------------
    */
    'queues' => [
        'default' => [
            'max_active_jobs' => env('FAIR_QUEUE_DEFAULT_MAX_ACTIVE_JOBS', 20),
            'policy' => env('FAIR_QUEUE_DEFAULT_POLICY', 'least_active'),
            'groups' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Watcher
    |--------------------------------------------------------------------------
    */
    'watcher' => [
        'sleep_seconds' => (int) env('FAIR_QUEUE_WATCHER_SLEEP_SECONDS', 1),
        'max_iterations' => env('FAIR_QUEUE_WATCHER_MAX_ITERATIONS', 1),
    ],
];
