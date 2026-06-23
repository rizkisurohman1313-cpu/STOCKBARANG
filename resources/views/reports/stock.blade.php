@extends('layouts.app')

@section('title', 'Laporan Stok Barang')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-text"></i> Laporan Stok Barang</h1>
    <p>Laporan stok barang keseluruhan</p>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Filter & Export</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="category" class="form-select">
                    <option value="">Semua</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status Stok</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="low">Rendah</option>
                    <option value="normal">Normal</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-success w-100"><i class="bi bi-file-earmark-excel"></i> Export Excel</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead>
                <tr class="table-light">
                    <th>Kode</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Stok Gudang</th>
                    <th>Stok Terpesan</th>
                    <th>Reorder Level</th>
                    <th>Harga Satuan</th>
                    <th>Nilai Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Tidak ada data</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
