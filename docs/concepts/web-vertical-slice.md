# Conceito — Vertical slice web

Um vertical slice atravessa as camadas principais do sistema com uma pequena
funcionalidade completa. Nesta fase, a solicitação parte da interface, grava
um `schedule_job`, o dashboard consulta o mesmo registro e exibe seu status.

Isso permite estudar o fluxo inteiro antes de investir em componentes de
interface mais sofisticados. A página é server-rendered e a atualização ocorre
por nova requisição HTTP; worker e watcher continuam sendo processos separados.
