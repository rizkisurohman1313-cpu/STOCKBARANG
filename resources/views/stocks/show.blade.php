@extends('layouts.app')

@section('title', 'Detail Stok: ' . $stock->product->nama_produk)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-bar-chart"></i> Stok {{ $stock->product->nama_produk }}</h1>
        <p>Produk: <code>{{ $stock->product->kode_produk }}</code></p>
    </div>
    <a href="{{ route('stocks.adjustment', $stock->product->product_id) }}" class="btn btn-primary"><i class="bi bi-pencil"></i> Penyesuaian Stok</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Informasi Stok</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-muted">Stok di Gudang</p>
                        <h3 class="text-primary">{{ $stock->quantity_on_hand }} {{ $stock->product->unit }}</h3>
                    </div>
                    <div class="col-md-6">
                        <p class="text-muted">Stok Terpesan</p>
                        <h3 class="text-warning">{{ $stock->quantity_reserved }} {{ $stock->product->unit }}</h3>
                    </div>
                </div>
                <hr>
                <p class="text-muted">Stok Tersedia untuk Dijual</p>
                <h3 class="text-success">{{ $stock->quantity_available }} {{ $stock->product->unit }}</h3>
                <hr>
                <p class="small text-muted">Terakhir diperbarui: {{ $stock->updated_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Riwayat Pergerakan Stok</h5>
                <a href="{{ route('stocks.movements', $stock->product->product_id) }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr class="table-light">
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Qty</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                @php
                                    $colors = [
                                        'penerimaan' => 'success',
                                        'pengeluaran' => 'danger',
                                        'penyesuaian' => 'warning',
                                        'retur' => 'info',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $colors[$movement->jenis_gerakan] ?? 'secondary' }}">
                                    {{ ucfirst($movement->jenis_gerakan) }}
                                </span>
                            </td>
                            <td><strong>{{ $movement->quantity }}</strong></td>
                            <td><small>{{ $movement->keterangan }}</small></td>
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
        <div class="card">
            <div class="card-header">
                <h5>Informasi Produk</h5>
            </div>
            <div class="card-body">
                <p><strong>Nama Produk:</strong></p>
                <p>{{ $stock->product->nama_produk }}</p>

                <p class="mt-3"><strong>Kategori:</strong></p>
                <p><span class="badge bg-light text-dark">{{ $stock->product->category->nama_kategori }}</span></p>

                <p class="mt-3"><strong>Supplier:</strong></p>
                <p>{{ $stock->product->supplier->nama_supplier }}</p>

                <p class="mt-3"><strong>Satuan:</strong></p>
                <p>{{ $stock->product->unit }}</p>

                <p class="mt-3"><strong>Reorder Level:</strong></p>
                <p>{{ $stock->product->reorder_level }} {{ $stock->product->unit }}</p>

                @if($stock->product->max_stock)
                <p class="mt-3"><strong>Max Stok:</strong></p>
                <p>{{ $stock->product->max_stock }} {{ $stock->product->unit }}</p>
                @endif

                <a href="{{ route('products.show', $stock->product->product_id) }}" class="btn btn-sm btn-outline-primary mt-3 w-100">Detail Produk</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <a href="{{ route('stocks.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>
@endsection
