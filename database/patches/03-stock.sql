-- SIAT Database Patch: Phase 3 Stock Management
-- Idempotent schema patch for stock tables
-- Run with: mysql -u root -p siat_db < database/patches/03-stock.sql

USE siat_db;

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
  movement_type ENUM('in', 'out', 'adjust') NOT NULL,
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
