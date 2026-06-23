@extends('layouts.app')

@section('title', 'Daftar Sales Order')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-file-earmark-arrow-up"></i> Sales Order</h1>
        <p>Kelola pesanan penjualan</p>
    </div>
    <a href="{{ route('sales-orders.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat SO</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="table-light">
                    <th>No. SO</th>
                    <th>Pelanggan</th>
                    <th>Tgl SO</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($salesOrders as $so)
                <tr>
                    <td><code>{{ $so->nomor_so }}</code></td>
                    <td>{{ $so->customer_name }}</td>
                    <td>{{ $so->tanggal_so->format('d M Y') }}</td>
                    <td><strong>Rp {{ number_format($so->total_harga ?? 0, 0, ',', '.') }}</strong></td>
                    <td>
                        <span class="badge bg-{{ $so->status == 'selesai' ? 'success' : ($so->status == 'dibatalkan' ? 'danger' : 'info') }}">
                            {{ ucfirst($so->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('sales-orders.show', $so->so_id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                        @if($so->status == 'draft')
                        <a href="{{ route('sales-orders.edit', $so->so_id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada SO</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $salesOrders->links() }}
    </div>
</div>
@endsection
