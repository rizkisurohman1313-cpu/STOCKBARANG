<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    use HasFactory;

    protected $table = 'sales_order_items';
    protected $primaryKey = 'soi_id';
    public $timestamps = false;

    protected $fillable = [
        'so_id',
        'product_id',
        'quantity_ordered',
        'quantity_shipped',
        'harga_satuan',
        'sub_total',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];

    // Relasi ke SalesOrder
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class, 'so_id', 'so_id');
    }

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
