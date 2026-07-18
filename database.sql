-- ==========================================================
--  DATABASE SETUP FOR ROLE-BASED LOGIN + CRUD SYSTEM
--  Run this whole file once in phpMyAdmin (Import tab) or via:
--  mysql -u root -p < database.sql
-- ==========================================================

CREATE DATABASE IF NOT EXISTS login_crud_system;
USE login_crud_system;

-- ----------------------------------------------------------
-- USERS TABLE
-- role can only be 'admin' or 'user'
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------------------------------------
-- RECORDS TABLE (this is the data that gets CRUD'ed)
-- created_by stores which user added the record
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ----------------------------------------------------------
-- DEFAULT ADMIN ACCOUNT
-- username: admin
-- password: admin123   (change this after first login!)
-- ----------------------------------------------------------
INSERT INTO users (username, password, role) VALUES
('admin', '$2b$10$V5clJhLRxHMQHvDqkdlSSuZAQ38xtEHfIUxgfBofcYVOphmOxyExC', 'admin');
