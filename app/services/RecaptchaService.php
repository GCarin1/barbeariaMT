<?php
/**
 * Don Barbero - Google reCAPTCHA v3 Service
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:58
 * @version 1.0.0
 */

declare(strict_types=1);

namespace services;

class RecaptchaService {
    private string $secretKey;
    private float $minScore;
    
    public function __construct() {
        $this->secretKey = RECAPTCHA_SECRET;
        $this->minScore = RECAPTCHA_MIN_SCORE;
    }
    
    /**
     * Verificar token do reCAPTCHA v3
     * 
     * @param string $token Token do cliente
     * @param string $action Ação esperada (login, signup, etc)
     * @return array Resultado da verificação
     */
    public function verify(string $token, string $action = ''): array {
        // Pular verificação em ambiente local
        if (APP_ENV === 'local') {
            return [
                'success' => true,
                'score' => 1.0,
                'action' => $action,
                'hostname' => 'localhost',
                'bypassed' => true
            ];
        }
        
        if (empty($token)) {
            return [
                'success' => false,
                'error' => 'Token reCAPTCHA não fornecido',
                'score' => 0
            ];
        }
        
        // Fazer request para Google
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $this->secretKey,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            error_log("reCAPTCHA API Error: HTTP {$httpCode}");
            return [
                'success' => false,
                'error' => 'Erro ao verificar reCAPTCHA',
                'score' => 0
            ];
        }
        
        $result = json_decode($response, true);
        
        if (!$result || !isset($result['success'])) {
            return [
                'success' => false,
                'error' => 'Resposta inválida do reCAPTCHA',
                'score' => 0
            ];
        }
        
        // Verificar sucesso
        if (!$result['success']) {
            $errors = $result['error-codes'] ?? [];
            error_log("reCAPTCHA Failed: " . implode(', ', $errors));
            return [
                'success' => false,
                'error' => 'Verificação reCAPTCHA falhou',
                'score' => 0,
                'error_codes' => $errors
            ];
        }
        
        // Verificar score
        $score = $result['score'] ?? 0;
        
        if ($score < $this->minScore) {
            return [
                'success' => false,
                'error' => 'Score reCAPTCHA muito baixo (possível bot)',
                'score' => $score
            ];
        }
        
        // Verificar ação (se fornecida)
        if (!empty($action) && isset($result['action']) && $result['action'] !== $action) {
            return [
                'success' => false,
                'error' => 'Ação reCAPTCHA não corresponde',
                'score' => $score
            ];
        }
        
        return [
            'success' => true,
            'score' => $score,
            'action' => $result['action'] ?? '',
            'hostname' => $result['hostname'] ?? ''
        ];
    }
    
    /**
     * Gerar HTML do script reCAPTCHA
     * 
     * @return string HTML do script
     */
    public static function getScriptTag(): string {
        $siteKey = RECAPTCHA_SITE_KEY;
        return "<script src=\"https://www.google.com/recaptcha/api.js?render={$siteKey}\"></script>";
    }
    
    /**
     * Gerar código JS para executar reCAPTCHA
     * 
     * @param string $action Ação (login, signup, etc)
     * @param string $callback Função callback JS
     * @return string Código JavaScript
     */
    public static function getExecuteScript(string $action, string $callback = 'onRecaptchaSuccess'): string {
        $siteKey = RECAPTCHA_SITE_KEY;
        return <<<JS
        grecaptcha.ready(function() {
            grecaptcha.execute('{$siteKey}', {action: '{$action}'}).then(function(token) {
                {$callback}(token);
            });
        });
        JS;
    }
}
