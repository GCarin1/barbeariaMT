<?php
/**
 * Don Barbero - Funções Auxiliares Globais
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */

declare(strict_types=1);

/**
 * Escapar HTML para prevenir XSS
 * 
 * @param string|null $string String para escapar
 * @return string String escapada
 */
function e(?string $string): string {
    if ($string === null) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Gerar URL completa
 * 
 * @param string $path Caminho da URL
 * @return string URL completa
 */
function url(string $path = ''): string {
    // Em ambiente local, usar host e porta da requisição atual
    if (APP_ENV === 'local' && isset($_SERVER['HTTP_HOST'])) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'];
    } else {
        $baseUrl = rtrim(APP_URL, '/');
    }
    
    $path = ltrim($path, '/');
    return $baseUrl . ($path ? '/' . $path : '');
}

/**
 * Gerar URL de asset
 * 
 * @param string $path Caminho do asset
 * @return string URL do asset
 */
function asset(string $path): string {
    return url(ltrim($path, '/'));
}

/**
 * Redirecionar para outra página
 * 
 * @param string $path Caminho para redirecionar
 * @param int $statusCode Código de status HTTP
 * @return never
 */
function redirect(string $path, int $statusCode = 302): never {
    header('Location: ' . url($path), true, $statusCode);
    exit;
}

/**
 * Retornar JSON
 * 
 * @param mixed $data Dados para retornar
 * @param int $statusCode Código de status HTTP
 * @return never
 */
function json($data, int $statusCode = 200): never {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Gerar token CSRF
 * 
 * @return string Token CSRF
 */
function generateCsrfToken(): string {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verificar token CSRF
 * 
 * @param string|null $token Token para verificar
 * @return bool True se válido
 */
function verifyCsrfToken(?string $token): bool {
    if (!isset($_SESSION[CSRF_TOKEN_NAME]) || $token === null) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Obter campo de token CSRF (HTML)
 * 
 * @return string HTML do campo hidden
 */
function csrfField(): string {
    $token = generateCsrfToken();
    return '<input type="hidden" name="' . e(CSRF_TOKEN_NAME) . '" value="' . e($token) . '">';
}

/**
 * Definir mensagem flash na sessão
 * 
 * @param string $type Tipo da mensagem (success, error, warning, info)
 * @param string $message Mensagem
 * @return void
 */
function flash(string $type, string $message): void {
    $_SESSION['_flash'][$type] = $message;
}

/**
 * Obter e limpar mensagens flash
 * 
 * @return array Array de mensagens flash
 */
function getFlash(): array {
    $flash = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $flash;
}

/**
 * Verificar se usuário está autenticado
 * 
 * @return bool True se autenticado
 */
function isAuthenticated(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Obter ID do usuário autenticado
 * 
 * @return string|null ID do usuário ou null
 */
function userId(): ?string {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Obter dados do usuário autenticado
 * 
 * @return array|null Dados do usuário ou null
 */
function user(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Verificar se usuário é admin
 * 
 * @return bool True se admin
 */
function isAdmin(): bool {
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Middleware: Requer autenticação
 * 
 * @return void
 */
function requireAuth(): void {
    if (!isAuthenticated()) {
        flash('error', 'Você precisa estar logado para acessar esta página.');
        redirect('/auth/login');
    }
}

/**
 * Middleware: Requer papel de admin
 * 
 * @return void
 */
function requireAdmin(): void {
    requireAuth();
    if (!isAdmin()) {
        flash('error', 'Você não tem permissão para acessar esta área.');
        redirect('/dashboard');
    }
}

/**
 * Sanitizar entrada de dados
 * 
 * @param mixed $data Dados para sanitizar
 * @return mixed Dados sanitizados
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    if (is_string($data)) {
        return trim($data);
    }
    return $data;
}

/**
 * Validar email
 * 
 * @param string $email Email para validar
 * @return bool True se válido
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Formatar data para exibição
 * 
 * @param string $date Data no formato ISO
 * @param string $format Formato de saída
 * @return string Data formatada
 */
function formatDate(string $date, string $format = 'd/m/Y H:i'): string {
    try {
        $dt = new DateTime($date, new DateTimeZone('America/Sao_Paulo'));
        return $dt->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Formatar valor monetário
 * 
 * @param float $value Valor
 * @return string Valor formatado
 */
function formatMoney(float $value): string {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/**
 * Debugar e morrer
 * 
 * @param mixed ...$vars Variáveis para debugar
 * @return never
 */
function dd(...$vars): never {
    echo '<pre style="background: #1a1a1a; color: #00ff00; padding: 20px; margin: 20px; border-radius: 8px; font-family: monospace;">';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die(1);
}

/**
 * Criar diretório se não existir
 * 
 * @param string $path Caminho do diretório
 * @return bool True se criado ou já existe
 */
function ensureDir(string $path): bool {
    if (!is_dir($path)) {
        return mkdir($path, 0755, true);
    }
    return true;
}

/**
 * Limpar cache da aplicação
 * 
 * @return bool True se limpo com sucesso
 */
function clearCache(): bool {
    $cacheDir = BASE_PATH . '/cache';
    if (!is_dir($cacheDir)) {
        return true;
    }
    
    $files = glob($cacheDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    
    return true;
}
