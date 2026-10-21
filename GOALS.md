# Fair Queue — objetivos

## Propósito

Construir uma camada de admissão para Laravel Queue/Horizon que impeça um
único grupo, inicialmente um usuário, de monopolizar a capacidade disponível
dos workers.

## Resultados desejados

- Jobs são registrados antes de serem liberados para o broker.
- A política de seleção é configurável e testável.
- A quantidade de jobs ativos por fila e por grupo é controlada.
- Múltiplos schedulers não despacham o mesmo job por engano.
- O ciclo de vida do job é observável.
- O projeto explica os conceitos de filas, concorrência e sistemas distribuídos.
- O código pode evoluir para um pacote Composer independente.

## Escopo atual

- Laravel Queue e Horizon.
- Persistência intermediária em banco.
- Seleção least-active com desempate FIFO.
- Claim atômico e recuperação de jobs órfãos.
- Testes automatizados e documentação de decisões.

## Fora do escopo inicial

- Garantia de exactly-once entre banco e broker.
- Substituição do Horizon.
- Suporte imediato a todos os brokers Laravel.
- Painel completo de métricas antes do núcleo estar estável.

## Critérios de sucesso

O projeto deve demonstrar, com testes e documentação, que usuários com muitos
jobs não monopolizam a fila, que jobs não ficam presos indefinidamente e que
as limitações de consistência distribuída são explicitamente tratadas.
