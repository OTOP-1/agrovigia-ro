# Spec Final — AgroVigia RO

**Instituto Federal de Rondônia — Campus Porto Velho Calama**

**Disciplina:** Desenvolvimento de Sistemas

**Atividade:** Spec final + sistema publicado no GitHub

**Versão:** 2.2 (adaptação segura para InfinityFree), de 27/10/2026 — substitui a spec inicial (v1.0). As mudanças estão resumidas na seção 10 e detalhadas no arquivo `CHANGELOG.md` do repositório.

**Repositório:** https://github.com/OTOP-1/agrovigia-ro

---

## 1. Objetivo

O AgroVigia RO é um sistema web para produtores rurais de Rondônia registrarem ocorrências de pragas e doenças em suas lavouras, com foto e localização. O sistema identifica automaticamente quando ocorrências de pragas/doenças se concentram no mesmo município em um curto período e registra alertas internos para produtores cadastrados naquela região. O sistema não envia mensagens externas por WhatsApp/SMS nesta versão. Administradores gerenciam todos os perfis cadastrados, e cada pessoa mantém o próprio perfil atualizado.

## 2. Usuários

| Tipo de usuário | O que pode fazer |
|-----------|----------------------------------------------------------------|
| **Visitante (não autenticado)** | Acessar apenas as páginas de login e de criação de conta. Não acessa nenhum dado de propriedades, talhões, ocorrências, alertas ou perfis. |
| **Produtor rural** | Criar a própria conta e fazer login; cadastrar, editar e excluir suas propriedades, talhões e ocorrências (com foto); consultar o painel com mapa e gráfico; ver os alertas emitidos para o seu município; editar o próprio perfil (nome, e-mail, telefone, município) e trocar a própria senha. Não altera o próprio tipo de conta e não acessa a área de administração. |
| **Administrador** | Tudo o que o produtor faz com a própria conta, mais a gestão completa de todos os perfis: ver o painel de administração com os totais do sistema; listar, buscar e filtrar perfis; criar perfis; editar qualquer dado de qualquer perfil, inclusive tipo de conta e senha; ativar e desativar contas; excluir perfis. Não pode excluir, desativar nem rebaixar a própria conta. |

## 3. Casos de uso principais

1. **UC1** — Visitante cria uma conta de produtor.
2. **UC2** — Usuário faz login (e é bloqueado se a conta estiver desativada).
3. **UC3** — Produtor cadastra uma propriedade.
4. **UC4** — Produtor cadastra um talhão dentro de uma propriedade.
5. **UC5** — Produtor registra uma ocorrência (sintoma + foto) em um talhão.
6. **UC6** — Sistema verifica se a ocorrência registrada, somada às demais do mesmo município nos últimos dias, forma uma concentração e, se sim, gera um alerta para os produtores da região.
7. **UC7** — Produtor consulta a lista de ocorrências e o status de cada uma.
8. **UC8** — Produtor consulta o painel (mapa + gráfico) com o histórico de ocorrências.
9. **UC9** — Produtor consulta os alertas emitidos para o seu município.
10. **UC10** — Produtor edita ou exclui uma propriedade, talhão ou ocorrência própria.
11. **UC11** — Usuário edita o próprio perfil e troca a própria senha.
12. **UC12** — Administrador consulta a visão geral do sistema e lista, busca e filtra os perfis cadastrados.
13. **UC13** — Administrador cria um perfil.
14. **UC14** — Administrador edita um perfil (dados, tipo de conta, redefinição de senha).
15. **UC15** — Administrador ativa ou desativa uma conta.
16. **UC16** — Administrador exclui um perfil.

## 4. Requisitos funcionais (RF)

