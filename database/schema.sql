-- SIAT (Sistem Inventori ATK Terpadu) Database Schema
-- Phase 1: Authentication and User Management

-- Create database
CREATE DATABASE IF NOT EXISTS siat_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE siat_db;

-- User roles lookup table
CREATE TABLE IF NOT EXISTS user_role (
  id_role INT PRIMARY KEY AUTO_INCREMENT,
  nama_role VARCHAR(50) NOT NULL UNIQUE,
  deskripsi VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users table
CREATE TABLE IF NOT EXISTS user (
  id_user INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,  -- bcrypt hash (255 for future algorithm changes)
  nama VARCHAR(100) NOT NULL,
  nip VARCHAR(20) UNIQUE,  -- Nomor Induk Pegawai (employee ID)
  unit VARCHAR(100) NULL,
  must_change_password TINYINT(1) DEFAULT 0,
  level INT NOT NULL,  -- Foreign key to user_role.id_role
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_login TIMESTAMP NULL,
  
  INDEX idx_username (username),
  INDEX idx_nip (nip),
  INDEX idx_level (level),
  
  FOREIGN KEY (level) REFERENCES user_role(id_role)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default roles (Admin and Pegawai)
INSERT INTO user_role (id_role, nama_role, deskripsi) VALUES
  (1, 'Admin', 'Administrator - manages warehouse and approves requests'),
  (2, 'Pegawai', 'Employee - can request office supplies')
ON DUPLICATE KEY UPDATE nama_role = VALUES(nama_role);

-- Insert default admin account
-- Username: admin
-- Password: admin123 (bcrypt hash generated with PASSWORD_DEFAULT)
-- Note: Admin should change password after first login
INSERT INTO user (username, password, nama, nip, level) VALUES
  ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', '000000', 1)
ON DUPLICATE KEY UPDATE username = VALUES(username);

-- ============================================================================
-- Phase 3: Stock Management Tables
-- ============================================================================

-- Stock categories lookup table
CREATE TABLE IF NOT EXISTS stock_category (
  id_category INT PRIMARY KEY AUTO_INCREMENT,
  category_name VARCHAR(100) NOT NULL UNIQUE,
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX idx_category_name (category_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock items with three-state tracking (Available, Reserved, Used)
CREATE TABLE IF NOT EXISTS stock_item (
  id_item INT PRIMARY KEY AUTO_INCREMENT,
  category_id INT NOT NULL,
  item_name VARCHAR(255) NOT NULL,
  available_qty INT NOT NULL DEFAULT 0,
  reserved_qty INT NOT NULL DEFAULT 0,
  used_qty INT NOT NULL DEFAULT 0,
  low_stock_threshold INT NOT NULL DEFAULT 10,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX idx_category (category_id),
  INDEX idx_item_name (item_name),
  INDEX idx_low_stock (available_qty, low_stock_threshold),
  UNIQUE KEY uk_category_item (category_id, item_name),
  
  FOREIGN KEY (category_id) REFERENCES stock_category(id_category)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  
  CHECK (available_qty >= 0),
  CHECK (reserved_qty >= 0),
  CHECK (used_qty >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock movement audit log
CREATE TABLE IF NOT EXISTS stock_movement (
  id_movement INT PRIMARY KEY AUTO_INCREMENT,
  item_id INT NOT NULL,
  movement_type ENUM('in', 'out', 'adjust', 'reserve', 'deliver', 'cancel') NOT NULL,
  qty_delta INT NOT NULL,
  reason VARCHAR(255),
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  INDEX idx_item (item_id),
  INDEX idx_user (user_id),
  INDEX idx_created (created_at),
  INDEX idx_type (movement_type),
  
  FOREIGN KEY (item_id) REFERENCES stock_item(id_item)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (user_id) REFERENCES user(id_user)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Phase 4: Request Management Workflow Tables
-- ============================================================================

-- Request header table
CREATE TABLE IF NOT EXISTS request_header (
  id_request INT PRIMARY KEY AUTO_INCREMENT,
  request_no VARCHAR(50) NOT NULL,
  user_id INT NOT NULL,
  status ENUM('pending', 'approved', 'rejected', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
  notes TEXT,
  approved_by INT NULL,
  rejected_by INT NULL,
  delivered_by INT NULL,
  cancelled_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  approved_at TIMESTAMP NULL DEFAULT NULL,
  rejected_at TIMESTAMP NULL DEFAULT NULL,
  delivered_at TIMESTAMP NULL DEFAULT NULL,
  cancelled_at TIMESTAMP NULL DEFAULT NULL,

  UNIQUE KEY uk_request_no (request_no),
  INDEX idx_request_status (status),
  INDEX idx_request_user (user_id),
  INDEX idx_request_created (created_at),

  FOREIGN KEY (user_id) REFERENCES user(id_user)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  FOREIGN KEY (approved_by) REFERENCES user(id_user)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (rejected_by) REFERENCES user(id_user)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (delivered_by) REFERENCES user(id_user)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  FOREIGN KEY (cancelled_by) REFERENCES user(id_user)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Request item table
CREATE TABLE IF NOT EXISTS request_item (
  id_request_item INT PRIMARY KEY AUTO_INCREMENT,
  request_id INT NOT NULL,
  item_id INT NOT NULL,
  qty_requested INT NOT NULL,
  qty_approved INT NOT NULL DEFAULT 0,
  qty_delivered INT NOT NULL DEFAULT 0,
  note VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_request_id (request_id),
  INDEX idx_item_id (item_id),

  FOREIGN KEY (request_id) REFERENCES request_header(id_request)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (item_id) REFERENCES stock_item(id_item)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,

  CHECK (qty_requested >= 0),
  CHECK (qty_approved >= 0),
  CHECK (qty_delivered >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
