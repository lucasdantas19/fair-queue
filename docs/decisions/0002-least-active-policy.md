# ADR 0002 — Política least-active

## Status

Aceita para a primeira versão.

## Decisão

Escolher o grupo com menor quantidade de jobs ativos. Em caso de empate,
escolher o job pendente mais antigo.

## Motivo

Ordenar pela quantidade de jobs pendentes favorece justamente quem acumulou
mais jobs. A quantidade de jobs ativos representa melhor o uso atual da
capacidade.

## Alternativas futuras

- round-robin;
- weighted fair queueing;
- prioridade com aging;
- limites diferentes por grupo.
