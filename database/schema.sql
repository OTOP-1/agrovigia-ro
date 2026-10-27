-- ============================================================
-- AgroVigia RO - Script do banco de dados (tabelas + dados iniciais)
-- Estrutura: Produtor -> Propriedade -> Talhão -> Ocorrência -> Alerta
--
-- COMO USAR
--   1) Crie um banco vazio chamado  agrovigia_ro  (utf8mb4_unicode_ci).
--      No phpMyAdmin: aba "Bancos de dados" > nome > utf8mb4_unicode_ci > Criar.
--      Em hospedagem, use o banco que o painel do provedor já criou para você.
--   2) Selecione esse banco e importe este arquivo (aba "Importar"), ou pelo terminal:
--        mysql -u root -p agrovigia_ro < database/schema.sql
--
-- (Se você tem permissão para criar bancos e prefere fazer tudo por script,
--  descomente as duas linhas abaixo antes de importar.)
-- CREATE DATABASE IF NOT EXISTS agrovigia_ro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE agrovigia_ro;
--
-- SEGURANÇA: esta versão não cria senhas, contas admin ou dados de produtores fictícios.
-- Cadastre sua conta pela interface e promova-a no phpMyAdmin do SEU banco.
-- ============================================================

-- ------------------------------------------------------------
-- Produtores rurais (usuários do sistema)
-- ------------------------------------------------------------
CREATE TABLE produtores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL COMMENT 'contato do produtor, sem envio automatico nesta versao',
    municipio VARCHAR(100) NOT NULL,
    perfil ENUM('produtor','admin') NOT NULL DEFAULT 'produtor' COMMENT 'admin gerencia todos os perfis',
    ativo TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0 = conta desativada, não consegue logar',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Propriedades rurais (1 produtor -> N propriedades)
-- ------------------------------------------------------------
CREATE TABLE propriedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produtor_id INT NOT NULL,
    nome VARCHAR(120) NOT NULL COMMENT 'nome/apelido da propriedade',
    municipio VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produtor_id) REFERENCES produtores(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Talhões (1 propriedade -> N talhões) - áreas de cultivo
-- ------------------------------------------------------------
CREATE TABLE talhoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    propriedade_id INT NOT NULL,
    nome VARCHAR(80) NOT NULL COMMENT 'ex: Talhão 1, Área do café',
    cultura VARCHAR(80) NOT NULL COMMENT 'ex: cacau, café, hortifruti, pastagem',
    area_hectares DECIMAL(8,2) NULL,
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (propriedade_id) REFERENCES propriedades(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Ocorrências (1 talhão -> N ocorrências) - núcleo inovador
-- ------------------------------------------------------------
CREATE TABLE ocorrencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    talhao_id INT NOT NULL,
    sintoma_descricao TEXT NOT NULL,
    foto VARCHAR(255) NULL COMMENT 'caminho do arquivo em uploads/ocorrencias',
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    status ENUM('nova','em_analise','confirmada','resolvida') NOT NULL DEFAULT 'nova',
    criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (talhao_id) REFERENCES talhoes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Alertas internos simulados (nenhuma mensagem externa enviada)
-- ------------------------------------------------------------
CREATE TABLE alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_origem_id INT NOT NULL COMMENT 'ocorrencia que gerou o registro interno de alerta',
    municipio VARCHAR(100) NOT NULL,
    quantidade_ocorrencias INT NOT NULL COMMENT 'quantas ocorrências geraram esse alerta',
    canal ENUM('whatsapp','sms') NOT NULL DEFAULT 'whatsapp',
    destinatario VARCHAR(20) NOT NULL,
    mensagem TEXT NOT NULL,
    enviado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ocorrencia_origem_id) REFERENCES ocorrencias(id) ON DELETE CASCADE
) ENGINE=InnoDB;
