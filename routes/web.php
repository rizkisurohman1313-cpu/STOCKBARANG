<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\AuthController;

// Redirect root ke login atau dashboard
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// Authentication Routes (Public)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authentication Routes (jika menggunakan Breeze/Fortify)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Management (hanya admin dan manajer stok)
    Route::middleware('role:admin,manajer_stok')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Category Management (admin dan manajer stok)
    Route::middleware('role:admin,manajer_stok,operator')->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    // Supplier Management (admin dan manajer stok)
    Route::middleware('role:admin,manajer_stok,operator')->group(function () {
        Route::resource('suppliers', SupplierController::class);
    });

    // Product Management (admin dan manajer stok)
    Route::middleware('role:admin,manajer_stok,operator')->group(function () {
        Route::resource('products', ProductController::class);
    });

    // Stock Management
    Route::middleware('role:admin,manajer_stok,operator')->group(function () {
        Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
        Route::get('/stocks/{stock}', [StockController::class, 'show'])->name('stocks.show');
        Route::get('/stocks/{productId}/adjustment', [StockController::class, 'adjustment'])->name('stocks.adjustment');
        Route::post('/stocks/{productId}/adjustment', [StockController::class, 'storeAdjustment'])->name('stocks.storeAdjustment');
        Route::get('/stocks/{productId}/movements', [StockController::class, 'movements'])->name('stocks.movements');
        Route::get('/stocks/low-stock', [StockController::class, 'lowStock'])->name('stocks.lowStock');
    });

    // Purchase Order Management (admin dan manajer stok)
    Route::middleware('role:admin,manajer_stok,operator')->group(function () {
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::post('/purchase-orders/{purchaseOrder}/update-status', [PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.updateStatus');
    });

    // Receiving Management (admin dan manajer stok dan operator)
    Route::middleware('role:admin,manajer_stok,operator')->group(function () {
        Route::resource('receivings', ReceivingController::class);
    });

    // Sales Order Management
    Route::middleware('role:admin,manajer_stok,operator')->group(function () {
        Route::resource('sales-orders', SalesOrderController::class);
        Route::post('/sales-orders/{salesOrder}/update-status', [SalesOrderController::class, 'updateStatus'])->name('sales-orders.updateStatus');
    });

    // View-only routes (semua role bisa akses)
    Route::middleware('role:admin,manajer_stok,operator,viewer')->group(function () {
        Route::get('/reports/stock', function () {
            return view('reports.stock');
        })->name('reports.stock');
        
        Route::get('/reports/purchase-orders', function () {
            return view('reports.purchase-orders');
        })->name('reports.purchase-orders');

        Route::get('/reports/sales-orders', function () {
            return view('reports.sales-orders');
        })->name('reports.sales-orders');
    });
});
