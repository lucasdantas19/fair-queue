# Fair Queue — roadmap

## Objetivo

Construir uma biblioteca para Laravel que funcione como uma camada de admissão
entre a aplicação e o Laravel Queue/Horizon. A aplicação registra o job, a
Fair Queue decide quando ele pode ser liberado e o Horizon continua
responsável por executar e monitorar os workers.

O requisito central é impedir que um único grupo — inicialmente um usuário —
consuma toda a capacidade disponível enquanto existirem jobs de outros grupos.

## Arquitetura-alvo

```text
Aplicação
   │ push()
   ▼
Pending Job Store (schedule_jobs)
   │ seleção + claim atômico
   ▼
Fair Dispatcher
   │ dispatch()
   ▼
Laravel Queue / Redis
   │
   ▼
Horizon workers
   │ eventos de ciclo de vida
   ▼
Status e métricas
```

Horizon não será substituído. Ele continuará gerenciando processos,
supervisores, retries e observabilidade. A biblioteca será responsável pela
política de seleção e pelo controle de admissão.

## Princípios

- A política de fairness deve ser independente do domínio da aplicação.
- A seleção precisa ser determinística e explicável.
- Reservar um job deve ser uma operação atômica.
- O limite global e o limite por grupo devem ser configuráveis.
- Jobs não podem ficar indefinidamente sem oportunidade de execução.
- O comportamento deve ser testável sem depender de workers reais.
- A integração com Laravel deve preservar a API familiar de `dispatch()`.

## Etapas

### Fase 0 — Fundamentos e contrato

- [x] Documentar objetivo, arquitetura e decisões iniciais.
- [x] Definir `pending`, `dispatched`, `running`, `finished` e `failed`.
- [ ] Definir a API pública da biblioteca.
- [ ] Separar código de domínio da aplicação do código reutilizável.

### Fase 1 — Seleção justa (em andamento)

- [x] Registrar jobs pendentes na tabela intermediária.
- [x] Selecionar o grupo com menor quantidade de jobs ativos.
- [x] Desempatar pelo job pendente mais antigo.
- [x] Retornar `null` quando não houver job elegível.
- [x] Tornar o limite global configurável por fila.
- [x] Adicionar índices para a seleção por fila, grupo e status.
- [x] Cobrir fila vazia e jobs em execução nos testes.
- [x] Extrair a política inicial para `LeastActivePolicy`.
- [ ] Adicionar políticas configuráveis de prioridade e peso.

Critério de conclusão: com vários grupos pendentes, o grupo com menos jobs
ativos é escolhido; em empate, o job mais antigo é escolhido; nenhum job é
duplicado pelo seletor em uma execução isolada.

### Fase 2 — Dispatch e claim atômico (concluída com limitação documentada)

- [x] Implementar o dispatch real do payload.
- [x] Criar a transição atômica `pending -> claimed`.
- [x] Usar transação e locking compatíveis com os bancos suportados.
- [x] Evitar que dois schedulers liberem o mesmo job.
- [x] Tornar o watcher contínuo ou integrá-lo a um scheduler configurável.

Critério de conclusão: schedulers concorrentes podem disputar uma fila sem
duplicar claims no banco. A garantia exactly-once entre banco e broker
permanece explicitamente fora desta fase.

### Fase 3 — Ciclo de vida (concluída com limitação documentada)

- [x] Correlacionar o job intermediário com o job real do Laravel.
- [x] Atualizar para `running` em `JobProcessing`.
- [x] Atualizar para `finished` em `JobProcessed`.
- [x] Atualizar para `failed` em falhas e registrar o erro.
- [x] Recuperar jobs `claimed` que ficaram órfãos após a queda do scheduler.
- [x] Definir comportamento para retries e timeout do worker.
- [x] Criar token estável e registrar tentativas de dispatch.
- [x] Implementar deduplicação/idempotência no limite entre banco e broker.

### Fase 4 — Configuração e políticas (concluída com limitação documentada)

- [ ] Criar configuração publicada da biblioteca.
- [x] Configurar capacidade global por fila.
- [x] Configurar limite por grupo.
- [x] Implementar round-robin/least-active como políticas separadas.
- [x] Avaliar weighted fair queueing e prioridades.
- [x] Adicionar aging para evitar starvation.

