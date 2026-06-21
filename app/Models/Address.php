<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('address', 'address_id', 'int')]
class Address extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'buyer_id',
        'street',
        'city',
        'postal_code',
        'province',
        'barangay',
        'region',
        'unit_floor',
        'additional_notes',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'buyer_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class, 'buyer_address_id', 'address_id');
    }

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class, 'address_id', 'address_id');
    }

    public function sellerApplications(): HasMany
    {
        return $this->hasMany(SellerApplication::class, 'address_id', 'address_id');
    }
}
