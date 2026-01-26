<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOption extends Model
{
    protected $fillable = ['product_id', 'option_group', 'option_name', 'extra_price'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
