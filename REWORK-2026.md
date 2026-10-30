# MS BARBEARIA · PREMIUM SUITE (2026)

## Stack mantida
PHP 8.4 + PDO/MySQL 8.4 + HTML + CSS + JavaScript (sem SPA, sem framework).
O projeto usa a identidade MS original: logo, vermelho, dourado e preto, com efeitos 3D feitos em CSS e JavaScript.

## Rotas
- \`/\`: home redesenhada, experiências e serviços
- \`/produtos.php\`: catálogo + filtros, sacola com armazenamento local e envio ao WhatsApp
- \`/agendamento.php\`: fluxo de agendamento preservado
- \`/admin/index.php\`: dashboard geral, agendamentos e CRM
- \`/admin/servicos.php\`: editar preços e cadastrar novos serviços
- \`/admin/produtos.php\`: cadastrar/editar produtos, fotos, custo, tamanho e estoque
- \`/admin/vendas.php\`: acompanhar pedidos, confirmar vendas, cancelar e estornar
- \`/admin/financeiro.php\`: receitas, custos, despesas e resultado por mês

## Operação comercial
1. Admin > Produtos: publique um item com nome, categoria, tamanho, preço, custo e estoque.
2. Para controlar tamanho/cor individualmente, cadastre cada variação como um item próprio, com estoque próprio.
3. Fotos JPEG/PNG/WebP (até 1 MB) são armazenadas no próprio MySQL, porque o disco Free do Render não é persistente.
4. A sacola cria um **pedido pendente** e encaminha o cliente ao WhatsApp. Não cobra nem aprova o pagamento.
5. Admin > Pedidos: somente após concluir o pagamento/retirada, clique em **Concluir venda**; isso reduz o estoque em transação atômica e entra no faturamento.
6. Cancelar um pedido pendente não altera o estoque. Estornar venda concluída restaura as unidades e exclui a receita do relatório.
7. Admin > Financeiro: despesas são lançadas manualmente. Resultado operacional estimado = serviços comparecidos + vendas concluídas − custo histórico das peças − despesas registradas. Não inclui impostos ou custos não cadastrados.

## Migração do banco
Aplicar \`database/migrations/002_commerce.sql\` uma vez ao MySQL 8.4.
A migração é aditiva, usa \`CREATE TABLE IF NOT EXISTS\` e não altera as tabelas \`usuarios\`, \`servicos\` e \`agendamentos\`.
Antes de alterações em produção, preserve um backup SQL.

## Ambiente
A aplicação requer \`DB_HOST\`, \`DB_PORT\`, \`DB_NAME\`, \`DB_USER\`, \`DB_PASSWORD\`, \`DB_SSL_CA_PEM_B64\` e as credenciais de admin \`ADMIN_USER\`, \`ADMIN_PASSWORD_SHA256\`. Nunca armazene valores reais no repositório.

## Limitações conhecidas
- Não existe integração com gateway de pagamento; o WhatsApp é somente canal para confirmar o pedido.
- Pedidos pendentes não reservam estoque, então a confirmação administrativa exige disponibilidade real.
- Aiven Free e Render Free podem suspender serviços por inatividade; serviços comerciais que exigem alta disponibilidade precisam de planos com SLA.
- Para foto adicional/galeria por produto, criar uma tabela de imagens separada futuramente.

## MS Store — dados demonstrativos (2026-10-30)
- Seed idempotente: \`database/seeds/003_concept_products.sql\`.
- Oito peças **conceituais**, identificadas por categoria \`· DEMO\`, com **estoque zero**.
- O catálogo mostra ilustrações vetoriais; **não são fotos de produtos reais**.
- A compra de peças \`· DEMO\` é bloqueada visualmente e no servidor por estoque zero.
- Valores são referências fictícias, não ofertas comerciais. Para vender, use **Admin > Produtos e estoque** e atualize categoria (remova \`· DEMO\`), preço, descrição, foto e quantidade de estoque real.
- As peças conceituais não contam nos KPIs de estoque disponível ou estoque baixo.
