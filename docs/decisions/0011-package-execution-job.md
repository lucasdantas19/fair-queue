# ADR 0011 — Job de execução no pacote

## Status

Aceita para a Fase 7.

## Decisão

O job `FairQueue\Jobs\ExecuteScheduledJob` passa a ser a implementação
reutilizável do wrapper de execução. Ele usa o model Eloquent do pacote,
valida o `dispatch_token` e o status `running` antes de executar o payload,
mantendo unicidade e tags de observabilidade.

`App\Jobs\ExecuteScheduledJob` permanece como uma classe bridge que estende o
job do pacote. Assim, dispatchers e testes existentes continuam referenciando
o namespace da aplicação durante a migração.

## Alternativas

- Mover o job diretamente e atualizar todos os consumidores: reduziria a
  compatibilidade durante a extração.
- Manter a implementação somente em `App`: não criaria um adapter reutilizável
  para instalações novas do pacote.

## Consequências

- O pacote agora depende dos componentes Illuminate necessários para fila e
  Eloquent.
- A classe bridge preserva a API atual da aplicação.
- Dispatch, listener de lifecycle e eventos ainda permanecem no adapter da
  aplicação.
