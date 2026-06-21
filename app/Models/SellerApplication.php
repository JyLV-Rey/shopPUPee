<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Table('seller_application', 'application_id', 'int')]
class SellerApplication extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'seller_name',
        'buyer_id',
        'valid_id_url',
        'status',
        'address_id',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id', 'buyer_id');
    }

    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class, 'application_id', 'application_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'address_id');
    }
}
