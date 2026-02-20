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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notification` (`id_notification`, `user_id`, `title`, `message`, `type`, `source_id`, `is_read`, `created_at`, `read_at`) VALUES (1, 1, 'Stok menipis', 'Stok Kertas F4 tersisa 0 (minimum 1).', 'stock', 2, 0, '2026-02-20 17:59:39', NULL);
INSERT INTO `notification` (`id_notification`, `user_id`, `title`, `message`, `type`, `source_id`, `is_read`, `created_at`, `read_at`) VALUES (2, 1, 'Stok menipis', 'Stok Isi Hekter No. 3 tersisa 1 (minimum 1).', 'stock', 7, 0, '2026-02-20 17:59:39', NULL);
INSERT INTO `notification` (`id_notification`, `user_id`, `title`, `message`, `type`, `source_id`, `is_read`, `created_at`, `read_at`) VALUES (3, 1, 'Stok menipis', 'Stok Warna tersisa 1 (minimum 1).', 'stock', 9, 0, '2026-02-20 17:59:39', NULL);


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
# TABLE STRUCTURE FOR: signatory_role
#

DROP TABLE IF EXISTS `signatory_role`;

CREATE TABLE `signatory_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_code` (`role_code`),
  KEY `idx_signatory_user` (`user_id`),
  CONSTRAINT `signatory_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `signatory_role` (`id`, `role_code`, `role_name`, `user_id`, `created_at`, `updated_at`) VALUES (1, 'bendahara', 'Bendahara ATK Perkara', 16, '2026-02-20 10:25:11', '2026-02-20 10:43:12');
INSERT INTO `signatory_role` (`id`, `role_code`, `role_name`, `user_id`, `created_at`, `updated_at`) VALUES (2, 'pengelola', 'Pengelola Biaya Proses / Panitera', 9, '2026-02-20 10:25:11', '2026-02-20 10:43:12');
INSERT INTO `signatory_role` (`id`, `role_code`, `role_name`, `user_id`, `created_at`, `updated_at`) VALUES (3, 'ppk', 'Petugas Pembuat Komitmen', 19, '2026-02-20 10:25:11', '2026-02-20 10:43:12');


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
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Pcs',
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

INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `unit`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (1, 'BRG-0001', 1, 'Kertas A4', 'Pcs', 13, 0, 0, 1, '2026-02-20 17:59:39', '2026-02-20 17:59:39');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `unit`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (2, 'BRG-0002', 1, 'Kertas F4', 'Pcs', 0, 0, 0, 1, '2026-02-20 17:59:39', '2026-02-20 17:59:39');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `unit`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (3, 'BRG-0003', 3, 'Canon Hitam 790', 'Pcs', 3, 0, 0, 1, '2026-02-20 17:59:39', '2026-02-20 17:59:39');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `unit`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (4, 'BRG-0004', 3, 'Warna 790', 'Pcs', 2, 0, 0, 1, '2026-02-20 17:59:39', '2026-02-20 17:59:39');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `unit`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (5, 'BRG-0005', 3, 'Data Warna', 'Pcs', 12, 0, 0, 1, '2026-02-20 17:59:39', '2026-02-20 17:59:39');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `unit`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (6, 'BRG-0006', 4, 'Isi Hekter No. 23', 'Pcs', 16, 0, 0, 1, '2026-02-20 17:59:39', '2026-02-20 17:59:39');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `unit`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (7, 'BRG-0007', 4, 'Isi Hekter No. 3', 'Pcs', 1, 0, 0, 1, '2026-02-20 17:59:39', '2026-02-20 17:59:39');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `unit`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (8, 'BRG-0008', 2, 'Hitam', 'Pcs', 3, 0, 0, 1, '2026-02-20 17:59:39', '2026-02-20 17:59:39');
INSERT INTO `stock_item` (`id_item`, `item_code`, `category_id`, `item_name`, `unit`, `available_qty`, `reserved_qty`, `used_qty`, `low_stock_threshold`, `created_at`, `updated_at`) VALUES (9, 'BRG-0009', 2, 'Warna', 'Pcs', 1, 0, 0, 1, '2026-02-20 17:59:39', '2026-02-20 17:59:39');


