# MS Barbearia

> Sistema web em PHP/MySQL para cadastro, autenticação, disponibilidade e gerenciamento de agendamentos de uma barbearia.

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-PDO-4479A1?logo=mysql&logoColor=white)
![CI](https://img.shields.io/github/actions/workflow/status/Vinicius-Calegari/MS--Barberaria/php-quality.yml?label=PHP%20syntax)

## O problema

Centralizar cadastro de clientes e disponibilidade de horários em um fluxo web simples, reduzindo dependência de atendimento manual para tarefas repetitivas de agendamento.

## Arquitetura atual

```text
Browser
  │
  ├── autenticação / cadastro
  ├── consulta de horários
  ├── criação de reserva
  └── cancelamento
        │
        ▼
PHP + sessão segura + CSRF
        │
        ▼
PDO / prepared statements
        │
        ▼
MySQL
  ├── usuarios
  └── agendamentos
```

A agenda não depende mais de arquivo JSON: disponibilidade, reservas e cancelamentos utilizam o MySQL como fonte única de verdade.

## Funcionalidades

- cadastro de clientes;
- autenticação com sessão;
- senhas processadas com `password_hash`;
- consulta dinâmica de horários diretamente no banco;
- criação e cancelamento de agendamentos;
- bloqueio de dupla reserva por barbeiro + horário;
- reaproveitamento seguro de um slot anteriormente cancelado;
- integração de contato via WhatsApp;
- interface responsiva.

## Banco de dados

O schema versionado está em [`database/schema.sql`](database/schema.sql). A configuração de conexão fica em [`config/database.php`](config/database.php).

Variáveis esperadas:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=ms_barbearia
DB_USER=root
DB_PASSWORD=
```

Copie `.env.example` como referência, mas **não versione credenciais reais**.

A tabela `agendamentos` possui restrição única em `(barbeiro, data_agendamento)`. Isso faz o banco rejeitar duas reservas simultâneas para o mesmo slot, inclusive quando duas requisições chegam quase ao mesmo tempo.

## Executar localmente

Requisitos: PHP 8+, extensão `pdo_mysql` e MySQL/MariaDB.

```bash
git clone https://github.com/Vinicius-Calegari/MS--Barberaria.git
cd MS--Barberaria
php -S localhost:8080
```

Crie/configure o banco e aplique [`database/schema.sql`](database/schema.sql) antes do primeiro uso.

## Docker / produção

O `Dockerfile` utiliza FrankenPHP/PHP 8.4 e instala `pdo_mysql`. O banco deve ser um serviço persistente separado; não grave dados de negócio no filesystem efêmero do container.

## Segurança aplicada

- `password_hash` / `password_verify`;
- PDO com prepared statements e emulação desabilitada;
- credenciais fora do código;
- sessão regenerada após login e cadastro;
- cookies de sessão `HttpOnly`, `SameSite=Lax` e `Secure` sob HTTPS;
- token CSRF em login, cadastro, criação e cancelamento de reservas;
- validação server-side de serviço, data e horário;
- mensagem genérica para credenciais inválidas;
- restrição única no banco para concorrência de reservas;
- erros internos registrados no servidor sem expor stack trace ao usuário.

## Fluxo de concorrência

```text
Usuário escolhe o slot
        │
        ▼
validação server-side
        │
        ▼
transação MySQL
        │
        ├─ slot cancelado existente? → reutiliza a linha
        │
        └─ slot novo? → INSERT
                         │
                         └─ UNIQUE impede corrida / dupla reserva
```

Se o MySQL retornar violação de unicidade (`1062` / SQLSTATE `23000`), o usuário recebe uma mensagem para escolher outro horário.

## Qualidade

`.github/workflows/php-quality.yml` valida a sintaxe de todos os arquivos PHP em cada push e pull request.

## Estrutura

```text
config/
├── database.php       # conexão PDO
└── security.php       # sessão e CSRF

database/
└── schema.sql         # schema versionado

agendamento.php        # criação/listagem de reservas
buscar-horarios.php    # disponibilidade via MySQL
cancelar_agendamento.php
login.php
processa_login.php
cadastro.php
processa_cadastro.php
Dockerfile
```

## Roadmap

- [x] persistência integral da agenda no MySQL
- [x] proteção CSRF nas operações principais
- [x] garantia de horário único no banco
- [x] cookies de sessão endurecidos
- [x] validação automática de sintaxe no CI
- [ ] testes automatizados de integração com MySQL isolado
- [ ] rate limiting persistente para autenticação
- [ ] camada de serviço para regras de negócio
- [ ] observabilidade estruturada
- [ ] otimização dos assets grandes da galeria

---

Desenvolvido por [Vinícius Calegari](https://github.com/Vinicius-Calegari).
