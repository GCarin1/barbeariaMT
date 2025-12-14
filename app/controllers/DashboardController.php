<?php
/**
 * Don Barbero - Controller do Dashboard do Cliente
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @updated 03/11/2025 16:08 - Fase 4: Dashboard completo
 * @version 1.2.0
 */

declare(strict_types=1);

namespace controllers;

use models\Appointment;
use models\Service;
use models\User;
use services\AppointmentService;

class DashboardController {
    private Appointment $appointmentModel;
    private Service $serviceModel;
    private User $userModel;
    private AppointmentService $appointmentService;
    
    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->serviceModel = new Service();
        $this->userModel = new User();
        $this->appointmentService = new AppointmentService();
    }
    
    /**
     * Dashboard principal do cliente
     */
    public function index(): void {
        $userId = userId();
        
        // Buscar agendamentos do usuário
        $appointments = $this->appointmentModel->getUserAppointments($userId, [
            'order' => 'start_at.desc'
        ]);
        
        // Enriquecer com dados de serviço
        $appointments = $this->enrichAppointments($appointments);
        
        // Separar por status
        $upcoming = array_filter($appointments, function($apt) {
            return strtotime($apt['start_at']) >= time() && 
                   $apt['status'] !== Appointment::STATUS_CANCELADO;
        });
        
        $past = array_filter($appointments, function($apt) {
            return strtotime($apt['start_at']) < time() || 
                   $apt['status'] === Appointment::STATUS_CANCELADO;
        });
        
        $pageTitle = 'Meus Agendamentos - ' . APP_NAME;
        require_once VIEWS_PATH . '/dashboard/index.php';
    }
    
    /**
     * Formulário de novo agendamento
     */
    public function new(): void {
        $pageTitle = 'Novo Agendamento - ' . APP_NAME;
        
        // Buscar serviços disponíveis
        $services = $this->serviceModel->getActive();
        
        require_once VIEWS_PATH . '/dashboard/new.php';
    }
    
    /**
     * Buscar slots disponíveis
     */
    public function slots(): void {
        // Validar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json(['error' => 'Método não permitido'], 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $date = $data['date'] ?? '';
        $serviceId = (int)($data['service_id'] ?? 0);
        
        if (empty($date) || $serviceId <= 0) {
            json(['error' => 'Data e serviço são obrigatórios'], 400);
        }
        
        // Buscar slots disponíveis
        $result = $this->appointmentService->getAvailableSlots($date, $serviceId);
        
        if (isset($result['error'])) {
            json(['error' => $result['error']], 400);
        }
        
        json(['success' => true, 'data' => $result], 200);
    }
    
    /**
     * Salvar novo agendamento
     */
    public function store(): void {
        // Ler dados JSON
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Verificar CSRF (do JSON, não do $_POST)
        if (!verifyCsrfToken($data[CSRF_TOKEN_NAME] ?? null)) {
            json(['error' => 'Token de segurança inválido.'], 403);
        }
        
        $userId = userId();
        $serviceId = (int)($data['service_id'] ?? 0);
        $startAt = $data['start_at'] ?? '';
        $endAt = $data['end_at'] ?? '';
        
        // Validações
        if ($serviceId <= 0 || empty($startAt) || empty($endAt)) {
            json(['error' => 'Dados incompletos.'], 400);
        }
        
        // Validar datas
        $start = new \DateTime($startAt, new \DateTimeZone('America/Sao_Paulo'));
        $end = new \DateTime($endAt, new \DateTimeZone('America/Sao_Paulo'));
        $now = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
        
        // Não permitir agendamento no passado
        if ($start < $now) {
            json(['error' => 'Não é possível agendar no passado.'], 400);
        }
        
        // Verificar se o slot ainda está disponível
        $date = $start->format('Y-m-d');
        $availableSlots = $this->appointmentService->getAvailableSlots($date, $serviceId);
        
        if (isset($availableSlots['error'])) {
            json(['error' => $availableSlots['error']], 400);
        }
        
        $slotAvailable = false;
        foreach ($availableSlots['slots'] as $slot) {
            if ($slot['start'] === $startAt && $slot['end'] === $endAt) {
                $slotAvailable = true;
                break;
            }
        }
        
        if (!$slotAvailable) {
            json(['error' => 'Este horário não está mais disponível.'], 400);
        }
        
        // Criar agendamento
        $appointment = $this->appointmentModel->createAppointment([
            'user_id' => $userId,
            'service_id' => $serviceId,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => Appointment::STATUS_AGUARDANDO,
            'payment_confirmed' => false
        ]);
        
        if ($appointment === null) {
            json(['error' => 'Erro ao criar agendamento. Tente novamente.'], 500);
        }
        
        json([
            'success' => true,
            'message' => 'Agendamento criado com sucesso!',
            'data' => [
                'id' => $appointment['id'],
                'control_code' => $appointment['control_code'],
                'start_at' => $appointment['start_at'],
                'end_at' => $appointment['end_at'],
                'status' => $appointment['status']
            ]
        ], 201);
    }
    
    /**
     * Cancelar agendamento
     */
    public function cancel(string $id): void {
        // Verificar CSRF
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? null)) {
            flash('error', 'Token de segurança inválido.');
            redirect('/dashboard');
        }
        
        $userId = userId();
        
        // Tentar cancelar
        $result = $this->appointmentModel->cancelAppointment($id, $userId);
        
        if ($result['success']) {
            flash('success', $result['message']);
        } else {
            flash('error', $result['message']);
        }
        
        redirect('/dashboard');
    }
    
    /**
     * Enriquecer agendamentos com dados de serviço
     */
    private function enrichAppointments(array $appointments): array {
        foreach ($appointments as &$apt) {
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
     * Exibir formulário de edição de perfil
     */
    public function profile(): void {
        $userId = userId();
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            flash('error', 'Usuário não encontrado.');
            redirect('/dashboard');
        }
        
        $pageTitle = 'Meu Perfil - ' . APP_NAME;
        $oldInput = $_SESSION['_old_input'] ?? [];
        unset($_SESSION['_old_input']);
        
        require_once VIEWS_PATH . '/dashboard/profile.php';
    }
    
    /**
     * Atualizar perfil do usuário
     */
    public function updateProfile(): void {
        // Verificar CSRF
        if (!verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? null)) {
            flash('error', 'Token de segurança inválido.');
            redirect('/dashboard/profile');
        }
        
        $userId = userId();
        $data = sanitize($_POST);
        
        // Salvar old input
        $_SESSION['_old_input'] = [
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'whatsapp' => $data['whatsapp'] ?? ''
        ];
        
        // Validações
        $validator = new \services\ValidationService();
        $validator
            ->required('name', $data['name'] ?? '', 'O nome é obrigatório.')
            ->min('name', $data['name'] ?? '', 3)
            ->required('email', $data['email'] ?? '', 'O email é obrigatório.')
            ->email('email', $data['email'] ?? '');
        
        // Verificar se email já existe (exceto o próprio)
        if ($this->userModel->emailExists($data['email'], $userId)) {
            flash('error', 'Este email já está em uso por outro usuário.');
            redirect('/dashboard/profile');
        }
        
        // Atualizar senha se fornecida
        if (!empty($data['new_password'])) {
            if (empty($data['current_password'])) {
                flash('error', 'Informe a senha atual para alterar a senha.');
                redirect('/dashboard/profile');
            }
            
            $user = $this->userModel->find($userId);
            if (!$this->userModel->verifyPassword($data['current_password'], $user['password_hash'])) {
                flash('error', 'Senha atual incorreta.');
                redirect('/dashboard/profile');
            }
            
            $validator->strongPassword('new_password', $data['new_password']);
            
            if (!empty($data['confirm_password']) && $data['new_password'] !== $data['confirm_password']) {
                flash('error', 'As senhas não correspondem.');
                redirect('/dashboard/profile');
            }
        }
        
        if ($validator->fails()) {
            flash('error', $validator->getFirstError());
            redirect('/dashboard/profile');
        }
        
        // Atualizar dados
        $updateData = [
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'whatsapp' => !empty($data['whatsapp']) ? $data['whatsapp'] : null
        ];
        
        $result = $this->userModel->update($userId, $updateData);
        
        if (!$result) {
            flash('error', 'Erro ao atualizar perfil.');
            redirect('/dashboard/profile');
        }
        
        // Atualizar senha se fornecida
        if (!empty($data['new_password'])) {
            $this->userModel->updatePassword($userId, $data['new_password']);
        }
        
        // Atualizar sessão
        $_SESSION['user']['name'] = $updateData['name'];
        $_SESSION['user']['email'] = $updateData['email'];
        
        unset($_SESSION['_old_input']);
        flash('success', 'Perfil atualizado com sucesso!');
        redirect('/dashboard/profile');
    }
}
