-- ================================================================
-- Don Barbero - Database Schema (Supabase PostgreSQL)
-- 
-- @author Dante Testa <https://dantetesta.com.br>
-- @created 03/11/2025 15:34
-- @version 1.0.0
-- 
-- INSTRUÇÕES:
-- 1. Acesse o Supabase Dashboard
-- 2. Vá para SQL Editor
-- 3. Cole e execute este script completo
-- 4. Verifique se todas as tabelas foram criadas
-- ================================================================

-- Ativar extensões necessárias
CREATE EXTENSION IF NOT EXISTS pgcrypto;
CREATE EXTENSION IF NOT EXISTS btree_gist;

-- ================================================================
-- TABELA: users
-- Armazena usuários do sistema (clientes e admins)
-- ================================================================
CREATE TABLE IF NOT EXISTS users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    role VARCHAR(20) NOT NULL DEFAULT 'client',
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    whatsapp VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    
    -- Constraints
    CONSTRAINT check_role CHECK (role IN ('client', 'admin')),
    CONSTRAINT check_name_length CHECK (char_length(name) >= 3),
    CONSTRAINT check_email_format CHECK (email ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$')
);

-- Índices para otimização
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_created_at ON users(created_at DESC);

-- Comentários na tabela
COMMENT ON TABLE users IS 'Usuários do sistema (clientes e administradores)';
COMMENT ON COLUMN users.role IS 'Tipo de usuário: client ou admin';
COMMENT ON COLUMN users.password_hash IS 'Hash Argon2id da senha';

-- ================================================================
-- TABELA: services
-- Serviços oferecidos pela barbearia
-- ================================================================
CREATE TABLE IF NOT EXISTS services (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    duration_minutes INT NOT NULL,
    price NUMERIC(10,2) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    
    -- Constraints
    CONSTRAINT check_duration CHECK (duration_minutes > 0 AND duration_minutes <= 300),
    CONSTRAINT check_price CHECK (price >= 0)
);

-- Índice
CREATE INDEX IF NOT EXISTS idx_services_active ON services(active) WHERE active = TRUE;

-- Comentários
COMMENT ON TABLE services IS 'Serviços disponíveis (Cabelo, Barba, Combo)';
COMMENT ON COLUMN services.duration_minutes IS 'Duração do serviço em minutos';
COMMENT ON COLUMN services.price IS 'Preço do serviço em reais';

-- ================================================================
-- TABELA: barber_settings
-- Configurações de horário de funcionamento
-- ================================================================
CREATE TABLE IF NOT EXISTS barber_settings (
    id SERIAL PRIMARY KEY,
    start_hour TIME NOT NULL DEFAULT '08:00',
    end_hour TIME NOT NULL DEFAULT '19:00',
    working_days VARCHAR(50) NOT NULL DEFAULT '1,2,3,4,5,6',
    slot_interval_minutes INT NOT NULL DEFAULT 15,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    
    -- Constraints
    CONSTRAINT check_hours CHECK (end_hour > start_hour),
    CONSTRAINT check_working_days CHECK (working_days ~ '^[1-7](,[1-7])*$'),
    CONSTRAINT check_interval CHECK (slot_interval_minutes >= 5 AND slot_interval_minutes <= 60)
);

-- Comentários
COMMENT ON TABLE barber_settings IS 'Configurações de horário de funcionamento do barbeiro';
COMMENT ON COLUMN barber_settings.working_days IS 'Dias da semana (1=Segunda, 7=Domingo) separados por vírgula';
COMMENT ON COLUMN barber_settings.slot_interval_minutes IS 'Intervalo mínimo entre slots em minutos';

-- ================================================================
-- TABELA: appointments
-- Agendamentos dos clientes
-- ================================================================
CREATE TABLE IF NOT EXISTS appointments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    service_id INT NOT NULL REFERENCES services(id),
    start_at TIMESTAMPTZ NOT NULL,
    end_at TIMESTAMPTZ NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'aguardando',
    payment_confirmed BOOLEAN NOT NULL DEFAULT FALSE,
    control_code VARCHAR(20) UNIQUE,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    
    -- Constraints
    CONSTRAINT check_time_range CHECK (end_at > start_at),
    CONSTRAINT check_status CHECK (status IN ('aguardando', 'confirmado', 'concluido', 'cancelado')),
    CONSTRAINT check_control_code CHECK (control_code IS NULL OR char_length(control_code) >= 8)
);

