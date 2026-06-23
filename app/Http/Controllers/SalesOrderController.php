<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('sales-orders.index', compact('salesOrders'));
    }

    public function create()
    {
        $products = Product::where('status', 'aktif')->get();
        return view('sales-orders.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'nullable|email|max:100',
            'customer_telepon' => 'nullable|string|max:20',
            'tanggal_so' => 'required|date',
            'tanggal_pengiriman_diharapkan' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        // Generate nomor SO
        $nomor_so = 'SO-' . date('Ymd') . '-' . str_pad(SalesOrder::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

        $so = SalesOrder::create([
            'nomor_so' => $nomor_so,
            'user_id' => auth()->user()->user_id,
            'tanggal_so' => $validated['tanggal_so'],
            'tanggal_pengiriman_diharapkan' => $validated['tanggal_pengiriman_diharapkan'] ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_telepon' => $validated['customer_telepon'] ?? null,
            'status' => 'draft',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $total_harga = 0;
        foreach ($validated['items'] as $item) {
            $sub_total = $item['quantity_ordered'] * $item['harga_satuan'];
            SalesOrderItem::create([
                'so_id' => $so->so_id,
                'product_id' => $item['product_id'],
                'quantity_ordered' => $item['quantity_ordered'],
                'harga_satuan' => $item['harga_satuan'],
                'sub_total' => $sub_total,
            ]);
            $total_harga += $sub_total;
        }

        $so->update(['total_harga' => $total_harga]);

        return redirect()->route('sales-orders.show', $so->so_id)
            ->with('success', 'Sales Order berhasil dibuat');
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['user', 'items.product']);
        return view('sales-orders.show', compact('salesOrder'));
    }

    public function edit(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return redirect()->route('sales-orders.show', $salesOrder->so_id)
                ->with('error', 'Hanya SO dengan status draft yang bisa diubah');
        }

        $products = Product::where('status', 'aktif')->get();
        $salesOrder->load('items');

        return view('sales-orders.edit', compact('salesOrder', 'products'));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return redirect()->route('sales-orders.show', $salesOrder->so_id)
                ->with('error', 'Hanya SO dengan status draft yang bisa diubah');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_email' => 'nullable|email|max:100',
            'customer_telepon' => 'nullable|string|max:20',
            'tanggal_so' => 'required|date',
            'tanggal_pengiriman_diharapkan' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'catatan' => 'nullable|string',
        ]);

        $salesOrder->update([
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_telepon' => $validated['customer_telepon'],
            'tanggal_so' => $validated['tanggal_so'],
            'tanggal_pengiriman_diharapkan' => $validated['tanggal_pengiriman_diharapkan'],
            'catatan' => $validated['catatan'],
        ]);

        SalesOrderItem::where('so_id', $salesOrder->so_id)->delete();

        $total_harga = 0;
        foreach ($validated['items'] as $item) {
            $sub_total = $item['quantity_ordered'] * $item['harga_satuan'];
            SalesOrderItem::create([
                'so_id' => $salesOrder->so_id,
                'product_id' => $item['product_id'],
                'quantity_ordered' => $item['quantity_ordered'],
                'harga_satuan' => $item['harga_satuan'],
                'sub_total' => $sub_total,
            ]);
            $total_harga += $sub_total;
        }

        $salesOrder->update(['total_harga' => $total_harga]);

        return redirect()->route('sales-orders.show', $salesOrder->so_id)
            ->with('success', 'Sales Order berhasil diubah');
    }

    public function updateStatus(Request $request, SalesOrder $salesOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:dikonfirmasi,dikirim,selesai,dibatalkan',
        ]);

        $newStatus = $validated['status'];

        // Validasi stok ketika dikonfirmasi
        if ($newStatus === 'dikonfirmasi') {
            foreach ($salesOrder->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)->first();
                if (!$stock || $stock->quantity_available < $item->quantity_ordered) {
                    return redirect()->route('sales-orders.show', $salesOrder->so_id)
                        ->with('error', 'Stok tidak cukup untuk ' . $item->product->nama_produk);
                }
            }
        }

        // Update reserved stok jika dikonfirmasi
        if ($newStatus === 'dikonfirmasi') {
            foreach ($salesOrder->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)->first();
                if ($stock) {
                    $stock->quantity_reserved += $item->quantity_ordered;
                    $stock->quantity_available = $stock->quantity_on_hand - $stock->quantity_reserved;
                    $stock->save();
                }
            }
        }

        // Update stok jika dikirim
        if ($newStatus === 'dikirim') {
            foreach ($salesOrder->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)->first();
                if ($stock) {
                    $stock->quantity_on_hand -= $item->quantity_ordered;
                    $stock->quantity_reserved -= $item->quantity_ordered;
                    $stock->quantity_available = $stock->quantity_on_hand - $stock->quantity_reserved;
                    $stock->save();

                    // Catat stock movement
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'user_id' => auth()->user()->user_id,
                        'jenis_gerakan' => 'pengeluaran',
                        'quantity' => $item->quantity_ordered,
                        'reference_type' => 'sales_order',
                        'reference_id' => $salesOrder->so_id,
                        'keterangan' => 'Pengiriman SO ' . $salesOrder->nomor_so,
                    ]);
                }
                $item->quantity_shipped = $item->quantity_ordered;
                $item->save();
            }
        }

        $salesOrder->update(['status' => $newStatus]);

        return redirect()->route('sales-orders.show', $salesOrder->so_id)
            ->with('success', 'Status SO berhasil diubah');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            return redirect()->route('sales-orders.index')
                ->with('error', 'Hanya SO dengan status draft yang bisa dihapus');
        }

        SalesOrderItem::where('so_id', $salesOrder->so_id)->delete();
        $salesOrder->delete();

        return redirect()->route('sales-orders.index')
            ->with('success', 'Sales Order berhasil dihapus');
    }
}
