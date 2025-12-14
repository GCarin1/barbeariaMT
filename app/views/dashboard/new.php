<?php
/**
 * Don Barbero - Novo Agendamento
 * 
 * @author Dante Testa <https://dantetesta.com.br>
 * @created 03/11/2025 16:08
 * @version 1.2.0
 */

$pageTitle = 'Novo Agendamento - ' . APP_NAME;

ob_start();
?>

<section class="py-8 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
    <div class="container mx-auto max-w-4xl">
        <!-- Header -->
        <div class="mb-8">
            <a href="<?= url('/dashboard') ?>" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar para Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Novo Agendamento</h1>
            <p class="text-gray-600">Escolha o serviço e horário desejado</p>
        </div>
        
        <!-- Step 1: Escolher Serviço -->
        <div class="bg-white rounded-xl shadow-md p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                Escolha o Serviço
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php foreach ($services as $service): ?>
                    <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-lg transition-all cursor-pointer group" 
                         onclick="selectService(<?= $service['id'] ?>, '<?= e($service['name']) ?>', <?= $service['duration_minutes'] ?>, <?= $service['price'] ?>)">
                        <div class="text-center">
                            <!-- Icon -->
                            <div class="w-16 h-16 bg-blue-100 group-hover:bg-blue-600 rounded-full flex items-center justify-center mb-4 mx-auto transition-colors">
                                <svg class="w-8 h-8 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            
                            <h3 class="text-xl font-bold text-gray-900 mb-2"><?= e($service['name']) ?></h3>
                            <p class="text-gray-600 mb-3"><?= $service['duration_minutes'] ?> minutos</p>
                            <p class="text-2xl font-bold text-blue-600"><?= formatMoney((float)$service['price']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Step 2: Escolher Data -->
        <div class="bg-white rounded-xl shadow-md p-8 mb-8" id="dateSection" style="display: none;">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                Escolha a Data
            </h2>
            
            <div id="calendarContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Datas serão carregadas dinamicamente -->
            </div>
        </div>
        
        <!-- Step 3: Escolher Horário -->
        <div class="bg-white rounded-xl shadow-md p-8 mb-8" id="timeSection" style="display: none;">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">3</span>
                Escolha o Horário
            </h2>
            
            <div id="selectedInfo" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <strong>Serviço:</strong> <span id="infoService">-</span><br>
                    <strong>Data:</strong> <span id="infoDate">-</span>
                </p>
            </div>
            
            <div id="loadingSlots" class="text-center py-8" style="display: none;">
                <div class="spinner mx-auto mb-4"></div>
                <p class="text-gray-600">Buscando horários disponíveis...</p>
            </div>
            
            <div id="slotsContainer" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <!-- Slots serão carregados dinamicamente -->
            </div>
            
            <div id="noSlots" class="text-center py-8" style="display: none;">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-600">Nenhum horário disponível para esta data.</p>
                <button onclick="backToDate()" class="mt-4 text-blue-600 hover:text-blue-700 font-semibold">
                    ← Escolher outra data
                </button>
            </div>
        </div>
        
        <!-- Step 4: Confirmação (Fase 6) -->
        <div class="bg-white rounded-xl shadow-md p-8" id="confirmSection" style="display: none;">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">4</span>
                Confirmar Agendamento
            </h2>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Resumo do Agendamento:</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Serviço:</dt>
                        <dd class="font-semibold" id="confirmService">-</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Data:</dt>
                        <dd class="font-semibold" id="confirmDate">-</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Horário:</dt>
                        <dd class="font-semibold" id="confirmTime">-</dd>
                    </div>
                    <div class="flex justify-between border-t pt-2 mt-2">
                        <dt class="text-gray-900 font-bold">Valor:</dt>
                        <dd class="text-blue-600 font-bold text-xl" id="confirmPrice">-</dd>
                    </div>
                </dl>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <strong>✓ Quase lá!</strong> Revise as informações e clique em "Confirmar Agendamento" para finalizar.
                </p>
            </div>
            
            <div id="confirmError" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 text-red-800" style="display: none;"></div>
            
            <div class="flex gap-4">
                <button onclick="backToTime()" id="btnBack" class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                    ← Voltar
                </button>
                <button onclick="confirmAppointment()" id="btnConfirm" class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Confirmar Agendamento
                </button>
            </div>
        </div>
    </div>
</section>

<script>
    // Estado do agendamento
    let appointmentState = {
        service: null,
        date: null,
        slot: null
    };
    
    // Selecionar serviço
    function selectService(id, name, duration, price) {
        appointmentState.service = {id, name, duration, price};
        
        // Mostrar seção de data
        document.getElementById('dateSection').style.display = 'block';
        document.getElementById('dateSection').scrollIntoView({behavior: 'smooth'});
        
        // Carregar datas disponíveis
        loadAvailableDates();
    }
    
    // Carregar datas disponíveis
    async function loadAvailableDates() {
        const container = document.getElementById('calendarContainer');
        container.innerHTML = '<div class="col-span-full text-center py-8"><div class="spinner mx-auto"></div></div>';
        
        try {
            // Por enquanto, gerar próximas 14 datas (segundas a sábados)
            const dates = generateNext14Dates();
            
            container.innerHTML = '';
            dates.forEach(dateObj => {
                const btn = document.createElement('button');
                btn.className = 'border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:bg-blue-50 transition-all text-center';
                btn.innerHTML = `
                    <div class="text-sm text-gray-600 mb-1">${dateObj.dayName}</div>
                    <div class="text-2xl font-bold text-gray-900">${dateObj.day}</div>
                    <div class="text-sm text-gray-600">${dateObj.monthYear}</div>
                `;
                btn.onclick = () => selectDate(dateObj.date, dateObj.formatted);
                container.appendChild(btn);
            });
        } catch (error) {
            container.innerHTML = '<div class="col-span-full text-center text-red-600">Erro ao carregar datas</div>';
        }
    }
    
    // Gerar próximas 14 datas (exemplo)
    function generateNext14Dates() {
        const dates = [];
        const workingDays = [1, 2, 3, 4, 5, 6]; // Segunda a Sábado
        const today = new Date();
        let current = new Date(today);
        
        while (dates.length < 14) {
            const dayOfWeek = current.getDay();
            const adjustedDay = dayOfWeek === 0 ? 7 : dayOfWeek;
            
            if (workingDays.includes(adjustedDay)) {
                dates.push({
                    date: current.toISOString().split('T')[0],
                    formatted: current.toLocaleDateString('pt-BR'),
                    day: current.getDate(),
                    monthYear: current.toLocaleDateString('pt-BR', {month: 'short', year: 'numeric'}),
                    dayName: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'][current.getDay()]
                });
            }
            
            current.setDate(current.getDate() + 1);
        }
        
        return dates;
    }
    
    // Selecionar data
    async function selectDate(date, formatted) {
        appointmentState.date = {date, formatted};
        
        // Mostrar seção de horários
        document.getElementById('timeSection').style.display = 'block';
        document.getElementById('timeSection').scrollIntoView({behavior: 'smooth'});
        
        // Atualizar info
        document.getElementById('infoService').textContent = appointmentState.service.name;
        document.getElementById('infoDate').textContent = formatted;
        
        // Carregar slots
        await loadSlots(date, appointmentState.service.id);
    }
    
    // Carregar slots disponíveis
    async function loadSlots(date, serviceId) {
        const container = document.getElementById('slotsContainer');
        const loading = document.getElementById('loadingSlots');
        const noSlots = document.getElementById('noSlots');
        
        container.innerHTML = '';
        container.style.display = 'none';
        noSlots.style.display = 'none';
        loading.style.display = 'block';
        
        try {
            const response = await fetch('<?= url('/dashboard/slots') ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({date, service_id: serviceId})
            });
            
            const result = await response.json();
            loading.style.display = 'none';
            
            if (result.success && result.data.slots.length > 0) {
                container.style.display = 'grid';
                result.data.slots.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.className = 'border-2 border-gray-200 rounded-lg py-3 px-2 hover:border-blue-500 hover:bg-blue-50 transition-all text-center font-semibold text-gray-900';
                    btn.textContent = slot.start_time;
                    btn.onclick = () => selectSlot(slot);
                    container.appendChild(btn);
                });
            } else {
                noSlots.style.display = 'block';
            }
        } catch (error) {
            loading.style.display = 'none';
            noSlots.style.display = 'block';
            console.error('Erro ao carregar slots:', error);
        }
    }
    
    // Selecionar slot
    function selectSlot(slot) {
        appointmentState.slot = slot;
        
        // Mostrar confirmação
        document.getElementById('confirmSection').style.display = 'block';
        document.getElementById('confirmSection').scrollIntoView({behavior: 'smooth'});
        
        // Preencher resumo
        document.getElementById('confirmService').textContent = appointmentState.service.name;
        document.getElementById('confirmDate').textContent = appointmentState.date.formatted;
        document.getElementById('confirmTime').textContent = `${slot.start_time} - ${slot.end_time}`;
        document.getElementById('confirmPrice').textContent = `R$ ${appointmentState.service.price.toFixed(2).replace('.', ',')}`;
    }
    
    // Voltar para data
    function backToDate() {
        document.getElementById('timeSection').style.display = 'none';
        document.getElementById('confirmSection').style.display = 'none';
        appointmentState.date = null;
        appointmentState.slot = null;
    }
    
    // Voltar para horário
    function backToTime() {
        document.getElementById('confirmSection').style.display = 'none';
        appointmentState.slot = null;
    }
    
    // Confirmar agendamento
    async function confirmAppointment() {
        const btnConfirm = document.getElementById('btnConfirm');
        const btnBack = document.getElementById('btnBack');
        const errorDiv = document.getElementById('confirmError');
        
        // Desabilitar botões
        btnConfirm.disabled = true;
        btnBack.disabled = true;
        btnConfirm.innerHTML = '<div class="spinner-custom inline-block mr-2"></div> Salvando...';
        errorDiv.style.display = 'none';
        
        try {
            const response = await fetch('<?= url('/dashboard/store') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= generateCsrfToken() ?>'
                },
                body: JSON.stringify({
                    service_id: appointmentState.service.id,
                    start_at: appointmentState.slot.start,
                    end_at: appointmentState.slot.end,
                    <?= CSRF_TOKEN_NAME ?>: '<?= generateCsrfToken() ?>'
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Sucesso! Mostrar mensagem e redirecionar
                alert('✓ Agendamento confirmado com sucesso!\n\nCódigo: ' + result.data.control_code + '\n\nVocê será redirecionado para seus agendamentos.');
                window.location.href = '<?= url('/dashboard') ?>';
            } else {
                // Erro
                errorDiv.textContent = result.error || 'Erro ao confirmar agendamento.';
                errorDiv.style.display = 'block';
                btnConfirm.disabled = false;
                btnBack.disabled = false;
                btnConfirm.textContent = 'Confirmar Agendamento';
            }
        } catch (error) {
            errorDiv.textContent = 'Erro de conexão. Tente novamente.';
            errorDiv.style.display = 'block';
            btnConfirm.disabled = false;
            btnBack.disabled = false;
            btnConfirm.textContent = 'Confirmar Agendamento';
            console.error('Erro:', error);
        }
    }
</script>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/app.php';
?>