| RF | Descrição | UC |
|------|--------------------------------------------------------------------------------------------|-------------|
| RF01 | O sistema deve permitir que um visitante crie uma conta informando nome, e-mail, senha, telefone e município. A conta criada pelo cadastro público é sempre do tipo produtor e nasce ativa. | UC1 |
| RF02 | O sistema deve permitir login com e-mail e senha, negar o acesso a contas desativadas e levar o administrador à área de administração e o produtor à página inicial. | UC2 |
| RF03 | O sistema deve impedir o acesso a qualquer página interna sem login ativo, conferindo a conta no banco a cada página: conta desativada ou excluída perde o acesso imediatamente. | UC2 |
| RF04 | O sistema deve permitir que o produtor cadastre uma propriedade (nome, município e, opcionalmente, coordenadas). | UC3 |
| RF05 | O sistema deve permitir que o produtor cadastre um talhão vinculado a uma de suas propriedades (nome, cultura e, opcionalmente, área em hectares). | UC4 |
| RF06 | O sistema deve permitir que o produtor registre uma ocorrência vinculada a um de seus talhões, com descrição do sintoma e, opcionalmente, foto (jpg/jpeg/png/webp, até 2MB, validada pelo conteúdo) e coordenadas. | UC5 |
| RF07 | Após o cadastro de uma ocorrência, o sistema deve contar as ocorrências do mesmo município nos últimos 7 dias e, se o total for 3 ou mais, gerar um registro de alerta para cada produtor com propriedade naquele município. | UC6 |
| RF08 | O sistema deve listar as ocorrências do produtor logado, com filtro por talhão, mostrando foto, descrição, status e data. | UC7 |
| RF09 | O sistema deve exibir um painel com mapa das ocorrências que possuem coordenadas e um gráfico de ocorrências por dia nos últimos 30 dias. | UC8 |
| RF10 | O sistema deve listar os alertas emitidos para o município do produtor logado. | UC9 |
| RF11 | O sistema deve permitir que o produtor edite ou exclua propriedade, talhão ou ocorrência que lhe pertença e impedir essa ação sobre registros de outro produtor. | UC10 |
| RF12 | O sistema deve permitir que o usuário logado edite o próprio nome, e-mail, telefone e município, recusando e-mail que já pertença a outra conta. | UC11 |
| RF13 | O sistema deve permitir que o usuário troque a própria senha informando a senha atual, a nova senha (de 10 a 72 caracteres) e a confirmação. | UC11 |
| RF14 | O sistema deve exibir ao administrador um painel com os totais de perfis, administradores, contas desativadas, propriedades, talhões, ocorrências e alertas, e a lista dos cadastros mais recentes. | UC12 |
| RF15 | O sistema deve listar todos os perfis ao administrador, com busca por nome, e-mail ou município, filtros por tipo e status e a quantidade de propriedades e ocorrências de cada perfil. | UC12 |
| RF16 | O sistema deve permitir que o administrador crie um perfil informando dados, senha inicial, tipo de conta e status. | UC13 |
| RF17 | O sistema deve permitir que o administrador edite qualquer dado de qualquer perfil, incluindo tipo de conta (produtor ou administrador) e redefinição de senha. | UC14 |
| RF18 | O sistema deve permitir que o administrador ative e desative contas; conta desativada não consegue fazer login. | UC15 |
| RF19 | O sistema deve permitir que o administrador exclua um perfil, removendo também suas propriedades, talhões, ocorrências, alertas e as fotos armazenadas no servidor. | UC16 |
| RF20 | O sistema deve impedir que o administrador exclua, desative ou rebaixe a própria conta. | UC14, UC15, UC16 |
| RF21 | O sistema deve negar o acesso às telas de administração a qualquer usuário que não seja administrador. | UC12 a UC16 |

*(Todo requisito corresponde a pelo menos um caso de uso da seção 3; nenhum RF está "solto".)*

## 5. Requisitos não funcionais (RNF)

