@extends('layouts.app')

@section('title', 'Edit Purchase Order: ' . $purchaseOrder->nomor_po)

@section('content')
<div class="page-header">
    <h1>Edit Purchase Order</h1>
    <p>{{ $purchaseOrder->nomor_po }}</p>
</div>

<div class="card">
    <form action="{{ route('purchase-orders.update', $purchaseOrder->po_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select" required>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->supplier_id }}" @selected($s->supplier_id == $purchaseOrder->supplier_id)>{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal PO</label>
                    <input type="date" name="tanggal_po" class="form-control" value="{{ $purchaseOrder->tanggal_po->format('Y-m-d') }}" required>
                </div>
            </div>

            <h6 class="mb-3">Detail Items</h6>
            <div id="itemsContainer">
                @foreach($purchaseOrder->items as $key => $item)
                <div class="item-row row mb-2">
                    <div class="col-md-4">
                        <select name="items[{{ $key }}][product_id]" class="form-select" required>
                            @foreach($products as $p)
                            <option value="{{ $p->product_id }}" @selected($p->product_id == $item->product_id) data-price="{{ $p->harga_beli }}">
                                {{ $p->kode_produk }} - {{ $p->nama_produk }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[{{ $key }}][quantity_ordered]" class="form-control qty-input" value="{{ $item->quantity_ordered }}" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[{{ $key }}][harga_satuan]" class="form-control price-input" value="{{ $item->harga_satuan }}" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control subtotal" value="{{ $item->sub_total }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-item">Hapus</button>
                    </div>
                </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-outline-primary btn-sm" id="addItem">+ Tambah Item</button>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('purchase-orders.show', $purchaseOrder->po_id) }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
