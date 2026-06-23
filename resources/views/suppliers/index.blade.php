@extends('layouts.app')

@section('title', 'Daftar Supplier')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-shop"></i> Daftar Supplier</h1>
        <p>Kelola data supplier/pemasok</p>
    </div>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Supplier</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="table-light">
                    <th>Nama Supplier</th>
                    <th>Contact Person</th>
                    <th>Telp/Email</th>
                    <th>Kota</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td><strong>{{ $supplier->nama_supplier }}</strong></td>
                    <td>{{ $supplier->contact_person ?? '-' }}</td>
                    <td>{{ $supplier->telepon ?? '-' }}</td>
                    <td>{{ $supplier->kota ?? '-' }}</td>
                    <td>
                        @if($supplier->status === 'aktif')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('suppliers.show', $supplier->supplier_id) }}" class="btn btn-outline-info"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('suppliers.destroy', $supplier->supplier_id) }}" style="display:inline;" onsubmit="return confirm('Yakin?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada supplier</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
