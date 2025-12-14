-- ================================================================
-- Don Barbero - Database Schema (MySQL Adaptado)
-- ================================================================

CREATE DATABASE IF NOT EXISTS barbearia01;
USE barbearia01;

-- ================================================================
-- TABELA: users
-- ================================================================
CREATE TABLE IF NOT EXISTS users (
    id CHAR(36) PRIMARY KEY NOT NULL DEFAULT (UUID()),
    role VARCHAR(20) NOT NULL DEFAULT 'client',
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    whatsapp VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CHECK (role IN ('client', 'admin')),
    CHECK (CHAR_LENGTH(name) >= 3),
    CHECK (email REGEXP '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$')
);

-- ================================================================
-- TABELA: services
-- ================================================================
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    duration_minutes INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CHECK (duration_minutes > 0 AND duration_minutes <= 300),
    CHECK (price >= 0)
);

-- ================================================================
-- TABELA: barber_settings
-- ================================================================
CREATE TABLE IF NOT EXISTS barber_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    start_hour TIME NOT NULL DEFAULT '08:00',
    end_hour TIME NOT NULL DEFAULT '19:00',
    working_days VARCHAR(50) NOT NULL DEFAULT '1,2,3,4,5,6',
    slot_interval_minutes INT NOT NULL DEFAULT 15,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CHECK (end_hour > start_hour),
    CHECK (slot_interval_minutes >= 5 AND slot_interval_minutes <= 60)
);

-- ================================================================
-- TABELA: appointments
-- ================================================================
CREATE TABLE IF NOT EXISTS appointments (
    id CHAR(36) PRIMARY KEY NOT NULL DEFAULT (UUID()),
    user_id CHAR(36) NOT NULL,
    service_id INT NOT NULL,
    start_at DATETIME NOT NULL,
    end_at DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'aguardando',
    payment_confirmed BOOLEAN NOT NULL DEFAULT FALSE,
    control_code VARCHAR(20) UNIQUE,
    notes TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id),
    CHECK (end_at > start_at),
    CHECK (status IN ('aguardando', 'confirmado', 'concluido', 'cancelado'))
);

-- ================================================================
-- TABELA: payments_ledger
-- ================================================================
CREATE TABLE IF NOT EXISTS payments_ledger (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id CHAR(36) NOT NULL UNIQUE,
    amount DECIMAL(10,2) NOT NULL,
    paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    payment_method VARCHAR(30),
    notes TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    CHECK (amount >= 0)
);

-- ================================================================
-- TABELA: audit_log
-- ================================================================
CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id CHAR(36),
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(30) NOT NULL,
    entity_id VARCHAR(50),
    old_data JSON,
    new_data JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ================================================================
-- SEED DATA
-- ================================================================
-- Serviços padrão
INSERT IGNORE INTO services (name, duration_minutes, price) VALUES
    ('Cabelo', 45, 40.00),
    ('Barba', 30, 30.00),
    ('Combo', 60, 60.00);

-- Configuração padrão do barbeiro
INSERT IGNORE INTO barber_settings (start_hour, end_hour, working_days, slot_interval_minutes)
VALUES ('08:00','19:00','1,2,3,4,5,6',15);

-- Usuário admin (hash placeholder)
INSERT IGNORE INTO users (role, name, email, password_hash)
VALUES ('admin','Administrador','admin@donbarbero.com.br','$argon2id$v=19$m=65536,t=4,p=1$placeholder$placeholder');
