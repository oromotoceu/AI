<?php
// conexão com SQLite e criação das tabelas
// config/db.php

$databasePath = __DIR__ . '/../db/database.sqlite';

if (!file_exists(dirname($databasePath))) {
    mkdir(dirname($databasePath), 0777, true);
}

$pdo = new PDO('sqlite:' . $databasePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$senha=password_hash("@4dm1n", PASSWORD_DEFAULT);

// Criação das tabelas
$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT CHECK(role IN ('ADMINISTRADOR', 'USUARIO', 'VISITANTE')) DEFAULT 'VISITANTE',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    type TEXT CHECK(type IN ('produto', 'serviço')) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tokens (
    token TEXT PRIMARY KEY,
    user_id INTEGER NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS config (
    id INTEGER PRIMARY KEY CHECK (id = 1),
    empresa_nome TEXT,
    empresa_email TEXT,
    permite_visitante INTEGER DEFAULT 1,
    permite_cadastro INTEGER DEFAULT 1,
    conta_envio_email TEXT
);

INSERT OR IGNORE INTO users (name, email, password, role)
VALUES ('Administrador', 'admin@localhost', '$senha', 'ADMINISTRADOR');

INSERT OR IGNORE INTO config (id, empresa_nome, empresa_email, permite_visitante, permite_cadastro,conta_envio_email)
VALUES (1, 'Minha Empresa', 'contato@empresa.com', 1, 1, 'naoresponder@empresa.com');
");


