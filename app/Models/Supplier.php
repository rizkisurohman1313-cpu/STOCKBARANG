<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';
    protected $primaryKey = 'supplier_id';
    public $timestamps = true;

    protected $fillable = [
        'nama_supplier',
        'contact_person',
        'email',
        'telepon',
        'alamat',
        'kota',
        'provinsi',
        'kode_pos',
        'bank_account',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Products
    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id', 'supplier_id');
    }

    // Relasi ke PurchaseOrders
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id', 'supplier_id');
    }

    // Relasi ke Receiving
    public function receivings()
    {
        return $this->hasMany(Receiving::class, 'supplier_id', 'supplier_id');
    }
}
