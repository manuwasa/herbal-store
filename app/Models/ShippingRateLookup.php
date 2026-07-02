<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRateLookup extends Model
{
    public $timestamps = false; // immutable — only created_at, set explicitly

    protected $fillable = [
        'order_id',
        'branch_id',
        'session_id',
        'origin_area_id',
        'destination_area_id',
        'weight_grams',
        'raw_request',
        'raw_response',
        'selected_courier',
        'selected_service',
        'selected_price',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'raw_request' => 'array',
            'raw_response' => 'array',
            'selected_price' => 'decimal:2',
            'weight_grams' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
