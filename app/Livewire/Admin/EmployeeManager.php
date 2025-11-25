<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\UserProductPermission;
use Livewire\Component;

class EmployeeManager extends Component
{
    public $employees;

    public function mount()
    {
        $this->loadEmployees();
    }

    public function loadEmployees()
    {
        $user = auth()->user();
        
        // Only managers can access this
        if (!$user->hasRole('manager')) {
            abort(403, 'Unauthorized');
        }

        // Get employees (cashiers) in the manager's branch
        $this->employees = User::with('productPermissions')
            ->where('branch_id', $user->branch_id)
            ->where('role', 'cashier')
            ->get();
    }

    public function togglePermission($userId, $permission)
    {
        $user = User::findOrFail($userId);
        
        // Ensure the employee is in the manager's branch
        if ($user->branch_id !== auth()->user()->branch_id) {
            $this->dispatch('error', 'You can only manage employees in your branch.');
            return;
        }

        // Get or create permissions record
        $permissions = UserProductPermission::firstOrCreate(
            ['user_id' => $userId],
            [
                'can_create_product' => false,
                'can_read_product' => false,
                'can_update_product' => false,
                'can_delete_product' => false,
            ]
        );

        // Toggle the permission
        $permissions->$permission = !$permissions->$permission;
        $permissions->save();

        // Auto-uncheck Products menu if all CRUD permissions are disabled
        $allDisabled = !$permissions->can_create_product && 
                       !$permissions->can_read_product && 
                       !$permissions->can_update_product && 
                       !$permissions->can_delete_product;
        
        if ($allDisabled) {
            // Remove Products menu access
            \App\Models\UserMenuAccess::where('user_id', $userId)
                ->where('menu_key', 'products')
                ->delete();
        } else {
            // Ensure Products menu access exists if at least one permission is enabled
            \App\Models\UserMenuAccess::firstOrCreate([
                'user_id' => $userId,
                'menu_key' => 'products'
            ]);
        }

        $this->dispatch('success', 'Permission updated successfully!');
        $this->loadEmployees();
    }

    public function render()
    {
        return view('livewire.admin.employee-manager')->layout('layouts.app');
    }
}
