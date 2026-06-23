@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-box"></i> Daftar Produk</h1>
        <p>Kelola semua produk/barang di gudang</p>
    </div>
    @if(auth()->user()->canEdit())
    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <form method="GET" action="{{ route('products.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control" placeholder="Cari produk...">
                    <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr class="table-light">
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Supplier</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td><code>{{ $product->kode_produk }}</code></td>
                    <td>{{ $product->nama_produk }}</td>
                    <td><span class="badge bg-light text-dark">{{ $product->category->nama_kategori }}</span></td>
                    <td>{{ $product->supplier->nama_supplier }}</td>
                    <td><strong>Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</strong></td>
                    <td>
                        @if($product->stock)
                            <span class="badge bg-info">{{ $product->stock->quantity_on_hand }} {{ $product->unit }}</span>
                        @else
                            <span class="badge bg-secondary">0</span>
                        @endif
                    </td>
                    <td>
                        @if($product->status === 'aktif')
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-outline-info" title="Lihat">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(auth()->user()->canEdit())
                            <a href="{{ route('products.edit', $product->product_id) }}" class="btn btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endif
                            @if(auth()->user()->canDelete())
                            <form method="POST" action="{{ route('products.destroy', $product->product_id) }}" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> Tidak ada produk
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $products->links() }}
    </div>
</div>
@endsection
