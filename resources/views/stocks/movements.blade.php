@extends('layouts.app')

@section('title', 'Riwayat Pergerakan Stok')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-clock-history"></i> Riwayat Pergerakan Stok</h1>
    <p>Produk: {{ $product->nama_produk }}</p>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="table-light">
                    <th>Tanggal/Waktu</th>
                    <th>Jenis Gerakan</th>
                    <th>Qty</th>
                    <th>User</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td>{{ $movement->created_at->format('d M Y, H:i:s') }}</td>
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
                    <td>{{ $movement->user->nama_lengkap }}</td>
                    <td>{{ $movement->keterangan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada pergerakan stok</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $movements->links() }}
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <a href="{{ route('stocks.show', $product->stock->stock_id ?? 0) }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>
@endsection
