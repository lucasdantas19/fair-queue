<?php

namespace App\Console\Commands;

use App\Actions\FairQueue\PruneScheduleJobsAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('fair-queue:prune {--queue= : Prune jobs from only this queue} {--dry-run : Count jobs without deleting them}')]
#[Description('Remove finished and failed Fair Queue records past retention')]
class PruneScheduleJobs extends Command
{
    public function handle(PruneScheduleJobsAction $pruner): int
    {
        $count = $pruner->execute(
            $this->option('queue'),
            null,
            (bool) $this->option('dry-run'),
        );

        $action = $this->option('dry-run') ? 'Found' : 'Pruned';
        $this->info("{$action} {$count} schedule job(s).");

        return self::SUCCESS;
    }
}
