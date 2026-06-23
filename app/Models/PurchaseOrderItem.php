<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $table = 'purchase_order_items';
    protected $primaryKey = 'poi_id';
    public $timestamps = false;

    protected $fillable = [
        'po_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'harga_satuan',
        'sub_total',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];

    // Relasi ke PurchaseOrder
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
