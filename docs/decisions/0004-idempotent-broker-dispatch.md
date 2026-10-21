# ADR 0004 — Wrapper único para dispatch no broker

## Status

Aceita para a Fase 3.

## Contexto

O banco e o broker não compartilham uma transação. Se o processo cair depois
que o broker aceitar uma mensagem, mas antes de schedule_jobs ser atualizado,
o scheduler pode tentar liberá-la novamente.

## Decisão

O job intermediário será liberado como ExecuteScheduledJob, um job Laravel
único identificado pelo dispatch_token. O wrapper valida o token e o estado
do registro antes de executar o payload original de forma síncrona.

Eventos de ciclo de vida só alteram o registro quando o token e o status atual
forem compatíveis com a transição. Uma entrega repetida de um job já finalizado
não reabre sua capacidade nem executa o payload novamente.

## Consequências

- O token estável passa a ser também a chave de unicidade do broker.
- O cache configurado pelo Laravel precisa suportar locks para que a unicidade
  funcione entre processos.
- O mecanismo reduz duplicações, mas não prova exactly-once em uma falha
  arbitrária entre banco, cache e broker.
- O payload original continua serializado e suas restrições permanecem.

## Alternativas consideradas

- Enviar diretamente o payload original: não oferece uma chave genérica de
  unicidade.
- Marcar o job como dispatched antes do envio: evita reenvio, mas pode perder
  jobs quando o broker não aceita a mensagem.
- Usar uma tabela de deduplicação própria: ofereceria mais controle, porém
  duplicaria o mecanismo de locks do Laravel.
