-- ============================================================
-- AgroVigia RO - Schema do banco de dados
-- Programa Centelha 3 Rondônia - Fase 1 (simulado)
-- Estrutura: Produtor -> Propriedade -> Talhão -> Ocorrência
-- (mesma lógica de Ordem->Estudante do CRUD Hogwarts,
--  aqui: Propriedade->Talhão)
-- ============================================================

CREATE DATABASE IF NOT EXISTS agrovigia_ro
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE agrovigia_ro;

-- ------------------------------------------------------------
-- Produtores rurais (usuários do sistema)
-- ------------------------------------------------------------
CREATE TABLE produtores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL COMMENT 'usado para alertas via WhatsApp/SMS',
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
-- Alertas disparados (log dos avisos enviados aos vizinhos/técnico)
-- ------------------------------------------------------------
CREATE TABLE alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_origem_id INT NOT NULL COMMENT 'ocorrência que disparou o alerta',
    municipio VARCHAR(100) NOT NULL,
    quantidade_ocorrencias INT NOT NULL COMMENT 'quantas ocorrências geraram esse alerta',
    canal ENUM('whatsapp','sms') NOT NULL DEFAULT 'whatsapp',
    destinatario VARCHAR(20) NOT NULL,
    mensagem TEXT NOT NULL,
    enviado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ocorrencia_origem_id) REFERENCES ocorrencias(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Dados de exemplo (opcional, útil para testar o CRUD e o mapa)
-- ------------------------------------------------------------
INSERT INTO produtores (nome, email, senha_hash, telefone, municipio, perfil) VALUES
('Produtor Teste', 'produtor@teste.com', '$2y$12$grKwzDQFuM0kR/GG3hIJ7eFxybC.RzITqhxtf3S9r//FLy8.mtJne', '69999990000', 'Ouro Preto do Oeste', 'produtor'),
('Administrador', 'admin@agrovigia.com', '$2y$12$u89iJvTCGYIDfywdGAkscu96t2uVsBr.IjPwfozHrlRbrkcKbl7xK', '69999991111', 'Porto Velho', 'admin');
-- senha do produtor de teste: 123456
-- senha do administrador:     admin123  (TROQUE no primeiro login, em "Meu perfil")

INSERT INTO propriedades (produtor_id, nome, municipio, latitude, longitude) VALUES
(1, 'Sítio Boa Esperança', 'Ouro Preto do Oeste', -10.7500, -62.2167);

INSERT INTO talhoes (propriedade_id, nome, cultura, area_hectares) VALUES
(1, 'Talhão 1 - Cacau', 'cacau', 3.50),
(1, 'Talhão 2 - Café', 'café', 2.00);
