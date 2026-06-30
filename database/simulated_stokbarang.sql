-- Simulated database for the stokbarang project
-- Database: stokbarang_sim

CREATE DATABASE IF NOT EXISTS `stokbarang_sim` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `stokbarang_sim`;

-- Users
CREATE TABLE `users` (
  `user_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(200) DEFAULT NULL,
  `role` VARCHAR(50) DEFAULT 'operator',
  `status` TINYINT DEFAULT 1,
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `email_verified_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
);

-- Categories
CREATE TABLE `categories` (
  `category_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `status` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`category_id`)
);

-- Suppliers
CREATE TABLE `suppliers` (
  `supplier_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_supplier` VARCHAR(200) NOT NULL,
  `contact_person` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `telepon` VARCHAR(50) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `kota` VARCHAR(100) DEFAULT NULL,
  `provinsi` VARCHAR(100) DEFAULT NULL,
  `kode_pos` VARCHAR(20) DEFAULT NULL,
  `bank_account` VARCHAR(100) DEFAULT NULL,
  `status` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`supplier_id`)
);

-- Products
CREATE TABLE `products` (
  `product_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_produk` VARCHAR(100) DEFAULT NULL,
  `nama_produk` VARCHAR(200) NOT NULL,
  `category_id` BIGINT UNSIGNED DEFAULT NULL,
  `supplier_id` BIGINT UNSIGNED DEFAULT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `unit` VARCHAR(50) DEFAULT 'pcs',
  `harga_beli` DECIMAL(12,2) DEFAULT 0.00,
  `harga_jual` DECIMAL(12,2) DEFAULT 0.00,
  `reorder_level` INT DEFAULT 0,
  `max_stock` INT DEFAULT 0,
  `status` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `products_category_id_index` (`category_id`),
  KEY `products_supplier_id_index` (`supplier_id`)
);

-- Purchase Orders
CREATE TABLE `purchase_orders` (
  `po_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_po` VARCHAR(100) DEFAULT NULL,
  `supplier_id` BIGINT UNSIGNED DEFAULT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `tanggal_po` DATE DEFAULT NULL,
  `tanggal_diharapkan` DATE DEFAULT NULL,
  `total_harga` DECIMAL(12,2) DEFAULT 0.00,
  `status` TINYINT DEFAULT 0,
  `catatan` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`po_id`),
  KEY `purchase_orders_supplier_id_index` (`supplier_id`),
  KEY `purchase_orders_user_id_index` (`user_id`)
);

CREATE TABLE `purchase_order_items` (
  `poi_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `po_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity_ordered` INT DEFAULT 0,
  `quantity_received` INT DEFAULT 0,
  `harga_satuan` DECIMAL(12,2) DEFAULT 0.00,
  `sub_total` DECIMAL(12,2) DEFAULT 0.00,
  PRIMARY KEY (`poi_id`),
  KEY `poi_po_id_index` (`po_id`),
  KEY `poi_product_id_index` (`product_id`)
);

-- Receiving
CREATE TABLE `receiving` (
  `receiving_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_terima` VARCHAR(100) DEFAULT NULL,
  `po_id` BIGINT UNSIGNED DEFAULT NULL,
  `supplier_id` BIGINT UNSIGNED DEFAULT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `tanggal_terima` DATETIME DEFAULT NULL,
  `total_harga` DECIMAL(12,2) DEFAULT 0.00,
  `status` TINYINT DEFAULT 0,
  `catatan` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`receiving_id`),
  KEY `receiving_po_id_index` (`po_id`)
);

