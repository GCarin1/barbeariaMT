<?php
/**
 * Don Barbero - BarberSettings Model
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:46
 * @version 1.0.0
 */

declare(strict_types=1);

namespace models;

class BarberSettings extends BaseModel {
    protected string $table = 'barber_settings';
    protected string $primaryKey = 'id';
    
    /**
     * Obter configurações atuais
     * 
     * @return array|null Configurações ou null
     */
    public function getSettings(): ?array {
        $settings = $this->all(['limit' => 1]);
        return $settings[0] ?? null;
    }
    
    /**
     * Atualizar configurações
     * 
     * @param array $data Novos dados
     * @return bool Sucesso
     */
    public function updateSettings(array $data): bool {
        $current = $this->getSettings();
        
        if ($current === null) {
            // Criar se não existir
            $result = $this->create($data);
            return $result !== null;
        }
        
        // Atualizar
        $result = $this->update($current['id'], $data);
        return $result !== null;
    }
    
    /**
     * Obter horário de início
     * 
     * @return string|null Horário (HH:MM)
     */
    public function getStartHour(): ?string {
        $settings = $this->getSettings();
        return $settings['start_hour'] ?? null;
    }
    
    /**
     * Obter horário de término
     * 
     * @return string|null Horário (HH:MM)
     */
    public function getEndHour(): ?string {
        $settings = $this->getSettings();
        return $settings['end_hour'] ?? null;
    }
    
    /**
     * Obter dias de trabalho
     * 
     * @return array Array de dias (1-7)
     */
    public function getWorkingDays(): array {
        $settings = $this->getSettings();
        if ($settings === null || empty($settings['working_days'])) {
            return [];
        }
        
        return array_map('intval', explode(',', $settings['working_days']));
    }
    
    /**
     * Verificar se dia está disponível
     * 
     * @param int $dayOfWeek Dia da semana (1=Segunda, 7=Domingo)
     * @return bool True se disponível
     */
    public function isDayAvailable(int $dayOfWeek): bool {
        return in_array($dayOfWeek, $this->getWorkingDays());
    }
}