#
# TABLE STRUCTURE FOR: stock_movement
#

DROP TABLE IF EXISTS `stock_movement`;

CREATE TABLE `stock_movement` (
  `id_movement` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `movement_type` enum('in','out','adjust','reserve','deliver','cancel') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in',
  `qty_delta` int(11) NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_movement`),
  KEY `idx_item` (`item_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_created` (`created_at`),
  KEY `idx_type` (`movement_type`),
  CONSTRAINT `stock_movement_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `stock_item` (`id_item`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `stock_movement_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stock_movement` (`id_movement`, `item_id`, `movement_type`, `qty_delta`, `reason`, `purchase_date`, `user_id`, `created_at`) VALUES (1, 1, 'in', 13, 'Import stok awal', '2025-01-01', 1, '2026-02-20 17:59:39');
INSERT INTO `stock_movement` (`id_movement`, `item_id`, `movement_type`, `qty_delta`, `reason`, `purchase_date`, `user_id`, `created_at`) VALUES (2, 2, 'in', 0, 'Import stok awal', '2025-01-01', 1, '2026-02-20 17:59:39');
INSERT INTO `stock_movement` (`id_movement`, `item_id`, `movement_type`, `qty_delta`, `reason`, `purchase_date`, `user_id`, `created_at`) VALUES (3, 3, 'in', 3, 'Import stok awal', '2025-01-01', 1, '2026-02-20 17:59:39');
INSERT INTO `stock_movement` (`id_movement`, `item_id`, `movement_type`, `qty_delta`, `reason`, `purchase_date`, `user_id`, `created_at`) VALUES (4, 4, 'in', 2, 'Import stok awal', '2025-01-01', 1, '2026-02-20 17:59:39');
INSERT INTO `stock_movement` (`id_movement`, `item_id`, `movement_type`, `qty_delta`, `reason`, `purchase_date`, `user_id`, `created_at`) VALUES (5, 5, 'in', 12, 'Import stok awal', '2025-01-01', 1, '2026-02-20 17:59:39');
INSERT INTO `stock_movement` (`id_movement`, `item_id`, `movement_type`, `qty_delta`, `reason`, `purchase_date`, `user_id`, `created_at`) VALUES (6, 6, 'in', 16, 'Import stok awal', '2025-01-01', 1, '2026-02-20 17:59:39');
INSERT INTO `stock_movement` (`id_movement`, `item_id`, `movement_type`, `qty_delta`, `reason`, `purchase_date`, `user_id`, `created_at`) VALUES (7, 7, 'in', 1, 'Import stok awal', '2025-01-01', 1, '2026-02-20 17:59:39');
INSERT INTO `stock_movement` (`id_movement`, `item_id`, `movement_type`, `qty_delta`, `reason`, `purchase_date`, `user_id`, `created_at`) VALUES (8, 8, 'in', 3, 'Import stok awal', '2025-01-01', 1, '2026-02-20 17:59:39');
INSERT INTO `stock_movement` (`id_movement`, `item_id`, `movement_type`, `qty_delta`, `reason`, `purchase_date`, `user_id`, `created_at`) VALUES (9, 9, 'in', 1, 'Import stok awal', '2025-01-01', 1, '2026-02-20 17:59:39');


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
  `jabatan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `nip` (`nip`),
  KEY `idx_username` (`username`),
  KEY `idx_nip` (`nip`),
  KEY `idx_level` (`level`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`level`) REFERENCES `user_role` (`id_role`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (1, 'admin', '$2y$10$/AIatMdVBwD1MOCaPUiAI.Wl46FZ1QgZvQ0hKoTJrfoY5riVLZIH2', 'Administrator', '000000', 1, 1, '2026-02-10 14:05:15', '2026-02-20 17:48:26', '2026-02-20 17:48:26', NULL, 0);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (4, 'rtriadi', '$2y$10$KyC2joc.N.MZ.DdwRZW8DuKSES8CC9i4dt3H.HH.gcNiROgGl0Bp2', 'Rahmat Triadi, S.Kom.', '199510212020121004', 2, 1, '2026-02-13 14:04:15', '2026-02-20 15:54:31', '2026-02-20 15:54:31', 'Pranata Komputer Ahli Pertama', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (5, '196610121992032002', '$2y$10$ooj5JmYg4ZncqaYjFF3g4OUPSA6GHrFBO86ydgNbIQmopfvXC7pRu', 'Dra. Mukasipa, M.H.', '196610121992032002', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Hakim Tingkat Pertama', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (6, '196601011993031011', '$2y$10$LaJ.pK56.EVuv3LLT4x5.eWtgCmnZMi0CxfmeVgzncxqQe04NyzES', 'Drs. Satrio Am. Karim,', '196601011993031011', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Hakim Tingkat Pertama', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (7, '196807031992021001', '$2y$10$I6fHfam4o7CzJ2NV3ao21.LaMWcANmh.F.bAOQtlfH6gSz8hJ4jey', 'Abdul Hakim, S.Ag., S.H., M.H.', '196807031992021001', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Ketua Pengadilan', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (8, '197906132006041003', '$2y$10$wYA0cpddOoC1THRRJIunJeJmt4E6baGEcDKjg8ARuhtuh0BJ731.u', 'Dr. Mukhtaruddin Bahrum, S.H.I., M.HI', '197906132006041003', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Wakil Ketua Pengadilan', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (9, '197109181998031003', '$2y$10$Bb.tS4SpfOxWE1ZbOUZxPu3vxvEk6kyQVxsbxQHfYWMViW2JCO.Oi', 'Muhiddin Litti, S.Ag., M.H.I.', '197109181998031003', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Panitera Tingkat Pertama', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (10, '197009252000032003', '$2y$10$TxfQkfNcvvXJP1I5KBacIOeldlSUTHhB6I3J3eeHH.Gtl5sUvPPU.', 'Dra. Niswaty Puluhulawa, S.H., M.H.', '197009252000032003', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Sekretaris', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (11, '197211052005021002', '$2y$10$ibtUxN0cWpRWlM.QR9bvtuESRJEJXeu4R1vicGuisUmE8kFzpYPbG', 'Muhamad Anwar Umar, S.Ag.', '197211052005021002', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Hakim Tingkat Pertama', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (12, '197106301998032001', '$2y$10$Um1anUbDO7hMBckodH4TyuPIA1QL3vc645b4ZtC6eLHpYeiP49kVu', 'Luthfiyah, S.Ag., M.H', '197106301998032001', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Panitera Muda', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (13, '198302222009042007', '$2y$10$BrD9GzFDV3kRJuITihUW6.6stYKK1nEJlMqrfAjZT7macsEJFa1gW', 'Fauziah Ahmad, S.Kom., M.H.', '198302222009042007', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Kepala Subbagian', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (14, '197107031998031006', '$2y$10$DmGVY4kL2w1Z55XI1vx4Qu2cyULPabZGU3ZERrzXnNGte36aYoIdO', 'Djarnawi H. Datau, S.Ag.', '197107031998031006', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Panitera Pengganti', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (15, '196711271992021001', '$2y$10$7pV6UUalKeFQ4YR0.vUiSeMhKpPBDbTF2ovid8Ha/hmQWZ3fskfMO', 'Suratman Nang, S.H.', '196711271992021001', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Panitera Pengganti', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (16, '197806152006042013', '$2y$10$v0KiLj9/w5NWjzSdjAo06eWR3G1O83RaPljbPG3yBLyc/ur2m8dfm', 'Isma Katili, S.Ag.', '197806152006042013', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Panitera Pengganti', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (17, '196712021987031001', '$2y$10$LBHDpgK0krl018Pc4h1sEOvXDYBHHNrr.tZSEJVRGForW9C6Rf0Q.', 'Irsan Masri, S.H.I.', '196712021987031001', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Panitera Muda', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (18, '198410062009042006', '$2y$10$QelSXsxVMfoCW4Ty.H0QDeS1H9cofV1e0Uaf41At2d/ILdZuM37hu', 'Nizma Rizky Datau, S.H.I.', '198410062009042006', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Panitera Pengganti', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (19, '197811072009122002', '$2y$10$iN8qK8hNgO5fpPZh3n6JzOUYMzgXPRC/GFS1.9XJ57A9iBgYMIZM.', 'Mardiana Abubakar, S.H.I.,M.H.', '197811072009122002', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Panitera Muda', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (20, '198209112009122003', '$2y$10$gaqWLjluWcSlqlaHJV4Li.zzsVDc35BWytRr3ThMs735H.cZElp1W', 'Maryam Palilati, S.Kom.', '198209112009122003', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Kepala Subbagian', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (21, '197902072009121004', '$2y$10$qnJL5m.a94lsA7m4kdsIC.OAnoE5NjbzIn5K/N7POikeYONqYwA8K', 'Haryono Daud, S.H.I.,M.H.', '197902072009121004', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Panitera Pengganti', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (22, '198503032009121005', '$2y$10$XWGnrBEk2Z0reY9KSX14ceK.nO0FbTQd4IY0bjCHF3aZ4nhmdCP1i', 'Mohammad Natsir Djuma Puloli, S.E.', '198503032009121005', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Operator - Penata Layanan Operasional', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (23, '198301122011011003', '$2y$10$dVes0.KAH1vx1IEnGbdp8e0ddpTp/n9h.d5UdnWrmVVjIw3DZ.l8q', 'Sutrisno Rivai, S.E.,M.H', '198301122011011003', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Kepala Subbagian', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (24, '198612282011012004', '$2y$10$.N6sZxGh6V3BG8nuo4QImO0fPa/4SARYKJ/oB0hYaytlf2Ll4r.7S', 'Yusni Kasim, S.AP', '198612282011012004', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Operator - Penata Layanan Operasional', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (25, '198310102006042001', '$2y$10$LjC24RGPZIelI6fosuFP5ejHFmSii8pkTPzDkTq/7nclXb4juElEm', 'Rinda Wanni, S.H., M.H.', '198310102006042001', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Panitera Pengganti', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (26, '196906081994011001', '$2y$10$CZDxiqeArVbYyYyd1S0xfu/HAge/AER1vWnOwtUZZ.LaY0njhKFFm', 'Usman', '196906081994011001', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Juru Sita', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (27, '198212052002121005', '$2y$10$rA.EJcq1Gvd9h2z/QjM7fOfAonUcMcc9.UGuz.JgbavHsEnN860Ha', 'Abdullah Yunus Pou', '198212052002121005', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Juru Sita', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (28, '197204252006041003', '$2y$10$troKQsJVu8yJoubxfH9yJORFixqhRRJLM.L7CqBUzfH4TMHhk9hNC', 'Ronny Bakari', '197204252006041003', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Juru Sita', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (29, '199411012024051001', '$2y$10$FsZsrUrboIuUy9hq51AIRuipWw2DT9wzZN8OX4zxjMCSHWK21u5VK', 'Dwi Novrialsyah Hinelo, S.H.', '199411012024051001', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Klerek - Analis Perkara Peradilan', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (30, '200001122024051001', '$2y$10$/pRodBAU.VOJD2Jcn.78zuT5LojsV357q9.3zM0lYk.M49qiptdr2', 'Muhammad Musa, S.H.', '200001122024051001', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Klerek - Analis Perkara Peradilan', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (31, '200102212024051001', '$2y$10$Ejp/TIZsp.pN1IvE51iBWOfVyxJsGWYmOWOUb8x87q64rz9wX9laC', 'Rivaldo Ragana Rizal, S.H.', '200102212024051001', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Klerek - Analis Perkara Peradilan', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (32, '198705152009041004', '$2y$10$tRHGIJ9gnK5FtCltijDv8e3uIs.8wgnmFmDoa3HYptvAI2AnLbGLC', 'Zeyn Saidi', '198705152009041004', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Juru Sita', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (33, '200107092025062012', '$2y$10$Re0K19F/zLq/IX7Hk09xM.wTHuq0DRxTvrfKztjU1KIXsHBJS/a3.', 'FADHILA NUR AMALIA AFIFFAH, S.H.', '200107092025062012', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Klerek - Analis Perkara Peradilan', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (34, '200002202025062017', '$2y$10$vZhbHjFFdSOTNK70DDlES.c070FEdLwnH9W3zshaBl/OX65WBzwMG', 'ARFANIAR JALASENATRI, S.T.', '200002202025062017', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Teknisi Sarana dan Prasarana', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (35, '199602162022031008', '$2y$10$dn3iQg6rQzjcaK1FarH9ZeHvW4E9XOYWo/csUu/.6arSiP/rB6jPC', 'Mochamad Ichsan Maulana, S.Ak.', '199602162022031008', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Operator - Penata Layanan Operasional', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (36, '199711062020122007', '$2y$10$mRNpGIGnXdA65HSywpRbIuBRBBBFw5VEXduXM3.DkgwzbMqmm5HQa', 'Aqza Noviardini Putri, A.Md.', '199711062020122007', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Arsiparis Terampil', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (37, '197402172007011004', '$2y$10$riIvQc2Ps.uTpdP4uSDvTO4tLEBRvEUEplnUuwhLNDVZ.P3eFXoOq', 'Abdul Kadir Tuliyabu', '197402172007011004', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Juru Sita', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (38, '198810062022031003', '$2y$10$DTj3f1fhJlXnhX1idCohwuIa/fHTlSGq/hlWG5vY/Y5w/9X/.yyLa', 'Reza Talib, A.Md', '198810062022031003', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Juru Sita Pengganti', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (39, '198306222025211040', '$2y$10$GDhA5rVabNEBGnhc3N47T.o8g18.JK92yMkjQoCk.6Y2O.UQS9vBe', 'SALIM MAKO, S.E', '198306222025211040', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Operator - Penata Layanan Operasional', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (40, '197912242025211013', '$2y$10$al30OUBv7pTGWxYEjcPThOoB81gfW2k9fTyrBvPlEa5t.l9nA0hr2', 'SUPRIANTO SUTRISNO, S.E.', '197912242025211013', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Operator - Penata Layanan Operasional', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (41, '198302162025212027', '$2y$10$BHZd4Yfg.sgHcOtk7nloAOr0iXDslIsRvwZsb47q8TH4sG7BWYXQq', 'FUTUHA ALMAHDALI, S.H.I.', '198302162025212027', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Operator - Penata Layanan Operasional', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (42, '199506302025211024', '$2y$10$Vc2BsYXj1fi0ClvCOJnYbO8x5Z5iCYPR0B7vHM5YxXwpfpdXqQ8JW', 'RIFALDI IRNIANSAH MASRI, S.H.', '199506302025211024', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Operator - Penata Layanan Operasional', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (43, '198008032025211033', '$2y$10$qCFmXjz0Luins0DijRRzDOUwPXVa5LinxRkpartsbVmy9.2.upL0G', 'FADLY HUSAIN, A.Md.', '198008032025211033', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Pengelola Layanan Operasional', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (44, '199903162025211008', '$2y$10$ITzJWVkpfxABK.r2BwIiU.udXWNmB8/C1HJELT6UFIWmI5xK4fXhK', 'ZULKIFLI LIHAWA', '199903162025211008', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Operator Layanan Operasional', 1);
INSERT INTO `user` (`id_user`, `username`, `password`, `nama`, `nip`, `level`, `is_active`, `created_at`, `updated_at`, `last_login`, `jabatan`, `must_change_password`) VALUES (45, '198904122025211045', '$2y$10$t0JocIIHlyw4iFPJeRXdW.D7C8H0lDYfqGJkLvCuCb4.ulhAcDgou', 'M. ZUBAIR', '198904122025211045', 2, 1, '2026-02-20 10:39:28', '2026-02-20 10:39:28', NULL, 'Operator Layanan Operasional', 1);


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


#
# TABLE STRUCTURE FOR: yearly_rollover
#

DROP TABLE IF EXISTS `yearly_rollover`;

CREATE TABLE `yearly_rollover` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `year` int(4) NOT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'completed',
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `closed_at` datetime DEFAULT NULL,
  `closed_by` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_year` (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

