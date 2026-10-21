# Build log

## 2026-10-21 — Fundação da Fair Queue

- Criado o roadmap do projeto.
- Definida a arquitetura com `schedule_jobs` entre a aplicação e Horizon.
- Implementada a política `LeastActivePolicy`.
- Implementados limite global por fila, claim, dispatch e ciclo de vida.
- Implementada recuperação de jobs presos em `claimed`.
- Adicionados token de dispatch e rastreamento de tentativas.
- Última validação: 14 testes e 37 assertions passaram.

## 2026-10-21 — Claim concorrente e watcher configurável

- Adicionado lock transacional por fila antes da seleção e do claim.
- Adicionados testes para claim e para a indisponibilidade de um job já
  reivindicado por um segundo scheduler.
- O watcher passou a aceitar iterações configuráveis e a opção `--once`.
- Registrada a decisão ADR 0003 sobre o lock por fila.

### Limitações desta fase

- A garantia cobre claims duplicados no banco, não exactly-once entre banco e
  broker.
- A concorrência real depende do banco; SQLite não é evidência suficiente para
  múltiplos processos.

### Limitações atuais

- Banco e broker ainda não participam de uma transação distribuída.
- Idempotência completa ainda não foi implementada.
- A política ainda faz consultas por grupo e precisa de otimização.

## 2026-10-21 — Observabilidade e diagnóstico

- Emitidos eventos de seleção, claim, dispatch, conclusão e falha.
- Criadas métricas de estado, espera média, throughput e distribuição por
  grupo.
- Criado o comando fair-queue:diagnose com filtro de fila e janela temporal.
- Adicionadas tags estáveis de schedule job e dispatch token para Horizon.
- Registrados ADR 0007 e o conceito de observabilidade.
- Validação final: Pint passou; 28 testes e 73 assertions passaram.

### Limitações desta fase

- As métricas são calculadas sob demanda a partir do banco.
- Ainda não há séries temporais, contadores incrementais ou dashboard próprio.
- O custo do diagnóstico pode crescer com o histórico antes da retenção.

## 2026-10-21 — Primeiro núcleo do pacote Composer

- Criado o pacote local packages/fair-queue com composer.json próprio.
- Extraídos contratos de política e cursor, GroupSnapshot e as políticas
  independentes least-active e round-robin.
- Adicionado autoload Composer do monorepo e script test:package.
- Adicionados README, ADR 0008 e testes independentes do pacote.
- Validação do pacote: 2 testes e 3 assertions passaram.

### Limitações desta etapa

- Actions Eloquent, jobs, listeners, eventos e migrations ainda usam adapters
  da aplicação.
- Não há ainda CI, análise estática dedicada ou publicação versionada.

## 2026-10-21 — Adapter Eloquent do pacote

- Adicionado EloquentScheduleJob ao pacote.
- Adicionada migration consolidada com índices e campos de lifecycle.
- Adicionado FairQueueServiceProvider com publicação de config e migration.
- App\Models\ScheduleJob passou a herdar do model do pacote como bridge.
- Registrado ADR 0010 sobre migrations históricas e provider.
- Validação: 3 testes e 5 assertions no pacote; 28 testes e 73 assertions na
  aplicação.

### Limitações desta etapa

- O provider do pacote ainda não é registrado na aplicação atual para evitar
  duplicação das migrations.
- Actions, dispatch, listeners, eventos e comandos continuam no namespace App.

## 2026-10-21 — Job de execução no pacote

- Extraído `FairQueue\Jobs\ExecuteScheduledJob` para o pacote.
- Mantido `App\Jobs\ExecuteScheduledJob` como bridge compatível.
- O job reutilizável consulta o model Eloquent do pacote e preserva a
  identidade única pelo `dispatch_token` e as tags de observabilidade.
- Registrado ADR 0011 sobre a fronteira do job e a migração incremental.
- Validação: 4 testes e 7 assertions no pacote; 28 testes e 73 assertions na
  aplicação; Pint passou.

