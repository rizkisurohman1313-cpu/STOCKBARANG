<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $table = 'sales_orders';
    protected $primaryKey = 'so_id';
    public $timestamps = true;

    protected $fillable = [
        'nomor_so',
        'user_id',
        'tanggal_so',
        'tanggal_pengiriman_diharapkan',
        'customer_name',
        'customer_email',
        'customer_telepon',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_so' => 'date',
        'tanggal_pengiriman_diharapkan' => 'date',
        'total_harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke SalesOrderItems
    public function items()
    {
        return $this->hasMany(SalesOrderItem::class, 'so_id', 'so_id');
    }
}
