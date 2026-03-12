-- Script SQL para criação do banco de dados (TicketPE - Sistema de Missas)
-- Otimizado para PostgreSQL

-- Tabela de Paróquias
CREATE TABLE paroquias (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    status TEXT CHECK(status IN ('pendente', 'aprovada', 'rejeitada')) DEFAULT 'pendente',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Tabela de Usuários
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100),
    tipo TEXT CHECK(tipo IN ('admin', 'paroquia')) DEFAULT 'paroquia',
    paroquia_id INTEGER,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (paroquia_id) REFERENCES paroquias (id) ON DELETE SET NULL
);

-- Tabela de Igrejas
CREATE TABLE igrejas (
    id SERIAL PRIMARY KEY,
    paroquia_id INTEGER NOT NULL,
    nome VARCHAR(255) NOT NULL,
    bairro VARCHAR(255) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (paroquia_id) REFERENCES paroquias (id) ON DELETE CASCADE
);

-- Tabela de Horários de Missas
CREATE TABLE horario_missas (
    id SERIAL PRIMARY KEY,
    igreja_id INTEGER NOT NULL,
    dia_semana VARCHAR(255) NOT NULL,
    horario TIME NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (igreja_id) REFERENCES igrejas (id) ON DELETE CASCADE
);

-- Tabelas Auxiliares do Laravel (Sanctum, Sessões, etc.)
CREATE TABLE personal_access_tokens (
    id SERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT,
    last_used_at TIMESTAMP,
    expires_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP
);

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INTEGER,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);
CREATE INDEX sessions_user_id_index ON sessions (user_id);
CREATE INDEX sessions_last_activity_index ON sessions (last_activity);