| RNF | Descrição |
|------|------------------------------------------------------------------------------------------------------|
| RNF01 | **Senha nunca em texto puro:** as senhas devem ser armazenadas apenas como hash (`password_hash`, bcrypt) e conferidas com `password_verify`. Vale para o cadastro, a troca de senha e a redefinição feita pelo administrador. |
| RNF02 | **Validação no servidor:** todo formulário deve ser validado no back-end antes de gravar no banco, independentemente de qualquer validação do navegador. Nas telas de perfil e administração: nome de 3 a 120 caracteres, e-mail válido e único, telefone com DDD (10 a 13 dígitos), município obrigatório e senha de 10 a 72 caracteres. Fotos JPG/PNG/WEBP são validadas pelo conteúdo e limitadas a 2 MB. |
| RNF03 | **Isolamento entre contas:** toda consulta, edição ou exclusão de propriedade, talhão ou ocorrência confirma no banco que o registro pertence ao produtor autenticado. |
| RNF04 | **Controle de acesso por perfil no servidor:** as páginas de administração só executam para contas do tipo administrador, independentemente do que o navegador exiba no menu. |
| RNF05 | **Sessão segura:** o identificador de sessão é renovado no login e na troca de senha, o cookie de sessão usa `HttpOnly` e `SameSite=Lax` (e `Secure` quando o site é servido por HTTPS), e a conta é revalidada no banco a cada requisição (existência, status ativo e tipo de conta). |
| RNF06 | **Proteção contra CSRF em alterações:** login, cadastro, perfil, administração e operações de propriedades, talhões e ocorrências usam token por sessão. Exclusões e logout são feitos somente por POST. |
| RNF07 | **Desempenho:** as páginas de listagem (propriedades, ocorrências, painel, lista de perfis) devem responder em até 2 segundos com até 500 ocorrências cadastradas, usando índices nas chaves estrangeiras. |
| RNF08 | **Uploads seguros:** fotos enviadas ficam em pasta com bloqueio de execução de scripts PHP (`.htaccess`), e o nome do arquivo gravado é gerado pelo sistema, nunca o nome enviado pelo usuário. |
| RNF09 | **Credenciais fora do código:** usuário, senha e servidor do banco, o caminho da aplicação e o modo de depuração vêm do arquivo `.env`, que não é versionado (está no `.gitignore`); o repositório contém apenas o `.env.example`, com valores de exemplo. Em produção os detalhes técnicos de erro ficam ocultos (`APP_DEBUG=0`) e o navegador não consegue baixar o `.env`, os scripts `.sql` nem as pastas internas (`.htaccess`). |

## 6. Modelo de dados (DER simples)

```
produtores (1) ──< propriedades (N)
propriedades (1) ──< talhoes (N)
talhoes (1) ──< ocorrencias (N)
ocorrencias (1) ──< alertas (N)   [ocorrencia_origem_id]
produtores ... alertas            [ligação lógica pelo município, sem FK direta]
```

**produtores**
`id (PK)`, `nome`, `email (único)`, `senha_hash`, `telefone`, `municipio`, `perfil` (`produtor` | `admin`), `ativo` (0 | 1), `criado_em`

**propriedades**
`id (PK)`, `produtor_id (FK → produtores.id)`, `nome`, `municipio`, `latitude`, `longitude`, `criado_em`

**talhoes**
`id (PK)`, `propriedade_id (FK → propriedades.id)`, `nome`, `cultura`, `area_hectares`, `criado_em`

**ocorrencias**
`id (PK)`, `talhao_id (FK → talhoes.id)`, `sintoma_descricao`, `foto`, `latitude`, `longitude`, `status` (`nova` | `em_analise` | `confirmada` | `resolvida`), `criado_em`

**alertas**
`id (PK)`, `ocorrencia_origem_id (FK → ocorrencias.id)`, `municipio`, `quantidade_ocorrencias`, `canal`, `destinatario`, `mensagem`, `enviado_em`

As cinco tabelas, ligadas em cadeia por chave estrangeira com exclusão em cascata (`produtores → propriedades → talhoes → ocorrencias → alertas`), sustentam todos os RFs: RF01 a RF03 e RF12 a RF21 usam `produtores` (os campos `perfil` e `ativo` sustentam RF02, RF17, RF18, RF20 e RF21); RF04 usa `propriedades`; RF05 usa `talhoes`; RF06, RF08 e RF09 usam `ocorrencias`; RF07 e RF10 usam `alertas`; RF11 e RF19 dependem do encadeamento de chaves estrangeiras para confirmar posse e para apagar os dados vinculados em cascata.

## 7. Critérios de aceite

