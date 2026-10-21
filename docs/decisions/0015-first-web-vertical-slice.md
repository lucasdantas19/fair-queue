# ADR 0015 — Primeiro vertical slice web

## Status

Aceita para a Fase 8.

## Decisão

A primeira interface demonstrável será server-rendered com Blade e consultará
`schedule_jobs` sob demanda. O dashboard exibirá resumo, métricas da janela,
jobs recentes e filtros por fila e status. A área do usuário exibirá os jobs
solicitados pelo usuário autenticado.

## Alternativas

- Criar um dashboard em tempo real com WebSockets: adicionaria infraestrutura
  antes de validar o fluxo principal.
- Usar dados estáticos para validar apenas o layout: não demonstraria o
  comportamento real da Fair Queue.
- Integrar diretamente com Horizon: aumentaria o acoplamento e não substituiria
  a fonte de verdade intermediária `schedule_jobs`.

## Consequências

- A interface pode ser testada com a aplicação, banco e worker locais.
- Os dados são atualizados ao recarregar a página; não há atualização em tempo
  real nesta fase.
- A tela não controla workers, watcher ou políticas operacionais.
