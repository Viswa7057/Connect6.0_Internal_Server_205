-- Database setup script for Haloocom Voiceblast
CREATE DATABASE IF NOT EXISTS voiceblast_db;
USE voiceblast_db;

-- 1. Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Lists table
CREATE TABLE IF NOT EXISTS lists (
    list_id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    audio_file VARCHAR(255) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Leads table
CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    list_id VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    status ENUM('pending', 'ringing', 'answered', 'completed', 'failed', 'not_connected') DEFAULT 'pending',
    dtmf_response VARCHAR(5) NULL,
    recording_file VARCHAR(255) NULL,
    dial_time DATETIME NULL,
    duration INT DEFAULT 0,
    error_reason VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (list_id) REFERENCES lists(list_id) ON DELETE CASCADE,
    INDEX idx_list_status (list_id, status),
    INDEX idx_status (status),
    INDEX idx_phone (phone_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. API Logs table (for tracking real-time API lead push counts)
CREATE TABLE IF NOT EXISTS api_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    list_id VARCHAR(50) NOT NULL,
    success_count INT DEFAULT 0,
    fail_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (list_id) REFERENCES lists(list_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. IVR Ratings table (for tracking automated IVR rating surveys)
CREATE TABLE IF NOT EXISTS ivr_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    calledid VARCHAR(50) NULL,
    callerid VARCHAR(50) NULL,
    ivr_name VARCHAR(100) NULL,
    uniq VARCHAR(50) NULL,
    channel VARCHAR(100) NULL,
    rating VARCHAR(10) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

