# AgroVigia RO

MVP do projeto apresentado na Fase 1 (simulado) do Programa Centelha 3
Rondônia. Sistema de cadastro de propriedades, talhões e ocorrências
(pragas/doenças) com alerta automático quando várias ocorrências
parecidas se concentram na mesma região.

Segue a mesma lógica do CRUD Hogwarts (PHP + PDO + JOIN entre tabelas),
adaptada para o relacionamento:

```
Produtor → Propriedade → Talhão → Ocorrência
```

## Estrutura de pastas

```
agrovigia-ro/
├── config/
│   ├── database.php     -> conexão PDO
│   └── config.php       -> sessão, constantes, helpers
├── includes/
│   ├── header.php / footer.php
│   ├── alertas.php      -> lógica de concentração de ocorrências + disparo de alerta
│   └── usuarios.php     -> validação/regras compartilhadas de perfis (perfil + admin)
├── propriedades/        -> CRUD de propriedades
├── talhoes/              -> CRUD de talhões
├── ocorrencias/           -> CRUD de ocorrências (com upload de foto)
├── dashboard/            -> painel com mapa (Leaflet) e gráfico (Chart.js)
├── perfil/               -> "Meu perfil": o usuário edita seus dados e troca a senha
├── admin/                -> área do administrador: gerencia todos os perfis
├── assets/css/style.css
├── uploads/ocorrencias/  -> fotos enviadas (gerado em tempo de execução)
├── sql/schema.sql        -> script de criação do banco (instalação nova)
├── sql/migracao_admin.sql -> só para quem JÁ tinha o banco: adiciona perfil/ativo + admin
├── index.php             -> página inicial (logada)
├── login.php / cadastro.php / logout.php
└── README.md
```

## Como rodar (XAMPP / WAMP / Laragon)

1. Copie a pasta `agrovigia-ro` para dentro de `htdocs` (XAMPP) ou `www` (WAMP/Laragon).
2. Crie o banco importando o script `sql/schema.sql` (via phpMyAdmin ou linha de comando):
   ```
   mysql -u root -p < sql/schema.sql
   ```
3. Ajuste, se preciso, as credenciais em `config/database.php` (usuário/senha do MySQL).
4. Acesse `http://localhost/agrovigia-ro/login.php`.
5. Contas criadas pelo script:
   - **Administrador**: `admin@agrovigia.com` / `admin123`
     (**troque a senha no primeiro login**, em "Meu perfil")
   - **Produtor de teste**: `produtor@teste.com` / `123456`

   Ou cadastre um novo produtor em "Cadastre-se".

> **Já tinha importado o banco antes?** Não recrie nada: rode apenas
> `sql/migracao_admin.sql` (adiciona as colunas `perfil` e `ativo` em
> `produtores` e cria o admin).

## O que já está pronto (MVP)

- CRUD completo de Propriedade → Talhão → Ocorrência, com upload de foto.
- Regra de alerta automático (`includes/alertas.php`): quando o número de
  ocorrências no mesmo município, dentro de uma janela de dias
  (`ALERTA_QTD_MINIMA` / `ALERTA_JANELA_DIAS` em `config/config.php`),
  atinge o limite, o sistema registra um alerta para cada produtor da
  região na tabela `alertas` e grava em `uploads/alertas_enviados.log`.
- Painel com mapa (Leaflet/OpenStreetMap) e gráfico de ocorrências por dia
  (Chart.js).

## Perfis, administração e "Meu perfil"

**Tipos de conta:** `produtor` (padrão para quem se cadastra) e `admin`.
Ninguém vira admin pelo cadastro público: só outro admin promove alguém.

**Meu perfil** (`perfil/editar.php`, qualquer usuário logado): edita nome,
e-mail, telefone e município; troca a senha informando a senha atual.
O usuário não consegue alterar o próprio tipo de conta.

**Administração** (`admin/`, só admin): painel com totais do sistema,
lista de perfis com busca e filtros (tipo/status), e gestão completa:
criar perfil, editar qualquer dado, redefinir senha, promover/rebaixar,
desativar/reativar (conta desativada não loga) e excluir (apaga em cascata
as propriedades, talhões, ocorrências e alertas, inclusive as fotos do disco).

**Travas de segurança:**
- o admin não pode excluir, desativar nem rebaixar a própria conta
  (evita ficar sem nenhum administrador);
- a conta é revalidada no banco a cada página: se o admin desativar ou
  excluir alguém logado, a pessoa é deslogada na próxima requisição;
- ações de alterar/excluir usam POST + token CSRF (nunca link GET);
- login renova o ID de sessão (`session_regenerate_id`).

## O que ainda precisa ser plugado (próximos passos, citados no formulário)

- **Envio real de WhatsApp/SMS**: hoje `enviarWhatsAppOuSms()` em
  `includes/alertas.php` só grava um log. É o ponto exato para plugar a
  API do WhatsApp Business ou um gateway de SMS (ex: Twilio) quando
  tiver as credenciais.
- **Geolocalização automática**: hoje latitude/longitude são digitadas
  manualmente no formulário; dá pra trocar por captura via
  `navigator.geolocation` no navegador ou clique no mapa.
- **Validação por técnico antes do alerta amplo** (mencionado no
  formulário como forma de reduzir falsos positivos): pode entrar como
  um status extra em `alertas` (ex: `pendente_validacao`) antes do envio.

## Tecnologias

PHP 8+, MySQL/MariaDB (PDO), HTML/CSS, Leaflet.js (mapa), Chart.js (gráfico).