- **RF01** — Dado que um visitante preenche nome, e-mail, telefone, município e senha, quando envia o cadastro com um e-mail ainda não usado, então uma conta do tipo produtor, ativa, é criada e ele é redirecionado ao login.
- **RF02** — Dado que existe uma conta ativa, quando o usuário informa e-mail e senha corretos, então ele é autenticado e levado à página inicial (produtor) ou à área de administração (administrador); com a senha errada, o login é recusado, e com a conta desativada, o acesso é negado mesmo com a senha correta.
- **RF03** — Dado que um usuário não está logado, ou teve a conta desativada ou excluída durante a sessão, quando ele tenta abrir qualquer página interna, então é redirecionado ao login.
- **RF04** — Dado que um produtor está logado, quando ele cadastra uma propriedade com nome e município preenchidos, então a propriedade aparece na lista de propriedades dele.
- **RF05** — Dado que um produtor tem ao menos uma propriedade, quando ele cadastra um talhão informando nome e cultura, então o talhão aparece na lista de talhões daquela propriedade.
- **RF06** — Dado que um produtor tem ao menos um talhão, quando ele registra uma ocorrência com descrição do sintoma (com ou sem foto), então a ocorrência aparece na listagem daquele talhão com status "nova".
- **RF07** — Dado que já existem 2 ocorrências no mesmo município nos últimos 7 dias, quando uma 3ª ocorrência é registrada nesse município, então um alerta é criado para cada produtor com propriedade naquele município.
- **RF08** — Dado que um produtor tem ocorrências em mais de um talhão, quando ele filtra a listagem por um talhão, então apenas as ocorrências daquele talhão são exibidas.
- **RF09** — Dado que existem ocorrências com coordenadas nos últimos 30 dias, quando o produtor abre o painel, então o mapa exibe um marcador por ocorrência georreferenciada e o gráfico exibe a contagem diária correspondente.
- **RF10** — Dado que existe ao menos um alerta para o município do produtor, quando ele abre o painel, então o alerta aparece com município, quantidade de ocorrências e data.
- **RF11** — Dado que um produtor tenta editar ou excluir um registro de outro produtor (ex.: trocando o `id` na URL), quando a requisição é enviada, então o sistema não realiza a alteração e o registro permanece intacto.
- **RF12** — Dado que um usuário está logado, quando ele altera nome, telefone ou município em "Meu perfil" e salva dados válidos, então os novos dados aparecem ao reabrir a página e o nome mostrado no menu é atualizado; se o e-mail informado já pertence a outra conta, a alteração é recusada com mensagem de erro.
- **RF13** — Dado que um usuário informa a senha atual correta, uma nova senha com 10 a 72 caracteres e a confirmação igual, quando envia o formulário, então a senha é trocada e o próximo login só funciona com a nova; se a senha atual estiver errada, nada é alterado.
- **RF14** — Dado que um administrador está logado, quando ele abre a tela de Administração, então vê os totais de perfis, administradores, contas desativadas, propriedades, talhões, ocorrências e alertas, e a lista dos cadastros mais recentes.
- **RF15** — Dado que existem perfis de tipos e status variados, quando o administrador busca por um trecho de nome, e-mail ou município e/ou filtra por tipo ou status, então apenas os perfis que atendem aos filtros são listados, cada um com a quantidade de propriedades e ocorrências.
- **RF16** — Dado que o administrador preenche os dados, a senha inicial (mínimo de 10 caracteres), o tipo e o status, quando envia com um e-mail inédito, então o perfil é criado e aparece na lista; com e-mail já usado, a criação é recusada.
- **RF17** — Dado que o administrador abre a edição de outro perfil, quando altera dados, tipo de conta ou preenche uma nova senha e salva, então as mudanças valem no próximo acesso daquela pessoa; se deixar a senha em branco, a senha atual é mantida.
- **RF18** — Dado que o administrador desativa uma conta, quando a pessoa tenta entrar, ou já estava logada e abre outra página, então o acesso é negado e ela é desconectada; ao reativar a conta, o login volta a funcionar.
- **RF19** — Dado que um produtor tem propriedades, talhões e ocorrências com foto, quando o administrador exclui esse perfil, então o perfil, os dados vinculados e as fotos armazenadas no servidor são removidos.
- **RF20** — Dado que o administrador está logado, quando ele tenta excluir, desativar ou rebaixar a própria conta, então o sistema recusa a operação e a conta permanece inalterada.
- **RF21** — Dado que um produtor está logado, quando ele abre diretamente o endereço de uma tela de administração, então o acesso é negado e ele é redirecionado à página inicial.

