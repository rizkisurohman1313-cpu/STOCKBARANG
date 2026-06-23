@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-box-seam"></i> Tambah Produk Baru</h1>
    <p>Tambahkan produk baru ke dalam sistem</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Form Produk</h5>
            </div>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Produk <span class="text-danger">*</span></label>
                        <input type="text" name="kode_produk" class="form-control @error('kode_produk') is-invalid @enderror" 
                               value="{{ old('kode_produk') }}" placeholder="Mis: PRD-001" required>
                        @error('kode_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_produk" class="form-control @error('nama_produk') is-invalid @enderror" 
                               value="{{ old('nama_produk') }}" required>
                        @error('nama_produk')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}" @selected(old('category_id') == $category->category_id)>
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
                                    <option value="">Pilih Supplier</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->supplier_id }}" @selected(old('supplier_id') == $supplier->supplier_id)>
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
                        <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" 
                                       value="{{ old('unit') }}" placeholder="Mis: pcs, kg, liter" required>
                                @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Harga Beli <span class="text-danger">*</span></label>
                                <input type="number" name="harga_beli" class="form-control @error('harga_beli') is-invalid @enderror" 
                                       value="{{ old('harga_beli') }}" step="0.01" required>
                                @error('harga_beli')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Harga Jual <span class="text-danger">*</span></label>
                                <input type="number" name="harga_jual" class="form-control @error('harga_jual') is-invalid @enderror" 
                                       value="{{ old('harga_jual') }}" step="0.01" required>
                                @error('harga_jual')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Reorder Level <span class="text-danger">*</span></label>
                                <input type="number" name="reorder_level" class="form-control @error('reorder_level') is-invalid @enderror" 
                                       value="{{ old('reorder_level', 10) }}" min="0" required>
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
                                       value="{{ old('max_stock') }}" min="0">
                                @error('max_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="aktif" @selected(old('status', 'aktif') == 'aktif')>Aktif</option>
                                    <option value="nonaktif" @selected(old('status') == 'nonaktif')>Nonaktif</option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Produk</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-header">
                <h5><i class="bi bi-info-circle"></i> Informasi</h5>
            </div>
            <div class="card-body">
                <p><strong>Kode Produk</strong></p>
                <p class="small text-muted">Gunakan format unik seperti PRD-001, ELEC-002, dll</p>

                <p class="mt-3"><strong>Reorder Level</strong></p>
                <p class="small text-muted">Stok minimal sebelum sistem mengingatkan untuk reorder</p>

                <p class="mt-3"><strong>Max Stok</strong></p>
                <p class="small text-muted">Stok maksimal yang boleh disimpan (opsional)</p>
            </div>
        </div>
    </div>
</div>
@endsection
