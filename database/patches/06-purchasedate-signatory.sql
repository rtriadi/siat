-- Patch 06: Add purchase_date and signatory_role table

USE siat_db;

-- 1. Add purchase_date to stock_movement
ALTER TABLE stock_movement ADD COLUMN purchase_date DATE NULL AFTER reason;

-- 2. Create signatory_role table
CREATE TABLE IF NOT EXISTS signatory_role (
  id INT PRIMARY KEY AUTO_INCREMENT,
  role_code VARCHAR(50) NOT NULL UNIQUE,
  role_name VARCHAR(100) NOT NULL,
  user_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_signatory_user (user_id),
  FOREIGN KEY (user_id) REFERENCES user(id_user)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Insert default signatory roles
INSERT IGNORE INTO signatory_role (role_code, role_name) VALUES
  ('bendahara', 'Bendahara ATK Perkara'),
  ('pengelola', 'Pengelola Biaya Proses / Panitera'),
  ('ppk', 'Petugas Pembuat Komitmen');
