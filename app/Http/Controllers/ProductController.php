<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Stock;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'supplier', 'stock'])
            ->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', 'aktif')->get();
        $suppliers = Supplier::where('status', 'aktif')->get();
        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_produk' => 'required|string|unique:products|max:50',
            'nama_produk' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,category_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'deskripsi' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $product = Product::create($validated);

        // Buat stock record
        Stock::create([
            'product_id' => $product->product_id,
            'quantity_on_hand' => 0,
            'quantity_reserved' => 0,
            'quantity_available' => 0,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier', 'stock', 'stockMovements']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', 'aktif')->get();
        $suppliers = Supplier::where('status', 'aktif')->get();
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'kode_produk' => 'required|string|max:50|unique:products,kode_produk,' . $product->product_id . ',product_id',
            'nama_produk' => 'required|string|max:100',
            'category_id' => 'required|exists:categories,category_id',
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'deskripsi' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diubah');
    }

    public function destroy(Product $product)
    {
        if ($product->purchaseOrderItems()->count() > 0 || 
            $product->salesOrderItems()->count() > 0) {
            return redirect()->route('products.index')
                ->with('error', 'Produk tidak dapat dihapus karena masih terlibat dalam transaksi');
        }

        Stock::where('product_id', $product->product_id)->delete();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus');
    }
}
