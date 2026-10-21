# ADR 0012 — Lifecycle no pacote

## Status

Aceita para a Fase 7.

## Decisão

O pacote passa a fornecer `FairQueue\\Listeners\\ScheduleJobLifecycleListener`
e os eventos `FairQueue\\Events\\ScheduleJobCompleted` e
`FairQueue\\Events\\ScheduleJobFailed`.

O listener recebe o model Eloquent por configuração e preserva as transições
condicionais de status, tentativas, exceções e timeout. A aplicação mantém um
bridge que injeta `App\\Models\\ScheduleJob` e converte os dois eventos para
os namespaces históricos de `App\\Events`.

## Alternativas

- Remover os eventos antigos e atualizar todos os consumidores: quebraria a
  compatibilidade da aplicação durante a extração.
- Manter o listener somente em `App`: impediria a reutilização do adapter de
  lifecycle em uma instalação nova do pacote.

## Consequências

- A lógica de ciclo de vida passa a ser reutilizável pelo pacote.
- O pacote depende dos eventos de fila do Laravel e de Eloquent nesta camada.
- Os eventos de seleção, claim e dispatch continuam na integração da aplicação
  até que seus contratos sejam estabilizados.
