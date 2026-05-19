<?php

// =============================================
//  CONFIGURAÇÕES GERAIS DA APLICAÇÃO
// =============================================

define('APP_NAME', 'RH Padronizador');
define('APP_URL', 'http://localhost/gerador-curriculo-rh/public');
define('APP_VERSION', '1.0.0');
define('APP_LANG', 'pt-BR');

// =============================================
//  BANCO DE DADOS
// =============================================

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'rh_padronizador');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// =============================================
//  API CLAUDE (ANTHROPIC)
// =============================================

define('CLAUDE_API_KEY', 'sk-ant-COLOQUE_SUA_CHAVE_AQUI');
define('CLAUDE_MODEL', 'claude-sonnet-4-20250514');
define('CLAUDE_MAX_TOKENS', 4096);

// =============================================
//  UPLOAD DE ARQUIVOS
// =============================================

define('UPLOAD_PATH', __DIR__ . '/../public/uploads/curriculos/');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
define('UPLOAD_ALLOWED_EXT', ['pdf', 'docx']);

// =============================================
//  SESSÃO
// =============================================

define('SESSION_NAME', 'rh_session');
define('SESSION_LIFETIME', 3600 * 8); // 8 horas

// =============================================
//  PDF GERADO
// =============================================

define('PDF_OUTPUT_PATH', __DIR__ . '/../public/uploads/curriculos/padronizados/');

// =============================================
//  AMBIENTE
// =============================================

define('APP_ENV', 'development'); // 'development' ou 'production'
define('APP_DEBUG', true);

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Report de erros baseado no ambiente
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
