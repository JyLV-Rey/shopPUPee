<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('delivery', 'delivery_id', 'int')]
class Delivery extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'delivery_status',
        'courier_service',
        'buyer_address_id',
    ];

    protected function casts(): array
    {
        return [
            'tracking_number' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'buyer_address_id', 'address_id');
    }
}
