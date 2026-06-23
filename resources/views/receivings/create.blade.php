@extends('layouts.app')

@section('title', 'Terima Barang')

@section('extra-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 1;
    
    document.getElementById('addItem').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'item-row row mb-2';
        newRow.innerHTML = `
            <div class="col-md-4">
                <select name="items[${itemCount}][product_id]" class="form-select" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p)
                    <option value="{{ $p->product_id }}">{{ $p->kode_produk }} - {{ $p->nama_produk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemCount}][quantity_received]" class="form-control qty-input" placeholder="Qty" min="1" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="items[${itemCount}][harga_satuan]" class="form-control price-input" placeholder="Harga" step="0.01" required>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item">Hapus</button>
            </div>
        `;
        container.appendChild(newRow);
        itemCount++;
        attachRemoveListeners();
    });
    
    function attachRemoveListeners() {
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.removeEventListener('click', removeItem);
            btn.addEventListener('click', removeItem);
        });
    }
    
    function removeItem(e) {
        e.preventDefault();
        const itemRows = document.querySelectorAll('.item-row');
        if (itemRows.length > 1) {
            e.target.closest('.item-row').remove();
        } else {
            alert('Minimal harus ada 1 item');
        }
    }
    
    attachRemoveListeners();
});
</script>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-earmark-check"></i> Terima Barang</h1>
</div>

<div class="card">
    <form action="{{ route('receivings.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">Pilih Supplier</option>
                        @foreach($suppliers as $s)
                        <option value="{{ $s->supplier_id }}">{{ $s->nama_supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Purchase Order</label>
                    <select name="po_id" class="form-select">
                        <option value="">Pilih PO (Opsional)</option>
                        @foreach($purchaseOrders as $po)
                        <option value="{{ $po->po_id }}">{{ $po->nomor_po }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Terima <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="tanggal_terima" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>
            </div>

            <h6 class="mb-3">Detail Items</h6>
            <div id="itemsContainer">
                <div class="item-row row mb-2">
                    <div class="col-md-4">
                        <select name="items[0][product_id]" class="form-select" required>
                            <option value="">Pilih Produk</option>
                            @foreach($products as $p)
                            <option value="{{ $p->product_id }}">{{ $p->kode_produk }} - {{ $p->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[0][quantity_received]" class="form-control qty-input" placeholder="Qty" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="items[0][harga_satuan]" class="form-control price-input" placeholder="Harga" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-item">Hapus</button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="addItem">+ Tambah Item</button>

            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="catatan" class="form-control" rows="2"></textarea>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Terima Barang</button>
            <a href="{{ route('receivings.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
