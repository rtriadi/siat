-- Add unit and must_change_password columns to user table
-- Idempotent patch for existing databases

SET @unit_exists = (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'user'
    AND COLUMN_NAME = 'unit'
);

SET @sql_add_unit = IF(
  @unit_exists = 0,
  'ALTER TABLE `user` ADD COLUMN `unit` VARCHAR(100) NULL',
  'SELECT 1'
);

PREPARE stmt FROM @sql_add_unit;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @must_change_exists = (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'user'
    AND COLUMN_NAME = 'must_change_password'
);

SET @sql_add_must_change = IF(
  @must_change_exists = 0,
  'ALTER TABLE `user` ADD COLUMN `must_change_password` TINYINT(1) NOT NULL DEFAULT 0',
  'SELECT 1'
);

PREPARE stmt FROM @sql_add_must_change;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `user`
SET `must_change_password` = 0
WHERE `must_change_password` IS NULL;

UPDATE `user`
SET `must_change_password` = 0
WHERE `username` = 'admin';