## 8. Decisão de stack e deploy

**Stack:** PHP 8+ no back-end, com PDO para acesso ao MySQL/MariaDB, e HTML/CSS no front-end, com Leaflet.js (mapa) e Chart.js (gráfico) carregados via CDN. Foi escolhida por ser a stack já estudada em sala no CRUD Hogwarts, o que permite concentrar o esforço no domínio do problema e nas regras de negócio em vez de aprender uma ferramenta nova. O desenvolvimento e os testes foram feitos em ambiente local com XAMPP, no Windows.

**Código-fonte e versionamento:** o projeto está em um repositório no GitHub, com `README.md`, esta spec (`spec.md`), o `CHANGELOG.md`, o script do banco (`database/schema.sql`), o script de migração para bancos antigos (`database/migracao_admin.sql`) e o `.env.example`. O arquivo `.env` (credenciais) e as fotos enviadas ficam fora do repositório por meio do `.gitignore`; o projeto não gera log público com telefones.

**Deploy:** hospedagem gratuita com PHP e MySQL — o provedor escolhido é o InfinityFree (alternativa: GoogieHost); o passo a passo está na seção 9.6. Uma VPS simples com Apache/Nginx, PHP-FPM e MySQL também serviria. As opções foram escolhidas justamente **porque** dependem de um servidor PHP tradicional com banco relacional, algo que plataformas serverless como a Vercel não hospedam nativamente para PHP; portanto, stack e destino de deploy são compatíveis. Antes de usar o sistema publicado, crie uma conta com senha forte e promova-a a administrador no banco, use `APP_DEBUG=0` e confira que o `.env` não pode ser aberto pelo navegador.

O armazenamento de fotos usa o próprio disco do servidor; como o volume é pequeno neste projeto, não é necessário um serviço externo de armazenamento de arquivos nesta fase.

## 9. Instalação e execução

Esta seção permite instalar e rodar o sistema do zero, em um computador que nunca viu o projeto, sem perguntar nada aos autores.

### 9.1 Ambiente

- **Linguagem:** PHP 8.x (requer PHP 8.0 ou superior), com as extensões `pdo_mysql` e `mbstring` — ambas já vêm ativas no XAMPP.
- **Servidor local usado nos testes:** XAMPP para Windows (Apache + MariaDB/MySQL + phpMyAdmin). Qualquer pilha equivalente (Apache + PHP + MySQL/MariaDB) deve funcionar.
- **Versões usadas nos testes:** as que acompanham o XAMPP instalado (PHP 8.x e MariaDB 10.x). Para conferir as da sua máquina: `php -v` no terminal do XAMPP, ou o phpMyAdmin (rodapé da página inicial).
- **Sistema operacional testado:** Windows.
- **Dependências:** **nenhuma para instalar** — o projeto não usa Composer nem npm. As bibliotecas de front-end (Leaflet e Chart.js) são carregadas por CDN, portanto o **Painel precisa de internet** para exibir o mapa e o gráfico.
- **Controle de versão:** Git (para clonar o repositório).

### 9.2 Banco de dados

- **SGBD:** MySQL 5.7+ / 8 ou MariaDB 10.x (o XAMPP usa MariaDB; no phpMyAdmin aparece como "MySQL").
- **Nome do banco a criar:** `agrovigia_ro` (codificação `utf8mb4_unicode_ci`).
- **Script SQL:** `database/hosting.sql` — cria apenas as tabelas, sem contas ou senhas predefinidas. Ele **não** cria o banco; você cria o banco vazio e importa o script nele (assim o mesmo arquivo funciona no XAMPP e em hospedagens, onde o banco já vem criado pelo provedor).
- **Como executar o script:**
  - *phpMyAdmin:* criar o banco `agrovigia_ro` → clicar nele na coluna da esquerda → aba **Importar** → escolher `database/schema.sql` → **Executar**; ou
  - *terminal:* `mysql -u root -p agrovigia_ro < database/schema.sql`
- **Script de migração (`database/migracao_admin.sql`):** só para quem importou uma versão antiga do banco (sem as colunas `perfil` e `ativo`). Quem importa o `schema.sql` atual **não** precisa dele.

**Tabelas:**

