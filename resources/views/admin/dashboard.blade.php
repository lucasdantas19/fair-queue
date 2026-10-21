@php
    $statusClasses = [
        'pending' => 'waiting',
        'claimed' => 'waiting',
        'dispatched' => 'running',
        'running' => 'running',
        'finished' => 'done',
        'failed' => 'failed',
    ];
@endphp

@extends('layouts.app', ['title' => 'Dashboard admin · Fair Queue', 'section' => 'Monitoramento', 'active' => 'admin'])

@section('content')
<main class="content">
    <div class="heading">
        <div>
            <h1>Visão geral dos jobs.</h1>
            <p class="lead">Acompanhe o fluxo real das filas e identifique gargalos rapidamente.</p>
        </div>
        <div class="toolbar-actions"><span class="tag done">● Consulta ao vivo</span><a class="button" href="{{ request()->fullUrl() }}">Atualizar</a></div>
    </div>

    <section class="admin-grid" aria-label="Resumo dos jobs">
        <div class="stat"><span class="stat-label">Total de jobs</span><strong class="stat-value">{{ $summary['total'] }}</strong></div>
        <div class="stat"><span class="stat-label">Pendentes</span><strong class="stat-value stat-accent">{{ $summary['pending'] }}</strong></div>
        <div class="stat"><span class="stat-label">Ativos agora</span><strong class="stat-value" style="color:var(--amber)">{{ $summary['active'] }}</strong></div>
        <div class="stat"><span class="stat-label">Falharam</span><strong class="stat-value" style="color:var(--red)">{{ $summary['failed'] }}</strong></div>
    </section>

    <section class="grid grid-4" style="margin-bottom:1.5rem" aria-label="Métricas da janela">
        <div class="stat"><span class="stat-label">Concluídos na janela</span><strong class="stat-value">{{ $summary['finished_in_window'] }}</strong></div>
        <div class="stat"><span class="stat-label">Throughput/hora</span><strong class="stat-value">{{ number_format($summary['throughput_per_hour'], 2, ',', '.') }}</strong></div>
        <div class="stat"><span class="stat-label">Espera média</span><strong class="stat-value">{{ $summary['average_wait_seconds'] }}s</strong></div>
        <div class="stat"><span class="stat-label">Janela analisada</span><strong class="stat-value">{{ $summary['window_minutes'] }} min</strong></div>
    </section>

    <section class="card">
        <div class="toolbar">
            <div>
                <h2>Jobs recentes</h2>
                <p class="muted" style="margin:0;font-size:.85rem">{{ $jobs->count() }} registros exibidos</p>
            </div>
            <div class="toolbar-actions">
                <form method="GET" action="{{ route('admin.dashboard') }}" class="filters">
                    <select name="queue" aria-label="Filtrar por fila">
                        <option value="">Todas as filas</option>
                        @foreach ($queues as $queue)
                            <option value="{{ $queue }}" @selected($selectedQueue === $queue)>{{ $queue }}</option>
                        @endforeach
                    </select>
                    <select name="status" aria-label="Filtrar por status">
                        <option value="">Todos os status</option>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button class="button" type="submit">Filtrar</button>
                </form>
                <a class="button secondary" href="{{ route('home') }}">Solicitar job</a>
            </div>
        </div>

        @if ($jobs->isEmpty())
            <div class="notice"><span>ⓘ</span><div>Nenhum job encontrado para os filtros selecionados.</div></div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Job</th><th>Grupo</th><th>Fila</th><th>Status</th><th>Solicitado em</th></tr></thead><tbody>
                @foreach ($jobs as $job)
                    @php($status = $job->status->value)
                    <tr>
                        <td><strong>ScheduleJob #{{ $job->id }}</strong><br><span class="mono">{{ $job->uuid }}</span></td>
                        <td>{{ $job->group_key }}:{{ $job->group_value }}</td>
                        <td>{{ $job->queue }}</td>
                        <td><span class="tag {{ $statusClasses[$status] ?? 'done' }}">● {{ $statusLabels[$status] ?? $status }}</span></td>
                        <td class="muted">{{ $job->created_at?->format('d/m/Y H:i:s') }}</td>
                    </tr>
                @endforeach
            </tbody></table></div>
        @endif
    </section>

    <div class="notice"><span>ⓘ</span><div><strong>Como ler o fluxo</strong><br><b>Pendente</b> aguarda o watcher; <b>Despachado</b> já foi enviado ao broker; <b>Executando</b> está no Horizon; <b>Concluído</b> terminou. Esta tela consulta o banco sob demanda, então use “Atualizar” após cada etapa.</div></div>
</main>
@endsection
