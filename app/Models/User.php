<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'nama_lengkap',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relasi ke PurchaseOrders
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'user_id', 'user_id');
    }

    // Relasi ke Receiving
    public function receivings()
    {
        return $this->hasMany(Receiving::class, 'user_id', 'user_id');
    }

    // Relasi ke SalesOrders
    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class, 'user_id', 'user_id');
    }

    // Relasi ke StockMovements
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'user_id', 'user_id');
    }

    // Relasi ke AuditLogs
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id', 'user_id');
    }

    // Helper method untuk check role
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    // Helper method untuk check multiple roles
    public function hasAnyRole($roles)
    {
        return in_array($this->role, $roles);
    }

    // Helper method untuk check permissions
    public function canEdit()
    {
        return in_array($this->role, ['admin', 'manajer_stok', 'operator']);
    }

    public function canDelete()
    {
        return in_array($this->role, ['admin', 'manajer_stok']);
    }

    public function canView()
    {
        return in_array($this->role, ['admin', 'manajer_stok', 'operator', 'viewer']);
    }
}
