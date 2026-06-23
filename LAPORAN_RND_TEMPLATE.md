# DOKUMEN IMPLEMENTASI RESEARCH & DEVELOPMENT (R&D)
*Template ini dapat digunakan untuk menyusun laporan/tugas kuliah Anda.*

## 1. PENDAHULUAN (RESEARCH PHASE)

### A. Analisis Masalah (Needs Analysis)
*(Tuliskan masalah apa yang mendasari pembuatan aplikasi ini. Contoh: Pencatatan stok barang yang masih manual sering menyebabkan selisih data, hilangnya riwayat barang masuk/keluar, dan sulitnya memonitor stok yang hampir habis.)*

### B. Tujuan Pengembangan (Objectives)
*(Tuliskan tujuan yang ingin dicapai. Contoh: Mengembangkan sistem informasi manajemen stok barang berbasisi web (CRUD) untuk mempercepat proses pencatatan, meminimalisir *human error*, dan memberikan laporan secara real-time.)*

### C. Kerangka Berpikir (Framework)
*(Bagaimana alur penyelesaian masalahnya? Contoh: Mulai dari observasi masalah -> Studi literatur mengenai sistem stok dan Laravel -> Perancangan sistem (Database, UI) -> Pembuatan Aplikasi -> Pengujian Aplikasi.)*

---

## 2. DESAIN (DESIGN PHASE)

### A. Perancangan Database
Sistem dirancang dengan menggunakan pendekatan Relational Database Management System (RDBMS) yang ternormalisasi (3NF). 
- Menggunakan tabel utama seperti: `products`, `categories`, `suppliers`, `stocks`.
- Tabel transaksi seperti: `purchase_orders`, `sales_orders`, dan `receivings`.

### B. Perancangan Sistem/Alur Kerja
*(Jelaskan alurnya, misalnya Alur Pembelian: Draft -> PO -> Konfirmasi -> Barang Diterima -> Stok Bertambah otomatis.)*

### C. Perancangan Antarmuka (UI/UX)
Menggunakan pendekatan Modern Dashboard dengan Bootstrap 5 agar responsif dan mudah digunakan oleh berbagai role (Admin, Manager, Operator).

---

## 3. PENGEMBANGAN (DEVELOPMENT PHASE)
*(Ini adalah tahap di mana *project Laravel* yang Anda buat ini diwujudkan)*

Aplikasi ini adalah hasil nyata (produk) dari tahap *Development*. Pengembangan dilakukan dengan teknologi berikut:
- **Framework:** Laravel (PHP)
- **Database:** MySQL
- **Frontend:** Blade Templating Engine + Bootstrap 5
- **Arsitektur:** Model-View-Controller (MVC)

**Hasil Development:** (Sebutkan fitur-fitur yang sudah berhasil dibuat dari project Anda, seperti CRUD produk, multi-role authentication, auto-update stok).

---

## 4. IMPLEMENTASI (IMPLEMENTATION PHASE)

*(Tahap di mana aplikasi mulai dijalankan di lingkungan sebenarnya atau disimulasikan)*
1. Instalasi sistem pada local server (XAMPP / PHP Artisan Serve).
2. Setup database `stokbarang.sql`.
3. Simulasi input data master (Kategori, Supplier, Produk).
4. Simulasi proses transaksi (PO dan SO).

---

## 5. EVALUASI & PENGUJIAN (EVALUATION PHASE)

*(Tahap untuk menguji apakah produk yang di-develop sudah menyelesaikan masalah di tahap Research)*
- **Black-box Testing:** Menguji fungsionalitas CRUD, memastikan stok berkurang/bertambah saat transaksi terjadi.
- **Role Testing:** Memastikan Operator tidak bisa mengakses halaman Admin.
- **Kesimpulan:** Sistem sudah berjalan sesuai dengan rancangan awal dan siap digunakan untuk menyelesaikan masalah manajemen stok.
