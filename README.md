# AgroVigia RO — Versão para InfinityFree

Sistema escolar PHP + MySQL para cadastrar produtores, propriedades, talhões e ocorrências de pragas/doenças. Contém painel com Leaflet e Chart.js, além de alertas SIMULADOS no banco. **Não envia WhatsApp/SMS reais**.

## Publicação (código pronto, hospedagem ainda precisa ser configurada)

1. Entre na sua hospedagem InfinityFree e crie um banco em **MySQL Databases**.
2. No phpMyAdmin, **selecione esse banco** e importe **`database/hosting.sql`** do pacote completo, não de `htdocs`. Esse script instala cinco tabelas: `produtores`, `propriedades`, `talhoes`, `ocorrencias`, `alertas`. Não cria contas com senhas conhecidas.
3. Abra o pacote `AgroVigia_RO_PARA_HTDOCS.zip`. Use **Upload & Unzip** no gerenciador para extrair seus arquivos *diretamente* em `htdocs/` (não em `htdocs/agrovigia-ro-main/`). Pode também descompactar no computador e enviar as pastas/arquivos.
4. No File Manager, crie **`htdocs/.env`** (não é enviado preenchido), usando o modelo da `.env.example` deste pacote. Use `APP_BASE_URL=` vazio quando os arquivos estão na raiz de `htdocs`.
5. Insira as credenciais reais MySQL do painel do InfinityFree: `DB_HOST`, `DB_PORT=3306`, `DB_NAME` completo com prefixo, `DB_USER`, `DB_PASS`. Nunca publique o `.env` ou compartilhe a senha.
6. Verifique se `https://SEU-DOMINIO/.env` é bloqueado (403 ou 404) **ANTES de inserir credenciais reais**. Se exibir conteúdo, não prossiga com instalação até corrigir as regras de proteção.
7. Abra `https://SEU-DOMINIO/cadastro.php` e cadastre o primeiro produtor com seus próprios dados de teste e senha forte (mínimo 10 caracteres). Faça login em `/login.php`.
8. Para conceder perfil admin **somente à sua conta**, no phpMyAdmin selecione seu banco e execute o SQL abaixo trocando pelo e-mail real que cadastrou (execute apenas uma vez):

   ```sql
   UPDATE produtores SET perfil = 'admin' WHERE email = 'SEU_EMAIL_CADASTRADO';
   ```

   Saia e entre novamente: `/admin/index.php` ficará visível. **Não crie administrador com senha compartilhada.**
9. Teste cadastro, login, criar propriedade, talhão e ocorrência com foto pequena, mapa e alertas. Confira em aba anônima também.
10. Confira que `/config/config.php`, `/database/hosting.sql` e `/.env` não exibem conteúdo sensível. Os diretórios `uploads/ocorrencias/` não permitem execução de scripts e só recebem imagens validadas.

**Atenção:** o GitHub ou o ZIP não publicam automaticamente o site. Esse passo exige o File Manager da sua hospedagem e um banco MySQL real.

## Uso no XAMPP

Copie o pacote completo para `C:\xampp\htdocs\agrovigia-ro`, crie o banco `agrovigia_ro`, importe `database/hosting.sql` e coloque `APP_BASE_URL=/agrovigia-ro` no `.env`. Demais campos do banco local: host `127.0.0.1`, usuário `root` (se for o padrão do seu XAMPP), senha vazia somente no ambiente local. Use o mesmo procedimento de cadastro + promoção para ter o primeiro administrador.

## Segurança e funcionalidades

- `password_hash()` / `password_verify()`, PDO com parâmetros, sessão protegida.
- CSRF em formulários de cadastro, login e alterações, inclusive exclusões, todas por POST.
- Gestão de dados com filtros de propriedade por dono; administração só para perfil admin.
- Fotos até 2 MB, JPG/PNG/WEBP validados por conteúdo e nomes aleatórios; `.htaccess` protege diretórios.
- Alertas simulados gravados na tabela `alertas` quando existem 3 ocorrências na mesma região em 7 dias. **Não há integração de WhatsApp/SMS real.**
- Nenhuma conta de teste com senha fixa. Para avaliação, crie contas fictícias com senhas geradas pelo avaliador.

## Limitações

A integração com MySQL remoto, a persistência das fotos e o funcionamento no domínio InfinityFree precisam ser testados na conta de hospedagem. Não foi possível executar a integração completa com MySQL nesta preparação. O mapa e os gráficos dependem de CDNs de terceiros e conexão com a internet.

**Fonte original:** o ZIP `agrovigia-ro-main.zip` enviado pelo solicitante, preservando a ideia do projeto e os arquivos principais.
