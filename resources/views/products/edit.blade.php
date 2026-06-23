@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil"></i> Edit Produk</h1>
    <p>Ubah data produk</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Form Produk</h5>
            </div>
            <form action="{{ route('products.update', $product->product_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Produk <span class="text-danger">*</span></label>
                        <input type="text" name="kode_produk" class="form-control @error('kode_produk') is-invalid @enderror" 
                               value="{{ old('kode_produk', $product->kode_produk) }}" required>
                        @error('kode_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" 
                               value="{{ old('nama_produk', $product->nama_produk) }}" required>
                        @error('nama_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}" @selected(old('category_id', $product->category_id) == $category->category_id)>
                                        {{ $category->nama_kategori }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->supplier_id }}" @selected(old('supplier_id', $product->supplier_id) == $supplier->supplier_id)>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $product->deskripsi) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" 
                                       value="{{ old('unit', $product->unit) }}" required>
                                @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                                <input type="number" name="harga_beli" class="form-control @error('harga_beli') is-invalid @enderror" 
                                       value="{{ old('harga_beli', $product->harga_beli) }}" step="0.01" required>
                                @error('harga_beli')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                                <input type="number" name="harga_jual" class="form-control @error('harga_jual') is-invalid @enderror" 
                                       value="{{ old('harga_jual', $product->harga_jual) }}" step="0.01" required>
                                @error('harga_jual')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Reorder Level <span class="text-danger">*</span></label>
                                <input type="number" name="reorder_level" class="form-control @error('reorder_level') is-invalid @enderror" 
                                       value="{{ old('reorder_level', $product->reorder_level) }}" min="0" required>
                                @error('reorder_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Max Stok</label>
                                <input type="number" name="max_stock" class="form-control @error('max_stock') is-invalid @enderror" 
                                       value="{{ old('max_stock', $product->max_stock) }}" min="0">
                                @error('max_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="aktif" @selected(old('status', $product->status) == 'aktif')>Aktif</option>
                                    <option value="nonaktif" @selected(old('status', $product->status) == 'nonaktif')>Nonaktif</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
                    <a href="{{ route('products.show', $product->product_id) }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-box"></i> Stok Saat Ini</h5>
            </div>
            <div class="card-body">
                @if($product->stock)
                <p><strong>Stok Tersedia:</strong></p>
                <h4 class="text-primary">{{ $product->stock->quantity_on_hand }} {{ $product->unit }}</h4>
                <p class="small text-muted">Stok yang ready untuk dijual</p>

                <p class="mt-3"><strong>Stok Terpesan:</strong></p>
                <p>{{ $product->stock->quantity_reserved }} {{ $product->unit }}</p>

                <p class="mt-3"><strong>Stok Tersedia:</strong></p>
                <p>{{ $product->stock->quantity_available }} {{ $product->unit }}</p>
                @else
                <p class="text-muted">Belum ada stok</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
