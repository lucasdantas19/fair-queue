<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

## Fair Queue study workflow

Este projeto também é um caso de estudo e portfólio. Ao trabalhar nele:

- leia `GOALS.md`, `ROADMAP.md` e `docs/build-log.md` antes de implementar;
- trabalhe em apenas uma fase do roadmap por vez;
- explique os conceitos envolvidos antes de alterações arquiteturais;
- registre decisões importantes em `docs/decisions/`;
- registre os conceitos estudados em `docs/concepts/`;
- atualize `docs/build-log.md` com alterações, testes e limitações;
- execute os testes relevantes antes de concluir uma fase;
- não faça commit, push, deploy ou publicação sem autorização explícita;
- pare ao terminar a fase escolhida e aguarde revisão humana.

Toda implementação deve responder: o que mudou, por que mudou, quais
alternativas existiam, como foi validada e quais limitações permanecem.
