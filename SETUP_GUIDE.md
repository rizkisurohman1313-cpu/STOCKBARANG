# 📦 Aplikasi Manajemen Stok Barang - Dokumentasi Setup

## Deskripsi Singkat
Aplikasi Web Manajemen Stok Barang berbasis Laravel dengan fitur CRUD lengkap, sistem role-based access control (RBAC), dan tracking stok real-time.

## Fitur Utama
- ✅ **Dashboard Analytics** - Visualisasi data stok, pembelian, penerimaan, penjualan
- ✅ **Manajemen Pengguna** - CRUD users dengan 4 role (admin, manajer_stok, operator, viewer)
- ✅ **Manajemen Master Data**
  - Kategori Barang (CRUD)
  - Supplier/Pemasok (CRUD)
  - Produk/Barang (CRUD dengan tracking harga beli-jual)
- ✅ **Manajemen Stok**
  - Tracking stok real-time (quantity_on_hand, reserved, available)
  - Penyesuaian stok manual
  - Riwayat pergerakan stok lengkap
  - Alert stok rendah
- ✅ **Manajemen Pembelian**
  - Purchase Order (PO) - CRUD dengan status workflow
  - Penerimaan Barang - dengan validasi stok
  - Auto-update stok saat penerimaan
- ✅ **Manajemen Penjualan**
  - Sales Order (SO) - CRUD dengan status workflow
  - Tracking quantity_shipped
  - Stok reserved automatic
- ✅ **Audit Trail** - Pencatatan setiap transaksi
- ✅ **Role-Based Access Control** - 4 role dengan permission berbeda

## Struktur Database
Database sudah ternormalisasi (3NF) dengan 13 tabel utama:
- users (pengguna sistem)
- categories (kategori barang)
- suppliers (pemasok)
- products (produk/barang)
- stock (stok real-time)
- purchase_orders & purchase_order_items
- receiving & receiving_items
- sales_orders & sales_order_items
- stock_movements (audit trail stok)
- audit_logs (log perubahan data)

## Setup & Instalasi

### 1. Prerequisites
- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Laravel >= 10

### 2. Setup Database
```bash
# Jalankan SQL file untuk create database dan tables
mysql -u root -p < stokbarang.sql
```

### 3. Setup Laravel Project
```bash
# Install dependencies
composer install

# Copy .env dan setup
cp .env.example .env

# Update .env dengan konfigurasi database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=stok_barang_app
DB_USERNAME=root
DB_PASSWORD=

# Generate APP_KEY
php artisan key:generate

# Run migrations (jika ada)
php artisan migrate

# Seed sample data (optional)
php artisan db:seed
```

### 4. Setup Authentication
```bash
# Install Laravel Breeze untuk authentication
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install
npm run dev
```

### 5. Publish & Configure
```bash
# Publish vendor assets
php artisan vendor:publish

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### 6. Run Application
```bash
# Start development server
php artisan serve

