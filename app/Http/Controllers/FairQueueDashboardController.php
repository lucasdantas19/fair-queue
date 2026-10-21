<?php

namespace App\Http\Controllers;

use App\Actions\FairQueue\FairQueueMetrics;
use App\Models\ScheduleJob;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FairQueueDashboardController extends Controller
{
    public function __invoke(Request $request, FairQueueMetrics $metrics): View
    {
        $filters = $request->validate([
            'queue' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'in:pending,claimed,dispatched,running,finished,failed'],
        ]);

        $queue = $filters['queue'] ?? null;
        $status = $filters['status'] ?? null;
        $query = ScheduleJob::query()
            ->when($queue, fn ($query) => $query->where('queue', $queue))
            ->when($status, fn ($query) => $query->where('status', $status));

        $jobs = (clone $query)
            ->latest('created_at')
            ->latest('id')
            ->limit(50)
            ->get();

        return view('admin.dashboard', [
            'summary' => $metrics->summary($queue),
            'jobs' => $jobs,
            'queues' => ScheduleJob::query()
                ->select('queue')
                ->distinct()
                ->orderBy('queue')
                ->pluck('queue'),
            'selectedQueue' => $queue,
            'selectedStatus' => $status,
            'statusLabels' => [
                'pending' => 'Pendente',
                'claimed' => 'Reivindicado',
                'dispatched' => 'Despachado',
                'running' => 'Executando',
                'finished' => 'Concluído',
                'failed' => 'Falhou',
            ],
        ]);
    }
}
