# ADR 0013 — Adapters de claim e dispatch no pacote

## Status

Aceita para a Fase 7.

## Decisão

O pacote fornece `ClaimNextScheduleJobAction`,
`DispatchScheduleJobAction`, `ScheduleJobSelector` e os eventos de seleção,
claim e dispatch.

As actions recebem o model Eloquent e o seletor por injeção. O bridge da
aplicação injeta `App\\Models\\ScheduleJob`, `App\\Jobs\\ExecuteScheduledJob`
e mantém `FairQueuePayloadContext` por meio dos hooks `beforeDispatch` e
`afterDispatch`.

## Consequências

- A garantia de claim transacional e a transição para `dispatched` deixam de
  depender da implementação concreta em `App`.
- A aplicação preserva sua API pública e seus eventos históricos.
- O seletor concreto e o evento `ScheduleJobSelected` ainda ficam na aplicação,
  pois a política configurável atual ainda usa seus adapters locais.
