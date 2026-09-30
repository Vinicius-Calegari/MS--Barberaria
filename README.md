# MS Barbearia

> Sistema web em PHP/MySQL para cadastro, autenticação e gerenciamento do fluxo de agendamentos de uma barbearia.

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-PDO-4479A1?logo=mysql&logoColor=white)
![CI](https://img.shields.io/github/actions/workflow/status/Vinicius-Calegari/MS--Barberaria/php-quality.yml?label=PHP%20syntax)

## O problema

Centralizar cadastro de clientes e disponibilidade de horários em um fluxo web simples, reduzindo dependência de atendimento manual para tarefas repetitivas de agendamento.

## Arquitetura atual

```text
Browser
  │
  ▼
PHP pages / handlers
  ├── autenticação + sessão
  ├── cadastro
  ├── disponibilidade
  ├── agendamento/cancelamento
  │
  ▼
PDO
  │
  ▼
MySQL
```

## Funcionalidades

- cadastro de clientes;
- autenticação com sessão;
- senhas processadas com `password_hash`;
- consulta de horários;
- criação e cancelamento de agendamentos;
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

## Executar localmente

Requisitos: PHP 8+, extensão `pdo_mysql` e MySQL/MariaDB.

```bash
git clone https://github.com/Vinicius-Calegari/MS--Barberaria.git
cd MS--Barberaria
php -S localhost:8080
```

Antes de iniciar, crie/configure o banco e aplique `database/schema.sql` quando necessário.

## Docker / produção

O repositório possui `Dockerfile` para tornar o runtime PHP reproduzível e permitir deploy em plataformas de containers. O banco deve ser um serviço persistente separado; não coloque dados MySQL dentro do filesystem efêmero da aplicação.

## Segurança

Já aplicadas ou previstas na arquitetura:

- `password_hash` para senhas;
- PDO/prepared statements para valores fornecidos pelo usuário;
- credenciais externas ao código;
- sessão regenerada após autenticação;
- validações no servidor;
- `.env` e dados locais fora do Git.

### Pendências importantes antes de tratar como produção real

- proteção CSRF em operações mutáveis;
- rate limiting / proteção contra brute force no login;
- cookies de sessão com flags adequadas ao HTTPS;
- transação + restrição única para impedir duas reservas concorrentes do mesmo horário;
- centralizar toda persistência da agenda no MySQL;
- logs estruturados sem dados pessoais sensíveis.

## Qualidade

`.github/workflows/php-quality.yml` valida sintaxe PHP em pushes e pull requests. O objetivo é expandir isso para testes automatizados conforme regras de negócio forem extraídas das páginas.

## Estrutura

```text
config/                 # configuração de banco
database/schema.sql     # schema versionado
css/                    # interface
img/                    # assets
agendamento.php         # reserva
buscar-horarios.php     # disponibilidade
processa_login.php      # autenticação
processa_cadastro.php   # cadastro
Dockerfile              # runtime de produção
```

## Roadmap

- [ ] persistência integral de agenda no MySQL
- [ ] proteção CSRF
- [ ] garantia transacional de horário único
- [ ] testes automatizados para autenticação/agendamento
- [ ] camada de serviço para regras de negócio
- [ ] observabilidade e tratamento centralizado de erros
- [ ] otimização dos assets de imagem

---

Desenvolvido por [Vinícius Calegari](https://github.com/Vinicius-Calegari).
