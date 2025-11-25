<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Branch;
use App\Models\UserMenuAccess;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ActivityLogger;

class UserManager extends Component
{
    public $users, $branches;
    public $name, $email, $password, $role, $branch_id;
    public $userId;
    public $editMode = false;
    public $showModal = false;
    public $selectedMenus = [];
    public $availableMenus = [
        'dashboard' => 'Dashboard',
        'pos' => 'Point of Sale',
        'branches' => 'Branches',
        'categories' => 'Categories',
        'products' => 'Products',
        'stock' => 'Inventory',
        'transactions' => 'Transactions',
        'customers' => 'Customers',
        'reports' => 'Reports & Analytics',
        'users' => 'Users',
        'settings' => 'Settings',
    ];

    public function render()
    {
        $this->users = User::with('branch')->get();
        $this->branches = Branch::all();
        return view('livewire.admin.user-manager')->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'cashier';
        $this->branch_id = '';
        $this->userId = null;
        $this->selectedMenus = [];
    }

    public function save()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required',
            'branch_id' => 'nullable|exists:branches,id',
        ];

        if (!$this->userId) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'branch_id' => $this->branch_id ?: null,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);

        // Get old menu access for comparison (if editing)
        $oldMenus = [];
        if ($this->userId) {
            $oldMenus = UserMenuAccess::where('user_id', $user->id)
                ->pluck('menu_key')
                ->toArray();
        }

        // Sync menu access
        UserMenuAccess::where('user_id', $user->id)->delete();
        foreach ($this->selectedMenus as $menuKey) {
            UserMenuAccess::create([
                'user_id' => $user->id,
                'menu_key' => $menuKey,
            ]);
        }

        // Auto-sync product CRUD permissions for cashiers when Products menu is selected
        if ($user->role === 'cashier') {
            $hasProductsMenu = in_array('products', $this->selectedMenus);
            
            // Get or create product permissions
            $permissions = \App\Models\UserProductPermission::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'can_create_product' => false,
                    'can_read_product' => false,
                    'can_update_product' => false,
                    'can_delete_product' => false,
                ]
            );
            
            // If Products menu is checked, enable all CRUD permissions
            if ($hasProductsMenu) {
                $permissions->update([
                    'can_create_product' => true,
                    'can_read_product' => true,
                    'can_update_product' => true,
                    'can_delete_product' => true,
                ]);
            } else {
                // If Products menu is unchecked, disable all CRUD permissions
                $permissions->update([
                    'can_create_product' => false,
                    'can_read_product' => false,
                    'can_update_product' => false,
                    'can_delete_product' => false,
                ]);
            }
        }

        // Log activity
        if ($this->userId) {
            ActivityLogger::logUpdate(
                User::class,
                $user->id,
                $user->name
            );
            
            // Log menu access changes if there are differences
            $addedMenus = array_diff($this->selectedMenus, $oldMenus);
            $removedMenus = array_diff($oldMenus, $this->selectedMenus);
            
            if (!empty($addedMenus) || !empty($removedMenus)) {
                $menuChanges = [];
                if (!empty($addedMenus)) {
                    $menuChanges[] = 'Added: ' . implode(', ', array_map(function($key) {
                        return $this->availableMenus[$key] ?? $key;
                    }, $addedMenus));
                }
                if (!empty($removedMenus)) {
                    $menuChanges[] = 'Removed: ' . implode(', ', array_map(function($key) {
                        return $this->availableMenus[$key] ?? $key;
                    }, $removedMenus));
                }
                
                ActivityLogger::log(
                    'updated',
                    auth()->user()->name . ' changed menu access for ' . $user->name . ': ' . implode('; ', $menuChanges),
                    User::class,
                    $user->id,
                    [
                        'old_menus' => $oldMenus,
                        'new_menus' => $this->selectedMenus,
                        'added' => $addedMenus,
                        'removed' => $removedMenus
                    ]
                );
            }
        } else {
            ActivityLogger::logCreate(
                User::class,
                $user->id,
                $user->name
            );
            
            // Log initial menu access for new users
            if (!empty($this->selectedMenus)) {
                $menuNames = array_map(function($key) {
                    return $this->availableMenus[$key] ?? $key;
                }, $this->selectedMenus);
                
                ActivityLogger::log(
                    'created',
                    auth()->user()->name . ' granted menu access to ' . $user->name . ': ' . implode(', ', $menuNames),
                    User::class,
                    $user->id,
                    [
                        'menus' => $this->selectedMenus
                    ]
                );
            }
        }

        $this->dispatch('success', $this->userId ? 'User updated successfully!' : 'User created successfully!');

        $this->closeModal();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->branch_id = $user->branch_id;
        $this->password = '';
        $this->selectedMenus = $user->menuAccess()->pluck('menu_key')->toArray();
        $this->editMode = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        $user = User::find($id);
        $userName = $user->name;
        
        $user->delete();
        
        // Log activity
        ActivityLogger::logDelete(
            User::class,
            $id,
            $userName
        );
        
        $this->dispatch('success', 'User deleted successfully!');
    }
}
