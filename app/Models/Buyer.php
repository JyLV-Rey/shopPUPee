<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletesFlag;
use Database\Factories\BuyerFactory;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Table('buyer', 'buyer_id', 'int')]
class Buyer extends Authenticatable
{
    use SoftDeletesFlag;
    /** @use HasFactory<BuyerFactory> */
    use HasFactory, Notifiable;

    protected $primaryKey = 'buyer_id';

    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'is_admin',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
            'is_deleted' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) ($this->is_admin ?? false);
    }

    public function isActive(): bool
    {
        return ! (bool) ($this->is_deleted ?? false);
    }

    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class, 'buyer_id', 'buyer_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'buyer_id', 'buyer_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class, 'buyer_id', 'buyer_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'buyer_id', 'buyer_id');
    }

    public function sellerApplications(): HasMany
    {
        return $this->hasMany(SellerApplication::class, 'buyer_id', 'buyer_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id', 'buyer_id');
    }
}
