# Changelog

Todas as mudanças notáveis neste projeto serão documentadas neste arquivo.

O formato é baseado em [Keep a Changelog](https://keepachangelog.com/pt-BR/1.0.0/),
e este projeto adere ao [Versionamento Semântico](https://semver.org/lang/pt-BR/).

---

## [1.0.0] - 2025-11-03

### ✅ Adicionado - Fase 1: Bootstrap do Projeto

#### Infraestrutura
- Estrutura MVC completa (Controllers, Models, Views)
- Sistema de roteamento customizado com suporte a middlewares
- Autoloader PSR-4 simplificado
- Front controller (index.php)
- Configurações centralizadas (config.php)
- Sistema de bootstrap da aplicação

#### Segurança
- Headers de segurança (CSP, X-Frame-Options, etc.)
- Sistema de CSRF tokens
- Helpers de sanitização e escape
- Configuração de sessões seguras
- .htaccess com regras de segurança
- Bloqueio de arquivos sensíveis

#### Frontend
- Layout responsivo mobile-first com Tailwind CSS
- Landing page profissional
- Navbar responsiva com menu mobile
- Footer informativo
- Sistema de flash messages
- Animações suaves (fade-in)

#### Controllers
- HomeController (landing page)
- AuthController (stubs para login/registro)
- DashboardController (stub para dashboard cliente)
- AdminController (stub para painel admin)

#### Views
- Layout principal (layouts/app.php)
- Landing page (home/index.php)
- Página de login (auth/login.php)
- Página de registro (auth/register.php)
- Dashboard básico (dashboard/index.php)

#### Helpers e Utilitários
- 20+ funções auxiliares globais
- Helpers de URL e assets
- Helpers de autenticação e autorização
- Helpers de formatação (datas, moeda)
- Função de debug (dd)

#### Documentação
- README.md completo com instruções
- .env.example com todas as variáveis
- .gitignore configurado
- CHANGELOG.md (este arquivo)
- Comentários inline em todo o código

#### Database
- Schema SQL completo para Supabase/PostgreSQL
- Tabelas: users, services, appointments, barber_settings, payments_ledger, audit_log
- Constraint de exclusão GIST para prevenir overlaps
- Índices otimizados
- Triggers para updated_at
- View completa de agendamentos
- Políticas RLS básicas
- Seed data (serviços, configurações, admin)

#### Rotas Implementadas
- GET / (home)
- GET /auth/login (formulário)
- GET /auth/register (formulário)
- GET /dashboard (requer auth)

### 📝 Notas de Desenvolvimento
- Desenvolvido por: Dante Testa (https://dantetesta.com.br)
- Data de início: 03/11/2025 15:34
- Stack: PHP 8.2, Supabase (PostgreSQL), Tailwind CSS
- Arquitetura: Custom MVC Pattern
- Deploy target: Apache (cPanel)

### 🎯 Próximos Passos (Fase 2)
- Executar schema.sql no Supabase
- Testar constraint de exclusão
- Validar seed data
- Criar Model base para comunicação com Supabase REST API

---

## [1.1.0] - 2025-11-03

### ✅ Adicionado - Fase 2: Banco de Dados Supabase

#### Models e Database Layer
- Classe `Database` para comunicação com Supabase REST API
- `BaseModel` abstrato com operações CRUD
- `User` model com autenticação e gestão de usuários
- `Service` model para gerenciar serviços
- `Appointment` model com lógica de cancelamento
- `BarberSettings` model para configurações

#### Funcionalidades Database
- SELECT com filtros, ordenação, limit/offset
- INSERT, UPDATE, DELETE via REST API
- Suporte a operadores (eq, gte, lte, neq)
- Find by ID e find one
- RPC para stored procedures
- Teste de conexão

#### Documentação
- `database/INSTRUCTIONS.md` com passo a passo
- `test-db.php` para validar conexão
- Schema SQL completo e documentado

### 📝 Detalhes Técnicos
- Comunicação via cURL com Supabase REST API
- Headers de autenticação (apikey + Bearer token)
- Timeouts configuráveis
- SSL verification em produção
- Error logging integrado

## [1.2.0] - 2025-11-03

### ✅ Adicionado - Fase 3: Autenticação + reCAPTCHA v3

#### AuthController Completo
- Sistema de registro com validações robustas
- Sistema de login com verificação de credenciais
- Logout seguro com limpeza de sessão
- Login automático após registro
- Regeneração de session ID (anti-fixation)
- Redirecionamento baseado em role (admin/client)

#### Serviços de Segurança
- **RecaptchaService**: Integração Google reCAPTCHA v3
  - Verificação server-side com score mínimo
  - Validação de action (login/signup)
  - Suporte a ações customizadas
- **RateLimitService**: Proteção contra brute force
  - Rate limiting por IP
  - Janela de tempo configurável
  - Cache em arquivos JSON
  - Cleanup automático
- **ValidationService**: Validações fluentes
  - Required, email, min/max length
  - Senha forte (10+ chars com complexidade)
  - Match para confirmação de senha
  - Mensagens personalizáveis

#### Segurança Implementada
- ✅ Hash Argon2id para senhas
- ✅ CSRF protection ativo
- ✅ XSS prevention (escape de outputs)
- ✅ Rate limiting (5 logins, 3 signups por 10min)
- ✅ reCAPTCHA v3 obrigatório (score >= 0.5)
- ✅ Session fixation prevention
- ✅ Rehash automático de senhas
- ✅ Sanitização de inputs

#### Views Atualizadas
- Formulário de registro com reCAPTCHA v3
- Formulário de login com reCAPTCHA v3
- Old input preservation em erros
- Feedback visual de erros
- Links de política do Google

### 📝 Detalhes Técnicos
- Validação em cascata (fluent interface)
- Email existence check antes de registro
- Password complexity: maiúsculas, minúsculas, números
- Session data structure: user_id, user{id, name, email, role}, timestamps
- Cache directory: /cache/rate_limit/

## [1.3.0] - 2025-11-03

### ✅ Adicionado - Fase 4: Dashboard do Cliente

#### DashboardController Completo
- Listagem de agendamentos do usuário
- Separação automática: próximos vs histórico
- Enriquecimento de dados (join com serviços)
- Cancelamento de agendamentos com validação
- Verificação de política de 2h
- CSRF protection

#### Views do Dashboard
- **dashboard/index.php**: Listagem completa
  - Cards responsivos para próximos agendamentos
  - Tabela de histórico
  - Empty state quando sem agendamentos
  - Status badges coloridos
  - Botão de cancelamento por agendamento
- **dashboard/new.php**: Formulário de novo agendamento
  - Grid de seleção de serviços
  - Interface preparada para Fase 5
  - Design mobile-first

#### Funcionalidades
- ✅ Listagem de agendamentos (futuros e passados)
- ✅ Visualização de detalhes completos
- ✅ Cancelamento com confirmação
- ✅ Validação de política de cancelamento (2h)
- ✅ Interface responsiva e moderna
- ✅ Empty states informativos
- ✅ Status coloridos por situação

### 🔧 Melhorias
- reCAPTCHA v3 bypass em ambiente local
- Função url() detecção automática de porta

## [1.4.0] - 2025-11-03

### ✅ Adicionado - Fase 5: Geração de Slots

#### AppointmentService
- **getAvailableSlots()**: Cálculo dinâmico de horários disponíveis
  - Validação de data (não permite passado)
  - Verifica dia da semana (barber_settings)
  - Gera slots a cada 15 minutos
  - Respeita duração do serviço
  - Filtra conflitos com agendamentos existentes
  - Antecedência mínima de 1h para hoje
- **getAvailableDates()**: Próximas 14 datas disponíveis
- **Detecção de overlapping**: Algoritmo preciso
- Timezone: America/Sao_Paulo

#### Interface Interativa
- **Step 1**: Grid de seleção de serviços
- **Step 2**: Calendário com próximas datas
- **Step 3**: Grid de slots disponíveis
- **Step 4**: Tela de confirmação (preparada para Fase 6)
- Loading states e empty states
- Smooth scroll entre etapas
- Responsivo mobile-first

#### Funcionalidades
- ✅ Cálculo automático de slots
- ✅ Verificação de disponibilidade em tempo real
- ✅ Respeita configurações do barbeiro
- ✅ Impede overlapping de horários
- ✅ Slots de 15 em 15 minutos
- ✅ Valida dias da semana
- ✅ API REST para busca de slots
- ✅ Interface fluida e intuitiva

### 📝 Detalhes Técnicos
- Algoritmo de slots com DateInterval
- Filtragem de conflitos via comparação de ranges
- JavaScript assíncrono (fetch API)
- Estado gerenciado no frontend
- Integração DashboardController + AppointmentService

## [1.5.0] - 2025-11-03

### ✅ Adicionado - Fase 6: CRUD Agendamento + Cancelamento

#### DashboardController - Método store()
- Validação completa de dados
- Verificação CSRF obrigatória
- Validação de data/hora (não permite passado)
- **Double-check de disponibilidade**: Re-valida se slot ainda está livre
- Criação de agendamento no banco
- Geração automática de control_code único
- Retorno JSON com dados do agendamento

#### Interface de Confirmação
- Função confirmAppointment() assíncrona
- Loading state durante salvamento
- Tratamento de erros com mensagens
- Redirecionamento automático após sucesso
- Modal com código de controle gerado
- Desabilitação de botões durante processo

#### Funcionalidades
- ✅ Criar agendamento completo
- ✅ Validação de slot em tempo real
- ✅ Geração de código único (formato: DByyyymmdd-XXXX)
- ✅ Persistência no Supabase
- ✅ Cancelamento com política de 2h (já implementado na Fase 4)
- ✅ Feedback visual completo
- ✅ Constraint de overlap no banco (PostgreSQL GIST)

#### Validações Server-Side
- Data/hora obrigatórios
- Service ID válido
- Não permitir agendamento no passado
- Slot deve estar disponível no momento da confirmação
- CSRF token válido
- Usuário autenticado

### 📝 Detalhes Técnicos
- Double-check de disponibilidade (race condition protection)
- Transaction-safe via Supabase REST API
- Control code: DByyyymmdd-XXXX (DB + data + 4 caracteres randômicos)
- Status inicial: 'aguardando'
- Payment confirmed: false (padrão)

### 🔧 Correções
- Slots agora respeitam duração específica de cada serviço
- Cabe lo: 45 min, Barba: 30 min, Combo: 60 min

## [Unreleased]

### 🔄 Em Desenvolvimento

#### Fase 7 - Painel Admin
- [ ] Login admin
- [ ] Dashboard administrativo
- [ ] Gerenciar agendamentos
- [ ] Atualizar status
- [ ] Confirmar pagamentos

#### Fase 8 - Relatórios Financeiros
- [ ] Faturamento por período
- [ ] Filtros avançados
- [ ] Gráficos (opcional)
- [ ] Export CSV

#### Fase 9 - Deploy em Produção
- [ ] Configuração cPanel
- [ ] HTTPS/SSL
- [ ] Otimizações de cache
- [ ] Monitoramento e logs
- [ ] Backup automático

---

## [0.1.0] - 2025-11-03

### Inicialização
- Projeto iniciado
- Estrutura de pastas criada
- Configuração inicial

---

**Formato de Versionamento:**
- MAJOR.MINOR.PATCH
- MAJOR: Mudanças incompatíveis com versões anteriores
- MINOR: Novas funcionalidades compatíveis
- PATCH: Correções de bugs

**Desenvolvido por:** [Dante Testa](https://dantetesta.com.br)
