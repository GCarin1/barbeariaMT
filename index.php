<?php
/**
 * Don Barbero - Front Controller
 * Sistema de Agendamento para Barbearia
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */

declare(strict_types=1);

// Configuração de timezone
date_default_timezone_set('America/Sao_Paulo');

// Iniciar sessão com configurações seguras
if (session_status() === PHP_SESSION_NONE) {
    // Configurações de segurança da sessão
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    
    // Em produção, ativar cookie_secure
    // ini_set('session.cookie_secure', '1');
    
    session_start();
}

// Carregar bootstrap da aplicação
require_once __DIR__ . '/config/bootstrap.php';