| Tabela | Campos principais | Chave primária | Relacionamentos |
|-----------------|----------------------------------------------------------------------|-----|----------------------------------------|
| produtores | id, nome, email (único), senha_hash, telefone, municipio, perfil (`produtor`/`admin`), ativo, criado_em | id | — |
| propriedades | id, produtor_id, nome, municipio, latitude, longitude, criado_em | id | produtor_id → produtores.id (exclusão em cascata) |
| talhoes | id, propriedade_id, nome, cultura, area_hectares, criado_em | id | propriedade_id → propriedades.id (exclusão em cascata) |
| ocorrencias | id, talhao_id, sintoma_descricao, foto, latitude, longitude, status, criado_em | id | talhao_id → talhoes.id (exclusão em cascata) |
| alertas | id, ocorrencia_origem_id, municipio, quantidade_ocorrencias, canal, destinatario, mensagem, enviado_em | id | ocorrencia_origem_id → ocorrencias.id (exclusão em cascata) |

**Dados iniciais:** a adaptação de hospedagem não insere contas, propriedades ou senhas padrão. O responsável cadastra uma conta pelo formulário e, para ser administrador, promove somente essa conta no phpMyAdmin.

### 9.3 Variáveis de ambiente

As variáveis ficam no arquivo `.env`, na pasta raiz do projeto. O repositório traz o `.env.example` com os nomes e valores de exemplo; o `.env` real **nunca** vai para o Git.

| Variável | Para que serve | Como preencher |
|-------------|--------------------------------------------|-------------------------------------------------------|
| DB_HOST | Servidor do banco de dados | Local (XAMPP): `localhost`. Hospedagem: o host que o painel do provedor mostra (não é `localhost`) |
| DB_PORT | Porta TCP do MySQL | Padrão: `3306` (use a indicada pela hospedagem) |
| DB_NAME | Nome do banco | Local: `agrovigia_ro`. Hospedagem: o nome que o provedor criou |
| DB_USER | Usuário do banco | Local (XAMPP): `root`. Hospedagem: o usuário mostrado pelo provedor |
| DB_PASS | Senha do banco | Local (XAMPP): vazia. Hospedagem: a senha do banco indicada pelo provedor |
| APP_BASE_URL | Caminho da aplicação na URL | XAMPP com a pasta `htdocs/agrovigia-ro`: `/agrovigia-ro`. Hospedagem com o site na raiz do endereço: deixar **vazio** |
| APP_DEBUG | Mostra (1) ou esconde (0) detalhes técnicos de erro | Local: `1`. Sistema publicado: **`0`** |

Se o `.env` não existir, o sistema mostra a mensagem "Configuração do banco ausente" explicando o que fazer.

### 9.4 Passo a passo de instalação (Windows + XAMPP)

1. Instalar o XAMPP e, no painel dele, iniciar os módulos **Apache** e **MySQL**.
2. Clonar o repositório dentro da pasta `htdocs` do XAMPP:
   ```
   cd C:\xampp\htdocs
   git clone https://github.com/OTOP-1/agrovigia-ro.git
   ```
   (Sem Git: baixar o ZIP pelo botão **Code → Download ZIP** do GitHub e extrair em `C:\xampp\htdocs\agrovigia-ro`.)
3. Instalar as dependências: **não há nada para instalar** (sem Composer/npm).
4. Abrir `http://localhost/phpmyadmin`, ir em **Bancos de dados**, criar o banco `agrovigia_ro` com a codificação `utf8mb4_unicode_ci`.
5. Clicar no banco `agrovigia_ro`, aba **Importar**, escolher o arquivo `database/schema.sql` do projeto e clicar em **Executar**.
6. Copiar o `.env.example` para `.env` (na pasta do projeto):
   ```
   cd C:\xampp\htdocs\agrovigia-ro
   copy .env.example .env
   ```
   No XAMPP, ajuste `DB_HOST=127.0.0.1`, `DB_NAME=agrovigia_ro`, `DB_USER=root`, `DB_PASS=` (se aplicável) e `APP_BASE_URL=/agrovigia-ro`. Se a pasta tiver outro nome, ajuste `APP_BASE_URL` (ex.: `/outro-nome`).
7. Abrir no navegador: **http://localhost/agrovigia-ro/login.php**