# Buka browser: http://localhost:8000
```

## Login Default Credentials
Dari database seed:
- **Admin**: username: `admin` | password: `admin123`
- **Manager**: username: `manager` | password: `manager123`
- **Operator**: username: `operator` | password: `operator123`

## Struktur Folder

```
project/
├── app/
│   ├── Models/           # Eloquent Models
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Supplier.php
│   │   ├── Product.php
│   │   ├── Stock.php
│   │   ├── PurchaseOrder.php
│   │   ├── Receiving.php
│   │   ├── SalesOrder.php
│   │   └── ...
│   ├── Http/
│   │   ├── Controllers/  # Controllers
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── ProductController.php
│   │   │   └── ...
│   │   └── Middleware/   # Middleware
│   │       └── CheckRole.php
│
├── resources/
│   └── views/            # Blade Templates
│       ├── layouts/app.blade.php
│       ├── dashboard/
│       ├── products/
│       ├── categories/
│       ├── suppliers/
│       ├── stocks/
│       ├── purchase-orders/
│       ├── receivings/
│       └── sales-orders/
│
├── routes/
│   └── web.php           # Web Routes
│
└── stokbarang.sql        # Database Schema
```

## Route Structure

### Public Routes
- `/` - Redirect ke dashboard
- `/login` - Login page
- `/register` - Register page

### Protected Routes (Auth Required)
- `/dashboard` - Dashboard

### Admin/Manajer Routes
- `/users` - User management (CRUD)

### Operator Routes
- `/categories` - Kategori (CRUD)
- `/suppliers` - Supplier (CRUD)
- `/products` - Produk (CRUD)
- `/stocks` - Stok (view & adjustment)
- `/purchase-orders` - PO (CRUD & status)
- `/receivings` - Penerimaan (CRUD)
- `/sales-orders` - SO (CRUD & status)
- `/reports/*` - Laporan (view only)

## Role Permission Matrix

| Feature | Admin | Manajer Stok | Operator | Viewer |
|---------|-------|--------------|----------|--------|
| Manage Users | ✅ | ✅ | ❌ | ❌ |
| Manage Categories | ✅ | ✅ | ✅ | ❌ |
| Manage Suppliers | ✅ | ✅ | ✅ | ❌ |
| Manage Products | ✅ | ✅ | ✅ | ❌ |
| Manage Stock | ✅ | ✅ | ✅ | ❌ |
| Create/Edit PO | ✅ | ✅ | ✅ | ❌ |
| Receiving Barang | ✅ | ✅ | ✅ | ❌ |
| Create/Edit SO | ✅ | ✅ | ✅ | ❌ |
| View Reports | ✅ | ✅ | ✅ | ✅ |
| View Dashboard | ✅ | ✅ | ✅ | ✅ |

## Middleware Implementation

Tambahkan di `app/Http/Kernel.php`:
```php
protected $routeMiddleware = [
    // ...
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

## Key Features Explanation

### 1. Dashboard
- Menampilkan statistik stok, nilai aset, transaksi
- Quick links ke fitur utama
- Alert stok rendah

### 2. Stock Management
- Real-time stock tracking
- Automatic stock update saat receiving & shipping
- Stock reserved untuk SO yang confirmed
- Adjustment stok dengan audit trail

### 3. Purchase Order Workflow
- Status: draft → diajukan → dikonfirmasi → diterima → dibatalkan
- Generate nomor PO otomatis
- Detail items dengan validasi

### 4. Receiving Management
- Link ke PO atau standalone
- Auto update stok
- Record harga untuk tracking cost

### 5. Sales Order Workflow
- Status: draft → dikonfirmasi → dikirim → selesai → dibatalkan
- Stock validation saat confirm
- Stock reserved automatic
- Stock released saat dikirim

### 6. Stock Movement Tracking
- Setiap perubahan stok tercatat
- Reference ke transaction (PO, SO, Receiving)
- Timestamp dan user info

## API Endpoints (Optional)
Jika ingin menambah API:
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('stocks', StockController::class);
    Route::apiResource('purchase-orders', PurchaseOrderController::class);
    // ...
});
```

## Troubleshooting

### 1. Password tidak cocok
Edit file `config/auth.php` atau hash password di database
```bash
php artisan tinker
> User::find(1)->update(['password' => Hash::make('admin123')])
```

### 2. Middleware tidak work
Pastikan middleware terdaftar di `Kernel.php`

### 3. Database koneksi error
Check `.env` file configuration

### 4. Permission denied
Check user role dan route middleware definition

## Best Practices Implemented

✅ **Security**
- Role-based access control
- Password hashing
- CSRF protection
- SQL injection prevention (Eloquent ORM)

✅ **Data Integrity**
- Database normalization (3NF)
- Foreign key constraints
- Transaction support
- Audit logging

✅ **Performance**
- Eager loading (with())
- Index pada frequently searched columns
- Pagination
- Query optimization

✅ **Code Organization**
- MVC pattern
- Eloquent ORM
- Blade templating
- Middleware

## Next Steps / Improvements

1. **API Development** - Tambah REST API untuk mobile app
2. **Export Reports** - Excel/PDF export untuk laporan
3. **Notification** - Email alert untuk stock rendah
4. **Dashboard Charts** - Visualisasi lebih interaktif
5. **Multi-warehouse** - Support multiple gudang
6. **Approval Workflow** - Approval layer untuk PO/SO
7. **Barcode Scanning** - QR code untuk receiving
8. **Prophecy Integration** - Prediksi stok

---

**Versi**: 1.0  
**Last Updated**: Juni 2024
**Support**: Dokumentasi di-update sesuai requirement
