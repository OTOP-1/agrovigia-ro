-- Somente para banco ANTIGO que não possui colunas perfil/ativo.
-- Faça backup antes. Não execute se as colunas já existirem.
ALTER TABLE produtores
    ADD COLUMN perfil ENUM('produtor','admin') NOT NULL DEFAULT 'produtor' AFTER municipio,
    ADD COLUMN ativo TINYINT(1) NOT NULL DEFAULT 1 AFTER perfil;
-- Para promover um administrador, cadastre uma conta com senha privada e depois
-- use o phpMyAdmin do banco correspondente:
-- UPDATE produtores SET perfil='admin' WHERE email='EMAIL_REAL_DA_CONTA';
