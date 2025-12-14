<?php
/**
 * Don Barbero - Controller da Home/Landing Page
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */

declare(strict_types=1);

namespace controllers;

class HomeController {
    /**
     * Página inicial / Landing page
     * 
     * @return void
     */
    public function index(): void {
        $services = SERVICES;
        require_once VIEWS_PATH . '/home/index.php';
    }
}
