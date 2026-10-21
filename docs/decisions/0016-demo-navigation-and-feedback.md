# ADR 0016 — Navegação e feedback do fluxo demonstrável

## Contexto

O primeiro vertical slice permitia solicitar jobs e consultar o dashboard,
mas o menu autenticado apontava para a landing page pública. Também faltavam
atalhos entre as telas e uma indicação clara de como observar as transições
`pending -> dispatched -> running -> finished`.

## Decisão

Manter o backend e as rotas existentes e melhorar a experiência no layout
Blade compartilhado:

- usar as rotas nomeadas `home` e `admin.dashboard` na navegação;
- adicionar logout visível e identificação do usuário autenticado;
- adicionar atalhos para solicitar e atualizar o dashboard;
- explicar na interface o papel do watcher e do Horizon;
- preservar a atualização sob demanda, sem introduzir polling ou WebSockets.

## Alternativas

Polling automático ou WebSockets deixariam a tela mais dinâmica, mas
introduziriam infraestrutura e comportamento operacional fora do objetivo
desta etapa. A atualização manual torna a separação entre interface, watcher
e worker explícita e continua adequada ao protótipo.

## Validação e limitações

Os testes HTTP verificam os links essenciais das páginas autenticadas. A
interface continua dependendo de MySQL/Redis, do watcher e do Horizon para
demonstrar o ciclo real; ela não inicia esses processos sozinha.
