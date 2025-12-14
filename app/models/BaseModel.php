<?php
/**
 * Don Barbero - Base Model
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:46
 * @version 1.0.0
 */

declare(strict_types=1);

namespace models;

use core\Database;

abstract class BaseModel {
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Buscar todos os registros
     * 
     * @param array $params Parâmetros (order, limit, etc.)
     * @return array Array de registros
     */
    public function all(array $params = []): array {
        return $this->db->select($this->table, $params);
    }
    
    /**
     * Buscar por ID
     * 
     * @param string|int $id ID do registro
     * @return array|null Registro ou null
     */
    public function find(string|int $id): ?array {
        return $this->db->findById($this->table, (string)$id, $this->primaryKey);
    }
    
    /**
     * Buscar por filtros
     * 
     * @param array $filters Filtros
     * @param array $params Parâmetros adicionais
     * @return array Array de registros
     */
    public function where(array $filters, array $params = []): array {
        $params['filters'] = $filters;
        return $this->db->select($this->table, $params);
    }
    
    /**
     * Buscar um único registro por filtros
     * 
     * @param array $filters Filtros
     * @return array|null Registro ou null
     */
    public function findOne(array $filters): ?array {
        return $this->db->findOne($this->table, $filters);
    }
    
    /**
     * Criar novo registro
     * 
     * @param array $data Dados do registro
     * @return array|null Registro criado ou null
     */
    public function create(array $data): ?array {
        $result = $this->db->insert($this->table, $data);
        return $result[0] ?? null;
    }
    
    /**
     * Atualizar registro por ID
     * 
     * @param string|int $id ID do registro
     * @param array $data Dados a atualizar
     * @return array|null Registro atualizado ou null
     */
    public function update(string|int $id, array $data): ?array {
        $result = $this->db->update($this->table, $data, [$this->primaryKey => (string)$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Deletar registro por ID
     * 
     * @param string|int $id ID do registro
     * @return bool Sucesso
     */
    public function delete(string|int $id): bool {
        return $this->db->delete($this->table, [$this->primaryKey => (string)$id]);
    }
    
    /**
     * Contar registros
     * 
     * @param array $filters Filtros
     * @return int Contagem
     */
    public function count(array $filters = []): int {
        $params = [
            'select' => 'count',
            'filters' => $filters
        ];
        
        $result = $this->db->select($this->table, $params);
        return (int)($result[0]['count'] ?? 0);
    }
}
