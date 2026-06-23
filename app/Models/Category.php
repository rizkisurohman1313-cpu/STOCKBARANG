<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    public $timestamps = true;

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi ke Products
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}