-- Coluna gerada para range temporal (usado na exclusão de overlaps)
ALTER TABLE appointments 
ADD COLUMN IF NOT EXISTS ts_range tstzrange 
GENERATED ALWAYS AS (tstzrange(start_at, end_at, '[)')) STORED;

-- Índices
CREATE INDEX IF NOT EXISTS idx_appointments_user ON appointments(user_id);
CREATE INDEX IF NOT EXISTS idx_appointments_service ON appointments(service_id);
CREATE INDEX IF NOT EXISTS idx_appointments_status ON appointments(status);
CREATE INDEX IF NOT EXISTS idx_appointments_start_at ON appointments(start_at);
CREATE INDEX IF NOT EXISTS idx_appointments_control_code ON appointments(control_code) WHERE control_code IS NOT NULL;

-- Índice GIST para ranges temporais
CREATE INDEX IF NOT EXISTS idx_appointments_range ON appointments USING GIST (ts_range);

-- CONSTRAINT DE EXCLUSÃO: Prevenir overlapping de agendamentos
-- Apenas agendamentos não-cancelados não podem se sobrepor
ALTER TABLE appointments 
DROP CONSTRAINT IF EXISTS no_overlapping_appointments;

ALTER TABLE appointments
ADD CONSTRAINT no_overlapping_appointments 
EXCLUDE USING gist (ts_range WITH &&) 
WHERE (status != 'cancelado');

-- Comentários
COMMENT ON TABLE appointments IS 'Agendamentos realizados pelos clientes';
COMMENT ON COLUMN appointments.status IS 'Status: aguardando, confirmado, concluido, cancelado';
COMMENT ON COLUMN appointments.control_code IS 'Código único para controle do agendamento';
COMMENT ON COLUMN appointments.ts_range IS 'Range temporal gerado automaticamente para exclusão de overlaps';
COMMENT ON CONSTRAINT no_overlapping_appointments ON appointments IS 'Previne sobreposição de horários (exceto cancelados)';

