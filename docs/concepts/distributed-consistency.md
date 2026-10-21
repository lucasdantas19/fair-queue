# Conceito — consistência entre banco e broker

O banco intermediário e o broker da fila são sistemas separados. Não é
possível confirmar ambos com a transação SQL comum usada pelo claim.

Se o broker aceitar o job e o processo morrer antes de atualizar o banco,
existe risco de reenvio. Por isso o projeto registra um token estável,
tentativas e erros de dispatch, mas ainda precisa de uma estratégia explícita
de deduplicação/idempotência.
