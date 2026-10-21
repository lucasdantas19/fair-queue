<?php

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiagnoseFairQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_diagnose_command_returns_json_metrics(): void
    {
        $this->artisan('fair-queue:diagnose', ['--window' => 30])
            ->assertExitCode(0)
            ->expectsOutputToContain('"window_minutes": 30');
    }
}
