<?php

namespace App\Http\Controllers;

use App\Models\Receiving;
use App\Models\ReceivingItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ReceivingController extends Controller
{
    public function index()
    {
        $receivings = Receiving::with(['supplier', 'user', 'purchaseOrder'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('receivings.index', compact('receivings'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'aktif')->get();
        $purchaseOrders = PurchaseOrder::where('status', 'dikonfirmasi')->get();
        $products = Product::where('status', 'aktif')->get();
        return view('receivings.create', compact('suppliers', 'purchaseOrders', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'po_id' => 'nullable|exists:purchase_orders,po_id',
            'tanggal_terima' => 'required|date_format:Y-m-d\TH:i',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity_received' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        // Generate nomor terima
        $nomor_terima = 'TRM-' . date('Ymd') . '-' . str_pad(Receiving::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $receiving = Receiving::create([
            'nomor_terima' => $nomor_terima,
            'supplier_id' => $validated['supplier_id'],
            'po_id' => $validated['po_id'],
            'user_id' => auth()->user()->user_id,
            'tanggal_terima' => $validated['tanggal_terima'],
            'status' => 'proses',
            'catatan' => $validated['catatan'],
        ]);

        $total_harga = 0;
        foreach ($validated['items'] as $item) {
            $sub_total = $item['quantity_received'] * $item['harga_satuan'];
            
            ReceivingItem::create([
                'receiving_id' => $receiving->receiving_id,
                'product_id' => $item['product_id'],
                'quantity_received' => $item['quantity_received'],
                'harga_satuan' => $item['harga_satuan'],
                'sub_total' => $sub_total,
            ]);

            // Update stok
            $stock = Stock::where('product_id', $item['product_id'])->first();
            if ($stock) {
                $stock->quantity_on_hand += $item['quantity_received'];
                $stock->quantity_available = $stock->quantity_on_hand - $stock->quantity_reserved;
                $stock->save();

                // Catat stock movement
                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'user_id' => auth()->user()->user_id,
                    'jenis_gerakan' => 'penerimaan',
                    'quantity' => $item['quantity_received'],
                    'reference_type' => 'receiving',
                    'reference_id' => $receiving->receiving_id,
                    'keterangan' => 'Penerimaan barang dari ' . $validated['supplier_id'],
                ]);
            }

            $total_harga += $sub_total;
        }

        $receiving->update([
            'total_harga' => $total_harga,
            'status' => 'selesai',
        ]);

        return redirect()->route('receivings.show', $receiving->receiving_id)
            ->with('success', 'Penerimaan barang berhasil dicatat');
    }

    public function show(Receiving $receiving)
    {
        $receiving->load(['supplier', 'user', 'purchaseOrder', 'items.product']);
        return view('receivings.show', compact('receiving'));
    }

    public function edit(Receiving $receiving)
    {
        if ($receiving->status !== 'proses') {
            return redirect()->route('receivings.show', $receiving->receiving_id)
                ->with('error', 'Hanya penerimaan dengan status proses yang bisa diubah');
        }

        $suppliers = Supplier::where('status', 'aktif')->get();
        $purchaseOrders = PurchaseOrder::where('status', 'dikonfirmasi')->get();
        $products = Product::where('status', 'aktif')->get();
        $receiving->load('items');

        return view('receivings.edit', compact('receiving', 'suppliers', 'purchaseOrders', 'products'));
    }

    public function update(Request $request, Receiving $receiving)
    {
        if ($receiving->status !== 'proses') {
            return redirect()->route('receivings.show', $receiving->receiving_id)
                ->with('error', 'Hanya penerimaan dengan status proses yang bisa diubah');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'po_id' => 'nullable|exists:purchase_orders,po_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity_received' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $receiving->update([
            'supplier_id' => $validated['supplier_id'],
            'po_id' => $validated['po_id'],
            'catatan' => $validated['catatan'],
        ]);

        ReceivingItem::where('receiving_id', $receiving->receiving_id)->delete();

        $total_harga = 0;
        foreach ($validated['items'] as $item) {
            $sub_total = $item['quantity_received'] * $item['harga_satuan'];
            ReceivingItem::create([
                'receiving_id' => $receiving->receiving_id,
                'product_id' => $item['product_id'],
                'quantity_received' => $item['quantity_received'],
                'harga_satuan' => $item['harga_satuan'],
                'sub_total' => $sub_total,
            ]);
            $total_harga += $sub_total;
        }

        $receiving->update(['total_harga' => $total_harga]);

        return redirect()->route('receivings.show', $receiving->receiving_id)
            ->with('success', 'Penerimaan berhasil diubah');
    }

    public function destroy(Receiving $receiving)
    {
        if ($receiving->status !== 'proses') {
            return redirect()->route('receivings.index')
                ->with('error', 'Hanya penerimaan dengan status proses yang bisa dihapus');
        }

        // Rollback stok jika sudah diterima
        if ($receiving->status === 'selesai') {
            foreach ($receiving->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)->first();
                if ($stock) {
                    $stock->quantity_on_hand -= $item->quantity_received;
                    $stock->quantity_available = $stock->quantity_on_hand - $stock->quantity_reserved;
                    $stock->save();
                }
            }
        }

        ReceivingItem::where('receiving_id', $receiving->receiving_id)->delete();
        $receiving->delete();

        return redirect()->route('receivings.index')
            ->with('success', 'Penerimaan barang berhasil dihapus');
    }
}
