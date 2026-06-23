<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'aktif')->get();
        $products = Product::where('status', 'aktif')->get();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'tanggal_po' => 'required|date',
            'tanggal_diharapkan' => 'nullable|date',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        // Generate nomor PO
        $nomor_po = 'PO-' . date('Ymd') . '-' . str_pad(PurchaseOrder::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $po = PurchaseOrder::create([
            'nomor_po' => $nomor_po,
            'supplier_id' => $validated['supplier_id'],
            'user_id' => auth()->user()->user_id,
            'tanggal_po' => $validated['tanggal_po'],
            'tanggal_diharapkan' => $validated['tanggal_diharapkan'] ?? null,
            'status' => 'draft',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $total_harga = 0;
        foreach ($validated['items'] as $item) {
            $sub_total = $item['quantity_ordered'] * $item['harga_satuan'];
            PurchaseOrderItem::create([
                'po_id' => $po->po_id,
                'product_id' => $item['product_id'],
                'quantity_ordered' => $item['quantity_ordered'],
                'harga_satuan' => $item['harga_satuan'],
                'sub_total' => $sub_total,
            ]);
            $total_harga += $sub_total;
        }

        $po->update(['total_harga' => $total_harga]);

        return redirect()->route('purchase-orders.show', $po->po_id)
            ->with('success', 'Purchase Order berhasil dibuat');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'user', 'items.product']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.show', $purchaseOrder->po_id)
                ->with('error', 'Hanya PO dengan status draft yang bisa diubah');
        }

        $suppliers = Supplier::where('status', 'aktif')->get();
        $products = Product::where('status', 'aktif')->get();
        $purchaseOrder->load('items');
        
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.show', $purchaseOrder->po_id)
                ->with('error', 'Hanya PO dengan status draft yang bisa diubah');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'tanggal_po' => 'required|date',
            'tanggal_diharapkan' => 'nullable|date',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
        ]);

        $purchaseOrder->update([
            'supplier_id' => $validated['supplier_id'],
            'tanggal_po' => $validated['tanggal_po'],
            'tanggal_diharapkan' => $validated['tanggal_diharapkan'],
            'catatan' => $validated['catatan'],
        ]);

        PurchaseOrderItem::where('po_id', $purchaseOrder->po_id)->delete();

        $total_harga = 0;
        foreach ($validated['items'] as $item) {
            $sub_total = $item['quantity_ordered'] * $item['harga_satuan'];
            PurchaseOrderItem::create([
                'po_id' => $purchaseOrder->po_id,
                'product_id' => $item['product_id'],
                'quantity_ordered' => $item['quantity_ordered'],
                'harga_satuan' => $item['harga_satuan'],
                'sub_total' => $sub_total,
            ]);
            $total_harga += $sub_total;
        }

        $purchaseOrder->update(['total_harga' => $total_harga]);

        return redirect()->route('purchase-orders.show', $purchaseOrder->po_id)
            ->with('success', 'Purchase Order berhasil diubah');
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:diajukan,dikonfirmasi,diterima,dibatalkan',
        ]);

        $purchaseOrder->update(['status' => $validated['status']]);

        return redirect()->route('purchase-orders.show', $purchaseOrder->po_id)
            ->with('success', 'Status PO berhasil diubah');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'Hanya PO dengan status draft yang bisa dihapus');
        }

        PurchaseOrderItem::where('po_id', $purchaseOrder->po_id)->delete();
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order berhasil dihapus');
    }
}
