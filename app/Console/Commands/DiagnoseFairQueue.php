<?php

namespace App\Console\Commands;

use App\Actions\FairQueue\FairQueueMetrics;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('fair-queue:diagnose {--queue= : Diagnose only this queue} {--window= : Metrics window in minutes}')]
#[Description('Show Fair Queue state and fairness metrics')]
class DiagnoseFairQueue extends Command
{
    public function handle(FairQueueMetrics $metrics): int
    {
        $summary = $metrics->summary(
            $this->option('queue'),
            $this->option('window') !== null ? (int) $this->option('window') : null,
        );

        $this->line(json_encode($summary, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }
}