### 9.5 Como testar

**Usuários de teste:** o banco não inclui credenciais padrão. Crie duas contas fictícias no cadastro com senhas fortes e diferentes. Para testar administração, promova uma das contas para `admin` no phpMyAdmin executando `UPDATE produtores SET perfil='admin' WHERE email='EMAIL_CADASTRADO';` e entre novamente.

**Roteiro do fluxo principal (nesta ordem):**

1. Entrar como produtor criado para os testes. Cadastrar uma propriedade fictícia, um talhão nessa propriedade e abrir sua lista de ocorrências.
2. Clicar em **+ Nova ocorrência**, descrever o sintoma (ex.: "folhas com manchas escuras"), preencher latitude `-10.75` e longitude `-62.21` (opcional: anexar uma foto JPG/PNG) e registrar. Repetir até ter **3 ocorrências** (mesmo município, mesma semana).
3. Abrir o **Painel**: o mapa mostra os marcadores, o gráfico mostra as ocorrências do dia e, na tabela "Alertas recentes na sua região", aparece o **alerta** gerado pela 3ª ocorrência.
4. Abrir **Meu perfil**, alterar o telefone e salvar; testar também a troca de senha.
5. Sair e entrar com a conta que foi promovida a administrador pelo phpMyAdmin: ver os totais, abrir **Gerenciar perfis**, desativar um produtor fictício (o acesso será negado) e reativar.

**Se algo não funcionar:**
- "Configuração do banco ausente" → faltou criar o `.env` (passo 6).
- "Não foi possível conectar ao banco" → conferir `DB_*` no `.env` e se o MySQL do XAMPP está iniciado.
- Páginas com erro 404 ou sem estilo → `APP_BASE_URL` diferente do nome da pasta.
- Mapa/gráfico em branco → computador sem internet (as bibliotecas vêm de CDN).
- Mudanças de CSS não aparecem → atualizar a página com Ctrl + F5.

**Problemas conhecidos e limitações:** listados na seção 11.

### 9.6 Publicação (sistema no ar)

Provedor escolhido: **InfinityFree** (hospedagem gratuita com PHP e MySQL). Passo a passo:

1. Criar a conta e a hospedagem gratuita no provedor; anotar o endereço do site.
2. No painel, criar um banco MySQL e anotar o **host** (não é `localhost`), o **nome do banco** e o **usuário**; a senha fica na área do cliente do provedor.
3. Abrir o phpMyAdmin do provedor, selecionar esse banco e importar o **`database/hosting.sql`** (aba **Importar**), usando um banco novo. O script não cria contas predefinidas.
4. No File Manager, criar `.env` de produção a partir do `.env.example`, com os dados do passo 2, `DB_PORT=3306`, `APP_BASE_URL=` (vazio) e `APP_DEBUG=0`. Antes de inserir credenciais, confirme que `/.env` está protegido.
5. Enviar o conteúdo do ZIP **`AgroVigia_RO_PARA_HTDOCS.zip`** para a raiz da pasta `htdocs`, sem pasta extra. O pacote não contém `.env` real nem SQL. Criar o `.env` só no servidor; manter os `.htaccess` presentes.
6. Abrir `/cadastro.php` e criar uma conta com senha forte. Para habilitar a administração, selecionar o banco no phpMyAdmin e promover **somente essa conta** executando `UPDATE produtores SET perfil='admin' WHERE email='EMAIL_CADASTRADO';`. Entrar novamente.
7. Conferir a segurança: abrir `https://ENDERECO-DO-SITE/.env` no navegador — o conteúdo do arquivo **não pode aparecer** (deve dar erro 403 ou 404).
8. Testar o link em uma aba anônima do navegador, sem estar logado, alguns dias antes da entrega e de novo no dia: provedores gratuitos podem ficar fora do ar.

## 10. Changelog (resumo — detalhes no arquivo `CHANGELOG.md`)

