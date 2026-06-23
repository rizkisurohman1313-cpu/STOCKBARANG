<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchase_orders';
    protected $primaryKey = 'po_id';
    public $timestamps = true;

    protected $fillable = [
        'nomor_po',
        'supplier_id',
        'user_id',
        'tanggal_po',
        'tanggal_diharapkan',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_po' => 'date',
        'tanggal_diharapkan' => 'date',
        'total_harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke PurchaseOrderItems
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class, 'po_id', 'po_id');
    }

    // Relasi ke Receiving
    public function receivings()
    {
        return $this->hasMany(Receiving::class, 'po_id', 'po_id');
    }
}
