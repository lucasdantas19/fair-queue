# ADR 0006 — Agregação de seleção e retenção de histórico

## Status

Aceita para a Fase 5.

## Decisão

A least-active calcula as contagens ativas de todos os grupos em uma
agregação SQL e as combina com os grupos pendentes por LEFT JOIN, evitando
uma consulta de contagem por grupo.

Registros finished e failed possuem retenções configuráveis e podem ser
removidos pelo comando fair-queue:prune. O comando oferece --dry-run e
filtro por fila.

## Consequências

- A seleção reduz o número de consultas e mantém a regra no banco.
- A limpeza evita crescimento indefinido de schedule_jobs.
- Jobs pending, claimed, dispatched e running nunca são removidos pela
  retenção.
- Redis continua sendo usado para cursor de round-robin, mas não substitui o
  lock transacional do claim.

## Alternativas consideradas

- Manter uma consulta por grupo: mais simples, mas escala com o número de
  grupos.
- Manter contadores em Redis: reduziria leituras, mas exigiria sincronização
  adicional com o ciclo de vida no banco.
- Remover por idade de created_at: poderia apagar jobs cujo ciclo ainda não
  terminou.
