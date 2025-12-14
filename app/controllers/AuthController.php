<?php
/**
 * Don Barbero - Controller de Autenticação
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @updated 03/11/2025 15:58 - Fase 3: Autenticação completa
 * @version 1.1.0
 */

declare(strict_types=1);

namespace controllers;

use models\User;
use services\RecaptchaService;
use services\RateLimitService;
use services\ValidationService;

class AuthController {
    private User $userModel;
    private RecaptchaService $recaptcha;
    private RateLimitService $rateLimit;
    
    public function __construct() {
        $this->userModel = new User();
        $this->recaptcha = new RecaptchaService();
        $this->rateLimit = new RateLimitService();
    }
    
    /**
     * Exibir formulário de registro
     */
    public function registerForm(): void {
        if (isAuthenticated()) {
            redirect('/dashboard');
        }
        
        $pageTitle = 'Cadastro - ' . APP_NAME;
        $oldInput = $_SESSION['_old_input'] ?? [];
        unset($_SESSION['_old_input']);
        
        require_once VIEWS_PATH . '/auth/register.php';
    }
    
    /**
     * Processar registro
     */
    public function register(): void {
        // Verificar CSRF
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? null)) {
            flash('error', 'Token de segurança inválido. Tente novamente.');
            redirect('/auth/register');
        }
        
        // Rate limiting
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $rateLimitKey = 'signup_' . $ip;
        $rateCheck = $this->rateLimit->check($rateLimitKey, RATE_LIMIT_SIGNUP, RATE_LIMIT_WINDOW);
        
        if (!$rateCheck['allowed']) {
            flash('error', $rateCheck['message']);
            redirect('/auth/register');
        }
        
        // Sanitizar inputs
        $data = sanitize($_POST);
        
        // Salvar old input para reexibir no formulário
        $_SESSION['_old_input'] = [
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'whatsapp' => $data['whatsapp'] ?? ''
        ];
        
        // Verificar reCAPTCHA v3
        $recaptchaToken = $data['recaptcha_token'] ?? '';
        $recaptchaResult = $this->recaptcha->verify($recaptchaToken, 'signup');
        
        if (!$recaptchaResult['success']) {
            flash('error', 'Falha na verificação de segurança. Tente novamente.');
            redirect('/auth/register');
        }
        
        // Validações
        $validator = new ValidationService();
        $validator
            ->required('name', $data['name'] ?? '', 'O nome é obrigatório.')
            ->min('name', $data['name'] ?? '', 3, 'O nome deve ter no mínimo 3 caracteres.')
            ->max('name', $data['name'] ?? '', 100, 'O nome deve ter no máximo 100 caracteres.')
            ->required('email', $data['email'] ?? '', 'O email é obrigatório.')
            ->email('email', $data['email'] ?? '', 'Email inválido.')
            ->required('password', $data['password'] ?? '', 'A senha é obrigatória.')
            ->strongPassword('password', $data['password'] ?? '')
            ->match('password_confirmation', $data['password_confirmation'] ?? '', $data['password'] ?? '', 'As senhas não correspondem.');
        
        if ($validator->fails()) {
            flash('error', $validator->getFirstError());
            redirect('/auth/register');
        }
        
        // Verificar se email já existe
        if ($this->userModel->emailExists($data['email'])) {
            flash('error', 'Este email já está cadastrado.');
            redirect('/auth/register');
        }
        
        // Criar usuário
        $user = $this->userModel->createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'whatsapp' => !empty($data['whatsapp']) ? $data['whatsapp'] : null,
            'password' => $data['password'],
            'role' => 'client'
        ]);
        
        if ($user === null) {
            flash('error', 'Erro ao criar conta. Tente novamente.');
            redirect('/auth/register');
        }
        
        // Limpar old input e rate limit
        unset($_SESSION['_old_input']);
        $this->rateLimit->clear($rateLimitKey);
        
        // Login automático
        $this->doLogin($user);
        
        flash('success', 'Conta criada com sucesso! Bem-vindo(a), ' . e($user['name']) . '!');
        redirect('/dashboard');
    }
    
    /**
     * Exibir formulário de login
     */
    public function loginForm(): void {
        if (isAuthenticated()) {
            redirect('/dashboard');
        }
        
        $pageTitle = 'Login - ' . APP_NAME;
        $oldInput = $_SESSION['_old_input'] ?? [];
        unset($_SESSION['_old_input']);
        
        require_once VIEWS_PATH . '/auth/login.php';
    }
    
    /**
     * Processar login
     */
    public function login(): void {
        // Verificar CSRF
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? null)) {
            flash('error', 'Token de segurança inválido. Tente novamente.');
            redirect('/auth/login');
        }
        
        // Rate limiting
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $rateLimitKey = 'login_' . $ip;
        $rateCheck = $this->rateLimit->check($rateLimitKey, RATE_LIMIT_LOGIN, RATE_LIMIT_WINDOW);
        
        if (!$rateCheck['allowed']) {
            flash('error', $rateCheck['message']);
            redirect('/auth/login');
        }
        
        // Sanitizar inputs
        $data = sanitize($_POST);
        
        // Salvar old input
        $_SESSION['_old_input'] = [
            'email' => $data['email'] ?? ''
        ];
        
        // Verificar reCAPTCHA v3
        $recaptchaToken = $data['recaptcha_token'] ?? '';
        $recaptchaResult = $this->recaptcha->verify($recaptchaToken, 'login');
        
        if (!$recaptchaResult['success']) {
            flash('error', 'Falha na verificação de segurança. Tente novamente.');
            redirect('/auth/login');
        }
        
        // Validações básicas
        if (empty($data['email']) || empty($data['password'])) {
            flash('error', 'Email e senha são obrigatórios.');
            redirect('/auth/login');
        }
        
        // Buscar usuário
        $user = $this->userModel->findByEmail($data['email']);
        
        if ($user === null) {
            flash('error', 'Email ou senha incorretos.');
            redirect('/auth/login');
        }
        
        // Verificar senha
        if (!$this->userModel->verifyPassword($data['password'], $user['password_hash'])) {
            flash('error', 'Email ou senha incorretos.');
            redirect('/auth/login');
        }
        
        // Verificar se precisa rehash da senha
        if ($this->userModel->needsRehash($user['password_hash'])) {
            $this->userModel->updatePassword($user['id'], $data['password']);
        }
        
        // Limpar old input e rate limit
        unset($_SESSION['_old_input']);
        $this->rateLimit->clear($rateLimitKey);
        
        // Fazer login
        $this->doLogin($user);
        
        flash('success', 'Login realizado com sucesso! Bem-vindo(a), ' . e($user['name']) . '!');
        
        // Redirecionar baseado no papel
        if ($user['role'] === 'admin') {
            redirect('/admin');
        } else {
            redirect('/dashboard');
        }
    }
    
    /**
     * Realizar login (sessão)
     */
    private function doLogin(array $user): void {
        // Regenerar session ID (prevenir session fixation)
        session_regenerate_id(true);
        
        // Salvar dados na sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
    }
    
    /**
     * Logout
     */
    public function logout(): void {
        // Limpar sessão
        $_SESSION = [];
        
        // Destruir cookie da sessão
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destruir sessão
        session_destroy();
        
        flash('success', 'Você saiu com sucesso.');
        redirect('/');
    }
}
