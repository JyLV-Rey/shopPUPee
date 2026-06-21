<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('payment', 'payment_id', 'int')]
class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
