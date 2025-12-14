<?php
/**
 * Don Barbero - Relatórios Financeiros
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 16:34
 * @version 1.0.0
 */

$pageTitle = 'Relatórios Financeiros - ' . APP_NAME;
ob_start();
?>

<section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="container mx-auto max-w-7xl">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Relatórios Financeiros</h1>
                    <p class="text-gray-600">Análise completa de faturamento e agendamentos</p>
                </div>
                <a href="<?= url('/admin') ?>" class="text-blue-600 hover:text-blue-700 font-semibold">
                    ← Voltar ao Painel
                </a>
            </div>
            <?php 
                $startDate = $_GET['start_date'] ?? date('Y-m-01');
                $endDate = $_GET['end_date'] ?? date('Y-m-d');
                $startFormatted = date('d/m/Y', strtotime($startDate));
                $endFormatted = date('d/m/Y', strtotime($endDate));
            ?>
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
                <p class="text-sm text-blue-800">
                    <strong>Período:</strong> <?= $startFormatted ?> até <?= $endFormatted ?>
                </p>
            </div>
        </div>
        
        <!-- Filtros Rápidos -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Filtros Rápidos</h3>
            <div class="flex flex-wrap gap-3">
                <a href="<?= url('/admin/finance') ?>?start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>" 
                   class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 font-semibold transition-colors">
                    📅 Hoje
                </a>
                <a href="<?= url('/admin/finance') ?>?start_date=<?= date('Y-m-d', strtotime('monday this week')) ?>&end_date=<?= date('Y-m-d', strtotime('sunday this week')) ?>" 
                   class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 font-semibold transition-colors">
                    📆 Esta Semana
                </a>
                <a href="<?= url('/admin/finance') ?>?start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-t') ?>" 
                   class="px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 font-semibold transition-colors">
                    📊 Este Mês
                </a>
                <a href="<?= url('/admin/finance') ?>?start_date=<?= date('Y-01-01') ?>&end_date=<?= date('Y-12-31') ?>" 
                   class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 font-semibold transition-colors">
                    📈 Este Ano
                </a>
                <a href="<?= url('/admin/finance') ?>?start_date=<?= date('Y-m-d', strtotime('-30 days')) ?>&end_date=<?= date('Y-m-d') ?>" 
                   class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-semibold transition-colors">
                    📉 Últimos 30 dias
                </a>
            </div>
        </div>
        
        <!-- Filtros Personalizados -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Período Personalizado</h3>
            <form method="GET" action="<?= url('/admin/finance') ?>" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Início</label>
                    <input type="date" name="start_date" value="<?= e($_GET['start_date'] ?? date('Y-m-01')) ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Fim</label>
                    <input type="date" name="end_date" value="<?= e($_GET['end_date'] ?? date('Y-m-d')) ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                        Buscar
                    </button>
                    <a href="<?= url('/admin/finance') ?>?start_date=<?= e($_GET['start_date'] ?? date('Y-m-01')) ?>&end_date=<?= e($_GET['end_date'] ?? date('Y-m-d')) ?>&export=csv" 
                       class="px-6 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700">
                        📥 Exportar CSV
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Cards de Totais -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-600 mb-1">Agendamentos Válidos</div>
                <div class="text-3xl font-bold text-gray-900"><?= $report['total_appointments'] ?></div>
                <div class="text-xs text-gray-500 mt-1">(Excluindo cancelados)</div>
            </div>
            <div class="bg-blue-50 rounded-lg shadow p-6">
                <div class="text-sm text-blue-700 mb-1">Faturamento Total</div>
                <div class="text-3xl font-bold text-blue-900"><?= formatMoney($report['total_revenue']) ?></div>
                <div class="text-xs text-gray-500 mt-1">(Apenas válidos)</div>
            </div>
            <div class="bg-green-50 rounded-lg shadow p-6">
                <div class="text-sm text-green-700 mb-1">✅ Recebido</div>
                <div class="text-3xl font-bold text-green-900"><?= formatMoney($report['total_paid']) ?></div>
            </div>
            <div class="bg-orange-50 rounded-lg shadow p-6">
                <div class="text-sm text-orange-700 mb-1">⏳ A Receber</div>
                <div class="text-3xl font-bold text-orange-900"><?= formatMoney($report['total_pending']) ?></div>
            </div>
            <div class="bg-red-50 rounded-lg shadow p-6">
                <div class="text-sm text-red-700 mb-1">❌ Cancelados</div>
                <div class="text-3xl font-bold text-red-900"><?= $report['total_canceled'] ?></div>
                <div class="text-xs text-gray-500 mt-1">(Não faturados)</div>
            </div>
        </div>
        
        <!-- Por Status -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Por Status</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php foreach ($report['by_status'] as $status => $data): ?>
                    <?php if ($data['count'] > 0): ?>
                        <div class="border rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1"><?= ucfirst($status) ?></div>
                            <div class="text-2xl font-bold text-gray-900"><?= $data['count'] ?></div>
                            <div class="text-sm text-blue-600 font-semibold"><?= formatMoney($data['revenue']) ?></div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Por Serviço -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Por Serviço</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($report['by_service'] as $service => $data): ?>
                    <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow">
                        <div class="text-lg font-bold text-gray-900 mb-2"><?= e($service) ?></div>
                        <div class="space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Quantidade:</span>
                                <span class="font-semibold text-gray-900"><?= $data['count'] ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Faturamento:</span>
                                <span class="font-semibold text-blue-600"><?= formatMoney($data['revenue']) ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Média:</span>
                                <span class="font-semibold text-gray-900"><?= formatMoney($data['revenue'] / $data['count']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/app.php';
?>
