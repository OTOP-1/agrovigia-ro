Spec Final — AgroVigia RO

Instituto Federal de Rondônia — Campus Porto Velho Calama

Disciplina: Desenvolvimento de Sistemas

Atividade: Spec final + sistema publicado no GitHub

Versão: 2.0 (final), de 20/10/2026 — substitui a spec inicial (v1.0). As mudanças em relação à v1.0 estão registradas na seção 9 (Changelog).

1. Objetivo

O AgroVigia RO é um sistema web para produtores rurais de Rondônia registrarem ocorrências de pragas e doenças em suas lavouras, com foto e localização. O sistema identifica automaticamente quando ocorrências parecidas se concentram no mesmo município em um curto período e avisa os demais produtores cadastrados naquela região, funcionando como um alerta preventivo comunitário. Administradores gerenciam todos os perfis cadastrados, e cada pessoa mantém o próprio perfil atualizado.

2. Usuários
Tipo de usuário	O que pode fazer
Visitante (não autenticado)	Acessar apenas as páginas de login e de criação de conta. Não acessa nenhum dado de propriedades, talhões, ocorrências, alertas ou perfis.
Produtor rural	Criar a própria conta e fazer login; cadastrar, editar e excluir suas propriedades, talhões e ocorrências (com foto); consultar o painel com mapa e gráfico; ver os alertas emitidos para o seu município; editar o próprio perfil (nome, e-mail, telefone, município) e trocar a própria senha. Não altera o próprio tipo de conta e não acessa a área de administração.
Administrador	Tudo o que o produtor faz com a própria conta, mais a gestão completa de todos os perfis: ver o painel de administração com os totais do sistema; listar, buscar e filtrar perfis; criar perfis; editar qualquer dado de qualquer perfil, inclusive tipo de conta e senha; ativar e desativar contas; excluir perfis. Não pode excluir, desativar nem rebaixar a própria conta.
3. Casos de uso principais
UC1 — Visitante cria uma conta de produtor.
UC2 — Usuário faz login (e é bloqueado se a conta estiver desativada).
UC3 — Produtor cadastra uma propriedade.
UC4 — Produtor cadastra um talhão dentro de uma propriedade.
UC5 — Produtor registra uma ocorrência (sintoma + foto) em um talhão.
UC6 — Sistema verifica se a ocorrência registrada, somada às demais do mesmo município nos últimos dias, forma uma concentração e, se sim, gera um alerta para os produtores da região.
UC7 — Produtor consulta a lista de ocorrências e o status de cada uma.
UC8 — Produtor consulta o painel (mapa + gráfico) com o histórico de ocorrências.
UC9 — Produtor consulta os alertas emitidos para o seu município.
UC10 — Produtor edita ou exclui uma propriedade, talhão ou ocorrência própria.
UC11 — Usuário edita o próprio perfil e troca a própria senha.
UC12 — Administrador consulta a visão geral do sistema e lista, busca e filtra os perfis cadastrados.
UC13 — Administrador cria um perfil.
UC14 — Administrador edita um perfil (dados, tipo de conta, redefinição de senha).
UC15 — Administrador ativa ou desativa uma conta.
UC16 — Administrador exclui um perfil.
4. Requisitos funcionais (RF)
RF	Descrição	UC
RF01	O sistema deve permitir que um visitante crie uma conta informando nome, e-mail, senha, telefone e município. A conta criada pelo cadastro público é sempre do tipo produtor e nasce ativa.	UC1
RF02	O sistema deve permitir login com e-mail e senha, negar o acesso a contas desativadas e levar o administrador à área de administração e o produtor à página inicial.	UC2
RF03	O sistema deve impedir o acesso a qualquer página interna sem login ativo, conferindo a conta no banco a cada página: conta desativada ou excluída perde o acesso imediatamente.	UC2
RF04	O sistema deve permitir que o produtor cadastre uma propriedade (nome, município e, opcionalmente, coordenadas).	UC3
RF05	O sistema deve permitir que o produtor cadastre um talhão vinculado a uma de suas propriedades (nome, cultura e, opcionalmente, área em hectares).	UC4
RF06	O sistema deve permitir que o produtor registre uma ocorrência vinculada a um de seus talhões, com descrição do sintoma e, opcionalmente, foto (jpg/jpeg/png/webp, até 5MB) e coordenadas.	UC5
RF07	Após o cadastro de uma ocorrência, o sistema deve contar as ocorrências do mesmo município nos últimos 7 dias e, se o total for 3 ou mais, gerar um registro de alerta para cada produtor com propriedade naquele município.	UC6
RF08	O sistema deve listar as ocorrências do produtor logado, com filtro por talhão, mostrando foto, descrição, status e data.	UC7
RF09	O sistema deve exibir um painel com mapa das ocorrências que possuem coordenadas e um gráfico de ocorrências por dia nos últimos 30 dias.	UC8
RF10	O sistema deve listar os alertas emitidos para o município do produtor logado.	UC9
RF11	O sistema deve permitir que o produtor edite ou exclua propriedade, talhão ou ocorrência que lhe pertença e impedir essa ação sobre registros de outro produtor.	UC10
RF12	O sistema deve permitir que o usuário logado edite o próprio nome, e-mail, telefone e município, recusando e-mail que já pertença a outra conta.	UC11
RF13	O sistema deve permitir que o usuário troque a própria senha informando a senha atual, a nova senha (mínimo de 6 caracteres) e a confirmação.	UC11
RF14	O sistema deve exibir ao administrador um painel com os totais de perfis, administradores, contas desativadas, propriedades, talhões, ocorrências e alertas, e a lista dos cadastros mais recentes.	UC12
RF15	O sistema deve listar todos os perfis ao administrador, com busca por nome, e-mail ou município, filtros por tipo e status e a quantidade de propriedades e ocorrências de cada perfil.	UC12
RF16	O sistema deve permitir que o administrador crie um perfil informando dados, senha inicial, tipo de conta e status.	UC13
RF17	O sistema deve permitir que o administrador edite qualquer dado de qualquer perfil, incluindo tipo de conta (produtor ou administrador) e redefinição de senha.	UC14
RF18	O sistema deve permitir que o administrador ative e desative contas; conta desativada não consegue fazer login.	UC15
RF19	O sistema deve permitir que o administrador exclua um perfil, removendo também suas propriedades, talhões, ocorrências, alertas e as fotos armazenadas no servidor.	UC16
RF20	O sistema deve impedir que o administrador exclua, desative ou rebaixe a própria conta.	UC14, UC15, UC16
RF21	O sistema deve negar o acesso às telas de administração a qualquer usuário que não seja administrador.	UC12 a UC16

