<?php
/**
 * Don Barbero - Definição de Rotas
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */

declare(strict_types=1);

// Registrar middlewares
$router->middleware('auth', function() {
    requireAuth();
});

$router->middleware('admin', function() {
    requireAdmin();
});

// ========================================
// ROTAS PÚBLICAS
// ========================================

// Home / Landing Page
$router->get('/', 'HomeController@index');
$router->get('/home', 'HomeController@index');

// Teste de Conexão DB (remover em produção)
$router->get('/test-db', function() {
    require_once __DIR__ . '/../test-db.php';
});

// Criar Admin (executar apenas UMA VEZ e depois remover)
$router->get('/create-admin', function() {
    require_once __DIR__ . '/../create-admin.php';
});

// Autenticação
$router->get('/auth/register', 'AuthController@registerForm');
$router->post('/auth/register', 'AuthController@register');
$router->get('/auth/login', 'AuthController@loginForm');
$router->post('/auth/login', 'AuthController@login');
$router->get('/auth/logout', 'AuthController@logout', ['auth']);

// ========================================
// ROTAS DO CLIENTE (Requer autenticação)
// ========================================

// Dashboard do Cliente
$router->get('/dashboard', 'DashboardController@index', ['auth']);
$router->get('/dashboard/new', 'DashboardController@new', ['auth']);
$router->get('/dashboard/profile', 'DashboardController@profile', ['auth']);
$router->post('/dashboard/profile/update', 'DashboardController@updateProfile', ['auth']);
$router->post('/dashboard/slots', 'DashboardController@slots', ['auth']);
$router->post('/dashboard/store', 'DashboardController@store', ['auth']);
$router->post('/dashboard/cancel/{id}', 'DashboardController@cancel', ['auth']);

// ========================================
// ROTAS DO ADMIN (Requer autenticação + admin)
// ========================================

// Login Admin
$router->get('/admin/login', 'AdminController@loginForm');
$router->post('/admin/login', 'AdminController@login');

// Painel Admin
$router->get('/admin', 'AdminController@index', ['admin']);
$router->post('/admin/update-status/{id}', 'AdminController@updateStatus', ['admin']);
$router->post('/admin/confirm-payment/{id}', 'AdminController@confirmPayment', ['admin']);
$router->get('/admin/finance', 'AdminController@finance', ['admin']);
