@extends('layouts.app', ['title' => 'Solicitar ação · Fair Queue', 'section' => 'Área de testes', 'active' => 'home'])

@section('content')
<main class="content">
    <div class="heading">
        <div><h1>Teste o equilíbrio<br>das suas filas.</h1><p class="lead">Solicite uma ação de teste e acompanhe como ela poderá percorrer a fila. Os controles abaixo são apenas a interface inicial.</p></div>
        <div class="toolbar-actions"><span class="tag waiting">● Pronto para solicitar</span><a class="button secondary" href="{{ route('admin.dashboard') }}">Ver dashboard</a></div>
    </div>

    <section class="grid grid-3" aria-label="Ações disponíveis">
        <article class="card action-card"><div class="number">10s</div><h2>Ação rápida</h2><p class="lead">Ideal para observar o comportamento imediato da fila.</p><form method="POST" action="{{ route('jobs.request') }}"><div class="choice" style="margin-top:1.4rem"><div><strong>Job de 10 segundos</strong><span>Prioridade normal</span></div>@csrf<input type="hidden" name="duration" value="10"><button type="submit">Solicitar</button></div></form></article>
        <article class="card action-card"><div class="number">1m</div><h2>Ação média</h2><p class="lead">Veja como uma tarefa permanece em execução por mais tempo.</p><form method="POST" action="{{ route('jobs.request') }}"><div class="choice" style="margin-top:1.4rem"><div><strong>Job de 1 minuto</strong><span>Prioridade normal</span></div>@csrf<input type="hidden" name="duration" value="60"><button type="submit">Solicitar</button></div></form></article>
        <article class="card action-card"><div class="number">5m</div><h2>Ação longa</h2><p class="lead">Use para simular ocupação e observar o balanceamento.</p><form method="POST" action="{{ route('jobs.request') }}"><div class="choice" style="margin-top:1.4rem"><div><strong>Job de 5 minutos</strong><span>Prioridade normal</span></div>@csrf<input type="hidden" name="duration" value="300"><button type="submit">Solicitar</button></div></form></article>
    </section>

    @if (session('status'))
        <div class="notice"><span>✓</span><div>{{ session('status') }}</div></div>
    @endif

    <div class="notice"><span>ⓘ</span><div><strong>Como acompanhar o teste</strong><br>Solicitar cria um job pendente. O watcher faz o claim e o dispatch; o Horizon executa o job. Abra o dashboard e use “Atualizar” para acompanhar cada transição.</div></div>

    <section class="card" style="margin-top:1.5rem">
        <div class="panel-head"><div><h2>Atividade recente</h2><p>Jobs solicitados pelo usuário autenticado.</p></div><span class="tag done">{{ $jobs->count() }} exibidos</span></div>
        <ul class="activity">
            @forelse ($jobs as $job)
                @php
                    $status = $job->status->value;
                    $statusLabels = ['pending' => 'Pendente', 'claimed' => 'Reivindicado', 'dispatched' => 'Despachado', 'running' => 'Executando', 'finished' => 'Concluído', 'failed' => 'Falhou'];
                @endphp
                <li><div class="job-name"><span class="dot"></span><div><strong>Job #{{ $job->id }}</strong><div class="job-meta">{{ $statusLabels[$status] ?? $status }} · {{ $job->created_at?->format('d/m/Y H:i') }}</div></div></div><span class="mono">{{ $job->queue }}</span></li>
            @empty
                <li><div class="job-name"><span class="dot" style="background:#b8c3d5"></span><div><strong>Nenhuma ação solicitada ainda</strong><div class="job-meta">Seus jobs aparecerão aqui</div></div></div><span class="muted">—</span></li>
            @endforelse
        </ul>
    </section>

    <section class="card" style="margin-top:1.5rem">
        <div class="panel-head"><div><h2>Roteiro rápido</h2><p>Uma visão simples do que cada processo faz.</p></div><a class="button secondary" href="{{ route('admin.dashboard') }}">Acompanhar estados</a></div>
        <div class="steps">
            <div class="step"><span class="step-number">1</span><strong>Solicite</strong><p>Escolha uma duração para registrar um job na Fair Queue.</p></div>
            <div class="step"><span class="step-number">2</span><strong>Despache</strong><p>Deixe o watcher selecionar e liberar o próximo job elegível.</p></div>
            <div class="step"><span class="step-number">3</span><strong>Execute</strong><p>Com o Horizon ativo, veja o job passar por execução até concluir.</p></div>
        </div>
    </section>
</main>
@endsection
