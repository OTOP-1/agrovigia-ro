# AgroVigia RO

Sistema web em que produtores rurais de Rondônia registram **ocorrências de pragas e doenças** nas lavouras
(com foto e localização). Quando várias ocorrências parecidas se concentram no mesmo município em poucos
dias, o sistema gera **alertas** para os produtores da região. Há também uma área de **administração** para
gerenciar todos os perfis e uma página "Meu perfil" para cada usuário editar os próprios dados.

Projeto da disciplina de Desenvolvimento de Sistemas — IFRO, Campus Porto Velho Calama
(ideia originada no Programa Centelha 3 Rondônia).

```
Produtor → Propriedade → Talhão → Ocorrência → Alerta
```

## Documentação

| Arquivo | O que tem |
|---|---|
| [`spec.md`](spec.md) | Spec final: objetivo, usuários, casos de uso, requisitos, modelo de dados, critérios de aceite, stack e **instalação e execução** (passo a passo completo) |
| [`CHANGELOG.md`](CHANGELOG.md) | Decisões, correções e revisões feitas ao longo do projeto |
| [`database/schema.sql`](database/schema.sql) | Script do banco (tabelas + dados iniciais fictícios) |
| [`.env.example`](.env.example) | Variáveis de ambiente (copie para `.env`) |

## Instalação rápida (XAMPP, Windows)

> O passo a passo completo, com variáveis, tabelas e roteiro de teste, está na seção **9** do [`spec.md`](spec.md).

1. Iniciar **Apache** e **MySQL** no XAMPP.
2. `cd C:\xampp\htdocs` e `git clone https://github.com/OTOP-1/agrovigia-ro.git`
3. No phpMyAdmin, criar o banco **`agrovigia_ro`** (`utf8mb4_unicode_ci`) e **importar** `database/schema.sql`.
4. Na pasta do projeto: `copy .env.example .env` (no XAMPP padrão não precisa editar nada).
5. Abrir **http://localhost/agrovigia-ro/login.php**

Não há dependências para instalar (sem Composer/npm). O painel precisa de internet (mapa e gráfico por CDN).

## Contas de teste (fictícias)

| Tipo | E-mail | Senha |
|---|---|---|
| Administrador | `admin@agrovigia.com` | `admin123` |
| Produtor | `produtor@teste.com` | `123456` |

> **Em qualquer sistema publicado, troque a senha do administrador no primeiro acesso** (menu "Meu perfil").

## Estrutura de pastas

```
agrovigia-ro/
├── config/          conexão PDO, leitor de .env e configurações gerais
├── includes/        header/footer, regra de alertas e funções de perfis
├── propriedades/    CRUD de propriedades
├── talhoes/         CRUD de talhões
├── ocorrencias/     CRUD de ocorrências (com upload de foto)
├── dashboard/       painel com mapa (Leaflet) e gráfico (Chart.js)
├── perfil/          "Meu perfil": editar dados e trocar senha
├── admin/           administração de perfis (somente administrador)
├── assets/css/      estilos
├── uploads/         fotos enviadas (não versionadas)
├── database/        schema.sql (instalação) e migracao_admin.sql (bancos antigos)
├── .env.example     modelo das variáveis de ambiente
├── spec.md · CHANGELOG.md · README.md
└── index.php · login.php · cadastro.php · logout.php
```

## Segurança (resumo)

Senhas com hash (bcrypt) · consultas parametrizadas (PDO) · validação no servidor · cada produtor só acessa
os próprios dados · credenciais fora do código (`.env`) · proteção CSRF nas telas de perfil e administração ·
sessão com cookie `HttpOnly`/`SameSite` · upload de foto validado e sem execução de PHP.
Limitações conhecidas estão na seção 11 do `spec.md`.

## Tecnologias

PHP 8 · MySQL/MariaDB (PDO) · HTML/CSS · Leaflet.js · Chart.js
