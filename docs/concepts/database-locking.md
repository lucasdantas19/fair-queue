# Conceito — locking e claim atômico

Selecionar e atualizar um job em operações separadas permite que dois
schedulers escolham o mesmo registro ou ultrapassem a capacidade global da
fila. O claim agrupa a decisão em uma transação e bloqueia os registros da
fila com `lockForUpdate()` antes de reavaliar a política e alterar o job.

Isso serializa schedulers que disputam a mesma fila e evita claims duplicados
no banco, mas não transforma banco e Redis em uma única transação. A falha
entre o dispatch e a atualização do status ainda exige idempotência e
recuperação.

O mecanismo depende de um banco que implemente locking transacional. SQLite é
útil para testes funcionais, mas não representa adequadamente a concorrência
entre processos.
