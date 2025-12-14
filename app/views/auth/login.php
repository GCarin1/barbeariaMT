<?php
/**
 * Don Barbero - Página de Login
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */

$pageTitle = 'Login - ' . APP_NAME;

// Script do reCAPTCHA v3 (apenas em produção)
$additionalHead = '';
if (APP_ENV !== 'local') {
    $additionalHead = '<script src="https://www.google.com/recaptcha/api.js?render=' . RECAPTCHA_SITE_KEY . '"></script>';
}

ob_start();
?>

<section class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl">
        <!-- Header -->
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Bem-vindo de Volta</h2>
            <p class="text-gray-600">Entre com suas credenciais</p>
        </div>
        
        <!-- Form -->
        <form action="<?= url('/auth/login') ?>" method="POST" class="mt-8 space-y-6" id="loginForm">
            <?= csrfField() ?>
            <input type="hidden" name="recaptcha_token" id="recaptcha_token">
            
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    autofocus
                    value="<?= e($oldInput['email'] ?? '') ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="seu@email.com"
                >
            </div>
            
            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                    placeholder="••••••••"
                >
            </div>
            
            <!-- Submit Button -->
            <div>
                <button 
                    type="submit"
                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-[1.02]"
                >
                    Entrar
                </button>
            </div>
            
            <!-- reCAPTCHA Info -->
            <div class="text-xs text-gray-500 text-center">
                Este site é protegido pelo reCAPTCHA e aplicam-se a 
                <a href="https://policies.google.com/privacy" target="_blank" class="text-blue-600 hover:underline">Política de Privacidade</a> e os 
                <a href="https://policies.google.com/terms" target="_blank" class="text-blue-600 hover:underline">Termos de Serviço</a> do Google.
            </div>
        </form>
        
        <!-- Links -->
        <div class="text-center space-y-2 pt-4 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                Não tem uma conta? 
                <a href="<?= url('/auth/register') ?>" class="font-medium text-blue-600 hover:text-blue-700 transition-colors">
                    Cadastre-se grátis
                </a>
            </p>
            <p class="text-sm text-gray-600">
                <a href="<?= url('/') ?>" class="font-medium text-gray-700 hover:text-gray-900 transition-colors">
                    ← Voltar para o início
                </a>
            </p>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

// Script reCAPTCHA v3 (apenas em produção)
$additionalScripts = '';
if (APP_ENV !== 'local') {
    $additionalScripts = <<<SCRIPT
<script>
    const form = document.getElementById('loginForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        grecaptcha.ready(function() {
            grecaptcha.execute('<?= RECAPTCHA_SITE_KEY ?>', {action: 'login'}).then(function(token) {
                document.getElementById('recaptcha_token').value = token;
                form.submit();
            });
        });
    });
</script>
SCRIPT;
} else {
    // Em local, submeter diretamente
    $additionalScripts = <<<SCRIPT
<script>
    // Em ambiente local, reCAPTCHA está desabilitado
    console.log('reCAPTCHA desabilitado em ambiente local');
</script>
SCRIPT;
}

require_once VIEWS_PATH . '/layouts/app.php';
?>
