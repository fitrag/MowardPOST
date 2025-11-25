<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['branch_id', 'product_id', 'quantity'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::saved(function ($stock) {
            \Illuminate\Support\Facades\Cache::put('products_last_updated', now()->timestamp);
        });

        static::deleted(function ($stock) {
            \Illuminate\Support\Facades\Cache::put('products_last_updated', now()->timestamp);
        });
    }
}
