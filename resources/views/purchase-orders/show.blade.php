@extends('layouts.app')

@section('title', 'Detail Purchase Order: ' . $purchaseOrder->nomor_po)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-file-earmark"></i> {{ $purchaseOrder->nomor_po }}</h1>
        <p>Supplier: {{ $purchaseOrder->supplier->nama_supplier }}</p>
    </div>
    <div>
        @if($purchaseOrder->status === 'draft')
        <a href="{{ route('purchase-orders.edit', $purchaseOrder->po_id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
        @endif
        <span class="badge bg-{{ $purchaseOrder->status == 'diterima' ? 'success' : 'info' }}">{{ ucfirst($purchaseOrder->status) }}</span>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header"><h5>Informasi PO</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nomor PO:</strong> {{ $purchaseOrder->nomor_po }}</p>
                        <p><strong>Tanggal:</strong> {{ $purchaseOrder->tanggal_po->format('d M Y') }}</p>
                        <p><strong>Tanggal Diharapkan:</strong> {{ $purchaseOrder->tanggal_diharapkan?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Supplier:</strong> {{ $purchaseOrder->supplier->nama_supplier }}</p>
                        <p><strong>User:</strong> {{ $purchaseOrder->user->nama_lengkap }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($purchaseOrder->status) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h5>Detail Items</h5></div>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr class="table-light">
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $item)
                        <tr>
                            <td>{{ $item->product->nama_produk }}</td>
                            <td>{{ $item->quantity_ordered }}</td>
                            <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($purchaseOrder->catatan)
        <div class="card">
            <div class="card-header"><h5>Catatan</h5></div>
            <div class="card-body">
                {{ $purchaseOrder->catatan }}
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Total</h5></div>
            <div class="card-body">
                <h3>Rp {{ number_format($purchaseOrder->total_harga ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>

        @if($purchaseOrder->status === 'draft')
        <div class="card mt-3">
            <div class="card-header"><h5>Aksi</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('purchase-orders.updateStatus', $purchaseOrder->po_id) }}">
                    @csrf
                    <input type="hidden" name="status" value="diajukan">
                    <button type="submit" class="btn btn-success w-100 mb-2">Ajukan PO</button>
                </form>
                <form method="POST" action="{{ route('purchase-orders.destroy', $purchaseOrder->po_id) }}" onsubmit="return confirm('Yakin?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">Hapus PO</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection
