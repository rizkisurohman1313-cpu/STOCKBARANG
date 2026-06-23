{{-- Supplier Show Stub --}}
@extends('layouts.app')

@section('title', 'Detail Supplier: ' . $supplier->nama_supplier)

@section('content')
<div class="page-header d-flex justify-content-between">
    <div>
        <h1><i class="bi bi-shop"></i> {{ $supplier->nama_supplier }}</h1>
    </div>
    <a href="{{ route('suppliers.edit', $supplier->supplier_id) }}" class="btn btn-warning">Edit</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Informasi Supplier</h5></div>
            <div class="card-body">
                <p><strong>Contact Person:</strong> {{ $supplier->contact_person ?? '-' }}</p>
                <p><strong>Email:</strong> {{ $supplier->email ?? '-' }}</p>
                <p><strong>Telepon:</strong> {{ $supplier->telepon ?? '-' }}</p>
                <p><strong>Alamat:</strong> {{ $supplier->alamat }}</p>
                <p><strong>Kota:</strong> {{ $supplier->kota ?? '-' }}</p>
                <p><strong>Status:</strong> <span class="badge bg-{{ $supplier->status == 'aktif' ? 'success' : 'danger' }}">{{ ucfirst($supplier->status) }}</span></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Statistik</h5></div>
            <div class="card-body">
                <p><strong>Produk:</strong> {{ $supplier->products()->count() }}</p>
                <p><strong>PO:</strong> {{ $supplier->purchaseOrders()->count() }}</p>
                <p><strong>Penerimaan:</strong> {{ $supplier->receivings()->count() }}</p>
            </div>
        </div>
    </div>
</div>
<a href="{{ route('suppliers.index') }}" class="btn btn-secondary mt-3">Kembali</a>
@endsection
