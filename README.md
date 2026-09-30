# MS Barbearia

Sistema web de atendimento e agendamento desenvolvido em PHP, com autenticação de clientes, disponibilidade de horários e integração com WhatsApp.

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![CI](https://img.shields.io/github/actions/workflow/status/Vinicius-Calegari/MS--Barberaria/php-quality.yml?label=PHP%20syntax)

## Funcionalidades

- cadastro e autenticação de clientes
- senhas armazenadas com `password_hash`
- sessões autenticadas
- agendamento e consulta de horários
- cancelamento vinculado ao usuário autenticado
- interface responsiva e integração com WhatsApp

## Segurança

A conexão PDO usa prepared statements nativos e credenciais externas ao código. Configure o ambiente com base em `.env.example`:

```text
DB_HOST=localhost
DB_PORT=3306
DB_NAME=ms_barbearia
DB_USER=root
DB_PASSWORD=
```

O login regenera o ID da sessão após autenticação e o cadastro aplica validação no servidor. Logs, arquivos `.env` e dados locais de agendamento são ignorados pelo Git.

> Dados pessoais de clientes nunca devem ser versionados. Se dados reais já apareceram no histórico do repositório, o commit atual não apaga cópias antigas do histórico.

## Desenvolvimento

Requer PHP 8+, PDO MySQL e MySQL/MariaDB. O schema básico é criado automaticamente pela aplicação quando o banco configurado já existe.

```text
config/database.php  conexão e schema
processa_login.php   autenticação
processa_cadastro.php cadastro
agendamento.php      fluxo de reserva
buscar-horarios.php  disponibilidade
css/                  estilos
img/                  assets
```

## Qualidade

O workflow de CI valida a sintaxe de todos os arquivos PHP em pushes e pull requests.

## Evolução recomendada

O projeto ainda mantém parte da disponibilidade em arquivo local. Para uma versão de produção, a agenda deve ficar integralmente no MySQL com transação/índice único para impedir reservas concorrentes do mesmo horário, além de CSRF e rate limiting nos formulários de autenticação.

---

Desenvolvido por [Vinícius Calegari](https://github.com/Vinicius-Calegari).
