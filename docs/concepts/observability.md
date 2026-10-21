# Conceito — observabilidade de filas

Eventos representam fatos do domínio: um job foi selecionado, reivindicado,
liberado, concluído ou falhou. Métricas são agregações desses fatos e ajudam a
responder perguntas diferentes.

Tempo de espera mede o intervalo entre o registro pendente e o início.
Throughput mede quantos jobs terminaram em uma janela. A distribuição por
grupo ajuda a verificar se a política está evitando monopólio.

O diagnóstico inicial lê o estado persistido. Em produção, séries temporais
dedicadas e contadores incrementais podem ser necessários para reduzir custo e
preservar histórico após a retenção.
