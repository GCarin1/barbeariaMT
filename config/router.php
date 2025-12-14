<?php
/**
 * Don Barbero - Sistema de Roteamento
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */

declare(strict_types=1);

class Router {
    /** @var array<string, array> */
    private array $routes = [];
    
    /** @var array<string, callable> */
    private array $middlewares = [];
    
    /**
     * Registrar uma rota GET
     * 
     * @param string $path Caminho da rota
     * @param string|callable $action Ação (Controller@method ou callable)
     * @param array<string> $middleware Middlewares para aplicar
     * @return void
     */
    public function get(string $path, $action, array $middleware = []): void {
        $this->addRoute('GET', $path, $action, $middleware);
    }
    
    /**
     * Registrar uma rota POST
     * 
     * @param string $path Caminho da rota
     * @param string|callable $action Ação (Controller@method ou callable)
     * @param array<string> $middleware Middlewares para aplicar
     * @return void
     */
    public function post(string $path, $action, array $middleware = []): void {
        $this->addRoute('POST', $path, $action, $middleware);
    }
    
    /**
     * Adicionar rota ao registro
     * 
     * @param string $method Método HTTP
     * @param string $path Caminho da rota
     * @param string|callable $action Ação
     * @param array<string> $middleware Middlewares
     * @return void
     */
    private function addRoute(string $method, string $path, $action, array $middleware): void {
        // Normalizar o path
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'action' => $action,
            'middleware' => $middleware,
            'pattern' => $this->convertToPattern($path),
        ];
    }
    
    /**
     * Converter path para pattern regex
     * 
     * @param string $path Caminho da rota
     * @return string Pattern regex
     */
    private function convertToPattern(string $path): string {
        // Converter {param} para grupos de captura
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    /**
     * Registrar middleware global
     * 
     * @param string $name Nome do middleware
     * @param callable $callback Função do middleware
     * @return void
     */
    public function middleware(string $name, callable $callback): void {
        $this->middlewares[$name] = $callback;
    }
    
    /**
     * Processar a requisição atual
     * 
     * @return void
     */
    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        
        // Normalizar o path
        $path = '/' . trim($path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }
        
        // Buscar rota correspondente
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                // Extrair parâmetros da rota
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Executar middlewares
                foreach ($route['middleware'] as $middlewareName) {
                    if (isset($this->middlewares[$middlewareName])) {
                        $this->middlewares[$middlewareName]();
                    }
                }
                
                // Executar ação
                $this->executeAction($route['action'], $params);
                return;
            }
        }
        
        // Rota não encontrada
        $this->notFound();
    }
    
    /**
     * Executar ação da rota
     * 
     * @param string|callable $action Ação
     * @param array<string, string> $params Parâmetros da rota
     * @return void
     */
    private function executeAction($action, array $params): void {
        if (is_callable($action)) {
            // Ação é um callable
            call_user_func_array($action, $params);
            return;
        }
        
        if (is_string($action) && strpos($action, '@') !== false) {
            // Ação é Controller@method
            [$controllerName, $method] = explode('@', $action, 2);
            
            // Carregar controller
            $controllerClass = 'controllers\\' . $controllerName;
            $controllerFile = APP_PATH . '/controllers/' . $controllerName . '.php';
            
            if (!file_exists($controllerFile)) {
                $this->error("Controller não encontrado: {$controllerName}");
                return;
            }
            
            require_once $controllerFile;
            
            if (!class_exists($controllerClass)) {
                $this->error("Classe do controller não existe: {$controllerClass}");
                return;
            }
            
            $controller = new $controllerClass();
            
            if (!method_exists($controller, $method)) {
                $this->error("Método não existe no controller: {$controllerClass}::{$method}");
                return;
            }
            
            // Chamar método do controller com parâmetros
            call_user_func_array([$controller, $method], $params);
            return;
        }
        
        $this->error('Ação da rota inválida.');
    }
    
    /**
     * Página não encontrada (404)
     * 
     * @return never
     */
    private function notFound(): never {
        http_response_code(404);
        $this->renderErrorPage('404', 'Página não encontrada', 'A página que você está procurando não existe.');
    }
    
    /**
     * Erro interno
     * 
     * @param string $message Mensagem de erro
     * @return never
     */
    private function error(string $message): never {
        http_response_code(500);
        $this->renderErrorPage('500', 'Erro Interno', $message);
    }
    
    /**
     * Renderizar página de erro
     * 
     * @param string $code Código do erro
     * @param string $title Título do erro
     * @param string $message Mensagem do erro
     * @return never
     */
    private function renderErrorPage(string $code, string $title, string $message): never {
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= e($code) ?> - <?= e(APP_NAME) ?></title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-50">
            <div class="min-h-screen flex items-center justify-center px-4">
                <div class="max-w-md w-full text-center">
                    <div class="text-6xl font-bold text-gray-900 mb-4"><?= e($code) ?></div>
                    <h1 class="text-2xl font-semibold text-gray-900 mb-2"><?= e($title) ?></h1>
                    <p class="text-gray-600 mb-8"><?= e($message) ?></p>
                    <a href="<?= url('/') ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        Voltar para o Início
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
