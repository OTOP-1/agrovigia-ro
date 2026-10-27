# Changelog — AgroVigia RO

Registro das decisões, correções e revisões do projeto, da mais recente para a mais antiga.
O código foi desenvolvido com apoio de um agente de IA (Claude); esta lista mostra o que foi
proposto, o que foi testado, o que estava errado e o que foi corrigido depois da revisão.

---

## 2.1 — 27/10/2026 — Preparação da entrega final

**Decisão:** publicar o sistema em hospedagem gratuita com PHP + MySQL (InfinityFree), porque a stack
(PHP + MySQL) exige um servidor PHP tradicional, que a Vercel não oferece para PHP.

**Revisão do código frente ao checklist da entrega (o que estava fora do padrão):**
- As credenciais do banco (`root`, senha vazia) estavam escritas em `config/database.php`.
  **Corrigido:** passaram para o arquivo `.env`, que fica fora do Git (`.gitignore`); o repositório tem só o
  `.env.example`. Se faltar o `.env`, o sistema mostra uma mensagem explicando o que fazer.
- O caminho `/agrovigia-ro` estava fixo no código, o que impediria o site de funcionar publicado na raiz de
  um endereço. **Corrigido:** virou a variável `APP_BASE_URL`.
- O erro de banco mostrado na tela durante os testes (13/10) exibia o caminho completo das pastas do
  computador (`C:\xampp\htdocs\...`). Em um sistema publicado isso vaza informação. **Corrigido:** novo modo
  `APP_DEBUG` — `0` esconde os detalhes (obrigatório online) e `1` os mostra (só no computador local).
- Cookie de sessão sem proteções extras. **Corrigido:** `HttpOnly`, `SameSite=Lax` e `Secure` (quando HTTPS).
- Arquivos sensíveis poderiam ser baixados pelo navegador numa hospedagem (`.env`, `.sql`). **Corrigido:**
  `.htaccess` na raiz e nas pastas `config/`, `includes/` e `database/`.

**Alterado:**
- Pasta `sql/` renomeada para `database/` (padrão pedido na entrega).
- `database/schema.sql` não cria mais o banco: ele cria só as tabelas e os dados iniciais. Assim o mesmo
  script funciona no XAMPP e em hospedagens, onde o banco já vem criado pelo provedor e o usuário não tem
  permissão para criar bancos.

**Adicionado:**
- `.env.example`, `config/env.php` (leitor de `.env` sem dependências), `.htaccess`.
- `spec.md` na raiz do repositório, com a nova seção **Instalação e execução** (ambiente, banco, variáveis,
  passo a passo, como testar e como publicar).
- Este `CHANGELOG.md` e o `README.md` atualizado.

---

## 2.0 — 20/10/2026 — Spec final

- Spec reescrita para refletir o sistema construído: tipo de usuário **Administrador**, casos de uso UC11–UC16,
  requisitos RF12–RF21, requisitos não funcionais de segurança (RNF04–RNF06) e critérios de aceite para
  todos os requisitos.
- Acrescentada a lista de **limitações conhecidas** (alertas simulados, CSRF só nas telas novas, exclusões
  por link GET nas telas antigas, cadastro público com validação simples).

---

## 1.3 — 13/10/2026 — Perfil e administração

**Adicionado (pedido do autor):** tela de gerenciamento com conta de administrador (listar, buscar, criar,
editar, ativar/desativar e excluir qualquer perfil) e página "Meu perfil" (edição dos próprios dados e troca
de senha).

**Decisões de projeto:**
- Ninguém vira administrador pelo cadastro público; só outro administrador promove alguém.
- O administrador não pode excluir, desativar nem rebaixar a própria conta (evita ficar sem administrador).
- A conta é revalidada no banco a cada página: desativar ou excluir alguém surte efeito imediato, mesmo com a
  sessão aberta.
- Ações que alteram dados no admin e no perfil usam POST com token CSRF, nunca link GET.
- Excluir um perfil apaga em cascata propriedades, talhões, ocorrências, alertas **e as fotos do disco**.

**Erros encontrados e corrigidos:**
- **Hash de senha errado no script do banco.** Na revisão do que o agente tinha gerado, constatou-se que o
  hash do produtor de teste não correspondia à senha informada (`123456`); o login de teste nunca
  funcionaria. Corrigido com hashes novos, conferidos antes de entregar.
- **`Unknown column 'perfil'`** ao abrir o sistema no XAMPP: o autor atualizou o código sem rodar a migração
  do banco. Solução: executar `migracao_admin.sql` (agora documentado como passo obrigatório para quem já
  tinha o banco).
- **Visual do painel de administração sem estilo** nos prints de teste (totais soltos, botões padrão do
  navegador): o CSS novo não estava sendo carregado (cache do navegador ou `style.css` antigo). Orientação:
  atualizar com Ctrl+F5 e conferir o final do `assets/css/style.css`.

**Testes:** as 10 telas do sistema foram abertas e testadas manualmente em 13/10 (registradas em prints).

---

## 1.2 — 06/10/2026 — Relatório do projeto

Documento de resumo do que foi desenvolvido (sistema e documentação).

## 1.1 — 29/09/2026 — Spec inicial (Anexo A)

Spec reescrita nas 8 seções obrigatórias do Anexo A: objetivo, usuários, casos de uso, RF01–RF11, RNF,
modelo de dados, critérios de aceite e decisão de stack/deploy.

## 1.0 — 22/09/2026 — Spec técnica

Documento técnico de referência (modelo de dados, regra de alerta, rotas, segurança do MVP).

## 0.1 — 25/08/2026 — MVP

Primeira versão do sistema, gerada a partir do rascunho do formulário do Centelha (Fase 1):

- CRUD de propriedades, talhões e ocorrências (com upload de foto), login/cadastro de produtor e painel com
  mapa (Leaflet) e gráfico (Chart.js).
- **Decisão — stack:** PHP + PDO + MySQL, a mesma do CRUD Hogwarts estudado em sala, para concentrar o esforço
  na regra de negócio e não em uma ferramenta nova.
- **Decisão — regra de alerta:** quando houver **3 ou mais** ocorrências no mesmo município nos últimos
  **7 dias**, o sistema gera um alerta para os produtores do município (valores configuráveis em
  `config/config.php`).
- **Decisão — envio:** o envio real de WhatsApp/SMS ficou simulado (grava no banco e em um log), isolado em
  uma única função para ser trocado depois pela API real.
- **Decisão — segurança básica desde o início:** senhas com hash, consultas parametrizadas (PDO), conferência
  de que cada registro pertence ao usuário logado e bloqueio de execução de PHP na pasta de uploads.
