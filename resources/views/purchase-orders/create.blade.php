@extends('layouts.app')

@section('title', 'Buat Purchase Order')

@section('content')
<div class="page-header">
    <h1><i class="bi bi-file-earmark-plus"></i> Buat Purchase Order</h1>
    <p>Buat pesanan pembelian ke supplier</p>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Form Purchase Order</h5>
            </div>
            <form action="{{ route('purchase-orders.store') }}" method="POST" id="poForm">
                @csrf
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $s)
                                <option value="{{ $s->supplier_id }}">{{ $s->nama_supplier }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal PO <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_po" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Diharapkan</label>
                            <input type="date" name="tanggal_diharapkan" class="form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="catatan" class="form-control" rows="2"></textarea>
                    </div>

                    <hr>

                    <h6 class="mb-3">Detail Items</h6>
                    <div id="itemsContainer">
                        <div class="item-row row mb-2">
                            <div class="col-md-4">
                                <select name="items[0][product_id]" class="form-select product-select" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $p)
                                    <option value="{{ $p->product_id }}" data-price="{{ $p->harga_beli }}">{{ $p->kode_produk }} - {{ $p->nama_produk }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][quantity_ordered]" class="form-control qty-input" placeholder="Qty" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][harga_satuan]" class="form-control price-input" placeholder="Harga" step="0.01" required>
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item">Hapus</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm" id="addItem">+ Tambah Item</button>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <p><strong>Total: Rp <span id="totalAmount">0</span></strong></p>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Buat PO</button>
                    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemCount = 1;
    const container = document.getElementById('itemsContainer');

    // Fungsi untuk menghitung subtotal dan total
    function calculateTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qtyInput = row.querySelector('.qty-input');
            const priceInput = row.querySelector('.price-input');
            const subtotalInput = row.querySelector('.subtotal');
            
            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const subtotal = qty * price;
            
            // Update subtotal field dengan format Rupiah
            subtotalInput.value = subtotal > 0 ? subtotal.toLocaleString('id-ID', {minimumFractionDigits: 0}) : '';
            grandTotal += subtotal;
        });
        
        // Update total amount
        const totalElement = document.getElementById('totalAmount');
        if (totalElement) {
            totalElement.textContent = grandTotal.toLocaleString('id-ID', {minimumFractionDigits: 0});
        }
    }

    // Fungsi untuk attach event listeners ke item row
    function attachItemListeners() {
        // Remove button
        document.querySelectorAll('.remove-item').forEach((btn, index) => {
            btn.onclick = null; // Clear previous listeners
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    this.closest('.item-row').remove();
                    calculateTotal();
                } else {
                    alert('Minimal harus ada 1 item');
                }
            });
        });

        // Product select - untuk set harga otomatis
        document.querySelectorAll('.product-select').forEach(select => {
            select.onchange = null; // Clear previous listeners
            select.addEventListener('change', function() {
                const row = this.closest('.item-row');
                const priceInput = row.querySelector('.price-input');
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.dataset.price || '';
                priceInput.value = price;
                calculateTotal();
            });
        });

        // Quantity input - real-time calculation
        document.querySelectorAll('.qty-input').forEach(input => {
            input.oninput = null; // Clear previous listeners
            input.addEventListener('input', calculateTotal);
            input.addEventListener('change', calculateTotal);
        });

        // Price input - real-time calculation
        document.querySelectorAll('.price-input').forEach(input => {
            input.oninput = null; // Clear previous listeners
            input.addEventListener('input', calculateTotal);
            input.addEventListener('change', calculateTotal);
        });
    }

    // Event listener untuk tombol tambah item
    document.getElementById('addItem').addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'item-row row mb-2';
        newRow.innerHTML = `
            <div class="col-md-4">
                <select name="items[${itemCount}][product_id]" class="form-select product-select" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p)
                    <option value="{{ $p->product_id }}" data-price="{{ $p->harga_beli }}">{{ $p->kode_produk }} - {{ $p->nama_produk }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemCount}][quantity_ordered]" class="form-control qty-input" placeholder="Qty" min="1" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemCount}][harga_satuan]" class="form-control price-input" placeholder="Harga" step="0.01" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item">Hapus</button>
            </div>
        `;
        container.appendChild(newRow);
        itemCount++;
        attachItemListeners();
        calculateTotal();
    });

    // Initial setup - attach listeners untuk item pertama
    attachItemListeners();
    calculateTotal();
});
</script>
