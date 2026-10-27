-- ============================================================
-- AgroVigia RO - Migração: perfil de administrador + conta ativa/inativa
-- Rode este script UMA vez se você já tinha importado o schema.sql antigo
-- (selecione o banco agrovigia_ro no phpMyAdmin antes de importar).
-- (Quem importar o schema.sql novo do zero NÃO precisa rodar este arquivo.)
-- ============================================================

ALTER TABLE produtores
    ADD COLUMN perfil ENUM('produtor','admin') NOT NULL DEFAULT 'produtor'
        COMMENT 'admin gerencia todos os perfis' AFTER municipio,
    ADD COLUMN ativo TINYINT(1) NOT NULL DEFAULT 1
        COMMENT '0 = conta desativada, não consegue logar' AFTER perfil;

-- Conta de administrador inicial
-- e-mail: admin@agrovigia.com   senha: admin123  (TROQUE no primeiro login, em "Meu perfil")
INSERT IGNORE INTO produtores (nome, email, senha_hash, telefone, municipio, perfil)
VALUES ('Administrador', 'admin@agrovigia.com', '$2y$12$u89iJvTCGYIDfywdGAkscu96t2uVsBr.IjPwfozHrlRbrkcKbl7xK', '69999991111', 'Porto Velho', 'admin');

-- Correção: o hash do produtor de teste do schema antigo não correspondia à senha 123456.
-- Este UPDATE só mexe na conta de teste (produtor@teste.com); senha passa a ser 123456.
UPDATE produtores
   SET senha_hash = '$2y$12$grKwzDQFuM0kR/GG3hIJ7eFxybC.RzITqhxtf3S9r//FLy8.mtJne'
 WHERE email = 'produtor@teste.com';
