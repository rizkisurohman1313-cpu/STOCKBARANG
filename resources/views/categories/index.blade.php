@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-tag"></i> Daftar Kategori</h1>
        <p>Kelola kategori produk</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Kategori</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="table-light">
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Produk</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td><strong>{{ $category->nama_kategori }}</strong></td>
                    <td>{{ Str::limit($category->deskripsi, 50) }}</td>
                    <td>
                        <span class="badge bg-info">{{ $category->products()->count() }}</span>
                    </td>
                    <td>
                        @if($category->status === 'aktif')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td><small>{{ $category->created_at->format('d M Y') }}</small></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('categories.show', $category->category_id) }}" class="btn btn-outline-info"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('categories.edit', $category->category_id) }}" class="btn btn-outline-warning"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('categories.destroy', $category->category_id) }}" style="display:inline;" onsubmit="return confirm('Yakin?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada kategori</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $categories->links() }}
    </div>
</div>
@endsection
