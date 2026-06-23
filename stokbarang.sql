-- =====================================================
-- DATABASE: Aplikasi Manajemen Stok Barang
-- Versi: 1.0
-- Status: Ternormalisasi (3NF)
-- =====================================================

-- Hapus database jika ada
DROP DATABASE IF EXISTS stok_barang_app;

-- Buat database
CREATE DATABASE stok_barang_app;
USE stok_barang_app;

-- =====================================================
-- 1. TABEL USERS (Pengguna Sistem)
-- =====================================================
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'manajer_stok', 'operator', 'viewer') DEFAULT 'viewer',
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. TABEL KATEGORI BARANG
-- =====================================================
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_kategori (nama_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. TABEL SUPPLIER/PEMASOK
-- =====================================================
CREATE TABLE suppliers (
    supplier_id INT PRIMARY KEY AUTO_INCREMENT,
    nama_supplier VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    telepon VARCHAR(20),
    alamat TEXT NOT NULL,
    kota VARCHAR(50),
    provinsi VARCHAR(50),
    kode_pos VARCHAR(10),
    bank_account VARCHAR(30),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_supplier (nama_supplier),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 4. TABEL PRODUK/BARANG
-- =====================================================
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    kode_produk VARCHAR(50) UNIQUE NOT NULL,
    nama_produk VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    supplier_id INT NOT NULL,
    deskripsi TEXT,
    unit VARCHAR(20) NOT NULL,
    harga_beli DECIMAL(12, 2) NOT NULL,
    harga_jual DECIMAL(12, 2) NOT NULL,
    reorder_level INT DEFAULT 10,
    max_stock INT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE RESTRICT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE RESTRICT,
    INDEX idx_kode (kode_produk),
    INDEX idx_nama (nama_produk),
    INDEX idx_kategori (category_id),
    INDEX idx_supplier_id (supplier_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. TABEL STOK BARANG
-- =====================================================
CREATE TABLE stock (
    stock_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL UNIQUE,
    quantity_on_hand INT DEFAULT 0,
    quantity_reserved INT DEFAULT 0,
    quantity_available INT DEFAULT 0,
    last_count_date DATETIME,
    last_movement_date DATETIME,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. TABEL PESANAN PEMBELIAN (PURCHASE ORDER)
-- =====================================================
CREATE TABLE purchase_orders (
    po_id INT PRIMARY KEY AUTO_INCREMENT,
    nomor_po VARCHAR(50) UNIQUE NOT NULL,
    supplier_id INT NOT NULL,
    user_id INT NOT NULL,
    tanggal_po DATE NOT NULL,
    tanggal_diharapkan DATE,
    total_harga DECIMAL(14, 2),
    status ENUM('draft', 'diajukan', 'dikonfirmasi', 'diterima', 'dibatalkan') DEFAULT 'draft',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_nomor (nomor_po),
    INDEX idx_supplier (supplier_id),
    INDEX idx_tanggal (tanggal_po),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 7. TABEL DETAIL PESANAN PEMBELIAN
-- =====================================================
CREATE TABLE purchase_order_items (
    poi_id INT PRIMARY KEY AUTO_INCREMENT,
    po_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    quantity_received INT DEFAULT 0,
    harga_satuan DECIMAL(12, 2) NOT NULL,
    sub_total DECIMAL(14, 2),
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    INDEX idx_po (po_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 8. TABEL PENERIMAAN BARANG (RECEIVING)
-- =====================================================
CREATE TABLE receiving (
    receiving_id INT PRIMARY KEY AUTO_INCREMENT,
    nomor_terima VARCHAR(50) UNIQUE NOT NULL,
    po_id INT,
    supplier_id INT NOT NULL,
    user_id INT NOT NULL,
    tanggal_terima DATETIME NOT NULL,
    total_harga DECIMAL(14, 2),
    status ENUM('proses', 'selesai', 'dibatalkan') DEFAULT 'proses',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id) ON DELETE SET NULL,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_nomor (nomor_terima),
    INDEX idx_supplier (supplier_id),
    INDEX idx_tanggal (tanggal_terima),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 9. TABEL DETAIL PENERIMAAN BARANG
-- =====================================================
CREATE TABLE receiving_items (
    ri_id INT PRIMARY KEY AUTO_INCREMENT,
    receiving_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_received INT NOT NULL,
    harga_satuan DECIMAL(12, 2) NOT NULL,
    sub_total DECIMAL(14, 2),
    FOREIGN KEY (receiving_id) REFERENCES receiving(receiving_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    INDEX idx_receiving (receiving_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 10. TABEL PESANAN PENJUALAN (SALES ORDER)
-- =====================================================
CREATE TABLE sales_orders (
    so_id INT PRIMARY KEY AUTO_INCREMENT,
    nomor_so VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    tanggal_so DATE NOT NULL,
    tanggal_pengiriman_diharapkan DATE,
    customer_name VARCHAR(100),
    customer_email VARCHAR(100),
    customer_telepon VARCHAR(20),
    total_harga DECIMAL(14, 2),
    status ENUM('draft', 'dikonfirmasi', 'dikirim', 'selesai', 'dibatalkan') DEFAULT 'draft',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_nomor (nomor_so),
    INDEX idx_tanggal (tanggal_so),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 11. TABEL DETAIL PESANAN PENJUALAN
-- =====================================================
CREATE TABLE sales_order_items (
    soi_id INT PRIMARY KEY AUTO_INCREMENT,
    so_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    quantity_shipped INT DEFAULT 0,
    harga_satuan DECIMAL(12, 2) NOT NULL,
    sub_total DECIMAL(14, 2),
    FOREIGN KEY (so_id) REFERENCES sales_orders(so_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    INDEX idx_so (so_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 12. TABEL RIWAYAT PERGERAKAN STOK
-- =====================================================
CREATE TABLE stock_movements (
    movement_id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    jenis_gerakan ENUM('penerimaan', 'pengeluaran', 'penyesuaian', 'retur') NOT NULL,
    quantity INT NOT NULL,
    reference_type VARCHAR(50),
    reference_id INT,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_product (product_id),
    INDEX idx_user (user_id),
    INDEX idx_tanggal (created_at),
    INDEX idx_jenis (jenis_gerakan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 13. TABEL AUDIT LOG
-- =====================================================
CREATE TABLE audit_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    table_name VARCHAR(50),
    action ENUM('insert', 'update', 'delete') NOT NULL,
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_tanggal (created_at),
    INDEX idx_table (table_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VIEWS (Untuk mempermudah query)
-- =====================================================

-- View: Stok Barang Saat Ini
CREATE VIEW vw_stok_barang_saat_ini AS
SELECT 
    p.product_id,
    p.kode_produk,
    p.nama_produk,
    c.nama_kategori,
    s.nama_supplier,
    p.unit,
    stock.quantity_on_hand,
    stock.quantity_reserved,
    stock.quantity_available,
    p.reorder_level,
    IF(stock.quantity_on_hand <= p.reorder_level, 'Perlu Reorder', 'OK') as status_stok,
    p.harga_jual
FROM products p
JOIN categories c ON p.category_id = c.category_id
JOIN suppliers s ON p.supplier_id = s.supplier_id
LEFT JOIN stock ON p.product_id = stock.product_id
WHERE p.status = 'aktif';

-- View: Produk Dengan Stok Rendah
CREATE VIEW vw_stok_rendah AS
SELECT 
    p.product_id,
    p.kode_produk,
    p.nama_produk,
    c.nama_kategori,
    s.nama_supplier,
    stock.quantity_on_hand,
    p.reorder_level,
    (p.reorder_level - stock.quantity_on_hand) as perlu_order
FROM products p
JOIN categories c ON p.category_id = c.category_id
JOIN suppliers s ON p.supplier_id = s.supplier_id
JOIN stock ON p.product_id = stock.product_id
WHERE stock.quantity_on_hand <= p.reorder_level
AND p.status = 'aktif'
ORDER BY perlu_order DESC;

-- View: Laporan Penerimaan Barang
CREATE VIEW vw_laporan_penerimaan AS
SELECT 
    r.receiving_id,
    r.nomor_terima,
    r.tanggal_terima,
    s.nama_supplier,
    p.kode_produk,
    p.nama_produk,
    ri.quantity_received,
    ri.harga_satuan,
    ri.sub_total,
    u.nama_lengkap as operator
FROM receiving r
JOIN receiving_items ri ON r.receiving_id = ri.receiving_id
JOIN products p ON ri.product_id = p.product_id
JOIN suppliers s ON r.supplier_id = s.supplier_id
JOIN users u ON r.user_id = u.user_id;

-- View: Laporan Penjualan Barang
CREATE VIEW vw_laporan_penjualan AS
SELECT 
    so.so_id,
    so.nomor_so,
    so.tanggal_so,
    so.customer_name,
    p.kode_produk,
    p.nama_produk,
    soi.quantity_ordered,
    soi.harga_satuan,
    soi.sub_total,
    u.nama_lengkap as operator
FROM sales_orders so
JOIN sales_order_items soi ON so.so_id = soi.so_id
JOIN products p ON soi.product_id = p.product_id
JOIN users u ON so.user_id = u.user_id;

-- =====================================================
-- DATA SAMPLE
-- =====================================================

-- Insert User Sample
INSERT INTO users (username, password, email, nama_lengkap, role, status) VALUES
('admin', MD5('admin123'), 'admin@stokbarang.com', 'Admin Sistem', 'admin', 'aktif'),
('manager', MD5('manager123'), 'manager@stokbarang.com', 'Manajer Stok', 'manajer_stok', 'aktif'),
('operator', MD5('operator123'), 'operator@stokbarang.com', 'Operator', 'operator', 'aktif');

-- Insert Kategori Sample
INSERT INTO categories (nama_kategori, deskripsi, status) VALUES
('Elektronik', 'Produk elektronik dan gadget', 'aktif'),
('Pakaian', 'Produk pakaian dan aksesoris', 'aktif'),
('Makanan', 'Produk makanan dan minuman', 'aktif'),
('Peralatan Rumah Tangga', 'Peralatan rumah tangga', 'aktif');

-- Insert Supplier Sample
INSERT INTO suppliers (nama_supplier, contact_person, email, telepon, alamat, kota, provinsi, kode_pos, status) VALUES
('PT Elektronik Indonesia', 'Budi Santoso', 'contact@elektronik.com', '0812-3456-7890', 'Jl. Merdeka No. 123', 'Jakarta', 'DKI Jakarta', '12345', 'aktif'),
('CV Fashion Supplier', 'Siti Nurhaliza', 'info@fashionsupplier.com', '0813-2345-6789', 'Jl. Diponegoro No. 456', 'Bandung', 'Jawa Barat', '40123', 'aktif'),
('UD Pangan Nusantara', 'Bambang Riyanto', 'supplier@pangannusantara.com', '0814-3456-7890', 'Jl. Ahmad Yani No. 789', 'Surabaya', 'Jawa Timur', '60123', 'aktif');

-- Insert Produk Sample
INSERT INTO products (kode_produk, nama_produk, category_id, supplier_id, deskripsi, unit, harga_beli, harga_jual, reorder_level, max_stock, status) VALUES
('ELEC-001', 'Smartphone Android', 1, 1, 'Smartphone 6.5 inch RAM 6GB', 'pcs', 2500000, 3200000, 5, 50, 'aktif'),
('ELEC-002', 'Headphone Wireless', 1, 1, 'Headphone Bluetooth noise cancelling', 'pcs', 500000, 750000, 10, 100, 'aktif'),
('PAKAIAN-001', 'Kaos Oblong', 2, 2, 'Kaos oblong bahan cotton premium', 'pcs', 50000, 100000, 20, 200, 'aktif'),
('PAKAIAN-002', 'Celana Jeans', 2, 2, 'Celana jeans standar pria', 'pcs', 150000, 250000, 10, 100, 'aktif'),
('MAKANAN-001', 'Minyak Goreng 2L', 3, 3, 'Minyak goreng premium 2 liter', 'botol', 30000, 50000, 30, 300, 'aktif'),
('MAKANAN-002', 'Gula Pasir 1kg', 3, 3, 'Gula pasir putih berkualitas', 'kg', 12000, 18000, 50, 500, 'aktif');

-- Insert Stok Sample
INSERT INTO stock (product_id, quantity_on_hand, quantity_reserved, quantity_available) VALUES
(1, 25, 5, 20),
(2, 45, 10, 35),
(3, 80, 20, 60),
(4, 35, 5, 30),
(5, 150, 30, 120),
(6, 200, 50, 150);

-- =====================================================
-- TRIGGER (untuk perhitungan otomatis)
-- =====================================================

-- Trigger untuk update sub_total purchase order items
DELIMITER //
CREATE TRIGGER trg_poi_subtotal BEFORE INSERT ON purchase_order_items
FOR EACH ROW
BEGIN
    SET NEW.sub_total = NEW.quantity_ordered * NEW.harga_satuan;
END//
DELIMITER ;

-- Trigger untuk update sub_total receiving items
DELIMITER //
CREATE TRIGGER trg_ri_subtotal BEFORE INSERT ON receiving_items
FOR EACH ROW
BEGIN
    SET NEW.sub_total = NEW.quantity_received * NEW.harga_satuan;
END//
DELIMITER ;

-- Trigger untuk update sub_total sales order items
DELIMITER //
CREATE TRIGGER trg_soi_subtotal BEFORE INSERT ON sales_order_items
FOR EACH ROW
BEGIN
    SET NEW.sub_total = NEW.quantity_ordered * NEW.harga_satuan;
END//
DELIMITER ;

-- Trigger untuk update quantity_available saat ada pergerakan stok
DELIMITER //
CREATE TRIGGER trg_update_stock_available AFTER INSERT ON stock_movements
FOR EACH ROW
BEGIN
    UPDATE stock 
    SET quantity_available = quantity_on_hand - quantity_reserved,
        last_movement_date = NOW()
    WHERE product_id = NEW.product_id;
END//
DELIMITER ;

-- =====================================================
-- INDEKS TAMBAHAN (untuk performa)
-- =====================================================
CREATE INDEX idx_po_items_po ON purchase_order_items(po_id);
CREATE INDEX idx_ri_receiving ON receiving_items(receiving_id);
CREATE INDEX idx_soi_so ON sales_order_items(so_id);

-- =====================================================
-- SELESAI
-- =====================================================
