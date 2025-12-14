<?php
/**
 * Don Barbero - Dashboard do Cliente
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 15:34
 * @updated 03/11/2025 16:08 - Fase 4
 * @version 1.2.0
 */

$pageTitle = 'Meus Agendamentos - ' . APP_NAME;

ob_start();
?>

<section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="container mx-auto max-w-7xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Meus Agendamentos</h1>
                    <p class="text-gray-600">Gerencie seus horários na Don Barbero</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="<?= url('/dashboard/new') ?>" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl transform hover:scale-105">
                        <span class="text-xl mr-2">+</span> Novo Agendamento
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Agendamentos Futuros -->
        <?php if (!empty($upcoming)): ?>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Próximos Agendamentos
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($upcoming as $apt): ?>
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow overflow-hidden border-l-4 <?= $apt['status'] === 'confirmado' ? 'border-green-500' : 'border-yellow-500' ?>">
                        <div class="p-6">
                            <!-- Status Badge -->
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold <?= $apt['status'] === 'confirmado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                    <?= ucfirst(e($apt['status'])) ?>
                                </span>
                                <span class="text-xs text-gray-500">#<?= e($apt['control_code']) ?></span>
                            </div>
                            
                            <!-- Serviço -->
                            <h3 class="text-xl font-bold text-gray-900 mb-2"><?= e($apt['service_name']) ?></h3>
                            
                            <!-- Data e Hora -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span><?= formatDate($apt['start_at'], 'd/m/Y') ?></span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span><?= formatDate($apt['start_at'], 'H:i') ?> - <?= formatDate($apt['end_at'], 'H:i') ?></span>
                                </div>
                            </div>
                            
                            <!-- Preço -->
                            <div class="text-2xl font-bold text-blue-600 mb-4">
                                <?= formatMoney((float)$apt['service_price']) ?>
                            </div>
                            
                            <!-- Ações -->
                            <form method="POST" action="<?= url('/dashboard/cancel/' . $apt['id']) ?>" onsubmit="return confirm('Tem certeza que deseja cancelar este agendamento?');">
                                <?= csrfField() ?>
                                <button type="submit" class="w-full bg-red-50 text-red-600 px-4 py-2 rounded-lg font-semibold hover:bg-red-100 transition-colors">
                                    Cancelar Agendamento
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-md p-12 text-center mb-8">
            <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Nenhum agendamento futuro</h3>
            <p class="text-gray-600 mb-6">Você ainda não tem horários agendados. Que tal fazer um agendamento agora?</p>
            <a href="<?= url('/dashboard/new') ?>" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Fazer Primeiro Agendamento
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Histórico -->
        <?php if (!empty($past)): ?>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Histórico
            </h2>
            
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horário</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serviço</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($past as $apt): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= formatDate($apt['start_at'], 'd/m/Y') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?= formatDate($apt['start_at'], 'H:i') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= e($apt['service_name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= formatMoney((float)$apt['service_price']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                        <?= $apt['status'] === 'concluido' ? 'bg-green-100 text-green-800' : 
                                            ($apt['status'] === 'cancelado' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') ?>">
                                        <?= ucfirst(e($apt['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/app.php';
?>
