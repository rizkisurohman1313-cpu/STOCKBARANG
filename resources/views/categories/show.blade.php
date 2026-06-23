@extends('layouts.app')

@section('title', 'Detail Kategori: ' . $category->nama_kategori)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-tag"></i> {{ $category->nama_kategori }}</h1>
    </div>
    <div>
        <a href="{{ route('categories.edit', $category->category_id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Informasi Kategori</h5>
            </div>
            <div class="card-body">
                <p><strong>Nama:</strong> {{ $category->nama_kategori }}</p>
                <p><strong>Status:</strong> 
                    @if($category->status === 'aktif')
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </p>
                @if($category->deskripsi)
                <p><strong>Deskripsi:</strong></p>
                <p>{{ $category->deskripsi }}</p>
                @endif
                <p class="small text-muted">Dibuat: {{ $category->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Produk dalam Kategori</h5>
            </div>
            <div class="card-body">
                <h3 class="text-primary">{{ $category->products()->count() }}</h3>
                <p class="text-muted">Jumlah produk</p>
                <a href="{{ route('products.index') }}?category={{ $category->category_id }}" class="btn btn-sm btn-primary">Lihat Produk</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <a href="{{ route('categories.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>
@endsection
