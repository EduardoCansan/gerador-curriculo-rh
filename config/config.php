<?php

// =============================================
//  APLICAÇÃO
// =============================================
define('APP_NAME', 'RH Padronizador');
define('APP_VERSION', '1.0.0');
define('APP_LANG', 'pt-BR');
define('APP_URL', Env::get('APP_URL', 'http://localhost/gerador-curriculo-rh/public'));
define('APP_ENV', Env::get('APP_ENV', 'development'));
define('APP_DEBUG', Env::get('APP_DEBUG', 'true') === 'true');

// =============================================
//  BANCO DE DADOS
// =============================================
define('DB_HOST', Env::get('DB_HOST', 'localhost'));
define('DB_PORT', Env::get('DB_PORT', '3306'));
define('DB_NAME', Env::get('DB_NAME', 'rh_padronizador'));
define('DB_USER', Env::get('DB_USER', 'root'));
define('DB_PASS', Env::get('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

// =============================================
//  API CLAUDE
// =============================================
define('CLAUDE_API_KEY', Env::get('CLAUDE_API_KEY'));
define('CLAUDE_MODEL', 'claude-sonnet-4-20250514');
define('CLAUDE_MAX_TOKENS', 4096);

// =============================================
//  GOOGLE OAUTH
// =============================================
// define('GOOGLE_CLIENT_ID',     Env::get('GOOGLE_CLIENT_ID'));
// define('GOOGLE_CLIENT_SECRET', Env::get('GOOGLE_CLIENT_SECRET'));
// define('GOOGLE_REDIRECT_URI',  APP_URL . '/auth/google/callback');

// =============================================
//  UPLOAD
// =============================================
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/curriculos/');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);
define('UPLOAD_ALLOWED_TYPES', ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);
define('UPLOAD_ALLOWED_EXT', ['pdf', 'docx']);
define('PDF_OUTPUT_PATH', __DIR__ . '/../public/uploads/curriculos/padronizados/');

// =============================================
//  SESSÃO
// =============================================
define('SESSION_NAME', 'rh_session');
define('SESSION_LIFETIME', 3600 * 8);

// =============================================
//  AMBIENTE
// =============================================
date_default_timezone_set('America/Sao_Paulo');

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}