# Fair Queue

Fair Queue é uma camada de admissão para Laravel Queue e Horizon que distribui
jobs de forma justa entre diferentes grupos, evitando que um único usuário
monopolize a capacidade dos workers.

O projeto registra jobs pendentes em uma tabela intermediária, aplica políticas
de fairness, faz claim atômico e libera os jobs para execução pelo Laravel
Queue/Horizon.

## O que o projeto demonstra

- seleção least-active e round-robin;
- limites globais e por grupo;
- claim transacional por fila;
- dispatch com token de correlação;
- ciclo de vida `pending`, `claimed`, `dispatched`, `running`, `finished` e
  `failed`;
- recuperação de jobs reivindicados que ficaram órfãos;
- retries, timeouts e deduplicação no limite entre banco e broker;
- métricas, diagnóstico e dashboard web;
- núcleo de políticas extraído para um pacote Composer local.

## Arquitetura

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
```

O Horizon continua responsável por workers, retries e monitoramento. A Fair
Queue controla a admissão e a política de seleção.

## Requisitos

- PHP 8.3+
- Composer
- MySQL ou SQLite para desenvolvimento
- Redis para o fluxo real com Horizon
- Node.js e npm para os assets frontend

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

Configure banco, Redis e `QUEUE_CONNECTION` no `.env` antes de executar o
fluxo real.

## Demonstração

```bash
php artisan serve
```

Em terminais separados:

```bash
php artisan app:watch-shedule-jobs
php artisan horizon
```

Depois acesse `http://localhost:8000/register`, crie um usuário e use `/home`
para solicitar jobs e `/admin/dashboard` para acompanhar os estados e as
métricas.

Diagnóstico pelo terminal:

```bash
php artisan fair-queue:diagnose --queue=default --window=30
```

## Testes e validações

```bash
composer test
composer run test:package
composer run lint:package
composer run analyse:package
```

## Documentação de estudo

- [Objetivos](GOALS.md)
- [Roadmap](ROADMAP.md)
- [Build log](docs/build-log.md)
- [Decisões arquiteturais](docs/decisions/)
- [Conceitos estudados](docs/concepts/)

## Limitações conhecidas

O projeto não promete exactly-once entre banco de dados e broker. A garantia de
claim duplicado cobre a persistência, mas a fronteira entre commit no banco e
envio ao broker continua sujeita às limitações de sistemas distribuídos.

MySQL, Redis e concorrência entre múltiplos processos devem ser validados no
ambiente de execução; os testes padrão usam infraestrutura isolada.

## Licença

Este projeto está disponível sob a [licença MIT](LICENSE).
