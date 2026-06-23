@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="page-header">
    <h1>Edit Kategori</h1>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Form Kategori</h5>
            </div>
            <form action="{{ route('categories.update', $category->category_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control @error('nama_kategori') is-invalid @enderror" 
                               value="{{ old('nama_kategori', $category->nama_kategori) }}" required>
                        @error('nama_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $category->deskripsi) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="aktif" @selected(old('status', $category->status) == 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status', $category->status) == 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
