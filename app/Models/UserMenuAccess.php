<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMenuAccess extends Model
{
    protected $table = 'user_menu_access';
    
    protected $fillable = [
        'user_id',
        'menu_key',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
