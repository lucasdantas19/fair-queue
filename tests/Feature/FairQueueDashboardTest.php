<?php

namespace Tests\Feature;

use App\Jobs\ActionJob;
use App\Models\ScheduleJob;
use App\Models\User;
use App\StatusScheduleJobsEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FairQueueDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_real_summary_and_jobs(): void
    {
        $user = User::factory()->create();
        ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'status' => StatusScheduleJobsEnum::RUNNING,
            'group_key' => 'user',
            'group_value' => $user->id,
        ]);
        ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'status' => StatusScheduleJobsEnum::FAILED,
            'group_key' => 'user',
            'group_value' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertViewHas('summary', fn (array $summary): bool => $summary['total'] === 2
                && $summary['active'] === 1
                && $summary['failed'] === 1)
            ->assertSee('ScheduleJob #')
            ->assertSee('Executando')
            ->assertSee('Falhou');
    }

    public function test_dashboard_filters_jobs_by_status(): void
    {
        $user = User::factory()->create();
        ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'status' => StatusScheduleJobsEnum::RUNNING,
            'group_key' => 'user',
            'group_value' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('admin.dashboard', ['status' => 'failed']));

        $response->assertOk()
            ->assertViewHas('jobs', fn ($jobs): bool => $jobs->isEmpty())
            ->assertSee('Nenhum job encontrado');
    }

    public function test_home_displays_the_authenticated_users_recent_jobs(): void
    {
        $user = User::factory()->create();
        ScheduleJob::factory()->create([
            'payload' => serialize(new ActionJob($user, 10)),
            'group_key' => 'user',
            'group_value' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Atividade recente')
            ->assertSee('Job #')
            ->assertSee('Dashboard')
            ->assertSee('Roteiro rápido')
            ->assertSee(route('admin.dashboard'));
    }

    public function test_authenticated_navigation_connects_the_testing_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('home'))
            ->assertSee('Solicitar job')
            ->assertSee('Atualizar')
            ->assertSee('/logout');
    }
}
