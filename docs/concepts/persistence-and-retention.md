# Conceito — persistência, índices e retenção

Índices compostos devem acompanhar os filtros e ordenações mais frequentes.
Na Fair Queue, a seleção usa fila, status, grupo e tempo; a recuperação e a
limpeza usam status com seus timestamps de ciclo de vida.

A seleção least-active calcula contagens agregadas em uma única consulta. Isso
evita o padrão N+1, no qual cada grupo pendente dispara uma nova consulta para
contar seus jobs ativos.

Retenção é uma política de capacidade do armazenamento, não de fairness.
Somente jobs terminais são candidatos à limpeza, com períodos separados para
sucesso e falha. O modo dry-run permite revisar o volume antes da remoção.
