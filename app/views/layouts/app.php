<?php
/**
 * Don Barbero - Layout Principal
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Don Barbero - Sistema de Agendamento Online. Agende seu horário de forma rápida e segura.">
    <meta name="author" content="Dante Testa - https://dantetesta.com.br">
    <meta name="theme-color" content="#1f2937">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($pageTitle ?? APP_NAME) ?>">
    <meta property="og:description" content="Sistema de Agendamento Online - Don Barbero">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= url($_SERVER['REQUEST_URI']) ?>">
    
    <title><?= e($pageTitle ?? APP_NAME) ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Configuração customizada do Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Animações suaves */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Loading spinner */
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    
    <?php if (isset($additionalHead)): ?>
        <?= $additionalHead ?>
    <?php endif; ?>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="<?= url('/') ?>" class="text-2xl font-bold text-gray-900 hover:text-gray-700 transition-colors">
                        Don Barbero
                    </a>
                </div>
                
                <!-- Menu Desktop -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="<?= url('/') ?>" class="text-gray-700 hover:text-gray-900 transition-colors">Início</a>
                    <?php if (isAuthenticated()): ?>
                        <a href="<?= url('/dashboard') ?>" class="text-gray-700 hover:text-gray-900 transition-colors">Meus Agendamentos</a>
                        <?php if (isAdmin()): ?>
                            <a href="<?= url('/admin') ?>" class="text-gray-700 hover:text-gray-900 transition-colors">Admin</a>
                        <?php endif; ?>
                        <a href="<?= url('/dashboard/profile') ?>" class="text-gray-700 hover:text-gray-900 transition-colors">Meu Perfil</a>
                        <a href="<?= url('/auth/logout') ?>" class="text-gray-700 hover:text-gray-900 transition-colors">Sair</a>
                        <span class="text-sm text-gray-500">Olá, <?= e(user()['name'] ?? 'Usuário') ?></span>
                    <?php else: ?>
                        <a href="<?= url('/auth/login') ?>" class="text-gray-700 hover:text-gray-900 transition-colors">Entrar</a>
                        <a href="<?= url('/auth/register') ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Cadastrar
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Menu Mobile (Toggle) -->
                <div class="md:hidden">
                    <button id="mobile-menu-toggle" type="button" class="text-gray-700 hover:text-gray-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Menu Mobile (Dropdown) -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 pt-2 pb-4 space-y-2">
                <a href="<?= url('/') ?>" class="block text-gray-700 hover:text-gray-900 py-2">Início</a>
                <?php if (isAuthenticated()): ?>
                    <a href="<?= url('/dashboard') ?>" class="block text-gray-700 hover:text-gray-900 py-2">Meus Agendamentos</a>
                    <?php if (isAdmin()): ?>
                        <a href="<?= url('/admin') ?>" class="block text-gray-700 hover:text-gray-900 py-2">Admin</a>
                    <?php endif; ?>
                    <a href="<?= url('/dashboard/profile') ?>" class="block text-gray-700 hover:text-gray-900 py-2">Meu Perfil</a>
                    <a href="<?= url('/auth/logout') ?>" class="block text-gray-700 hover:text-gray-900 py-2">Sair</a>
                    <span class="block text-sm text-gray-500 py-2">Olá, <?= e(user()['name'] ?? 'Usuário') ?></span>
                <?php else: ?>
                    <a href="<?= url('/auth/login') ?>" class="block text-gray-700 hover:text-gray-900 py-2">Entrar</a>
                    <a href="<?= url('/auth/register') ?>" class="block bg-blue-600 text-white px-4 py-2 rounded-lg text-center">
                        Cadastrar
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages -->
    <?php $flash = getFlash(); ?>
    <?php if (!empty($flash)): ?>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <?php foreach ($flash as $type => $message): ?>
                <?php
                    $bgColor = 'bg-blue-50';
                    $textColor = 'text-blue-800';
                    $borderColor = 'border-blue-200';
                    
                    if ($type === 'error') {
                        $bgColor = 'bg-red-50';
                        $textColor = 'text-red-800';
                        $borderColor = 'border-red-200';
                    } elseif ($type === 'success') {
                        $bgColor = 'bg-green-50';
                        $textColor = 'text-green-800';
                        $borderColor = 'border-green-200';
                    } elseif ($type === 'warning') {
                        $bgColor = 'bg-yellow-50';
                        $textColor = 'text-yellow-800';
                        $borderColor = 'border-yellow-200';
                    }
                ?>
                <div class="<?= $bgColor ?> <?= $textColor ?> border <?= $borderColor ?> px-4 py-3 rounded-lg fade-in" role="alert">
                    <p><?= e($message) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Conteúdo Principal -->
    <main class="min-h-screen">
        <?= $content ?? '' ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Don Barbero</h3>
                    <p class="text-gray-400">Sistema de agendamento online para barbearia profissional.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Links Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="<?= url('/') ?>" class="text-gray-400 hover:text-white transition-colors">Início</a></li>
                        <?php if (isAuthenticated()): ?>
                            <li><a href="<?= url('/dashboard') ?>" class="text-gray-400 hover:text-white transition-colors">Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="<?= url('/auth/login') ?>" class="text-gray-400 hover:text-white transition-colors">Entrar</a></li>
                            <li><a href="<?= url('/auth/register') ?>" class="text-gray-400 hover:text-white transition-colors">Cadastrar</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contato</h4>
                    <p class="text-gray-400">Entre em contato conosco para mais informações.</p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; <?= date('Y') ?> Don Barbero. Todos os direitos reservados.</p>
                <p class="mt-2">Desenvolvido por <a href="https://dantetesta.com.br" target="_blank" rel="noopener" class="text-blue-400 hover:text-blue-300">Dante Testa</a> | Versão <?= APP_VERSION ?></p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script>
        // Toggle mobile menu
        document.getElementById('mobile-menu-toggle')?.addEventListener('click', function() {
            document.getElementById('mobile-menu')?.classList.toggle('hidden');
        });
        
        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('[role="alert"]').forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    
    <?php if (isset($additionalScripts)): ?>
        <?= $additionalScripts ?>
    <?php endif; ?>
</body>
</html>
