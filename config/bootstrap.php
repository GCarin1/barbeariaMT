<?php

declare(strict_types=1);

// === LOAD .ENV (Windows-friendly) ===
$envPath = dirname(__DIR__) . '/.env';

if (file_exists($envPath)) {
    foreach (parse_ini_file($envPath) as $key => $value) {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// Carregar configurações
$config = require_once __DIR__ . '/config.php';

// Autoloader simples PSR-4
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/../app/';
    $file = $baseDir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Helpers globais
require_once __DIR__ . '/helpers.php';

// Router
require_once __DIR__ . '/router.php';
$router = new Router();

// Rotas
require_once __DIR__ . '/routes.php';

// Dispatch
$router->dispatch();
