<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'is_active'];

    // Relasi: Satu Kategori punya banyak Produk
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Otomatis buat slug dari nama saat membuat kategori baru
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }
}
