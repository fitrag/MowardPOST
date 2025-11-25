<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProductPermission extends Model
{
    protected $fillable = [
        'user_id',
        'can_create_product',
        'can_read_product',
        'can_update_product',
        'can_delete_product',
    ];

    protected $casts = [
        'can_create_product' => 'boolean',
        'can_read_product' => 'boolean',
        'can_update_product' => 'boolean',
        'can_delete_product' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
