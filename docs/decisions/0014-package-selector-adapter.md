# ADR 0014 — Seletor concreto no pacote

## Status

Aceita para a Fase 7.

## Decisão

O pacote fornece `SelectNextJobByQueueAction` e o contrato
`QueueFairnessPolicy`. A action recebe uma factory de política e o model
Eloquent por injeção, mantendo no pacote a verificação de capacidade global,
limite por grupo e seleção FIFO dentro do grupo escolhido.

`App\\Actions\\FairQueue\\SelectNextJobByQueueAction` permanece como bridge:
adapta a factory configurável da aplicação e os eventos históricos.

## Consequências

- A cadeia selector → claim → dispatch está agora no pacote.
- As políticas concretas ainda pertencem à aplicação, mas obedecem a um
  contrato de fila do pacote.
- A policy domain `FairQueue\\Contracts\\FairnessPolicy` continua separada do
  contrato Eloquent `QueueFairnessPolicy`.
