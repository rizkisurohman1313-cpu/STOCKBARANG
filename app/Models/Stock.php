<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stock';
    protected $primaryKey = 'stock_id';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'quantity_on_hand',
        'quantity_reserved',
        'quantity_available',
        'last_count_date',
        'last_movement_date',
        'updated_at',
    ];

    protected $casts = [
        'last_count_date' => 'datetime',
        'last_movement_date' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
