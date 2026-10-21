<?php

namespace App\Console\Commands;

use App\Actions\FairQueue\RecoverStaleClaimedJobsAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('fair-queue:recover {--queue= : Recover jobs from only this queue}')]
#[Description('Release stale claimed Fair Queue jobs back to pending')]
class RecoverStaleScheduleJobs extends Command
{
    public function handle(RecoverStaleClaimedJobsAction $recover): int
    {
        $recovered = $recover->execute($this->option('queue'));

        $this->info("Recovered {$recovered} stale job(s).");

        return self::SUCCESS;
    }
}