(Todo requisito corresponde a pelo menos um caso de uso da seção 3; nenhum RF está "solto".)

5. Requisitos não funcionais (RNF)
RNF	Descrição
RNF01	Senha nunca em texto puro: as senhas devem ser armazenadas apenas como hash (password_hash, bcrypt) e conferidas com password_verify. Vale para o cadastro, a troca de senha e a redefinição feita pelo administrador.
RNF02	Validação no servidor: todo formulário deve ser validado no back-end antes de gravar no banco, independentemente de qualquer validação do navegador. Nas telas de perfil e administração: nome de 3 a 120 caracteres, e-mail válido e único, telefone com DDD (10 a 13 dígitos), município obrigatório e senha com no mínimo 6 caracteres. Uploads de foto têm extensão e tamanho verificados no servidor.
RNF03	Isolamento entre contas: toda consulta, edição ou exclusão de propriedade, talhão ou ocorrência confirma no banco que o registro pertence ao produtor autenticado.
RNF04	Controle de acesso por perfil no servidor: as páginas de administração só executam para contas do tipo administrador, independentemente do que o navegador exiba no menu.
RNF05	Sessão segura: o identificador de sessão é renovado no login e na troca de senha, e a conta é revalidada no banco a cada requisição (existência, status ativo e tipo de conta).
RNF06	Proteção contra CSRF nas ações de perfil e administração: formulários de "Meu perfil" e de todas as telas de administração usam POST com token de sessão; ações de desativar e excluir perfis nunca são feitas por link GET.
RNF07	Desempenho: as páginas de listagem (propriedades, ocorrências, painel, lista de perfis) devem responder em até 2 segundos com até 500 ocorrências cadastradas, usando índices nas chaves estrangeiras.
RNF08	Uploads seguros: fotos enviadas ficam em pasta com bloqueio de execução de scripts PHP (.htaccess), e o nome do arquivo gravado é gerado pelo sistema, nunca o nome enviado pelo usuário.
6. Modelo de dados (DER simples)
produtores (1) ──< propriedades (N)
propriedades (1) ──< talhoes (N)
talhoes (1) ──< ocorrencias (N)
ocorrencias (1) ──< alertas (N)   [ocorrencia_origem_id]
produtores ... alertas            [ligação lógica pelo município, sem FK direta]

produtores id (PK), nome, email (único), senha_hash, telefone, municipio, perfil (produtor | admin), ativo (0 | 1), criado_em

propriedades id (PK), produtor_id (FK → produtores.id), nome, municipio, latitude, longitude, criado_em

talhoes id (PK), propriedade_id (FK → propriedades.id), nome, cultura, area_hectares, criado_em

ocorrencias id (PK), talhao_id (FK → talhoes.id), sintoma_descricao, foto, latitude, longitude, status (nova | em_analise | confirmada | resolvida), criado_em

