<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class, 'user_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'user_id');
    }

    public function cashierSales(): HasMany
    {
        return $this->hasMany(Sale::class, 'cashier_id');
    }

    public function cancelledSales(): HasMany
    {
        return $this->hasMany(Sale::class, 'cancelled_by');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'user_id');
    }
}
