<?php
/**
 * Don Barbero - Editar Perfil
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 16:38
 * @version 1.0.0
 */

$pageTitle = 'Meu Perfil - ' . APP_NAME;
ob_start();
?>

<section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="container mx-auto max-w-3xl">
        <div class="mb-8">
            <a href="<?= url('/dashboard') ?>" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Meu Perfil</h1>
            <p class="text-gray-600">Gerencie suas informações pessoais</p>
        </div>
        
        <form method="POST" action="<?= url('/dashboard/profile/update') ?>" class="space-y-6">
            <?= csrfField() ?>
            
            <!-- Dados Pessoais -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Dados Pessoais</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                        <input type="text" id="name" name="name" required
                               value="<?= e($oldInput['name'] ?? $user['name']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                        <input type="email" id="email" name="email" required
                               value="<?= e($oldInput['email'] ?? $user['email']) ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">WhatsApp (opcional)</label>
                        <input type="tel" id="whatsapp" name="whatsapp"
                               value="<?= e($oldInput['whatsapp'] ?? $user['whatsapp'] ?? '') ?>"
                               placeholder="(11) 98888-8888"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Alterar Senha -->
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Alterar Senha</h2>
                <p class="text-sm text-gray-600 mb-6">Deixe em branco se não quiser alterar</p>
                
                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Senha Atual</label>
                        <input type="password" id="current_password" name="current_password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                        <input type="password" id="new_password" name="new_password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Mínimo 10 caracteres com maiúsculas, minúsculas e números</p>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nova Senha</label>
                        <input type="password" id="confirm_password" name="confirm_password"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>
            
            <!-- Botões -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Salvar Alterações
                </button>
                <a href="<?= url('/dashboard') ?>" class="flex-1 text-center bg-gray-200 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</section>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/app.php';
?>
