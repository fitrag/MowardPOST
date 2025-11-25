<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'branch_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function menuAccess()
    {
        return $this->hasMany(UserMenuAccess::class);
    }

    public function productPermissions()
    {
        return $this->hasOne(UserProductPermission::class);
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasMenuAccess($menuKey)
    {
        // Owners have access to all menus
        if ($this->hasRole('owner')) {
            return true;
        }

        return $this->menuAccess()->where('menu_key', $menuKey)->exists();
    }

    public function canCreateProduct()
    {
        // Owners and managers have full access
        if ($this->hasRole('owner') || $this->hasRole('manager')) {
            return true;
        }

        return $this->productPermissions?->can_create_product ?? false;
    }

    public function canReadProduct()
    {
        // Owners and managers have full access
        if ($this->hasRole('owner') || $this->hasRole('manager')) {
            return true;
        }

        return $this->productPermissions?->can_read_product ?? false;
    }

    public function canUpdateProduct()
    {
        // Owners and managers have full access
        if ($this->hasRole('owner') || $this->hasRole('manager')) {
            return true;
        }

        return $this->productPermissions?->can_update_product ?? false;
    }

    public function canDeleteProduct()
    {
        // Owners and managers have full access
        if ($this->hasRole('owner') || $this->hasRole('manager')) {
            return true;
        }

        return $this->productPermissions?->can_delete_product ?? false;
    }
}
