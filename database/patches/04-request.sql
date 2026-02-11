-- SIAT Database Patch: Phase 4 Request Management Workflow
-- Idempotent schema patch for request tables and stock movement updates
-- Run with: mysql -u root -p siat_db < database/patches/04-request.sql

USE siat_db;

-- Update stock_movement enum to include request lifecycle movements
ALTER TABLE stock_movement
  MODIFY COLUMN movement_type ENUM('in', 'out', 'adjust', 'reserve', 'deliver', 'cancel') NOT NULL;

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
