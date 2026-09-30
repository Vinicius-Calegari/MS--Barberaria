# Contribuindo

- Use commits semânticos e alterações pequenas.
- Execute a validação de sintaxe PHP antes do PR.
- Todo POST mutável deve validar CSRF.
- Toda entrada deve ser validada no servidor.
- Acesso ao banco deve usar PDO/prepared statements.
- Nunca versione `.env`, senhas, dados de clientes ou dumps reais.
- Mudanças de agendamento devem considerar concorrência e transações.
