<?php
/**
 * Don Barbero - Appointment Model
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:46
 * @version 1.0.0
 */

declare(strict_types=1);

namespace models;

class Appointment extends BaseModel {
    protected string $table = 'appointments';
    protected string $primaryKey = 'id';
    
    public const STATUS_AGUARDANDO = 'aguardando';
    public const STATUS_CONFIRMADO = 'confirmado';
    public const STATUS_CONCLUIDO = 'concluido';
    public const STATUS_CANCELADO = 'cancelado';
    
    public function getUserAppointments(string $userId, array $params = []): array {
        $params['filters'] = ['user_id' => $userId];
        $params['order'] = $params['order'] ?? 'start_at.desc';
        return $this->where(['user_id' => $userId], $params);
    }
    
    public function getUserFutureAppointments(string $userId): array {
        return $this->where([
            'user_id' => $userId,
            'start_at' => ['operator' => 'gte', 'value' => date('c')],
            'status' => ['operator' => 'neq', 'value' => self::STATUS_CANCELADO]
        ], ['order' => 'start_at.asc']);
    }
    
    public function getByDate(string $date, array $params = []): array {
        $startOfDay = $date . 'T00:00:00-03:00';
        $endOfDay = $date . 'T23:59:59-03:00';
        
        return $this->where([
            'start_at' => ['operator' => 'gte', 'value' => $startOfDay],
            'end_at' => ['operator' => 'lte', 'value' => $endOfDay]
        ], $params);
    }
    
    public function getByPeriod(string $startDate, string $endDate, array $params = []): array {
        // Buscar todos os agendamentos
        $all = $this->all(['order' => $params['order'] ?? 'start_at.desc']);
        
        // Filtrar manualmente por período
        $filtered = array_filter($all, function($apt) use ($startDate, $endDate) {
            $aptDate = date('Y-m-d', strtotime($apt['start_at']));
            return $aptDate >= $startDate && $aptDate <= $endDate;
        });
        
        return array_values($filtered);
    }
    
    public function createAppointment(array $data): ?array {
        $required = ['user_id', 'service_id', 'start_at', 'end_at'];
        foreach ($required as $field) {
            if (empty($data[$field])) return null;
        }
        
        $controlCode = 'DB' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
        
        return $this->create([
            'user_id' => $data['user_id'],
            'service_id' => $data['service_id'],
            'start_at' => $data['start_at'],
            'end_at' => $data['end_at'],
            'status' => $data['status'] ?? self::STATUS_AGUARDANDO,
            'payment_confirmed' => $data['payment_confirmed'] ?? false,
            'control_code' => $controlCode,
            'notes' => $data['notes'] ?? null
        ]);
    }
    
    public function updateStatus(string|int $appointmentId, string $status): bool {
        $validStatuses = [self::STATUS_AGUARDANDO, self::STATUS_CONFIRMADO, self::STATUS_CONCLUIDO, self::STATUS_CANCELADO];
        if (!in_array($status, $validStatuses)) return false;
        
        $result = $this->update($appointmentId, ['status' => $status]);
        return $result !== null;
    }
    
    public function cancelAppointment(string|int $appointmentId, string $userId): array {
        $appointment = $this->find($appointmentId);
        
        if (!$appointment) return ['success' => false, 'message' => 'Agendamento não encontrado.'];
        if ($appointment['user_id'] !== $userId) return ['success' => false, 'message' => 'Sem permissão.'];
        if ($appointment['status'] === self::STATUS_CANCELADO) return ['success' => false, 'message' => 'Já cancelado.'];
        
        $startAt = new \DateTime($appointment['start_at']);
        $now = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
        $diff = $startAt->getTimestamp() - $now->getTimestamp();
        
        if ($diff < CANCELATION_ADVANCE_TIME) {
            $hours = round(CANCELATION_ADVANCE_TIME / 3600);
            return ['success' => false, 'message' => "Cancelamento requer {$hours}h de antecedência."];
        }
        
        $success = $this->updateStatus($appointmentId, self::STATUS_CANCELADO);
        return ['success' => $success, 'message' => $success ? 'Cancelado com sucesso.' : 'Erro ao cancelar.'];
    }
    
    public function confirmPayment(string|int $appointmentId): bool {
        $result = $this->update($appointmentId, ['payment_confirmed' => true]);
        return $result !== null;
    }
}
