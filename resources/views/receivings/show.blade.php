@extends('layouts.app')

@section('title', 'Detail Penerimaan: ' . $receiving->nomor_terima)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-file-earmark-check"></i> {{ $receiving->nomor_terima }}</h1>
        <p>Supplier: {{ $receiving->supplier->nama_supplier }}</p>
    </div>
    <div>
        @if($receiving->status === 'proses')
        <a href="{{ route('receivings.edit', $receiving->receiving_id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
        @endif
        <span class="badge bg-{{ $receiving->status == 'selesai' ? 'success' : 'warning' }}">{{ ucfirst($receiving->status) }}</span>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header"><h5>Informasi Penerimaan</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nomor Terima:</strong> {{ $receiving->nomor_terima }}</p>
                        <p><strong>Tanggal Terima:</strong> {{ $receiving->tanggal_terima->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Supplier:</strong> {{ $receiving->supplier->nama_supplier }}</p>
                        <p><strong>PO Terkait:</strong> {{ $receiving->purchaseOrder->nomor_po ?? '-' }}</p>
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
                        @foreach($receiving->items as $item)
                        <tr>
                            <td>{{ $item->product->nama_produk }}</td>
                            <td>{{ $item->quantity_received }}</td>
                            <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Total</h5></div>
            <div class="card-body">
                <h3>Rp {{ number_format($receiving->total_harga ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>

        @if($receiving->status === 'proses')
        <div class="card mt-3">
            <div class="card-header"><h5>Aksi</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('receivings.destroy', $receiving->receiving_id) }}" onsubmit="return confirm('Yakin?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">Hapus Penerimaan</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<a href="{{ route('receivings.index') }}" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection
