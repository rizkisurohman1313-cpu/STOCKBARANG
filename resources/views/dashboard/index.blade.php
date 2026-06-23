@extends('layouts.app')

@section('title', 'Dashboard - Aplikasi Manajemen Stok Barang')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-house-door"></i> Dashboard</h1>
    <p>Selamat datang di Aplikasi Manajemen Stok Barang</p>
</div>

<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card">
            <i class="bi bi-people text-primary"></i>
            <h6>Total Pengguna</h6>
            <div class="value">{{ $totalUsers }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card">
            <i class="bi bi-tag text-success"></i>
            <h6>Kategori Aktif</h6>
            <div class="value">{{ $totalCategories }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card">
            <i class="bi bi-shop text-warning"></i>
            <h6>Supplier Aktif</h6>
            <div class="value">{{ $totalSuppliers }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card stat-card">
            <i class="bi bi-box text-info"></i>
            <h6>Total Produk</h6>
            <div class="value">{{ $totalProducts }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-bar-chart"></i> Nilai Stok</h5>
            </div>
            <div class="card-body">
                <h3 class="text-primary">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h3>
                <p class="text-muted mb-0">Total nilai stok di gudang</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-exclamation-triangle"></i> Stok Rendah</h5>
            </div>
            <div class="card-body">
                <h3 class="text-warning">{{ $lowStockProducts }}</h3>
                <p class="text-muted mb-0">Produk yang perlu direorder</p>
                <a href="{{ route('stocks.lowStock') }}" class="btn btn-sm btn-warning mt-2">Lihat Detail</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-file-earmark-arrow-down"></i> Purchase Order</h5>
            </div>
            <div class="card-body">
                <h3>{{ $purchaseOrdersCount }}</h3>
                <p class="text-muted mb-0">PO sedang berlangsung</p>
                <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-primary mt-2">Lihat Semua</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-file-earmark-check"></i> Penerimaan</h5>
            </div>
            <div class="card-body">
                <h3>{{ $receivingCount }}</h3>
                <p class="text-muted mb-0">Penerimaan sedang berlangsung</p>
                <a href="{{ route('receivings.index') }}" class="btn btn-sm btn-primary mt-2">Lihat Semua</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-file-earmark-arrow-up"></i> Sales Order</h5>
            </div>
            <div class="card-body">
                <h3>{{ $salesOrdersCount }}</h3>
                <p class="text-muted mb-0">SO sedang berlangsung</p>
                <a href="{{ route('sales-orders.index') }}" class="btn btn-sm btn-primary mt-2">Lihat Semua</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-info-circle"></i> Informasi Sistem</h5>
            </div>
            <div class="card-body">
                <p><strong>Role Anda:</strong> <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}</span></p>
                <p><strong>Status Akun:</strong> <span class="badge bg-success">{{ ucfirst(auth()->user()->status) }}</span></p>
                <p><strong>Terakhir Login:</strong> {{ auth()->user()->updated_at->format('d M Y, H:i') }}</p>
                <hr>
                <p class="mb-0"><small class="text-muted">Aplikasi Manajemen Stok Barang v1.0 | &copy; 2024</small></p>
            </div>
        </div>
    </div>
</div>
@endsection
