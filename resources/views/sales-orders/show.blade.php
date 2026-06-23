@extends('layouts.app')

@section('title', 'Detail Sales Order: ' . $salesOrder->nomor_so)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-file-earmark"></i> {{ $salesOrder->nomor_so }}</h1>
        <p>Pelanggan: {{ $salesOrder->customer_name }}</p>
    </div>
    <div>
        @if($salesOrder->status === 'draft')
        <a href="{{ route('sales-orders.edit', $salesOrder->so_id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
        @endif
        <span class="badge bg-{{ $salesOrder->status == 'selesai' ? 'success' : 'info' }}">{{ ucfirst($salesOrder->status) }}</span>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header"><h5>Informasi SO</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nomor SO:</strong> {{ $salesOrder->nomor_so }}</p>
                        <p><strong>Tanggal:</strong> {{ $salesOrder->tanggal_so->format('d M Y') }}</p>
                        <p><strong>Pengiriman Diharapkan:</strong> {{ $salesOrder->tanggal_pengiriman_diharapkan?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Nama Pelanggan:</strong> {{ $salesOrder->customer_name }}</p>
                        <p><strong>Email:</strong> {{ $salesOrder->customer_email ?? '-' }}</p>
                        <p><strong>Telepon:</strong> {{ $salesOrder->customer_telepon ?? '-' }}</p>
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
                        @foreach($salesOrder->items as $item)
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
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Total</h5></div>
            <div class="card-body">
                <h3>Rp {{ number_format($salesOrder->total_harga ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>

        @if($salesOrder->status === 'draft')
        <div class="card mt-3">
            <div class="card-header"><h5>Aksi</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('sales-orders.updateStatus', $salesOrder->so_id) }}">
                    @csrf
                    <input type="hidden" name="status" value="dikonfirmasi">
                    <button type="submit" class="btn btn-success w-100 mb-2">Konfirmasi SO</button>
                </form>
                <form method="POST" action="{{ route('sales-orders.destroy', $salesOrder->so_id) }}" onsubmit="return confirm('Yakin?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger w-100">Hapus SO</button>
                </form>
            </div>
        </div>
        @elseif($salesOrder->status === 'dikonfirmasi')
        <div class="card mt-3">
            <div class="card-header"><h5>Aksi</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('sales-orders.updateStatus', $salesOrder->so_id) }}">
                    @csrf
                    <input type="hidden" name="status" value="dikirim">
                    <button type="submit" class="btn btn-info w-100">Tandai Dikirim</button>
                </form>
            </div>
        </div>
        @elseif($salesOrder->status === 'dikirim')
        <div class="card mt-3">
            <div class="card-header"><h5>Aksi</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('sales-orders.updateStatus', $salesOrder->so_id) }}">
                    @csrf
                    <input type="hidden" name="status" value="selesai">
                    <button type="submit" class="btn btn-success w-100">Tandai Selesai</button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

<a href="{{ route('sales-orders.index') }}" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection
