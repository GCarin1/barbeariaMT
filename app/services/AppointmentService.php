<?php
/**
 * Don Barbero - Appointment Service (Geração de Slots)
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 16:14
 * @version 1.0.0
 */

declare(strict_types=1);

namespace services;

use models\Appointment;
use models\BarberSettings;
use models\Service;
use DateTime;
use DateTimeZone;
use DateInterval;

class AppointmentService {
    private Appointment $appointmentModel;
    private BarberSettings $settingsModel;
    private Service $serviceModel;
    private DateTimeZone $timezone;
    
    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->settingsModel = new BarberSettings();
        $this->serviceModel = new Service();
        $this->timezone = new DateTimeZone('America/Sao_Paulo');
    }
    
    /**
     * Calcular slots disponíveis para uma data e serviço
     * 
     * @param string $date Data no formato YYYY-MM-DD
     * @param int $serviceId ID do serviço
     * @return array Array de slots com horários disponíveis
     */
    public function getAvailableSlots(string $date, int $serviceId): array {
        // Validar data
        if (!$this->isValidDate($date)) {
            return ['error' => 'Data inválida'];
        }
        
        // Não permitir datas passadas
        $today = new DateTime('now', $this->timezone);
        $today->setTime(0, 0, 0);
        $requestedDate = new DateTime($date, $this->timezone);
        
        if ($requestedDate < $today) {
            return ['error' => 'Não é possível agendar em datas passadas'];
        }
        
        // Buscar serviço
        $service = $this->serviceModel->find((string)$serviceId);
        if (!$service) {
            return ['error' => 'Serviço não encontrado'];
        }
        
        // Buscar configurações do barbeiro
        $settings = $this->settingsModel->getSettings();
        if (!$settings) {
            return ['error' => 'Configurações do barbeiro não encontradas'];
        }
        
        // Verificar se o dia está disponível
        $dayOfWeek = (int)$requestedDate->format('N'); // 1=Monday, 7=Sunday
        if (!$this->settingsModel->isDayAvailable($dayOfWeek)) {
            return ['error' => 'Barbeiro não trabalha neste dia da semana'];
        }
        
        // Gerar todos os slots possíveis
        $allSlots = $this->generateAllSlots($date, $service, $settings);
        
        // Buscar agendamentos existentes na data
        $existingAppointments = $this->appointmentModel->getByDate($date);
        
        // Filtrar slots disponíveis
        $availableSlots = $this->filterAvailableSlots($allSlots, $existingAppointments);
        
        return [
            'date' => $date,
            'service' => [
                'id' => $service['id'],
                'name' => $service['name'],
                'duration' => $service['duration_minutes'],
                'price' => $service['price']
            ],
            'slots' => $availableSlots
        ];
    }
    
    /**
     * Gerar todos os slots possíveis para o dia
     * 
     * @param string $date Data
     * @param array $service Serviço
     * @param array $settings Configurações
     * @return array Array de slots
     */
    private function generateAllSlots(string $date, array $service, array $settings): array {
        $slots = [];
        $duration = (int)$service['duration_minutes'];
        
        // Horário de início e fim
        $startTime = $settings['start_hour'];
        $endTime = $settings['end_hour'];
        
        // Criar DateTime para início do dia
        $current = new DateTime("{$date} {$startTime}", $this->timezone);
        $end = new DateTime("{$date} {$endTime}", $this->timezone);
        
        // Se é hoje, começar do próximo slot disponível (pelo menos 1h de antecedência)
        $now = new DateTime('now', $this->timezone);
        $minimumAdvance = clone $now;
        $minimumAdvance->add(new DateInterval('PT1H'));
        
        if ($current->format('Y-m-d') === $now->format('Y-m-d') && $current < $minimumAdvance) {
            $current = $minimumAdvance;
            // Arredondar para o próximo slot de 15 minutos
            $minutes = (int)$current->format('i');
            $roundedMinutes = ceil($minutes / 15) * 15;
            if ($roundedMinutes >= 60) {
                $current->add(new DateInterval('PT1H'));
                $current->setTime((int)$current->format('H'), 0, 0);
            } else {
                $current->setTime((int)$current->format('H'), $roundedMinutes, 0);
            }
        }
        
        // Gerar slots com base na duração do serviço
        // Cada slot inicia quando o anterior termina
        while ($current < $end) {
            $slotEnd = clone $current;
            $slotEnd->add(new DateInterval("PT{$duration}M"));
            
            // Verificar se o slot cabe antes do horário de fechamento
            if ($slotEnd <= $end) {
                $slots[] = [
                    'start' => $current->format('Y-m-d\TH:i:s'),
                    'end' => $slotEnd->format('Y-m-d\TH:i:s'),
                    'start_time' => $current->format('H:i'),
                    'end_time' => $slotEnd->format('H:i'),
                    'available' => true
                ];
            }
            
            // Avançar pela duração do serviço (não mais 15 min fixo)
            $current->add(new DateInterval("PT{$duration}M"));
        }
        
        return $slots;
    }
    
    /**
     * Filtrar slots disponíveis (remover conflitos)
     * 
     * @param array $allSlots Todos os slots
     * @param array $appointments Agendamentos existentes
     * @return array Slots disponíveis
     */
    private function filterAvailableSlots(array $allSlots, array $appointments): array {
        $available = [];
        
        foreach ($allSlots as $slot) {
            $hasConflict = false;
            
            $slotStart = new DateTime($slot['start'], $this->timezone);
            $slotEnd = new DateTime($slot['end'], $this->timezone);
            
            foreach ($appointments as $appointment) {
                // Ignorar agendamentos cancelados
                if ($appointment['status'] === Appointment::STATUS_CANCELADO) {
                    continue;
                }
                
                $aptStart = new DateTime($appointment['start_at'], $this->timezone);
                $aptEnd = new DateTime($appointment['end_at'], $this->timezone);
                
                // Verificar sobreposição
                if ($this->hasOverlap($slotStart, $slotEnd, $aptStart, $aptEnd)) {
                    $hasConflict = true;
                    break;
                }
            }
            
            if (!$hasConflict) {
                $available[] = $slot;
            }
        }
        
        return $available;
    }
    
    /**
     * Verificar se dois períodos se sobrepõem
     * 
     * @param DateTime $start1 Início 1
     * @param DateTime $end1 Fim 1
     * @param DateTime $start2 Início 2
     * @param DateTime $end2 Fim 2
     * @return bool True se houver sobreposição
     */
    private function hasOverlap(DateTime $start1, DateTime $end1, DateTime $start2, DateTime $end2): bool {
        return $start1 < $end2 && $end1 > $start2;
    }
    
    /**
     * Validar formato de data
     * 
     * @param string $date Data YYYY-MM-DD
     * @return bool True se válida
     */
    private function isValidDate(string $date): bool {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
    
    /**
     * Obter próximos 7 dias disponíveis
     * 
     * @return array Array de datas disponíveis
     */
    public function getAvailableDates(): array {
        $dates = [];
        $current = new DateTime('now', $this->timezone);
        $workingDays = $this->settingsModel->getWorkingDays();
        
        $daysChecked = 0;
        $maxDays = 30; // Buscar nos próximos 30 dias
        
        while (count($dates) < 14 && $daysChecked < $maxDays) {
            $dayOfWeek = (int)$current->format('N');
            
            if (in_array($dayOfWeek, $workingDays)) {
                $dates[] = [
                    'date' => $current->format('Y-m-d'),
                    'formatted' => $current->format('d/m/Y'),
                    'day_name' => $this->getDayName($dayOfWeek),
                    'is_today' => $current->format('Y-m-d') === (new DateTime('now', $this->timezone))->format('Y-m-d')
                ];
            }
            
            $current->add(new DateInterval('P1D'));
            $daysChecked++;
        }
        
        return $dates;
    }
    
    /**
     * Obter nome do dia da semana em português
     * 
     * @param int $dayOfWeek 1=Segunda, 7=Domingo
     * @return string Nome do dia
     */
    private function getDayName(int $dayOfWeek): string {
        $days = [
            1 => 'Segunda',
            2 => 'Terça',
            3 => 'Quarta',
            4 => 'Quinta',
            5 => 'Sexta',
            6 => 'Sábado',
            7 => 'Domingo'
        ];
        return $days[$dayOfWeek] ?? '';
    }
}
