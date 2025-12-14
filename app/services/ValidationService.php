<?php
/**
 * Don Barbero - Validation Service
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:58
 * @version 1.0.0
 */

declare(strict_types=1);

namespace services;

class ValidationService {
    private array $errors = [];
    
    /**
     * Validar campo obrigatório
     */
    public function required(string $field, $value, string $message = ''): self {
        if (empty($value) && $value !== '0') {
            $this->errors[$field] = $message ?: "O campo {$field} é obrigatório.";
        }
        return $this;
    }
    
    /**
     * Validar email
     */
    public function email(string $field, string $value, string $message = ''): self {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?: "Email inválido.";
        }
        return $this;
    }
    
    /**
     * Validar tamanho mínimo
     */
    public function min(string $field, string $value, int $min, string $message = ''): self {
        if (!empty($value) && mb_strlen($value) < $min) {
            $this->errors[$field] = $message ?: "O campo {$field} deve ter no mínimo {$min} caracteres.";
        }
        return $this;
    }
    
    /**
     * Validar tamanho máximo
     */
    public function max(string $field, string $value, int $max, string $message = ''): self {
        if (!empty($value) && mb_strlen($value) > $max) {
            $this->errors[$field] = $message ?: "O campo {$field} deve ter no máximo {$max} caracteres.";
        }
        return $this;
    }
    
    /**
     * Validar senha forte
     */
    public function strongPassword(string $field, string $value, string $message = ''): self {
        if (empty($value)) {
            return $this;
        }
        
        $hasUpper = preg_match('/[A-Z]/', $value);
        $hasLower = preg_match('/[a-z]/', $value);
        $hasNumber = preg_match('/[0-9]/', $value);
        $hasSpecial = preg_match('/[^A-Za-z0-9]/', $value);
        $minLength = mb_strlen($value) >= 10;
        
        if (!$hasUpper || !$hasLower || !$hasNumber || !$minLength) {
            $this->errors[$field] = $message ?: "A senha deve ter no mínimo 10 caracteres, incluindo maiúsculas, minúsculas e números.";
        }
        
        return $this;
    }
    
    /**
     * Validar se valores são iguais (confirmação)
     */
    public function match(string $field, string $value, string $compareValue, string $message = ''): self {
        if ($value !== $compareValue) {
            $this->errors[$field] = $message ?: "Os campos não correspondem.";
        }
        return $this;
    }
    
    /**
     * Verificar se há erros
     */
    public function fails(): bool {
        return !empty($this->errors);
    }
    
    /**
     * Obter erros
     */
    public function getErrors(): array {
        return $this->errors;
    }
    
    /**
     * Obter primeiro erro
     */
    public function getFirstError(): ?string {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
    
    /**
     * Limpar erros
     */
    public function clear(): self {
        $this->errors = [];
        return $this;
    }
}
