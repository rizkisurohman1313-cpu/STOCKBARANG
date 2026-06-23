@extends('layouts.app')

@section('title', 'Stok Rendah')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-exclamation-triangle"></i> Produk Stok Rendah</h1>
    <p>Produk yang perlu direorder karena stok di bawah reorder level</p>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="table-light">
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Stok Saat Ini</th>
                    <th>Reorder Level</th>
                    <th>Perlu Pesan</th>
                    <th>Supplier</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lowStocks as $stock)
                <tr class="table-warning">
                    <td><code>{{ $stock->product->kode_produk }}</code></td>
                    <td>{{ $stock->product->nama_produk }}</td>
                    <td><strong>{{ $stock->quantity_on_hand }} {{ $stock->product->unit }}</strong></td>
                    <td>{{ $stock->product->reorder_level }}</td>
                    <td class="text-danger">
                        <strong>{{ $stock->product->reorder_level - $stock->quantity_on_hand }} {{ $stock->product->unit }}</strong>
                    </td>
                    <td>{{ $stock->product->supplier->nama_supplier }}</td>
                    <td>
                        <a href="{{ route('stocks.show', $stock->stock_id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('purchase-orders.create') }}?product={{ $stock->product->product_id }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus"></i> PO</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Semua stok dalam kondisi normal</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $lowStocks->links() }}
    </div>
</div>
@endsection
