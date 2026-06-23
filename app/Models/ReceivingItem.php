<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingItem extends Model
{
    use HasFactory;

    protected $table = 'receiving_items';
    protected $primaryKey = 'ri_id';
    public $timestamps = false;

    protected $fillable = [
        'receiving_id',
        'product_id',
        'quantity_received',
        'harga_satuan',
        'sub_total',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'sub_total' => 'decimal:2',
    ];

    // Relasi ke Receiving
    public function receiving()
    {
        return $this->belongsTo(Receiving::class, 'receiving_id', 'receiving_id');
    }

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
