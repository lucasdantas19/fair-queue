# Conceito — retries e timeout do worker

O Laravel controla quando uma exceção resulta em retry e quando uma tentativa
é considerada falha final. A Fair Queue não deve duplicar essa política:
JobExceptionOccurred registra uma tentativa que ainda pode ser repetida, e
JobFailed registra a falha definitiva.

JobProcessing registra o número da tentativa e mantém o job ocupando
capacidade. JobTimedOut registra o timeout e a duração configurada, mas não
marca automaticamente o job como finalizado, porque o worker pode tentar
novamente conforme sua configuração.

A capacidade só é liberada por JobProcessed ou JobFailed. Assim, retries
não fazem o grupo parecer livre enquanto o Laravel ainda pode executar o job.
