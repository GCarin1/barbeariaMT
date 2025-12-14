<?php
/**
 * Don Barbero - Painel Admin
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 16:26
 * @version 1.0.0
 */

$pageTitle = 'Painel Admin - ' . APP_NAME;
ob_start();
?>

<section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="container mx-auto max-w-7xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Painel Administrativo</h1>
                    <p class="text-gray-600">Gerencie agendamentos e pagamentos</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="<?= url('/admin/finance') ?>" class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        📊 Relatórios Financeiros
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Total Hoje</div>
                <div class="text-2xl font-bold text-gray-900"><?= $stats['total'] ?></div>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-6">
                <div class="text-sm text-yellow-700 mb-1">Aguardando</div>
                <div class="text-2xl font-bold text-yellow-900"><?= $stats['aguardando'] ?></div>
            </div>
            <div class="bg-blue-50 rounded-lg shadow p-6">
                <div class="text-sm text-blue-700 mb-1">Confirmado</div>
                <div class="text-2xl font-bold text-blue-900"><?= $stats['confirmado'] ?></div>
            </div>
            <div class="bg-green-50 rounded-lg shadow p-6">
                <div class="text-sm text-green-700 mb-1">Concluído</div>
                <div class="text-2xl font-bold text-green-900"><?= $stats['concluido'] ?></div>
            </div>
            <div class="bg-gray-50 rounded-lg shadow p-6">
                <div class="text-sm text-gray-700 mb-1">Pago</div>
                <div class="text-xl font-bold text-green-600"><?= formatMoney($stats['total_paid']) ?></div>
            </div>
            <div class="bg-gray-50 rounded-lg shadow p-6">
                <div class="text-sm text-gray-700 mb-1">Pendente</div>
                <div class="text-xl font-bold text-orange-600"><?= formatMoney($stats['total_pending']) ?></div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex flex-wrap gap-3">
                <a href="?filter=all" class="px-4 py-2 rounded-lg <?= ($_GET['filter'] ?? 'all') === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    Todos
                </a>
                <a href="?filter=today" class="px-4 py-2 rounded-lg <?= ($_GET['filter'] ?? '') === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    Hoje
                </a>
                <a href="?filter=tomorrow" class="px-4 py-2 rounded-lg <?= ($_GET['filter'] ?? '') === 'tomorrow' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    Amanhã
                </a>
                <a href="?filter=week" class="px-4 py-2 rounded-lg <?= ($_GET['filter'] ?? '') === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                    Próximos 7 dias
                </a>
            </div>
        </div>
        
        <!-- Appointments List -->
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serviço</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pagamento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">WhatsApp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    Nenhum agendamento encontrado
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $apt): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= formatDate($apt['start_at'], 'd/m/Y') ?></div>
                                    <div class="text-sm text-gray-500"><?= formatDate($apt['start_at'], 'H:i') ?> - <?= formatDate($apt['end_at'], 'H:i') ?></div>
                                    <div class="text-xs text-gray-400">#<?= e($apt['control_code']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900"><?= e($apt['user_name']) ?></div>
                                    <div class="text-sm text-gray-500"><?= e($apt['user_email']) ?></div>
                                    <?php if ($apt['user_whatsapp']): ?>
                                        <div class="text-xs text-gray-400"><?= e($apt['user_whatsapp']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= e($apt['service_name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    <?= formatMoney((float)$apt['service_price']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" action="<?= url('/admin/update-status/' . $apt['id']) ?>" class="inline">
                                        <?= csrfField() ?>
                                        <select name="status" onchange="this.form.submit()" class="text-xs rounded-full px-3 py-1 font-semibold <?= 
                                            $apt['status'] === 'confirmado' ? 'bg-blue-100 text-blue-800' : 
                                            ($apt['status'] === 'concluido' ? 'bg-green-100 text-green-800' :
                                            ($apt['status'] === 'cancelado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) ?>">
                                            <option value="aguardando" <?= $apt['status'] === 'aguardando' ? 'selected' : '' ?>>Aguardando</option>
                                            <option value="confirmado" <?= $apt['status'] === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                            <option value="concluido" <?= $apt['status'] === 'concluido' ? 'selected' : '' ?>>Concluído</option>
                                            <option value="cancelado" <?= $apt['status'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($apt['payment_confirmed']): ?>
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">✓ Pago</span>
                                    <?php else: ?>
                                        <form method="POST" action="<?= url('/admin/confirm-payment/' . $apt['id']) ?>" class="inline">
                                            <?= csrfField() ?>
                                            <button type="submit" class="px-2 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 hover:bg-orange-200">
                                                Confirmar Pag.
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($apt['user_whatsapp']): ?>
                                        <a href="https://wa.me/55<?= preg_replace('/[^0-9]/', '', $apt['user_whatsapp']) ?>?text=<?= urlencode("Olá {$apt['user_name']}! 😊\n\nLembrando do seu agendamento:\n📅 {$apt['start_at']}\n✂️ {$apt['service_name']}\n💰 " . formatMoney((float)$apt['service_price']) . "\n\nVai conseguir vir? Confirma aí! 👍") ?>" 
                                           target="_blank" 
                                           class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-lg hover:bg-green-600">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                            Avisar
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">Sem WhatsApp</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button onclick="showModal(<?= htmlspecialchars(json_encode($apt), ENT_QUOTES) ?>)" class="text-blue-600 hover:text-blue-900 font-semibold">
                                        Ver Detalhes
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal de Detalhes -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-start mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Detalhes do Agendamento</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div id="modalContent" class="space-y-4">
                <!-- Conteúdo será preenchido dinamicamente -->
            </div>
        </div>
    </div>
</div>

<script>
function showModal(appointment) {
    const modal = document.getElementById('detailsModal');
    const content = document.getElementById('modalContent');
    
    const statusColors = {
        'aguardando': 'bg-yellow-100 text-yellow-800',
        'confirmado': 'bg-blue-100 text-blue-800',
        'concluido': 'bg-green-100 text-green-800',
        'cancelado': 'bg-red-100 text-red-800'
    };
    
    content.innerHTML = `
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2 bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-600 mb-1">Código de Controle</div>
                <div class="text-2xl font-bold text-gray-900">${appointment.control_code}</div>
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <div class="text-sm text-gray-600 mb-1">Cliente</div>
                <div class="font-semibold text-gray-900">${appointment.user_name}</div>
                <div class="text-sm text-gray-600">${appointment.user_email}</div>
                ${appointment.user_whatsapp ? `<div class="text-sm text-gray-600">${appointment.user_whatsapp}</div>` : ''}
            </div>
            
            <div class="col-span-2 md:col-span-1">
                <div class="text-sm text-gray-600 mb-1">Serviço</div>
                <div class="font-semibold text-gray-900">${appointment.service_name}</div>
                <div class="text-sm text-gray-600">${appointment.service_duration} minutos</div>
            </div>
            
            <div>
                <div class="text-sm text-gray-600 mb-1">Data</div>
                <div class="font-semibold text-gray-900">${formatDateTime(appointment.start_at, 'date')}</div>
            </div>
            
            <div>
                <div class="text-sm text-gray-600 mb-1">Horário</div>
                <div class="font-semibold text-gray-900">${formatDateTime(appointment.start_at, 'time')} - ${formatDateTime(appointment.end_at, 'time')}</div>
            </div>
            
            <div>
                <div class="text-sm text-gray-600 mb-1">Status</div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold ${statusColors[appointment.status] || 'bg-gray-100 text-gray-800'}">
                    ${appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1)}
                </span>
            </div>
            
            <div>
                <div class="text-sm text-gray-600 mb-1">Pagamento</div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold ${appointment.payment_confirmed ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'}">
                    ${appointment.payment_confirmed ? '✓ Pago' : 'Pendente'}
                </span>
            </div>
            
            <div class="col-span-2">
                <div class="text-sm text-gray-600 mb-1">Valor</div>
                <div class="text-3xl font-bold text-blue-600">R$ ${parseFloat(appointment.service_price).toFixed(2).replace('.', ',')}</div>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

function formatDateTime(dateStr, type) {
    const date = new Date(dateStr);
    if (type === 'date') {
        return date.toLocaleDateString('pt-BR');
    }
    return date.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
}

// Fechar modal ao clicar fora
document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/app.php';
?>
