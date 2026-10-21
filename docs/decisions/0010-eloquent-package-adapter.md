# ADR 0010 — Adapter Eloquent no pacote

## Status

Aceita para a Fase 7.

## Decisão

O pacote fornece EloquentScheduleJob, uma migration consolidada e
FairQueueServiceProvider com publicação de configuração e migration.
App\Models\ScheduleJob herda do model do pacote durante a migração.

O provider do pacote não é registrado na aplicação existente enquanto as
migrations históricas locais permanecerem ativas. Em uma instalação nova do
pacote, o provider poderá carregar a migration consolidada.

## Consequências

- O schema e os casts principais passam a ter uma representação reutilizável.
- Factories e referências existentes da aplicação continuam funcionando pelo
  bridge.
- Actions, listeners, eventos e comandos ainda precisam ser movidos para
  adapters/serviços do pacote.
- A instalação do pacote precisa escolher entre migrations publicadas ou
  migrations carregadas automaticamente, não ambas.
