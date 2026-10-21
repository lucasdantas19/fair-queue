# ADR 0009 — Enum de status no pacote

## Status

Aceita para a Fase 7.

## Decisão

O enum de ciclo de vida pertence ao namespace FairQueue. O namespace
App\StatusScheduleJobsEnum permanece como alias temporário para preservar a API
interna existente durante a migração dos adapters.

## Consequências

- O pacote passa a ser a fonte única dos valores de status.
- Código novo deve importar FairQueue\StatusScheduleJobsEnum.
- O alias da aplicação poderá ser removido quando Actions, model e migrations
  forem movidos para o pacote.
