<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'invoice_number',
        'staff_id',
        'total_price',
        'paid_amount',
        'subtotal',
        'tax_amount',
        'change_amount',
        'payment_method',
        'status',
        'customer_name',
        'table_number',
        'notes'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
