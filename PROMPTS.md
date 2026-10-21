# Prompts de trabalho

## Orientação

Leia `AGENTS.md`, `GOALS.md`, `ROADMAP.md`, `docs/build-log.md` e os arquivos
da fase atual. Faça uma análise somente leitura, explique o estado atual,
os conceitos relevantes e as ambiguidades. Não altere arquivos.

## Preparar uma fase

Leia os documentos de planejamento e proponha a implementação de uma única
fase. Liste arquivos, critérios de aceitação, testes, riscos, não objetivos e
decisões que precisam de confirmação humana. Aguarde aprovação antes de editar.

## Executar uma fase aprovada

Implemente apenas a fase aprovada. Preserve mudanças existentes, use testes
orientados ao comportamento, execute a validação e atualize o roadmap, o
build-log e os documentos de decisão/conceito relacionados. Não inicie a
Cpróxima fase automaticamente.

## Verificação

Verifique o diff lógico, execute os testes relevantes, confira migrations,
configuração e sintaxe, e relate evidências observadas. Separe falhas novas
de problemas preexistentes.

## Revisão

Faça uma revisão crítica da fase atual procurando condições de corrida,
duplicação, starvation, falhas de retry, problemas de segurança, acoplamento
com o domínio e testes ausentes. Proponha correções sem aplicá-las até haver
aprovação.

## Encerramento da fase

Atualize o status no `ROADMAP.md`, registre decisões e aprendizados, informe
os testes executados, as limitações restantes e o próximo passo recomendado.
Pare após essa entrega.