| Item | Spec inicial (v1.0) | Spec final (v2.1) | Motivo |
|-----------|---------------------------|----------------------------------------|-------------------------------------|
| Tipos de usuário | Visitante e produtor | Acrescentado o **administrador** | Era preciso um responsável para gerenciar todos os perfis cadastrados |
| Casos de uso | UC1 a UC10 | Acrescentados UC11 a UC16 | Cobrir a edição do próprio perfil e a gestão de perfis |
| Requisitos funcionais | RF01 a RF11 | RF01, RF02 e RF03 ajustados; acrescentados RF12 a RF21 | Cobrir "Meu perfil", administração e as regras de proteção da conta |
| Requisitos não funcionais | RNF01 a RNF05 | Reorganizados em RNF01 a RNF09; novos: controle de acesso por perfil, sessão segura, CSRF e credenciais fora do código | Segurança das novas telas e checklist da entrega final |
| Modelo de dados | `produtores` sem tipo nem status | Colunas `perfil` e `ativo` em `produtores`; script `migracao_admin.sql` | Sustentar administrador e desativação de contas |
| Dados de teste | Havia hash e conta de administrador com senha pública | Removidas contas predefinidas da instalação; administrador exige promoção controlada | Não expor acesso administrativo no site publicado |
| Configuração | Usuário e senha do banco escritos no código (`config/database.php`) | Tudo no `.env` (fora do Git) + `.env.example`; `APP_BASE_URL` e `APP_DEBUG` configuráveis | Nenhuma credencial no código-fonte; permitir rodar local e publicado |
| Banco de dados | Pasta `sql/`; script criava o banco | Pasta `database/` com `hosting.sql` exclusivo para hospedagem, sem criar banco ou usuários | Compatibilidade com banco de nome prefixado e maior segurança |
| Entrega | Arquivos locais e .zip | Repositório no GitHub com README, `spec.md`, `CHANGELOG.md` e `.gitignore`; sistema publicado online | Entrega final da atividade |
| Stack | PHP + MySQL | Sem mudança | — |

## 11. Limitações conhecidas e trabalho futuro

Itens que **não** fazem parte desta versão, registrados para que a spec reflita o que o sistema realmente faz:

- **Envio real de alertas:** o disparo de WhatsApp/SMS é simulado (o alerta é gravado no banco, sem log público com telefones). A integração com a API do WhatsApp Business ou com um gateway de SMS está isolada em uma única função, pronta para ser plugada.
- **Geolocalização:** latitude e longitude são digitadas manualmente; a captura automática pelo navegador ou por clique no mapa fica para uma próxima versão.
- **CSRF:** formulários de login, cadastro, alterações e exclusões possuem token; a validação de integração em hospedagem ainda precisa ser realizada pelo responsável.
- **Cadastro público:** validação de e-mail, comprimento de nome e telefone e senha mínima foi adicionada; faltam testes de carga e acessibilidade.
- **Limite de tentativas de login (rate limiting)** não existe. O HTTPS depende do provedor de hospedagem.
- **Primeiro administrador:** deve ser criado pela pessoa responsável via cadastro público e promovido exclusivamente em seu banco no phpMyAdmin. Não existem credenciais padrão publicadas.
- **Histórico de commits:** o desenvolvimento começou em agosto de 2026 em ambiente local e o repositório no GitHub foi criado depois; o histórico do Git reflete o trabalho a partir da criação do repositório, e o `CHANGELOG.md` registra a linha do tempo completa.
- **Validação técnica antes do alerta em massa** (para reduzir falsos positivos) e **painel específico para o técnico da EMATER-RO**, com visão de vários municípios.


## 12. Adaptação operacional para InfinityFree (27/10/2026)

O ZIP de publicação possui apenas arquivos executáveis e recursos necessários à aplicação, a serem extraídos **diretamente** em `htdocs`. O arquivo de conexão lê `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASS` do `.env` local ao servidor. O banco usado no InfinityFree deve ser previamente criado no painel com nome prefixado e selecionado antes de importar `database/hosting.sql`.

**Verificações realizadas na preparação:** inspeção de arquivos originais, revisão estática de autenticação e autorização, correções dos formulários POST, validação de imagem, bloqueios `.htaccess` e sintaxe PHP. **Não houve teste completo no MySQL da hospedagem**, que deve ser feito pelo responsável, assim como a confirmação de HTTPS, CSS, login, fluxo de ocorrência, gráficos, fotos e alertas simulados.

As mudanças desta adaptação estão discriminadas no CHANGELOG e no guia do README.
