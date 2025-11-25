<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CustomerPoint extends Model
{
    protected $fillable = [
        'customer_id',
        'transaction_id',
        'points',
        'type',
        'description',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
