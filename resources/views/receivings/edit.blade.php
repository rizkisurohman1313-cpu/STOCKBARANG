@extends('layouts.app')

@section('title', 'Edit Penerimaan')

@section('content')
<div class="page-header">
    <h1>Edit Penerimaan</h1>
    <p>{{ $receiving->nomor_terima }}</p>
</div>

<div class="card">
    <form action="{{ route('receivings.update', $receiving->receiving_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Supplier</label>
                    <select name="supplier_id" class="form-select" required>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->supplier_id }}" @selected($s->supplier_id == $receiving->supplier_id)>{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Purchase Order</label>
                    <select name="po_id" class="form-select">
                        <option value="">Pilih PO</option>
                        @foreach($purchaseOrders as $po)
                        <option value="{{ $po->po_id }}" @selected($po->po_id == $receiving->po_id)>{{ $po->nomor_po }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="mb-3">Detail Items</h6>
            <div id="itemsContainer">
                @foreach($receiving->items as $key => $item)
                <div class="item-row row mb-2">
                    <div class="col-md-4">
                        <select name="items[{{ $key }}][product_id]" class="form-select" required>
                            @foreach($products as $p)
                            <option value="{{ $p->product_id }}" @selected($p->product_id == $item->product_id)>
                                {{ $p->kode_produk }} - {{ $p->nama_produk }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[{{ $key }}][quantity_received]" class="form-control qty-input" value="{{ $item->quantity_received }}" min="1" required>
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
                <textarea name="catatan" class="form-control" rows="2">{{ old('catatan', $receiving->catatan) }}</textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('receivings.show', $receiving->receiving_id) }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
