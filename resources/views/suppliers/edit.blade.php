{{-- Edit Supplier Stub --}}
@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<div class="page-header">
    <h1>Edit Supplier</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <form action="{{ route('suppliers.update', $supplier->supplier_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="nama_supplier" class="form-control" value="{{ old('nama_supplier', $supplier->nama_supplier) }}" required>
                    </div>
                    {{-- Add more fields as needed --}}
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
