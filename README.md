# MS Barbearia

> Sistema web full stack em PHP/MySQL para clientes, agenda, preços e operação administrativa de uma barbearia.

![PHP](https://img.shields.io/badge/PHP-8%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-PDO-4479A1?logo=mysql&logoColor=white)
![CI](https://img.shields.io/github/actions/workflow/status/Vinicius-Calegari/MS--Barberaria/php-quality.yml?label=PHP%20syntax)

## Visão geral

A MS Barbearia deixou de ser apenas um formulário de agendamento. O sistema agora possui duas experiências separadas:

- **área do cliente:** cadastro, login, consulta de horários, reserva, acompanhamento e cancelamento;
- **painel administrativo:** agenda, confirmação, comparecimento/falta, gestão de preços, clientes e métricas operacionais.

## Arquitetura

```text
Cliente / Admin
      │
      ▼
PHP + sessão segura + CSRF
      │
      ├── área do cliente
      ├── painel administrativo
      ├── catálogo de serviços
      └── regras de agenda
      │
      ▼
PDO / prepared statements
      │
      ▼
MySQL
  ├── usuarios
  ├── servicos
  └── agendamentos
```

## Funcionalidades

### Cliente

- cadastro e autenticação;
- consulta de horários realmente disponíveis;
- criação de reserva;
- cancelamento pelo próprio cliente;
- acompanhamento de reservas futuras;
- preços carregados diretamente do catálogo vigente.

### Administração

- login administrativo separado;
- dashboard com atendimentos, pendências e clientes;
- confirmação e cancelamento de reservas;
- marcação de **compareceu** ou **faltou** após o horário agendado;
- correção do registro de comparecimento;
- gestão de preços sem editar código;
- receita prevista e receita realizada;
- busca e filtros da agenda;
- lista de clientes recentes.

## Preços históricos

Cada agendamento salva `preco_cobrado` no momento da reserva.

Isso significa que alterar o preço de um serviço no painel:

- atualiza o site e novas reservas;
- **não altera o valor histórico de reservas antigas**.

Essa separação evita relatórios financeiros incorretos.

## Comparecimento

O status operacional da reserva e o comparecimento são campos diferentes:

```text
status: pendente | confirmado | cancelado
comparecimento: aguardando | compareceu | faltou
```

A presença só pode ser registrada depois do horário marcado e reservas canceladas não aceitam registro de comparecimento.

## Banco de dados

O schema versionado está em [`database/schema.sql`](database/schema.sql) e a migration idempotente em [`database/migrate.php`](database/migrate.php).

A tabela `agendamentos` possui restrição única em `(barbeiro, data_agendamento)`, impedindo duas reservas simultâneas no mesmo slot mesmo quando as requisições chegam praticamente juntas.

## Segurança

- `password_hash` / `password_verify`;
- PDO com prepared statements;
- CSRF nas operações mutáveis;
- sessão regenerada após autenticação;
- cookies `HttpOnly`, `SameSite=Lax` e `Secure` sob HTTPS;
- credenciais administrativas somente por variáveis de ambiente;
- senha administrativa não versionada em texto claro;
- validação server-side;
- mensagem genérica para credenciais inválidas;
- erros internos registrados sem expor stack trace.

## Variáveis de ambiente

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=ms_barbearia
DB_USER=root
DB_PASSWORD=

ADMIN_USER=admin
ADMIN_PASSWORD_SHA256=<hash sha256 da senha>
```

## Executar localmente

```bash
git clone https://github.com/Vinicius-Calegari/MS--Barberaria.git
cd MS--Barberaria
php database/migrate.php
php -S localhost:8080
```

Requisitos: PHP 8+, `pdo_mysql` e MySQL/MariaDB.

## Produção

O deploy utiliza FrankenPHP em container e MySQL persistente no Railway. Antes de cada deploy, `database/migrate.php` prepara o schema necessário de forma idempotente.

## Qualidade

`.github/workflows/php-quality.yml` valida a sintaxe dos arquivos PHP em cada push e pull request.

## Estrutura principal

```text
admin/
├── _auth.php
├── login.php
├── index.php
├── action.php
└── service_action.php

config/
├── database.php
├── security.php
└── services.php

database/
├── migrate.php
└── schema.sql

agendamento.php
buscar-horarios.php
cancelar_agendamento.php
index.php
login.php
cadastro.php
```

## Roadmap

- [x] agenda integral no MySQL
- [x] proteção CSRF
- [x] bloqueio de dupla reserva
- [x] painel administrativo
- [x] preços editáveis pelo painel
- [x] preço histórico por reserva
- [x] confirmação de comparecimento/falta
- [x] receita prevista e realizada
- [x] CI de sintaxe PHP
- [ ] relatórios mensais exportáveis
- [ ] dashboard com gráficos
- [ ] lembretes automáticos por WhatsApp
- [ ] testes de integração com MySQL isolado
- [ ] rate limiting persistente no login

---

Desenvolvido por [Vinícius Calegari](https://github.com/Vinicius-Calegari).