### Limitações desta etapa

- O dispatch, o listener de lifecycle e os eventos ainda dependem dos adapters
  da aplicação.
- O pacote agora é explicitamente um adapter Laravel nessa parte, não apenas
  um núcleo de políticas independente.

## 2026-10-21 — Lifecycle no pacote

- Extraído `FairQueue\\Listeners\\ScheduleJobLifecycleListener` para o pacote.
- Extraídos os eventos de conclusão e falha do lifecycle.
- Mantido o listener da aplicação como bridge, preservando os modelos e
  eventos nos namespaces existentes.
- Registrado ADR 0012 sobre a configuração do model e compatibilidade dos
  eventos.
- Validação: 4 testes e 7 assertions no pacote; 28 testes e 73 assertions na
  aplicação; Pint passou.

### Limitações desta etapa

- Dispatch e eventos de seleção, claim e dispatch ainda não foram extraídos.
- O pacote continua usando os componentes Laravel nesta camada de integração.

## 2026-10-21 — Adapters de claim e dispatch no pacote

- Extraídos `ClaimNextScheduleJobAction` e `DispatchScheduleJobAction`.
- Criado o contrato `ScheduleJobSelector` para injetar o seletor da aplicação.
- Extraídos os eventos de seleção, claim e dispatch.
- Mantido o contexto de payload da aplicação através de hooks do bridge.
- Registrado ADR 0013 sobre a fronteira dos adapters de dispatch.
- Validação: 4 testes e 7 assertions no pacote; 28 testes e 73 assertions na
  aplicação; Pint passou.

### Limitações desta etapa

- O seletor concreto e sua política configurável ainda estão no namespace App.
- O pacote ainda não possui análise estática dedicada.

## 2026-10-21 — Seletor concreto no pacote

- Extraído `SelectNextJobByQueueAction` para o pacote.
- Criado `QueueFairnessPolicy`, separado do contrato de políticas puras.
- Mantido o bridge da aplicação para factory configurável e eventos legados.
- Registrado ADR 0014 sobre a separação entre policy de domínio e policy de
  consulta Eloquent.
- Validação: 4 testes e 7 assertions no pacote; 28 testes e 73 assertions na
  aplicação; Pint passou.

### Limitações desta etapa

- As implementações configuráveis de least-active e round-robin ainda ficam na
  aplicação e precisam ser alinhadas ao contrato público do pacote.
- A análise estática dedicada permanece pendente.

## 2026-10-21 — Análise estrutural e contrato das políticas

- As políticas configuráveis da aplicação passaram a estender o contrato
  `FairQueue\\Contracts\\QueueFairnessPolicy`.
- Adicionada análise estrutural dedicada para sintaxe e dependências proibidas
  do pacote.
- Integrado o comando `analyse:package` ao CI.
- Validação: análise estrutural, Pint, 4 testes do pacote e 28 testes da
  aplicação passaram.

### Limitações desta etapa

- PHPStan não está instalado como dependência direta; análise tipada fica para
  quando a dependência puder ser adicionada e o lock atualizado.

## 2026-10-21 — Primeiro vertical slice web

- Substituído o dashboard estático por métricas e jobs reais de `schedule_jobs`.
- Adicionados filtros por fila e status.
- A página autenticada passou a listar a atividade recente do próprio usuário.
- Adicionados testes HTTP para dashboard, filtros e atividade.
- Registrados ADR 0015 e o conceito de vertical slice web.
- Validação: 3 testes web e 11 assertions específicas; suíte completa com 31
  testes e 84 assertions passou.

### Limitações desta fase

- A atualização exige recarregar a página; não há WebSockets ou polling.
- O dashboard não controla workers, watcher ou configuração de políticas.
- A duração do payload não é desserializada na interface para evitar executar
  dados serializados apenas para apresentação.

## 2026-10-21 — Enum de domínio no pacote

