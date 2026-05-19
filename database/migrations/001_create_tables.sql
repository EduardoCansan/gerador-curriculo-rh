-- ==========================================
--  RH PADRONIZADOR — Migration Inicial
--  Execute no MySQL: source migration.sql
-- ==========================================

CREATE DATABASE IF NOT EXISTS rh_padronizador
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE rh_padronizador;

-- ---- TABELA DE USUÁRIOS ----
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    perfil     ENUM('admin','recrutador') NOT NULL DEFAULT 'recrutador',
    ativo      TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    INDEX idx_email (email),
    INDEX idx_perfil (perfil)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---- TABELA DE CURRÍCULOS ----
CREATE TABLE IF NOT EXISTS curriculos (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id       INT UNSIGNED NOT NULL,
    nome_candidato   VARCHAR(150) NOT NULL,
    tipo_entrada     ENUM('texto','arquivo') NOT NULL DEFAULT 'texto',
    texto_original   LONGTEXT,
    arquivo_original VARCHAR(255),
    dados_extraidos  JSON,
    pdf_padronizado  VARCHAR(255),
    status           ENUM('pendente','processado','erro') NOT NULL DEFAULT 'pendente',
    erro_mensagem    TEXT,
    processado_em    DATETIME,
    created_at       DATETIME NOT NULL,
    updated_at       DATETIME NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_status     (status),
    INDEX idx_usuario    (usuario_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
--  USUÁRIO ADMINISTRADOR PADRÃO
--  Login: admin@rh.com | Senha: admin123
-- ==========================================
INSERT INTO usuarios (name, email, password, perfil, ativo, created_at, updated_at)
VALUES (
    'Administrador',
    'admin@rh.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1,
    NOW(),
    NOW()
);

-- Recrutador de exemplo
INSERT INTO usuarios (name, email, password, perfil, ativo, created_at, updated_at)
VALUES (
    'Maria Recrutadora',
    'recrutador@rh.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'recrutador',
    1,
    NOW(),
    NOW()
);

-- Senha dos dois usuários acima: password (hash bcrypt)
-- TROQUE as senhas pelo painel de admin após o primeiro login!
