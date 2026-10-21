# ADR 0008 — Extração incremental para pacote local

## Status

Aceita para a Fase 7.

## Contexto

O núcleo atual mistura políticas, Eloquent, payloads Laravel, jobs da
aplicação e migrations. Uma extração integral em uma única alteração
acoplaria a API pública ao modelo atual e aumentaria o risco de regressão.

## Decisão

Extrair primeiro um pacote local com contratos, value objects e políticas
puras. A aplicação continua fornecendo adapters para persistência, cache e
dispatch enquanto esses contratos são definidos.

O pacote usa namespace FairQueue e possui composer.json próprio. O monorepo
mantém autoload para permitir testes independentes antes de publicar no
Packagist ou em um repositório separado.

## Consequências

- As políticas podem ser testadas sem banco ou modelos da aplicação.
- A API pública começa por tipos pequenos e explícitos.
- O núcleo operacional ainda não está completamente no pacote.
- A publicação e o versionamento semântico ficam para depois da extração dos
  adapters e da estabilização dos contratos.
