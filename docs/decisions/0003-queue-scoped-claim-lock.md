# ADR 0003 — Lock por fila durante o claim

## Status

Aceita para a Fase 2.

## Contexto

Dois schedulers podem calcular simultaneamente que existe capacidade e
selecionar jobs diferentes antes de atualizarem seus registros. Bloquear
somente o job selecionado não protege a contagem global de jobs ativos.

## Decisão

O claim será executado em uma transação que bloqueia os registros existentes da
fila com `lockForUpdate()`. A política é reavaliada depois desse bloqueio, e o
job selecionado é confirmado como `claimed` somente se ainda estiver pendente.

## Consequências

- Schedulers da mesma fila são serializados durante a seleção e o claim.
- A capacidade global é reavaliada sob o lock.
- A concorrência entre filas diferentes não é bloqueada.
- A solução depende de locking transacional do banco.
- O dispatch para o broker continua fora da transação e não tem garantia
  exactly-once.

## Alternativas consideradas

- Lockar apenas o registro selecionado: não protege a capacidade global.
- Usar lock distribuído em Redis: adicionaria uma dependência e uma segunda
  fonte de consistência nesta fase.
- Usar uma tabela de mutex por fila: seria mais explícito, mas aumentaria o
  modelo de persistência antes de validar o núcleo.
