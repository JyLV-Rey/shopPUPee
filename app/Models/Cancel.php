<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('cancel', 'cancel_id', 'int')]
class Cancel extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'cancel_reason',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
