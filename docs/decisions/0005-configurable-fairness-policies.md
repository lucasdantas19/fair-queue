# ADR 0005 — Políticas e limites configuráveis

## Status

Aceita para a Fase 4.

## Decisão

A seleção recebe uma política por nome, configurada globalmente ou por fila.
As políticas disponíveis inicialmente são least-active e round-robin.

O limite global continua sendo definido por fila. Um limite opcional por grupo
é configurado por group_key dentro da fila e é aplicado antes de liberar um
job.

Aging é opcional e usa o tempo do job pendente para reduzir o score efetivo de
um grupo no least-active. O valor padrão zero preserva o comportamento
anterior.

## Consequências

- A política pode ser trocada sem alterar o domínio da aplicação.
- Round-robin depende do cache para manter o cursor entre seleções.
- Limites por grupo são avaliados dentro do claim transacional.
- Aging melhora a espera de jobs antigos, mas pode priorizar um grupo com mais
  jobs ativos quando o tempo de espera superar o parâmetro configurado.
- A publicação de configuração para um pacote Composer fica para a Fase 7.

## Alternativas consideradas

- Selecionar a política por if/else no seletor: simples, mas acopla o núcleo a
  cada algoritmo.
- Persistir o cursor do round-robin em schedule_jobs: mistura estado de
  política com o registro de jobs.
- Implementar pesos e prioridades agora: exigiria definir contrato, origem dos
  pesos, aging de prioridades e métricas antes de haver casos de uso claros.
