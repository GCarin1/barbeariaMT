<?php
/**
 * Don Barbero - Controller do Painel Admin
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @version 1.0.0
 */

declare(strict_types=1);

namespace controllers;

use models\Appointment;
use models\Service;
use models\User;

class AdminController {
    private Appointment $appointmentModel;
    private Service $serviceModel;
    private User $userModel;
    
    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->serviceModel = new Service();
        $this->userModel = new User();
    }
    
    /**
     * Dashboard do admin
     */
    public function index(): void {
        // Filtros
        $filter = $_GET['filter'] ?? 'all';
        $status = $_GET['status'] ?? '';
        
        // Buscar agendamentos baseado no filtro
        $appointments = $this->getFilteredAppointments($filter, $status);
        
        // Enriquecer com dados de usuário e serviço
        $appointments = $this->enrichAppointments($appointments);
        
        // Estatísticas baseadas nos agendamentos filtrados
        $stats = $this->calculateStats($appointments);
        
        $pageTitle = 'Painel Admin - ' . APP_NAME;
        require_once VIEWS_PATH . '/admin/index.php';
    }
    
    /**
     * Atualizar status do agendamento
     */
    public function updateStatus(string $id): void {
        // Verificar CSRF
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? null)) {
            flash('error', 'Token de segurança inválido.');
            redirect('/admin');
        }
        
        $newStatus = $_POST['status'] ?? '';
        
        $validStatuses = [
            Appointment::STATUS_AGUARDANDO,
            Appointment::STATUS_CONFIRMADO,
            Appointment::STATUS_CONCLUIDO,
            Appointment::STATUS_CANCELADO
        ];
        
        if (!in_array($newStatus, $validStatuses)) {
            flash('error', 'Status inválido.');
            redirect('/admin');
        }
        
        $success = $this->appointmentModel->updateStatus($id, $newStatus);
        
        if ($success) {
            flash('success', 'Status atualizado com sucesso!');
        } else {
            flash('error', 'Erro ao atualizar status.');
        }
        
        redirect('/admin');
    }
    
    /**
     * Confirmar pagamento
     */
    public function confirmPayment(string $id): void {
        // Verificar CSRF
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? null)) {
            flash('error', 'Token de segurança inválido.');
            redirect('/admin');
        }
        
        $success = $this->appointmentModel->confirmPayment($id);
        
        if ($success) {
            flash('success', 'Pagamento confirmado com sucesso!');
        } else {
            flash('error', 'Erro ao confirmar pagamento.');
        }
        
        redirect('/admin');
    }
    
    /**
     * Relatórios financeiros
     */
    public function finance(): void {
        // Filtros de período
        $startDate = $_GET['start_date'] ?? date('Y-m-01'); // Primeiro dia do mês
        $endDate = $_GET['end_date'] ?? date('Y-m-d'); // Hoje
        
        // Buscar agendamentos do período
        $appointments = $this->appointmentModel->getByPeriod($startDate, $endDate);
        $appointments = $this->enrichAppointments($appointments);
        
        // Calcular relatório
        $report = $this->generateFinancialReport($appointments);
        
        // Exportar CSV se solicitado
        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $this->exportCSV($appointments, $startDate, $endDate);
            return;
        }
        
        $pageTitle = 'Relatórios Financeiros - ' . APP_NAME;
        require_once VIEWS_PATH . '/admin/finance.php';
    }
    
    /**
     * Gerar relatório financeiro
     */
    private function generateFinancialReport(array $appointments): array {
        $report = [
            'total_appointments' => 0, // Apenas agendamentos válidos (não cancelados)
            'total_canceled' => 0, // Separar cancelados
            'total_revenue' => 0,
            'total_paid' => 0,
            'total_pending' => 0,
            'by_service' => [],
            'by_status' => [
                'aguardando' => ['count' => 0, 'revenue' => 0],
                'confirmado' => ['count' => 0, 'revenue' => 0],
                'concluido' => ['count' => 0, 'revenue' => 0],
                'cancelado' => ['count' => 0, 'revenue' => 0]
            ],
            'by_payment' => [
                'paid' => ['count' => 0, 'revenue' => 0],
                'pending' => ['count' => 0, 'revenue' => 0]
            ]
        ];
        
        foreach ($appointments as $apt) {
            $price = (float)($apt['service_price'] ?? 0);
            $isCanceled = $apt['status'] === Appointment::STATUS_CANCELADO;
            
            // Contar status (incluindo cancelados para estatística)
            $report['by_status'][$apt['status']]['count']++;
            
            // CANCELADOS: Apenas contabilizar quantidade, NÃO contabilizar receita
            if ($isCanceled) {
                $report['total_canceled']++;
                // NÃO adiciona ao revenue
                continue; // Pula para o próximo
            }
            
            // Agendamentos VÁLIDOS (não cancelados)
            $report['total_appointments']++;
            $report['total_revenue'] += $price;
            $report['by_status'][$apt['status']]['revenue'] += $price;
            
            // Por pagamento
            if ($apt['payment_confirmed']) {
                $report['total_paid'] += $price;
                $report['by_payment']['paid']['count']++;
                $report['by_payment']['paid']['revenue'] += $price;
            } else {
                $report['total_pending'] += $price;
                $report['by_payment']['pending']['count']++;
                $report['by_payment']['pending']['revenue'] += $price;
            }
            
            // Por serviço
            $serviceName = $apt['service_name'] ?? 'Desconhecido';
            if (!isset($report['by_service'][$serviceName])) {
                $report['by_service'][$serviceName] = ['count' => 0, 'revenue' => 0];
            }
            $report['by_service'][$serviceName]['count']++;
            $report['by_service'][$serviceName]['revenue'] += $price;
        }
        
        return $report;
    }
    
    /**
     * Exportar relatório CSV
     */
    private function exportCSV(array $appointments, string $startDate, string $endDate): void {
        $filename = "relatorio_financeiro_{$startDate}_{$endDate}.csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalhos
        fputcsv($output, ['Data', 'Hora', 'Cliente', 'Email', 'Serviço', 'Valor', 'Status', 'Pagamento', 'Código']);
        
        // Dados
        foreach ($appointments as $apt) {
            fputcsv($output, [
                date('d/m/Y', strtotime($apt['start_at'])),
                date('H:i', strtotime($apt['start_at'])),
                $apt['user_name'],
                $apt['user_email'],
                $apt['service_name'],
                number_format((float)$apt['service_price'], 2, ',', '.'),
                ucfirst($apt['status']),
                $apt['payment_confirmed'] ? 'Pago' : 'Pendente',
                $apt['control_code']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Buscar agendamentos filtrados
     */
    private function getFilteredAppointments(string $filter, string $status): array {
        $params = ['order' => 'start_at.desc'];
        
        switch ($filter) {
            case 'today':
                $today = date('Y-m-d');
                return $this->appointmentModel->getByDate($today, $params);
                
            case 'tomorrow':
                $tomorrow = date('Y-m-d', strtotime('+1 day'));
                return $this->appointmentModel->getByDate($tomorrow, $params);
                
            case 'week':
                $startOfWeek = date('Y-m-d');
                $endOfWeek = date('Y-m-d', strtotime('+7 days'));
                return $this->appointmentModel->getByPeriod($startOfWeek, $endOfWeek);
                
            case 'all':
            default:
                $filters = [];
                if (!empty($status)) {
                    $filters['status'] = $status;
                }
                return $this->appointmentModel->where($filters, $params);
        }
    }
    
    /**
     * Enriquecer agendamentos com dados relacionados
     */
    private function enrichAppointments(array $appointments): array {
        foreach ($appointments as &$apt) {
            $user = $this->userModel->find($apt['user_id']);
            if ($user) {
                $apt['user_name'] = $user['name'];
                $apt['user_email'] = $user['email'];
                $apt['user_whatsapp'] = $user['whatsapp'];
            }
            
            $service = $this->serviceModel->find($apt['service_id']);
            if ($service) {
                $apt['service_name'] = $service['name'];
                $apt['service_price'] = $service['price'];
                $apt['service_duration'] = $service['duration_minutes'];
            }
        }
        return $appointments;
    }
    
    /**
     * Calcular estatísticas baseadas nos agendamentos
     */
    private function calculateStats(array $appointments): array {
        $stats = [
            'total' => count($appointments),
            'aguardando' => 0,
            'confirmado' => 0,
            'concluido' => 0,
            'cancelado' => 0,
            'total_paid' => 0,
            'total_pending' => 0
        ];
        
        foreach ($appointments as $apt) {
            $stats[$apt['status']]++;
            
            if (isset($apt['service_price'])) {
                if ($apt['payment_confirmed']) {
                    $stats['total_paid'] += (float)$apt['service_price'];
                } else {
                    $stats['total_pending'] += (float)$apt['service_price'];
                }
            }
        }
        
        return $stats;
    }
}
