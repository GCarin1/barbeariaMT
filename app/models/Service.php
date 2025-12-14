<?php
/**
 * Don Barbero - Service Model
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:46
 * @version 1.0.0
 */

declare(strict_types=1);

namespace models;

class Service extends BaseModel {
    protected string $table = 'services';
    protected string $primaryKey = 'id';
    
    /**
     * Buscar todos os serviços ativos
     * 
     * @return array Array de serviços
     */
    public function getActive(): array {
        return $this->where(
            ['active' => true],
            ['order' => 'id.asc']
        );
    }
    
    /**
     * Buscar serviço por nome
     * 
     * @param string $name Nome do serviço
     * @return array|null Serviço ou null
     */
    public function findByName(string $name): ?array {
        return $this->findOne(['name' => trim($name)]);
    }
    
    /**
     * Criar novo serviço
     * 
     * @param array $data Dados do serviço
     * @return array|null Serviço criado ou null
     */
    public function createService(array $data): ?array {
        // Validar dados obrigatórios
        $required = ['name', 'duration_minutes', 'price'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return null;
            }
        }
        
        // Preparar dados
        $serviceData = [
            'name' => trim($data['name']),
            'duration_minutes' => (int)$data['duration_minutes'],
            'price' => (float)$data['price'],
            'active' => $data['active'] ?? true
        ];
        
        return $this->create($serviceData);
    }
    
    /**
     * Ativar serviço
     * 
     * @param string|int $serviceId ID do serviço
     * @return bool Sucesso
     */
    public function activate(string|int $serviceId): bool {
        $result = $this->update($serviceId, ['active' => true]);
        return $result !== null;
    }
    
    /**
     * Desativar serviço
     * 
     * @param string|int $serviceId ID do serviço
     * @return bool Sucesso
     */
    public function deactivate(string|int $serviceId): bool {
        $result = $this->update($serviceId, ['active' => false]);
        return $result !== null;
    }
    
    /**
     * Obter preço do serviço
     * 
     * @param string|int $serviceId ID do serviço
     * @return float|null Preço ou null
     */
    public function getPrice(string|int $serviceId): ?float {
        $service = $this->find($serviceId);
        return $service !== null ? (float)$service['price'] : null;
    }
    
    /**
     * Obter duração do serviço
     * 
     * @param string|int $serviceId ID do serviço
     * @return int|null Duração em minutos ou null
     */
    public function getDuration(string|int $serviceId): ?int {
        $service = $this->find($serviceId);
        return $service !== null ? (int)$service['duration_minutes'] : null;
    }
}