alertas id (PK), ocorrencia_origem_id (FK → ocorrencias.id), municipio, quantidade_ocorrencias, canal, destinatario, mensagem, enviado_em

As cinco tabelas, ligadas em cadeia por chave estrangeira com exclusão em cascata (produtores → propriedades → talhoes → ocorrencias → alertas), sustentam todos os RFs: RF01 a RF03 e RF12 a RF21 usam produtores (os campos perfil e ativo sustentam RF02, RF17, RF18, RF20 e RF21); RF04 usa propriedades; RF05 usa talhoes; RF06, RF08 e RF09 usam ocorrencias; RF07 e RF10 usam alertas; RF11 e RF19 dependem do encadeamento de chaves estrangeiras para confirmar posse e para apagar os dados vinculados em cascata.

7. Critérios de aceite
RF01 — Dado que um visitante preenche nome, e-mail, telefone, município e senha, quando envia o cadastro com um e-mail ainda não usado, então uma conta do tipo produtor, ativa, é criada e ele é redirecionado ao login.
RF02 — Dado que existe uma conta ativa, quando o usuário informa e-mail e senha corretos, então ele é autenticado e levado à página inicial (produtor) ou à área de administração (administrador); com a senha errada, o login é recusado, e com a conta desativada, o acesso é negado mesmo com a senha correta.
RF03 — Dado que um usuário não está logado, ou teve a conta desativada ou excluída durante a sessão, quando ele tenta abrir qualquer página interna, então é redirecionado ao login.
RF04 — Dado que um produtor está logado, quando ele cadastra uma propriedade com nome e município preenchidos, então a propriedade aparece na lista de propriedades dele.
RF05 — Dado que um produtor tem ao menos uma propriedade, quando ele cadastra um talhão informando nome e cultura, então o talhão aparece na lista de talhões daquela propriedade.
RF06 — Dado que um produtor tem ao menos um talhão, quando ele registra uma ocorrência com descrição do sintoma (com ou sem foto), então a ocorrência aparece na listagem daquele talhão com status "nova".
RF07 — Dado que já existem 2 ocorrências no mesmo município nos últimos 7 dias, quando uma 3ª ocorrência é registrada nesse município, então um alerta é criado para cada produtor com propriedade naquele município.
RF08 — Dado que um produtor tem ocorrências em mais de um talhão, quando ele filtra a listagem por um talhão, então apenas as ocorrências daquele talhão são exibidas.
RF09 — Dado que existem ocorrências com coordenadas nos últimos 30 dias, quando o produtor abre o painel, então o mapa exibe um marcador por ocorrência georreferenciada e o gráfico exibe a contagem diária correspondente.
RF10 — Dado que existe ao menos um alerta para o município do produtor, quando ele abre o painel, então o alerta aparece com município, quantidade de ocorrências e data.
RF11 — Dado que um produtor tenta editar ou excluir um registro de outro produtor (ex.: trocando o id na URL), quando a requisição é enviada, então o sistema não realiza a alteração e o registro permanece intacto.
RF12 — Dado que um usuário está logado, quando ele altera nome, telefone ou município em "Meu perfil" e salva dados válidos, então os novos dados aparecem ao reabrir a página e o nome mostrado no menu é atualizado; se o e-mail informado já pertence a outra conta, a alteração é recusada com mensagem de erro.
RF13 — Dado que um usuário informa a senha atual correta, uma nova senha com 6 ou mais caracteres e a confirmação igual, quando envia o formulário, então a senha é trocada e o próximo login só funciona com a nova; se a senha atual estiver errada, nada é alterado.
RF14 — Dado que um administrador está logado, quando ele abre a tela de Administração, então vê os totais de perfis, administradores, contas desativadas, propriedades, talhões, ocorrências e alertas, e a lista dos cadastros mais recentes.
RF15 — Dado que existem perfis de tipos e status variados, quando o administrador busca por um trecho de nome, e-mail ou município e/ou filtra por tipo ou status, então apenas os perfis que atendem aos filtros são listados, cada um com a quantidade de propriedades e ocorrências.
RF16 — Dado que o administrador preenche os dados, a senha inicial (mínimo de 6 caracteres), o tipo e o status, quando envia com um e-mail inédito, então o perfil é criado e aparece na lista; com e-mail já usado, a criação é recusada.
RF17 — Dado que o administrador abre a edição de outro perfil, quando altera dados, tipo de conta ou preenche uma nova senha e salva, então as mudanças valem no próximo acesso daquela pessoa; se deixar a senha em branco, a senha atual é mantida.
RF18 — Dado que o administrador desativa uma conta, quando a pessoa tenta entrar, ou já estava logada e abre outra página, então o acesso é negado e ela é desconectada; ao reativar a conta, o login volta a funcionar.
RF19 — Dado que um produtor tem propriedades, talhões e ocorrências com foto, quando o administrador exclui esse perfil, então o perfil, os dados vinculados e as fotos armazenadas no servidor são removidos.
RF20 — Dado que o administrador está logado, quando ele tenta excluir, desativar ou rebaixar a própria conta, então o sistema recusa a operação e a conta permanece inalterada.
RF21 — Dado que um produtor está logado, quando ele abre diretamente o endereço de uma tela de administração, então o acesso é negado e ele é redirecionado à página inicial.
8. Decisão de stack e deploy

