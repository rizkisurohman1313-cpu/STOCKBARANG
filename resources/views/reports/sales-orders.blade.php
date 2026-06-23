@extends('layouts.app')

@section('title', 'Laporan Sales Order')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-text"></i> Laporan Sales Order</h1>
    <p>Laporan penjualan barang</p>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead>
                <tr class="table-light">
                    <th>No. SO</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Tidak ada data</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
