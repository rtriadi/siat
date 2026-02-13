#
# TABLE STRUCTURE FOR: notification
#

DROP TABLE IF EXISTS `notification`;

CREATE TABLE `notification` (
  `id_notification` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('request','stock') COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_notification`),
  KEY `idx_notification_user` (`user_id`),
  KEY `idx_notification_read` (`is_read`),
  KEY `idx_notification_created` (`created_at`),
  KEY `idx_notification_type` (`type`),
  CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# TABLE STRUCTURE FOR: request_header
#

DROP TABLE IF EXISTS `request_header`;

CREATE TABLE `request_header` (
  `id_request` int(11) NOT NULL AUTO_INCREMENT,
  `request_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `rejected_by` int(11) DEFAULT NULL,
  `delivered_by` int(11) DEFAULT NULL,
  `cancelled_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_request`),
  UNIQUE KEY `uk_request_no` (`request_no`),
  KEY `idx_request_status` (`status`),
  KEY `idx_request_user` (`user_id`),
  KEY `idx_request_created` (`created_at`),
  KEY `approved_by` (`approved_by`),
  KEY `rejected_by` (`rejected_by`),
  KEY `delivered_by` (`delivered_by`),
  KEY `cancelled_by` (`cancelled_by`),
  CONSTRAINT `request_header_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON UPDATE CASCADE,
  CONSTRAINT `request_header_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `request_header_ibfk_3` FOREIGN KEY (`rejected_by`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `request_header_ibfk_4` FOREIGN KEY (`delivered_by`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `request_header_ibfk_5` FOREIGN KEY (`cancelled_by`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# TABLE STRUCTURE FOR: request_item
#

DROP TABLE IF EXISTS `request_item`;

CREATE TABLE `request_item` (
  `id_request_item` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `qty_requested` int(11) NOT NULL,
  `qty_approved` int(11) NOT NULL DEFAULT 0,
  `qty_delivered` int(11) NOT NULL DEFAULT 0,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_request_item`),
  KEY `idx_request_id` (`request_id`),
  KEY `idx_item_id` (`item_id`),
  CONSTRAINT `request_item_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `request_header` (`id_request`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `request_item_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `stock_item` (`id_item`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`qty_requested` >= 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`qty_approved` >= 0),
  CONSTRAINT `CONSTRAINT_3` CHECK (`qty_delivered` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# TABLE STRUCTURE FOR: stock_category
#

DROP TABLE IF EXISTS `stock_category`;

CREATE TABLE `stock_category` (
  `id_category` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_category`),
  UNIQUE KEY `category_name` (`category_name`),
  KEY `idx_category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stock_category` (`id_category`, `category_name`, `description`, `created_at`, `updated_at`) VALUES (1, 'Kertas', '', '2026-02-13 16:41:43', '2026-02-13 16:41:43');
INSERT INTO `stock_category` (`id_category`, `category_name`, `description`, `created_at`, `updated_at`) VALUES (2, 'Catridge', '', '2026-02-13 16:42:43', '2026-02-13 16:42:43');
INSERT INTO `stock_category` (`id_category`, `category_name`, `description`, `created_at`, `updated_at`) VALUES (3, 'Tinta Printer', '', '2026-02-13 16:42:53', '2026-02-13 16:42:53');
INSERT INTO `stock_category` (`id_category`, `category_name`, `description`, `created_at`, `updated_at`) VALUES (4, 'Hekter', '', '2026-02-13 16:43:33', '2026-02-13 16:43:33');


#
# TABLE STRUCTURE FOR: stock_item
#

DROP TABLE IF EXISTS `stock_item`;

CREATE TABLE `stock_item` (
  `id_item` int(11) NOT NULL AUTO_INCREMENT,
  `item_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `available_qty` int(11) NOT NULL DEFAULT 0,
  `reserved_qty` int(11) NOT NULL DEFAULT 0,
  `used_qty` int(11) NOT NULL DEFAULT 0,
  `low_stock_threshold` int(11) NOT NULL DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_item`),
  UNIQUE KEY `uk_category_item` (`category_id`,`item_name`),
  KEY `idx_category` (`category_id`),
  KEY `idx_item_name` (`item_name`),
  KEY `idx_low_stock` (`available_qty`,`low_stock_threshold`),
  CONSTRAINT `stock_item_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `stock_category` (`id_category`) ON UPDATE CASCADE,
  CONSTRAINT `CONSTRAINT_1` CHECK (`available_qty` >= 0),
  CONSTRAINT `CONSTRAINT_2` CHECK (`reserved_qty` >= 0),
  CONSTRAINT `CONSTRAINT_3` CHECK (`used_qty` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (1, 'BRG-0001', 1, 'Kertas A4', 13, 0, 0, 1, '2026-02-13 16:45:26', '2026-02-13 16:45:26');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (2, 'BRG-0002', 1, 'Kertas F4', 0, 0, 0, 1, '2026-02-13 16:45:26', '2026-02-13 16:45:26');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (3, 'BRG-0003', 3, 'Canon Hitam 790', 3, 0, 0, 1, '2026-02-13 16:45:26', '2026-02-13 16:45:26');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (4, 'BRG-0004', 3, 'Warna 790', 2, 0, 0, 1, '2026-02-13 16:45:26', '2026-02-13 16:45:26');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (5, 'BRG-0005', 3, 'Data Warna', 12, 0, 0, 1, '2026-02-13 16:45:26', '2026-02-13 16:45:26');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (6, 'BRG-0006', 4, 'Isi Hekter No. 23', 16, 0, 0, 1, '2026-02-13 16:45:26', '2026-02-13 16:45:26');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (7, 'BRG-0007', 4, 'Isi Hekter No. 3', 1, 0, 0, 1, '2026-02-13 16:45:26', '2026-02-13 16:45:26');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (8, 'BRG-0008', 2, 'Hitam', 3, 0, 0, 1, '2026-02-13 16:45:26', '2026-02-13 16:45:26');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (9, 'BRG-0009', 2, 'Warna', 1, 0, 0, 1, '2026-02-13 16:45:26', '2026-02-13 16:45:26');


#
# TABLE STRUCTURE FOR: stock_movement
#

DROP TABLE IF EXISTS `stock_movement`;

CREATE TABLE `stock_movement` (
  `id_movement` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `movement_type` enum('in','out','adjust') COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_delta` int(11) NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_movement`),
  KEY `idx_item` (`item_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_created` (`created_at`),
  KEY `idx_type` (`movement_type`),
  CONSTRAINT `stock_movement_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `stock_item` (`id_item`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `stock_movement_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

#
# TABLE STRUCTURE FOR: user
#

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `unit` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `nip` (`nip`),
  KEY `idx_username` (`username`),
  KEY `idx_nip` (`nip`),
  KEY `idx_level` (`level`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`level`) REFERENCES `user_role` (`id_role`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `unit`, `must_change_password`) VALUES (1, 'admin', '$2y$10$/AIatMdVBwD1MOCaPUiAI.Wl46FZ1QgZvQ0hKoTJrfoY5riVLZIH2', 'Administrator', '000000', 1, 1, '2026-02-10 14:05:15', '2026-02-13 14:17:56', '2026-02-13 14:17:56', NULL, 0);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `unit`, `must_change_password`) VALUES (4, 'rtriadi', '$2y$10$KyC2joc.N.MZ.DdwRZW8DuKSES8CC9i4dt3H.HH.gcNiROgGl0Bp2', 'Rahmat Triadi, S.Kom.', '199510212020121004', 2, 1, '2026-02-13 14:04:15', '2026-02-13 14:17:40', '2026-02-13 14:17:40', 'Sub Bagian Perencanaan, TI dan Pelaporan', 1);


#
# TABLE STRUCTURE FOR: user_role
#

DROP TABLE IF EXISTS `user_role`;

CREATE TABLE `user_role` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `nama_role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `nama_role` (`nama_role`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_role` (`id_role`, `nama_role`, `deskripsi`, `created_at`, `updated_at`) VALUES (1, 'Admin', 'Administrator - manages warehouse and approves requests', '2026-02-10 14:05:15', '2026-02-10 14:05:15');
INSERT INTO `user_role` (`id_role`, `nama_role`, `deskripsi`, `created_at`, `updated_at`) VALUES (2, 'Pegawai', 'Employee - can request office supplies', '2026-02-10 14:05:15', '2026-02-10 14:05:15');