Stack: PHP 8+ no back-end, com PDO para acesso ao MySQL/MariaDB, e HTML/CSS no front-end, com Leaflet.js (mapa) e Chart.js (gráfico) carregados via CDN. Foi escolhida por ser a stack já estudada em sala no CRUD Hogwarts, o que permite concentrar o esforço no domínio do problema e nas regras de negócio em vez de aprender uma ferramenta nova. O desenvolvimento e os testes foram feitos em ambiente local com XAMPP.

Código-fonte e versionamento: o projeto está publicado em um repositório no GitHub, com README de instalação, script de criação do banco (sql/schema.sql) e script de migração para bancos já existentes (sql/migracao_admin.sql). As fotos enviadas e o log de alertas (que contém telefones) ficam fora do repositório por meio do .gitignore.

Deploy previsto: hospedagem compartilhada com suporte a PHP e MySQL (ex.: Hostinger, InfinityFree) ou uma VPS simples com Apache/Nginx, PHP-FPM e MySQL. As opções foram escolhidas justamente porque dependem de um servidor PHP tradicional com banco relacional, algo que plataformas serverless como a Vercel não hospedam nativamente para PHP; portanto, stack e destino de deploy são compatíveis. Antes de qualquer publicação em servidor real, é obrigatório trocar a senha do administrador de teste e criar um usuário de banco próprio, com senha.

O armazenamento de fotos usa o próprio disco do servidor; como o volume é pequeno neste projeto, não é necessário um serviço externo de armazenamento de arquivos nesta fase.

9. Changelog (spec inicial v1.0 → spec final v2.0)
Item	Spec inicial (v1.0)	Spec final (v2.0)	Motivo
Tipos de usuário	Visitante e produtor	Acrescentado o administrador	Era preciso um responsável para gerenciar todos os perfis cadastrados
Casos de uso	UC1 a UC10	Acrescentados UC11 a UC16	Cobrir a edição do próprio perfil e a gestão de perfis
Requisitos funcionais	RF01 a RF11	RF01, RF02 e RF03 ajustados; acrescentados RF12 a RF21	Cobrir "Meu perfil", administração e as regras de proteção da conta
Requisitos não funcionais	RNF01 a RNF05	Reorganizados em RNF01 a RNF08; novos: controle de acesso por perfil, sessão segura e CSRF	Segurança das novas telas
Modelo de dados	produtores sem tipo nem status	Colunas perfil e ativo em produtores; script migracao_admin.sql	Sustentar administrador e desativação de contas
Dados de teste	Hash do produtor de teste não correspondia à senha informada	Hash corrigido e conta de administrador inicial criada	Erro encontrado durante os testes do sistema
Entrega	Arquivos locais e .zip	Repositório no GitHub com README e .gitignore	Entrega final da atividade
Stack	PHP + MySQL	Sem mudança	—

11. Limitações conhecidas e trabalho futuro

Itens que não fazem parte desta versão, registrados para que a spec reflita o que o sistema realmente faz:

Envio real de alertas: o disparo de WhatsApp/SMS é simulado (o alerta é gravado no banco e em um arquivo de log). A integração com a API do WhatsApp Business ou com um gateway de SMS está isolada em uma única função, pronta para ser plugada.
Geolocalização: latitude e longitude são digitadas manualmente; a captura automática pelo navegador ou por clique no mapa fica para uma próxima versão.
CSRF nos formulários antigos: a proteção por token (RNF06) foi aplicada às telas de perfil e administração. Os formulários de login, cadastro, propriedades, talhões e ocorrências ainda não a utilizam, e a exclusão de propriedade, talhão e ocorrência ainda é feita por link GET.
Validação do cadastro público: o cadastro verifica apenas campos obrigatórios; as regras mais rígidas de e-mail, telefone e tamanho de senha (RNF02) estão nas telas de perfil e administração e devem ser estendidas ao cadastro.
Limite de tentativas de login (rate limiting) e uso de HTTPS são necessários antes de uma publicação em produção.
Validação técnica antes do alerta em massa (para reduzir falsos positivos) e painel específico para o técnico da EMATER-RO, com visão de vários municípios.
Conteúdo
Formulario_Centelha_Fase1_AgroVigioRO_rascunho.docx

