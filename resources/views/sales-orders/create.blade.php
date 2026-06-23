@extends('layouts.app')

@section('title', 'Buat Sales Order')

@section('extra-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 1;
    
    // Tombol Tambah Item
    document.getElementById('addItem').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'item-row row mb-2';
        newRow.innerHTML = `
            <div class="col-md-4">
                <select name="items[${itemCount}][product_id]" class="form-select" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p)
                    <option value="{{ $p->product_id }}" data-price="{{ $p->harga_jual }}">{{ $p->kode_produk }} - {{ $p->nama_produk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemCount}][quantity_ordered]" class="form-control qty-input" placeholder="Qty" min="1" required>
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
    
    // Tombol Hapus Item
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
    <h1><i class="bi bi-file-earmark-plus"></i> Buat Sales Order</h1>
</div>

<div class="card">
    <form action="{{ route('sales-orders.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Nama Pelanggan <span class="text-danger">*</span></label>
                    <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email Pelanggan</label>
                    <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telepon Pelanggan</label>
                    <input type="text" name="customer_telepon" class="form-control" value="{{ old('customer_telepon') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal SO <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_so" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Pengiriman Diharapkan</label>
                    <input type="date" name="tanggal_pengiriman_diharapkan" class="form-control">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan untuk SO ini">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <hr>
            <h6 class="mb-3">Detail Items</h6>
            <div id="itemsContainer">
                <div class="item-row row mb-2">
                    <div class="col-md-4">
                        <select name="items[0][product_id]" class="form-select" required>
                            <option value="">Pilih Produk</option>
                            @foreach($products as $p)
                            <option value="{{ $p->product_id }}" data-price="{{ $p->harga_jual }}">{{ $p->kode_produk }} - {{ $p->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[0][quantity_ordered]" class="form-control qty-input" placeholder="Qty" min="1" required>
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
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Buat SO</button>
            <a href="{{ route('sales-orders.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
