<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with('product')
            ->paginate(10);
        return view('stocks.index', compact('stocks'));
    }

    public function show(Stock $stock)
    {
        $stock->load(['product', 'product.supplier']);
        $movements = $stock->product->stockMovements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('stocks.show', compact('stock', 'movements'));
    }

    public function adjustment($productId)
    {
        $product = Product::findOrFail($productId);
        $stock = Stock::where('product_id', $productId)->firstOrFail();
        return view('stocks.adjustment', compact('product', 'stock'));
    }

    public function storeAdjustment(Request $request, $productId)
    {
        $validated = $request->validate([
            'jenis_gerakan' => 'required|in:penerimaan,pengeluaran,penyesuaian,retur',
            'quantity' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $stock = Stock::where('product_id', $productId)->firstOrFail();
        
        if ($validated['jenis_gerakan'] == 'pengeluaran' && $stock->quantity_available < $validated['quantity']) {
            return redirect()->back()
                ->with('error', 'Stok tidak cukup untuk pengeluaran');
        }

        // Update stok
        if ($validated['jenis_gerakan'] == 'penerimaan') {
            $stock->quantity_on_hand += $validated['quantity'];
        } elseif ($validated['jenis_gerakan'] == 'pengeluaran') {
            $stock->quantity_on_hand -= $validated['quantity'];
        } elseif ($validated['jenis_gerakan'] == 'retur') {
            $stock->quantity_on_hand += $validated['quantity'];
        }

        $stock->quantity_available = $stock->quantity_on_hand - $stock->quantity_reserved;
        $stock->save();

        // Catat movement
        StockMovement::create([
            'product_id' => $productId,
            'user_id' => auth()->user()->user_id,
            'jenis_gerakan' => $validated['jenis_gerakan'],
            'quantity' => $validated['quantity'],
            'reference_type' => 'manual',
            'reference_id' => null,
            'keterangan' => $validated['keterangan'] ?? 'Penyesuaian manual',
        ]);

        return redirect()->route('stocks.show', $stock->stock_id)
            ->with('success', 'Stok berhasil disesuaikan');
    }

    public function movements($productId)
    {
        $product = Product::findOrFail($productId);
        $movements = StockMovement::where('product_id', $productId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('stocks.movements', compact('product', 'movements'));
    }

    public function lowStock()
    {
        $lowStocks = Stock::with('product')
            ->whereRaw('quantity_on_hand <= (SELECT reorder_level FROM products WHERE product_id = stock.product_id)')
            ->paginate(10);

        return view('stocks.low-stock', compact('lowStocks'));
    }
}
