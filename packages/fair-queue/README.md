# Fair Queue Core

Núcleo independente das políticas de fairness da Fair Queue.

## Estado

Este pacote local contém contratos, value objects e as políticas
least-active e round-robin. Ele não depende de App\Models, App\Jobs ou da
aplicação Laravel. O enum de status também pertence ao namespace FairQueue.

O adapter Eloquent, o provider, a migration base, o job de execução e o
listener de lifecycle já estão disponíveis neste pacote, junto com os adapters
de seleção, claim e dispatch.

## Uso

Exemplo:

    use FairQueue\Policies\LeastActivePolicy;

    $policy = new LeastActivePolicy;
    $selected = $policy->select($groups);

$groups deve ser um iterable de FairQueue\ValueObjects\GroupSnapshot.
O round-robin recebe uma implementação de FairQueue\Contracts\CursorStore.

O job FairQueue\Jobs\ExecuteScheduledJob usa o dispatch_token como identidade
única e consulta FairQueue\Persistence\EloquentScheduleJob antes de executar
o payload serializado.

O listener FairQueue\\Listeners\\ScheduleJobLifecycleListener trata os eventos
de processamento do Laravel e publica os eventos de conclusão e falha do
namespace FairQueue\\Events.

As actions FairQueue\\Actions\\ClaimNextScheduleJobAction e
FairQueue\\Actions\\DispatchScheduleJobAction recebem o seletor e o model
por injeção, permitindo que uma aplicação forneça seus próprios bridges.
O seletor FairQueue\\Actions\\SelectNextJobByQueueAction recebe uma factory de
política compatível com FairQueue\\Contracts\\QueueFairnessPolicy.

## Instalação local

No monorepo, o autoload raiz expõe o namespace FairQueue. Para consumir o
pacote isoladamente, execute composer install e vendor/bin/phpunit.

O comando `composer run analyse:package` valida a sintaxe do pacote e impede
dependências diretas do namespace `App`. Uma análise tipada com PHPStan ainda
não faz parte desta versão.
