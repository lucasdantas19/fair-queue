# ADR 0007 — Eventos e diagnóstico operacional

## Status

Aceita para a Fase 6.

## Decisão

A Fair Queue emite eventos de seleção, claim, dispatch, conclusão e falha.
Esses eventos permitem consumidores externos adicionarem logs, métricas ou
integrações sem acoplar a política de fairness a um backend de observabilidade.

As métricas iniciais são calculadas a partir de schedule_jobs e expostas pelo
comando fair-queue:diagnose. O resumo inclui estado por status, jobs ativos,
espera média, throughput no período e conclusão por grupo.

ExecuteScheduledJob expõe tags estáveis de job e token para o Horizon.

## Consequências

- A observabilidade não altera a decisão de seleção.
- O diagnóstico consulta o banco e pode ser custoso em históricos grandes.
- Métricas históricas detalhadas e séries temporais ficam para uma evolução
  posterior.
- O comando é adequado para operação inicial, mas não substitui um dashboard.
