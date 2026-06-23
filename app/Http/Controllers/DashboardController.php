<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Stock;
use App\Models\PurchaseOrder;
use App\Models\Receiving;
use App\Models\SalesOrder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung data untuk dashboard
        $totalUsers = User::where('status', 'aktif')->count();
        $totalCategories = Category::where('status', 'aktif')->count();
        $totalSuppliers = Supplier::where('status', 'aktif')->count();
        $totalProducts = Product::where('status', 'aktif')->count();
        
        // Hitung stok
        $totalStockValue = Stock::with('product')
            ->get()
            ->sum(function($stock) {
                return $stock->quantity_on_hand * ($stock->product->harga_beli ?? 0);
            });

        $lowStockProducts = Stock::whereRaw('quantity_on_hand <= (SELECT reorder_level FROM products WHERE product_id = stock.product_id)')
            ->with('product')
            ->count();

        // Hitung transaksi
        $purchaseOrdersCount = PurchaseOrder::where('status', '!=', 'dibatalkan')->count();
        $salesOrdersCount = SalesOrder::where('status', '!=', 'dibatalkan')->count();
        $receivingCount = Receiving::where('status', '!=', 'dibatalkan')->count();

        return view('dashboard.index', [
            'totalUsers' => $totalUsers,
            'totalCategories' => $totalCategories,
            'totalSuppliers' => $totalSuppliers,
            'totalProducts' => $totalProducts,
            'totalStockValue' => $totalStockValue,
            'lowStockProducts' => $lowStockProducts,
            'purchaseOrdersCount' => $purchaseOrdersCount,
            'salesOrdersCount' => $salesOrdersCount,
            'receivingCount' => $receivingCount,
        ]);
    }
}
