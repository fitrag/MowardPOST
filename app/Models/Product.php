<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'price', 'cost', 'image', 'category_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    protected static function booted()
    {
        static::saved(function ($product) {
            \Illuminate\Support\Facades\Cache::put('products_last_updated', now()->timestamp);
        });

        static::deleted(function ($product) {
            \Illuminate\Support\Facades\Cache::put('products_last_updated', now()->timestamp);
        });
    }
}
