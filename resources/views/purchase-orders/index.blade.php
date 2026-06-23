@extends('layouts.app')

@section('title', 'Daftar Purchase Order')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-file-earmark-arrow-down"></i> Purchase Order</h1>
        <p>Kelola pesanan pembelian ke supplier</p>
    </div>
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat PO</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="table-light">
                    <th>No. PO</th>
                    <th>Supplier</th>
                    <th>Tgl PO</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrders as $po)
                <tr>
                    <td><code>{{ $po->nomor_po }}</code></td>
                    <td>{{ $po->supplier->nama_supplier }}</td>
                    <td>{{ $po->tanggal_po->format('d M Y') }}</td>
                    <td><strong>Rp {{ number_format($po->total_harga ?? 0, 0, ',', '.') }}</strong></td>
                    <td>
                        <span class="badge bg-{{ $po->status == 'diterima' ? 'success' : ($po->status == 'dibatalkan' ? 'danger' : 'info') }}">
                            {{ ucfirst($po->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('purchase-orders.show', $po->po_id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        @if($po->status == 'draft')
                        <a href="{{ route('purchase-orders.edit', $po->po_id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada PO</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $purchaseOrders->links() }}
    </div>
</div>
@endsection