-- ================================================================
-- TABELA: payments_ledger
-- Registro de pagamentos confirmados
-- ================================================================
CREATE TABLE IF NOT EXISTS payments_ledger (
    id SERIAL PRIMARY KEY,
    appointment_id UUID UNIQUE NOT NULL REFERENCES appointments(id) ON DELETE CASCADE,
    amount NUMERIC(10,2) NOT NULL,
    paid_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    payment_method VARCHAR(30),
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    
    -- Constraints
    CONSTRAINT check_amount CHECK (amount >= 0)
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_payments_appointment ON payments_ledger(appointment_id);
CREATE INDEX IF NOT EXISTS idx_payments_paid_at ON payments_ledger(paid_at DESC);
CREATE INDEX IF NOT EXISTS idx_payments_method ON payments_ledger(payment_method);

-- Comentários
COMMENT ON TABLE payments_ledger IS 'Registro financeiro de pagamentos confirmados';
COMMENT ON COLUMN payments_ledger.payment_method IS 'Método de pagamento: dinheiro, pix, cartao, etc.';

-- ================================================================
-- TABELA: audit_log (Opcional - para auditoria)
-- Registro de eventos importantes do sistema
-- ================================================================
CREATE TABLE IF NOT EXISTS audit_log (
    id SERIAL PRIMARY KEY,
    user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(30) NOT NULL,
    entity_id VARCHAR(50),
    old_data JSONB,
    new_data JSONB,
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Índices
CREATE INDEX IF NOT EXISTS idx_audit_user ON audit_log(user_id);
CREATE INDEX IF NOT EXISTS idx_audit_action ON audit_log(action);
CREATE INDEX IF NOT EXISTS idx_audit_entity ON audit_log(entity_type, entity_id);
CREATE INDEX IF NOT EXISTS idx_audit_created_at ON audit_log(created_at DESC);

-- Comentários
COMMENT ON TABLE audit_log IS 'Log de auditoria de ações importantes no sistema';
COMMENT ON COLUMN audit_log.action IS 'Ação realizada: login, create, update, delete, etc.';
COMMENT ON COLUMN audit_log.entity_type IS 'Tipo de entidade afetada: user, appointment, etc.';

-- ================================================================
-- SEED DATA - Dados Iniciais
-- ================================================================

-- Inserir serviços padrão
INSERT INTO services (name, duration_minutes, price) VALUES
    ('Cabelo', 45, 40.00),
    ('Barba', 30, 30.00),
    ('Combo', 60, 60.00)
ON CONFLICT (name) DO NOTHING;

-- Inserir configuração padrão do barbeiro
INSERT INTO barber_settings (start_hour, end_hour, working_days, slot_interval_minutes)
VALUES ('08:00', '19:00', '1,2,3,4,5,6', 15)
ON CONFLICT DO NOTHING;

-- Inserir usuário admin
-- Senha: admin@123 (hash será gerado pela aplicação na Fase 3)
-- Por enquanto, inserir com hash temporário
INSERT INTO users (role, name, email, whatsapp, password_hash) VALUES
    ('admin', 'Administrador', 'admin@donbarbero.com.br', NULL, '$argon2id$v=19$m=65536,t=4,p=1$placeholder$placeholder')
ON CONFLICT (email) DO NOTHING;

-- ================================================================
-- FUNCTIONS E TRIGGERS (Opcional)
-- ================================================================

-- Função para atualizar updated_at automaticamente
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Triggers para updated_at
DROP TRIGGER IF EXISTS update_users_updated_at ON users;
CREATE TRIGGER update_users_updated_at 
    BEFORE UPDATE ON users 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_services_updated_at ON services;
CREATE TRIGGER update_services_updated_at 
    BEFORE UPDATE ON services 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_appointments_updated_at ON appointments;
CREATE TRIGGER update_appointments_updated_at 
    BEFORE UPDATE ON appointments 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

DROP TRIGGER IF EXISTS update_barber_settings_updated_at ON barber_settings;
CREATE TRIGGER update_barber_settings_updated_at 
    BEFORE UPDATE ON barber_settings 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- ================================================================
-- VIEWS (Opcional - para facilitar consultas)
-- ================================================================

-- View de agendamentos com informações completas
CREATE OR REPLACE VIEW v_appointments_full AS
SELECT 
    a.id,
    a.control_code,
    a.start_at,
    a.end_at,
    a.status,
    a.payment_confirmed,
    a.notes,
    a.created_at,
    u.id AS user_id,
    u.name AS user_name,
    u.email AS user_email,
    u.whatsapp AS user_whatsapp,
    s.id AS service_id,
    s.name AS service_name,
    s.duration_minutes AS service_duration,
    s.price AS service_price
FROM appointments a
INNER JOIN users u ON a.user_id = u.id
INNER JOIN services s ON a.service_id = s.id
ORDER BY a.start_at DESC;

COMMENT ON VIEW v_appointments_full IS 'View completa de agendamentos com informações de usuário e serviço';

-- ================================================================
-- PERMISSÕES (RLS - Row Level Security)
-- Importante para Supabase
-- ================================================================

-- Habilitar RLS nas tabelas principais
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE appointments ENABLE ROW LEVEL SECURITY;
ALTER TABLE payments_ledger ENABLE ROW LEVEL SECURITY;

-- Políticas básicas (podem ser refinadas conforme necessidade)
-- Usuários podem ler seus próprios dados
CREATE POLICY users_select_own ON users 
    FOR SELECT USING (auth.uid()::text = id::text OR 
                      (SELECT role FROM users WHERE id::text = auth.uid()::text) = 'admin');

-- Usuários podem ver seus próprios agendamentos
CREATE POLICY appointments_select_own ON appointments 
    FOR SELECT USING (user_id::text = auth.uid()::text OR 
                      (SELECT role FROM users WHERE id::text = auth.uid()::text) = 'admin');

-- Admin pode ver todos os pagamentos
CREATE POLICY payments_select_admin ON payments_ledger 
    FOR SELECT USING ((SELECT role FROM users WHERE id::text = auth.uid()::text) = 'admin');

-- ================================================================
-- FINALIZAÇÃO
-- ================================================================

-- Verificar estrutura
DO $$
BEGIN
    RAISE NOTICE '✅ Schema criado com sucesso!';
    RAISE NOTICE 'Tabelas criadas: users, services, barber_settings, appointments, payments_ledger, audit_log';
    RAISE NOTICE 'Extensões: pgcrypto, btree_gist';
    RAISE NOTICE 'Constraint de exclusão ativa para prevenir overlaps';
    RAISE NOTICE 'Dados iniciais inseridos (3 serviços, 1 configuração, 1 admin)';
END $$;

-- Listar tabelas criadas
SELECT 
    schemaname,
    tablename,
    tableowner
FROM pg_tables
WHERE schemaname = 'public'
    AND tablename IN ('users', 'services', 'barber_settings', 'appointments', 'payments_ledger', 'audit_log')
ORDER BY tablename;
