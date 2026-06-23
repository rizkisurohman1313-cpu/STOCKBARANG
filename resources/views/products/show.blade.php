@extends('layouts.app')

@section('title', 'Detail Produk: ' . $product->nama_produk)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-box"></i> {{ $product->nama_produk }}</h1>
        <p>Kode: <code>{{ $product->kode_produk }}</code></p>
    </div>
    @if(auth()->user()->canEdit())
    <div>
        <a href="{{ route('products.edit', $product->product_id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
        <a href="{{ route('stocks.show', $product->stock->stock_id ?? 0) }}" class="btn btn-info"><i class="bi bi-bar-chart"></i> Stok</a>
    </div>
    @endif
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Informasi Produk</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama Produk:</strong></p>
                        <p>{{ $product->nama_produk }}</p>

                        <p class="mt-3"><strong>Kategori:</strong></p>
                        <p><span class="badge bg-light text-dark">{{ $product->category->nama_kategori }}</span></p>

                        <p class="mt-3"><strong>Supplier:</strong></p>
                        <p>{{ $product->supplier->nama_supplier }}</p>

                        <p class="mt-3"><strong>Satuan:</strong></p>
                        <p>{{ $product->unit }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Harga Beli:</strong></p>
                        <p>Rp {{ number_format($product->harga_beli, 0, ',', '.') }}</p>

                        <p class="mt-3"><strong>Harga Jual:</strong></p>
                        <p>Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>

                        <p class="mt-3"><strong>Margin:</strong></p>
                        <p>{{ round((($product->harga_jual - $product->harga_beli) / $product->harga_beli * 100), 2) }}%</p>

                        <p class="mt-3"><strong>Status:</strong></p>
                        <p>
                            @if($product->status === 'aktif')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($product->deskripsi)
                <hr>
                <p><strong>Deskripsi:</strong></p>
                <p>{{ $product->deskripsi }}</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Riwayat Pergerakan Stok</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr class="table-light">
                            <th>Tanggal</th>
                            <th>Jenis Gerakan</th>
                            <th>Qty</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->stockMovements()->latest()->limit(10)->get() as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $movement->jenis_gerakan)) }}</span>
                            </td>
                            <td><strong>{{ $movement->quantity }}</strong></td>
                            <td>{{ $movement->keterangan }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada pergerakan stok</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-info">
                <h5 class="text-white">Stok Saat Ini</h5>
            </div>
            <div class="card-body">
                @if($product->stock)
                    <div class="mb-3">
                        <p class="text-muted mb-1">Stok di Gudang</p>
                        <h3 class="text-primary">{{ $product->stock->quantity_on_hand }}</h3>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Stok Terpesan</p>
                        <p class="fs-5">{{ $product->stock->quantity_reserved }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-muted mb-1">Stok Tersedia untuk Dijual</p>
                        <p class="fs-5 text-success">{{ $product->stock->quantity_available }}</p>
                    </div>
                    <hr>
                    <p class="small text-muted mb-2">Reorder Level: <strong>{{ $product->reorder_level }}</strong></p>
                    @if($product->max_stock)
                    <p class="small text-muted">Max Stok: <strong>{{ $product->max_stock }}</strong></p>
                    @endif
                @else
                    <p class="text-muted">Belum ada data stok</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Informasi Supplier</h5>
            </div>
            <div class="card-body">
                <p><strong>{{ $product->supplier->nama_supplier }}</strong></p>
                @if($product->supplier->email)
                <p class="small text-muted">📧 {{ $product->supplier->email }}</p>
                @endif
                @if($product->supplier->telepon)
                <p class="small text-muted">📱 {{ $product->supplier->telepon }}</p>
                @endif
                <a href="{{ route('suppliers.show', $product->supplier->supplier_id) }}" class="btn btn-sm btn-outline-primary">Lihat Detail Supplier</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <a href="{{ route('products.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Daftar</a>
    </div>
</div>
@endsection
