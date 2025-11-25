<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['branch_id', 'user_id', 'customer_id', 'points_used', 'points_discount', 'total', 'cash', 'change', 'payment_method', 'status'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function getProfitAttribute()
    {
        return $this->items->sum(function ($item) {
            $cost = $item->cost ?? ($item->product->cost ?? 0);
            return ($item->price - $cost) * $item->quantity;
        });
    }
}
