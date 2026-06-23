@extends('layouts.app')

@section('title', 'Edit Sales Order: ' . $salesOrder->nomor_so)

@section('content')
<div class="page-header">
    <h1>Edit Sales Order</h1>
    <p>{{ $salesOrder->nomor_so }}</p>
</div>

<div class="card">
    <form action="{{ route('sales-orders.update', $salesOrder->so_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $salesOrder->customer_name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email Pelanggan</label>
                    <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', $salesOrder->customer_email) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telepon Pelanggan</label>
                    <input type="text" name="customer_telepon" class="form-control" value="{{ old('customer_telepon', $salesOrder->customer_telepon) }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal SO</label>
                    <input type="date" name="tanggal_so" class="form-control" value="{{ $salesOrder->tanggal_so->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pengiriman Diharapkan</label>
                    <input type="date" name="tanggal_pengiriman_diharapkan" class="form-control" value="{{ $salesOrder->tanggal_pengiriman_diharapkan?->format('Y-m-d') }}">
                </div>
            </div>

            <h6 class="mb-3">Detail Items</h6>
            <div id="itemsContainer">
                @foreach($salesOrder->items as $key => $item)
                <div class="item-row row mb-2">
                    <div class="col-md-4">
                        <select name="items[{{ $key }}][product_id]" class="form-select" required>
                            @foreach($products as $p)
                            <option value="{{ $p->product_id }}" @selected($p->product_id == $item->product_id) data-price="{{ $p->harga_jual }}">
                                {{ $p->kode_produk }} - {{ $p->nama_produk }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[{{ $key }}][quantity_ordered]" class="form-control qty-input" value="{{ $item->quantity_ordered }}" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="items[{{ $key }}][harga_satuan]" class="form-control price-input" value="{{ $item->harga_satuan }}" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-item">Hapus</button>
                    </div>
                </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addItem">+ Tambah Item</button>

            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $salesOrder->catatan) }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('sales-orders.show', $salesOrder->so_id) }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
