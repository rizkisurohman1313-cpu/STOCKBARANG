@extends('layouts.app')

@section('title', 'Daftar Stok Barang')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-bar-chart"></i> Daftar Stok</h1>
        <p>Monitor stok barang di gudang</p>
    </div>
    <a href="{{ route('stocks.lowStock') }}" class="btn btn-warning"><i class="bi bi-exclamation-triangle"></i> Stok Rendah</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="table-light">
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Unit</th>
                    <th>Stok Gudang</th>
                    <th>Stok Terpesan</th>
                    <th>Stok Tersedia</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                <tr>
                    <td><code>{{ $stock->product->kode_produk }}</code></td>
                    <td>{{ $stock->product->nama_produk }}</td>
                    <td>{{ $stock->product->unit }}</td>
                    <td><strong>{{ $stock->quantity_on_hand }}</strong></td>
                    <td>{{ $stock->quantity_reserved }}</td>
                    <td class="text-success"><strong>{{ $stock->quantity_available }}</strong></td>
                    <td>{{ $stock->product->reorder_level }}</td>
                    <td>
                        @if($stock->quantity_on_hand <= $stock->product->reorder_level)
                            <span class="badge bg-danger">Rendah</span>
                        @else
                            <span class="badge bg-success">Normal</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('stocks.show', $stock->stock_id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('stocks.adjustment', $stock->product->product_id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Tidak ada stok</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $stocks->links() }}
    </div>
</div>
@endsection
