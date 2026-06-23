@extends('layouts.app')

@section('title', 'Daftar Penerimaan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-file-earmark-check"></i> Penerimaan Barang</h1>
        <p>Catat penerimaan barang dari supplier</p>
    </div>
    <a href="{{ route('receivings.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Terima Barang</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="table-light">
                    <th>No. Terima</th>
                    <th>Supplier</th>
                    <th>Tgl Terima</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receivings as $receiving)
                <tr>
                    <td><code>{{ $receiving->nomor_terima }}</code></td>
                    <td>{{ $receiving->supplier->nama_supplier }}</td>
                    <td>{{ $receiving->tanggal_terima->format('d M Y, H:i') }}</td>
                    <td><strong>Rp {{ number_format($receiving->total_harga ?? 0, 0, ',', '.') }}</strong></td>
                    <td>
                        <span class="badge bg-{{ $receiving->status == 'selesai' ? 'success' : 'warning' }}">
                            {{ ucfirst($receiving->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('receivings.show', $receiving->receiving_id) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada penerimaan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $receivings->links() }}
    </div>
</div>
@endsection
