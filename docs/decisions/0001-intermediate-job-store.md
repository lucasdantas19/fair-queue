# ADR 0001 — Fila intermediária persistida

## Status

Aceita.

## Contexto

Enviar diretamente para Laravel Queue permite que um usuário com muitos jobs
ocupe a fila antes que a aplicação consiga aplicar uma política de fairness.

## Decisão

Persistir o job em `schedule_jobs` como `pending` e liberá-lo somente depois
que a política de fairness o selecionar.

## Consequências

- A aplicação ganha controle sobre admissão e capacidade.
- É necessário sincronizar o registro intermediário com o job real.
- O sistema passa a ter problemas de consistência entre banco e broker para
  tratar explicitamente.
