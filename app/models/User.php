<?php
/**
 * Don Barbero - User Model
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:46
 * @version 1.0.0
 */

declare(strict_types=1);

namespace models;

class User extends BaseModel {
    protected string $table = 'users';
    protected string $primaryKey = 'id';
    
    /**
     * Buscar usuário por email
     * 
     * @param string $email Email do usuário
     * @return array|null Usuário ou null
     */
    public function findByEmail(string $email): ?array {
        return $this->findOne(['email' => strtolower(trim($email))]);
    }
    
    /**
     * Verificar se email já existe
     * 
     * @param string $email Email
     * @param string|int|null $excludeId ID para excluir da busca (para updates)
     * @return bool True se existe
     */
    public function emailExists(string $email, string|int|null $excludeId = null): bool {
        $user = $this->findByEmail($email);
        
        if ($user === null) {
            return false;
        }
        
        if ($excludeId !== null && $user['id'] === $excludeId) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Criar novo usuário
     * 
     * @param array $data Dados do usuário
     * @return array|null Usuário criado ou null
     */
    public function createUser(array $data): ?array {
        // Validar dados obrigatórios
        $required = ['name', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return null;
            }
        }
        
        // Preparar dados
        $userData = [
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'role' => $data['role'] ?? 'client',
            'whatsapp' => !empty($data['whatsapp']) ? trim($data['whatsapp']) : null,
            'password_hash' => password_hash($data['password'], PASSWORD_ARGON2ID)
        ];
        
        return $this->create($userData);
    }
    
    /**
     * Verificar senha
     * 
     * @param string $password Senha em texto plano
     * @param string $hash Hash armazenado
     * @return bool True se válida
     */
    public function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
    
    /**
     * Verificar se precisa rehash da senha
     * 
     * @param string $hash Hash atual
     * @return bool True se precisa rehash
     */
    public function needsRehash(string $hash): bool {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID);
    }
    
    /**
     * Atualizar senha
     * 
     * @param string|int $userId ID do usuário
     * @param string $newPassword Nova senha
     * @return bool Sucesso
     */
    public function updatePassword(string|int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_ARGON2ID);
        $result = $this->update($userId, ['password_hash' => $hash]);
        return $result !== null;
    }
    
    /**
     * Buscar todos os clientes
     * 
     * @param array $params Parâmetros
     * @return array Array de clientes
     */
    public function getClients(array $params = []): array {
        $params['filters'] = ['role' => 'client'];
        $params['order'] = $params['order'] ?? 'created_at.desc';
        return $this->where(['role' => 'client'], $params);
    }
    
    /**
     * Buscar todos os admins
     * 
     * @return array Array de admins
     */
    public function getAdmins(): array {
        return $this->where(['role' => 'admin']);
    }
    
    /**
     * Verificar se usuário é admin
     * 
     * @param string|int $userId ID do usuário
     * @return bool True se admin
     */
    public function isAdmin(string|int $userId): bool {
        $user = $this->find($userId);
        return $user !== null && $user['role'] === 'admin';
    }
}
