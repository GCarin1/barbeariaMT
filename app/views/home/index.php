<?php
/**
 * Don Barbero - Landing Page
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */

$pageTitle = 'Don Barbero - Agendamento Online';

ob_start();
?>

<!-- Hero Section -->
<section class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white py-20 md:py-32">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center fade-in">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                Bem-vindo ao <span class="text-blue-400">Don Barbero</span>
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-8">
                Agende seu horário de forma rápida, segura e sem complicações
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= url('/auth/register') ?>" class="bg-blue-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-blue-700 transform hover:scale-105 transition-all shadow-lg">
                    Criar Conta Grátis
                </a>
                <a href="<?= url('/auth/login') ?>" class="bg-white text-gray-900 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all shadow-lg">
                    Já tenho Conta
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-16 md:py-24 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Nossos Serviços</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Serviços profissionais com qualidade e dedicação
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <?php foreach ($services as $index => $service): ?>
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-8 hover:shadow-2xl transition-all transform hover:-translate-y-2 border border-gray-200">
                    <!-- Icon -->
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <?php if ($service['name'] === 'Cabelo'): ?>
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        <?php elseif ($service['name'] === 'Barba'): ?>
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        <?php else: ?>
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 text-center"><?= e($service['name']) ?></h3>
                    <div class="text-center mb-4">
                        <span class="text-3xl font-bold text-blue-600"><?= formatMoney($service['price']) ?></span>
                    </div>
                    <p class="text-gray-600 text-center mb-6">
                        <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <?= $service['duration_minutes'] ?> minutos
                    </p>
                    <a href="<?= url('/auth/register') ?>" class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Agendar Agora
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 md:py-24 bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Por que Escolher Don Barbero?</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Tecnologia e praticidade para sua comodidade
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-6xl mx-auto">
            <!-- Feature 1 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Agendamento Rápido</h3>
                <p class="text-gray-600">Reserve seu horário em poucos cliques, 24/7</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">100% Seguro</h3>
                <p class="text-gray-600">Seus dados protegidos com tecnologia de ponta</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Lembretes Automáticos</h3>
                <p class="text-gray-600">Nunca mais esqueça seus compromissos</p>
            </div>
            
            <!-- Feature 4 -->
            <div class="text-center p-6">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4 mx-auto">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Fácil Cancelamento</h3>
                <p class="text-gray-600">Cancele com antecedência quando precisar</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 md:py-24 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-5xl font-bold mb-6">Pronto para Começar?</h2>
        <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
            Crie sua conta grátis e agende seu primeiro horário hoje mesmo
        </p>
        <a href="<?= url('/auth/register') ?>" class="inline-block bg-white text-blue-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transform hover:scale-105 transition-all shadow-lg">
            Cadastrar Gratuitamente
        </a>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 md:py-24 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Como Funciona</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Três passos simples para seu agendamento
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Step 1 -->
            <div class="text-center relative">
                <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mb-6 mx-auto">
                    1
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Crie sua Conta</h3>
                <p class="text-gray-600">Cadastro rápido e gratuito em menos de 1 minuto</p>
                <!-- Arrow for desktop -->
                <div class="hidden md:block absolute top-10 -right-8 text-blue-600">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="text-center relative">
                <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mb-6 mx-auto">
                    2
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Escolha o Serviço</h3>
                <p class="text-gray-600">Selecione entre Cabelo, Barba ou Combo</p>
                <!-- Arrow for desktop -->
                <div class="hidden md:block absolute top-10 -right-8 text-blue-600">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mb-6 mx-auto">
                    3
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Confirme o Horário</h3>
                <p class="text-gray-600">Escolha data e hora disponíveis e pronto!</p>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/app.php';
?>
