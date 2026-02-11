-- SIAT Database Patch: Phase 5 Notifications
-- Idempotent schema patch for notification table
-- Run with: mysql -u root -p siat_db < database/patches/05-notifications.sql

USE siat_db;

CREATE TABLE IF NOT EXISTS notification (
  id_notification INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  type ENUM('request', 'stock') NOT NULL,
  source_id INT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  read_at TIMESTAMP NULL DEFAULT NULL,

  INDEX idx_notification_user (user_id),
  INDEX idx_notification_read (is_read),
  INDEX idx_notification_created (created_at),
  INDEX idx_notification_type (type),

  FOREIGN KEY (user_id) REFERENCES user(id_user)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
