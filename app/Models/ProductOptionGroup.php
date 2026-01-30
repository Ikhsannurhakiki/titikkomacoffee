<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductOptionGroup extends Model
{
    protected $table = 'product_option_groups';

    protected $fillable = ['name'];

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_option_group_pivot');
    }
}
