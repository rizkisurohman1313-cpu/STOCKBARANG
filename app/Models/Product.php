<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';
    public $timestamps = true;

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'category_id',
        'supplier_id',
        'deskripsi',
        'unit',
        'harga_beli',
        'harga_jual',
        'reorder_level',
        'max_stock',
        'status',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    // Relasi ke Stock
    public function stock()
    {
        return $this->hasOne(Stock::class, 'product_id', 'product_id');
    }

    // Relasi ke PurchaseOrderItems
    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'product_id', 'product_id');
    }

    // Relasi ke ReceivingItems
    public function receivingItems()
    {
        return $this->hasMany(ReceivingItem::class, 'product_id', 'product_id');
    }

    // Relasi ke SalesOrderItems
    public function salesOrderItems()
    {
        return $this->hasMany(SalesOrderItem::class, 'product_id', 'product_id');
    }

    // Relasi ke StockMovements
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'product_id');
    }
}