Critério de conclusão: a política e os limites podem ser alterados por
configuração sem modificar o domínio; least-active, round-robin, limite por
grupo e aging têm testes comportamentais. A publicação para um pacote
Composer permanece para a Fase 7.

### Fase 5 — Persistência e performance (concluída com limitações documentadas)

- [x] Adicionar índices para seleção por fila, grupo e status.
- [x] Reduzir consultas por seleção.
- [x] Avaliar Redis para contadores e locks de alta concorrência.
- [x] Definir limpeza e retenção de registros históricos.
- [ ] Testar SQLite, MySQL e Redis onde aplicável.

Critério de conclusão: a seleção evita consultas N+1, os índices cobrem
seleção e recuperação, e a retenção de jobs terminais é configurável e
testável. MySQL e Redis permanecem validações pendentes quando os serviços não
estão disponíveis no ambiente.

### Fase 6 — Observabilidade (concluída com limitações documentadas)

- [x] Emitir eventos de seleção, claim, dispatch e conclusão.
- [x] Expor métricas de espera, throughput e fairness.
- [x] Integrar tags com Horizon.
- [x] Criar uma tela ou comando de diagnóstico.

Critério de conclusão: as transições principais emitem eventos, o Horizon
recebe tags estáveis e o comando de diagnóstico apresenta estado, espera,
throughput e distribuição por grupo. Um dashboard dedicado permanece fora do
escopo.

### Fase 7 — Qualidade de pacote (em andamento)

- [x] Extrair contratos e políticas puras para um pacote Composer local.
- [x] Remover dependências de `App\\Models` e `App\\Jobs` no núcleo extraído.
- [x] Adicionar documentação inicial de instalação e exemplos.
- [x] Adicionar testes unitários independentes para o pacote.
- [x] Preparar CI e política inicial de versionamento semântico.
- [x] Adicionar análise estática estrutural dedicada.

Critério parcial: as políticas extraídas não dependem da aplicação e passam
em testes próprios. O adapter Eloquent e a migration base já foram extraídos;
lifecycle e integração de concorrência ainda precisam ser formalizados como
adapters do pacote. O wrapper de execução, o listener de lifecycle, o seletor e
as actions de claim/dispatch já foram extraídos, com bridges compatíveis em
`App\\Jobs`, `App\\Listeners` e `App\\Actions`.

A análise tipada com PHPStan permanece opcional e pendente de uma dependência
direta instalada no ambiente.

### Fase 8 — Primeiro vertical slice web (concluída com limitações)

- [x] Exibir métricas reais de `schedule_jobs` no dashboard.
- [x] Listar jobs recentes com filtros por fila e status.
- [x] Exibir a atividade recente do usuário autenticado.
- [x] Cobrir dashboard e atividade com testes HTTP.
- [x] Documentar o fluxo demonstrável e seu escopo.

Critério de conclusão: a interface permite solicitar jobs e visualizar dados
reais persistidos, estados e métricas básicas. Atualização em tempo real,
gráficos avançados e controle operacional permanecem fora desta fase.

## Decisões em aberto

- O grupo será identificado por `group_key + group_value`, não apenas por
  `group_value`.
- O limite padrão inicial será global por fila; limites por grupo virão na
  fase de configuração.
- O payload serializado é aceitável para o protótipo, mas a API pública deve
  esconder esse detalhe e documentar as restrições de segurança.
- A primeira política será least-active com desempate FIFO. Algoritmos mais
  sofisticados só serão adicionados depois de métricas e testes de fairness.

## Critérios de sucesso do projeto

O projeto estará pronto para ser apresentado como portfólio quando conseguir
demonstrar, com testes e métricas, que:

1. um usuário com muitos jobs não monopoliza os workers;
2. um usuário com poucos jobs recebe atendimento previsível;
3. jobs não são duplicados em presença de múltiplos schedulers;
4. falhas e retries não deixam a capacidade contabilizada incorretamente;
5. a política pode ser configurada sem alterar o domínio da aplicação.
