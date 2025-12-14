<?php
/**
 * Don Barbero - Configurações Principais
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */

declare(strict_types=1);

// Definir constantes de ambiente
define('APP_NAME', 'Don Barbero');
define('APP_VERSION', '1.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'local');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8787');

// Configuração de erros baseada no ambiente
if (APP_ENV === 'production') {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', __DIR__ . '/../logs/error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Supabase Configuration
define('SUPABASE_URL', getenv('SUPABASE_URL') ?: '');
define('SUPABASE_KEY', getenv('SUPABASE_KEY') ?: '');

// Google reCAPTCHA v3 Configuration
define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: '');
define('RECAPTCHA_SECRET', getenv('RECAPTCHA_SECRET') ?: '');
define('RECAPTCHA_MIN_SCORE', 0.5); // Score mínimo aceitável

// Caminhos da aplicação
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/views');

// Configurações de segurança
define('CSRF_TOKEN_NAME', '_csrf_token');
define('SESSION_LIFETIME', 3600 * 2); // 2 horas

// Rate limiting (tentativas por minuto)
define('RATE_LIMIT_LOGIN', 5);
define('RATE_LIMIT_SIGNUP', 3);
define('RATE_LIMIT_WINDOW', 600); // 10 minutos em segundos

// Política de cancelamento (em segundos)
define('CANCELATION_ADVANCE_TIME', 7200); // 2 horas

// Configurações de serviços (default, podem vir do banco)
define('SERVICES', [
    ['name' => 'Cabelo', 'duration_minutes' => 45, 'price' => 40.00],
    ['name' => 'Barba', 'duration_minutes' => 30, 'price' => 30.00],
    ['name' => 'Combo', 'duration_minutes' => 60, 'price' => 60.00],
]);

// Headers de segurança
function setSecurityHeaders(): void {
    // Content Security Policy
    $csp = "default-src 'self' https://*.supabase.co https://www.google.com https://www.gstatic.com; " .
           "script-src 'self' https://www.google.com https://www.gstatic.com https://cdn.tailwindcss.com 'unsafe-inline'; " .
           "style-src 'self' https://cdn.tailwindcss.com 'unsafe-inline'; " .
           "img-src 'self' data:; " .
           "font-src 'self' data:; " .
           "connect-src 'self' https://*.supabase.co https://www.google.com;";
    
    header("Content-Security-Policy: $csp");
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer-when-downgrade');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    
    // HSTS (apenas em produção com HTTPS)
    if (APP_ENV === 'production' && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// Aplicar headers de segurança
setSecurityHeaders();

return [
    'app' => [
        'name' => APP_NAME,
        'version' => APP_VERSION,
        'env' => APP_ENV,
        'url' => APP_URL,
    ],
    'database' => [
        'supabase_url' => SUPABASE_URL,
        'supabase_key' => SUPABASE_KEY,
    ],
    'security' => [
        'recaptcha_site_key' => RECAPTCHA_SITE_KEY,
        'recaptcha_secret' => RECAPTCHA_SECRET,
        'recaptcha_min_score' => RECAPTCHA_MIN_SCORE,
        'csrf_token_name' => CSRF_TOKEN_NAME,
        'session_lifetime' => SESSION_LIFETIME,
    ],
    'rate_limiting' => [
        'login' => RATE_LIMIT_LOGIN,
        'signup' => RATE_LIMIT_SIGNUP,
        'window' => RATE_LIMIT_WINDOW,
    ],
];
