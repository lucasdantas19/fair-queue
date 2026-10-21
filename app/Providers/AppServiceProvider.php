<?php

namespace App\Providers;

use App\Actions\FairQueue\FairQueuePayloadContext;
use App\Listeners\ScheduleJobLifecycleListener;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobTimedOut;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Queue::createPayloadUsing(function () {
            $scheduleJobId = FairQueuePayloadContext::scheduleJobId();

            return $scheduleJobId === null
                ? []
                : ['fair_queue' => [
                    'schedule_job_id' => $scheduleJobId,
                    'dispatch_token' => FairQueuePayloadContext::dispatchToken(),
                ]];
        });

        Event::listen(JobProcessing::class, [ScheduleJobLifecycleListener::class, 'processing']);
        Event::listen(JobProcessed::class, [ScheduleJobLifecycleListener::class, 'processed']);
        Event::listen(JobFailed::class, [ScheduleJobLifecycleListener::class, 'failed']);
        Event::listen(JobExceptionOccurred::class, [ScheduleJobLifecycleListener::class, 'exceptionOccurred']);
        Event::listen(JobTimedOut::class, [ScheduleJobLifecycleListener::class, 'timedOut']);
    }
}