- Movido o enum de status para FairQueue\StatusScheduleJobsEnum.
- Mantido alias temporário em App\StatusScheduleJobsEnum para compatibilidade.
- Registrado ADR 0009 sobre a migração incremental do domínio.
- Validação: suíte do pacote e suíte da aplicação passaram.

## 2026-10-21 — CI e versionamento inicial do pacote

- Adicionado workflow de CI para Composer, Pint, pacote e aplicação.
- Adicionado CHANGELOG com política de versionamento semântico a partir da
  primeira publicação.
- Adicionado script lint:package.
- Validação do recorte: 3 testes e 5 assertions no pacote; 28 testes e 73
  assertions na aplicação.

### Limitações desta etapa

- A análise estática dedicada ainda não foi adicionada.
- Persistência, lifecycle, migrations e concorrência ainda não foram movidos
  para o pacote.

## 2026-10-21 — Persistência, performance e retenção

- Substituído o N+1 da least-active por uma agregação SQL com LEFT JOIN.
- Adicionados índices para recuperação de claims e limpeza de estados terminais.
- Criado PruneScheduleJobsAction com retenções separadas para sucesso e falha.
- Criado o comando fair-queue:prune, com filtro por fila e --dry-run.
- Avaliado Redis: ele permanece adequado para cursor e locks auxiliares, mas
  não substitui o lock transacional do claim.
- Registrados ADR 0006 e o conceito de persistência/retenção.
- Validação específica: 11 testes e 20 assertions passaram.

### Limitações desta fase

- MySQL e Redis não estavam disponíveis para teste de integração neste
  ambiente.
- A política round-robin ainda usa cache para o cursor.
- A retenção é manual via comando; agendamento automático fica para integração
  operacional posterior.

## 2026-10-21 — Configuração e políticas de fairness

- Criado contrato de política e factory configurável.
- Separadas as políticas least-active e round-robin.
- Adicionado limite opcional de jobs ativos por grupo.
- Adicionado aging opcional ao score least-active.
- Weighted fair queueing e prioridades foram avaliados e adiados até existir
  contrato público e métricas adequados.
- Registrado ADR 0005 sobre políticas e limites configuráveis.
- Validação: 23 testes e 56 assertions passaram.

### Limitações desta fase

- A configuração ainda é local da aplicação e não é publicável como pacote.
- Round-robin usa cache para o cursor e depende de cache compartilhado em
  múltiplos processos.
- A política ainda executa consultas por grupo; a otimização fica para a
  Fase 5.

## 2026-10-21 — Ciclo de vida, retries e idempotência

- Criado o wrapper único ExecuteScheduledJob, identificado pelo dispatch_token.
- Tornadas condicionais as transições de ciclo de vida para evitar reabrir jobs
  finalizados em entregas repetidas.
- Registrados attempts, exceções que resultarão em retry e timeouts do worker.
- Adicionadas colunas de tentativa e timeout à tabela schedule_jobs.
- Registrados ADR 0004 e o conceito de retries/timeouts.
- Validação final: Pint passou; 20 testes e 52 assertions passaram.

### Limitações desta fase

- Exactly-once entre banco, cache e broker não é garantido.
- A unicidade depende de um cache compartilhado e compatível com locks.
- O worker continua responsável por tries, backoff, retryUntil e timeout.

## 2026-10-21 — Navegação e experiência de demonstração

- Corrigidos os links do layout autenticado para apontar às rotas nomeadas de
  solicitação e dashboard.
- Adicionado logout visível, identificação do usuário e navegação responsiva.
- Adicionados atalhos para solicitar jobs, atualizar o dashboard e retornar ao
  fluxo de teste.
- Adicionado roteiro visual explicando o papel do watcher e do Horizon e os
  estados observáveis do job.
- Registrado ADR 0016 sobre navegação e feedback do fluxo demonstrável.
- Adicionados testes HTTP para os links essenciais entre as telas.

### Limitações desta etapa

- A atualização do dashboard continua manual; polling e WebSockets ficaram
  fora do escopo.
- A interface não inicia watcher, Horizon ou serviços de banco/broker.