CREATE TABLE `receiving_items` (
  `ri_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `receiving_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity_received` INT DEFAULT 0,
  `harga_satuan` DECIMAL(12,2) DEFAULT 0.00,
  `sub_total` DECIMAL(12,2) DEFAULT 0.00,
  PRIMARY KEY (`ri_id`),
  KEY `ri_receiving_id_index` (`receiving_id`),
  KEY `ri_product_id_index` (`product_id`)
);

-- Sales Orders
CREATE TABLE `sales_orders` (
  `so_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_so` VARCHAR(100) DEFAULT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `tanggal_so` DATE DEFAULT NULL,
  `tanggal_pengiriman_diharapkan` DATE DEFAULT NULL,
  `customer_name` VARCHAR(200) DEFAULT NULL,
  `customer_email` VARCHAR(150) DEFAULT NULL,
  `customer_telepon` VARCHAR(50) DEFAULT NULL,
  `total_harga` DECIMAL(12,2) DEFAULT 0.00,
  `status` TINYINT DEFAULT 0,
  `catatan` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`so_id`),
  KEY `sales_orders_user_id_index` (`user_id`)
);

CREATE TABLE `sales_order_items` (
  `soi_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `so_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity_ordered` INT DEFAULT 0,
  `quantity_shipped` INT DEFAULT 0,
  `harga_satuan` DECIMAL(12,2) DEFAULT 0.00,
  `sub_total` DECIMAL(12,2) DEFAULT 0.00,
  PRIMARY KEY (`soi_id`),
  KEY `soi_so_id_index` (`so_id`),
  KEY `soi_product_id_index` (`product_id`)
);

-- Stock
CREATE TABLE `stock` (
  `stock_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity_on_hand` INT DEFAULT 0,
  `quantity_reserved` INT DEFAULT 0,
  `quantity_available` INT DEFAULT 0,
  `last_count_date` DATETIME DEFAULT NULL,
  `last_movement_date` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`stock_id`),
  UNIQUE KEY `stock_product_unique` (`product_id`),
  KEY `stock_product_id_index` (`product_id`)
);

-- Stock movements
CREATE TABLE `stock_movements` (
  `movement_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `jenis_gerakan` VARCHAR(50) DEFAULT NULL,
  `quantity` INT DEFAULT 0,
  `reference_type` VARCHAR(100) DEFAULT NULL,
  `reference_id` BIGINT DEFAULT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`movement_id`),
  KEY `stock_movements_product_id_index` (`product_id`)
);

-- Audit logs
CREATE TABLE `audit_logs` (
  `log_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `table_name` VARCHAR(100) DEFAULT NULL,
  `action` VARCHAR(50) DEFAULT NULL,
  `record_id` BIGINT DEFAULT NULL,
  `old_values` JSON DEFAULT NULL,
  `new_values` JSON DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`log_id`)
);

-- Basic seed data for simulation
INSERT INTO `users` (`username`,`email`,`password`,`nama_lengkap`,`role`,`status`,`created_at`) VALUES
('admin','admin@example.com','password','Administrator','admin',1,NOW()),
('operator','operator@example.com','password','Operator','operator',1,NOW());

INSERT INTO `categories` (`nama_kategori`,`deskripsi`,`status`) VALUES
('Umum','Kategori standar',1),
('Elektronik','Perangkat elektronik',1);

INSERT INTO `suppliers` (`nama_supplier`,`contact_person`,`email`,`telepon`,`status`) VALUES
('PT Sumber','Budi','budi@sumber.co.id','081234567890',1),
('CV Mitra','Sinta','sinta@mitra.co.id','082345678901',1);

INSERT INTO `products` (`kode_produk`,`nama_produk`,`category_id`,`supplier_id`,`unit`,`harga_beli`,`harga_jual`,`reorder_level`,`max_stock`,`status`) VALUES
('PRD-001','Baut 5mm',1,1,'pcs',100.00,150.00,50,100,1),
('PRD-002','Resistor 10k',2,2,'pcs',50.00,75.00,200,1000,1);

INSERT INTO `stock` (`product_id`,`quantity_on_hand`,`quantity_reserved`,`quantity_available`,`updated_at`) VALUES
(1,200,0,200,NOW()),
(2,500,0,500,NOW());

-- End of simulated dump
