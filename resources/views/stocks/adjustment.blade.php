@extends('layouts.app')

@section('title', 'Penyesuaian Stok')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-pencil-square"></i> Penyesuaian Stok</h1>
    <p>Produk: {{ $product->nama_produk }}</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Form Penyesuaian Stok</h5>
            </div>
            <form action="{{ route('stocks.storeAdjustment', $product->product_id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Gerakan <span class="text-danger">*</span></label>
                        <select name="jenis_gerakan" class="form-select @error('jenis_gerakan') is-invalid @enderror" required>
                            <option value="">Pilih Jenis</option>
                            <option value="penerimaan" @selected(old('jenis_gerakan') == 'penerimaan')>Penerimaan</option>
                            <option value="pengeluaran" @selected(old('jenis_gerakan') == 'pengeluaran')>Pengeluaran</option>
                            <option value="penyesuaian" @selected(old('jenis_gerakan') == 'penyesuaian')>Penyesuaian</option>
                            <option value="retur" @selected(old('jenis_gerakan') == 'retur')>Retur</option>
                        </select>
                        @error('jenis_gerakan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                               value="{{ old('quantity') }}" min="1" required>
                        <small class="text-muted">Masukkan jumlah {{ $product->unit }}</small>
                        @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Masukkan keterangan...">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Penyesuaian</button>
                    <a href="{{ route('stocks.show', $stock->stock_id) }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info">
                <h5 class="text-white">Stok Saat Ini</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Stok Gudang</p>
                <h3 class="text-primary">{{ $stock->quantity_on_hand }}</h3>

                <p class="mt-3 text-muted">Stok Terpesan</p>
                <p class="fs-5">{{ $stock->quantity_reserved }}</p>

                <p class="mt-3 text-muted">Stok Tersedia</p>
                <p class="fs-5 text-success">{{ $stock->quantity_available }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
