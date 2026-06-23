<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    use HasFactory;

    protected $table = 'receiving';
    protected $primaryKey = 'receiving_id';
    public $timestamps = true;

    protected $fillable = [
        'nomor_terima',
        'po_id',
        'supplier_id',
        'user_id',
        'tanggal_terima',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_terima' => 'datetime',
        'total_harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke PurchaseOrder
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id', 'po_id');
    }

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

    // Relasi ke ReceivingItems
    public function items()
    {
        return $this->hasMany(ReceivingItem::class, 'receiving_id', 'receiving_id');
    }
}
