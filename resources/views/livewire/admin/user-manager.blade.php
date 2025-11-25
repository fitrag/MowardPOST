@section('header', 'User Management')

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <!-- Local header removed -->
        </div>
        <button wire:click="create" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg transition-colors duration-150 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="create"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24" wire:loading wire:target="create">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span wire:loading.remove wire:target="create">Add User</span>
            <span wire:loading wire:target="create">Loading...</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50/50">
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Name</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Email</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Role</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Branch</th>
                    <th class="px-6 py-3.5 text-right text-sm font-medium text-zinc-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($users as $user)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-medium text-sm">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-zinc-900">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-600">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($user->role === 'owner') bg-purple-100 text-purple-700
                                @elseif($user->role === 'manager') bg-blue-100 text-blue-700
                                @else bg-emerald-100 text-emerald-700
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm text-zinc-600">
                                @if($user->branch)
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    {{ $user->branch->name }}
                                @else
                                    <span class="text-zinc-400">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="edit({{ $user->id }})" class="text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 px-3 py-1.5 rounded-md text-sm font-medium transition-colors disabled:opacity-50" wire:loading.attr="disabled" wire:target="edit({{ $user->id }})">
                                    <span wire:loading.remove wire:target="edit({{ $user->id }})">Edit</span>
                                    <span wire:loading wire:target="edit({{ $user->id }})">Loading...</span>
                                </button>
                                <button onclick="confirmDeleteUser({{ $user->id }})" class="text-red-600 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                </div>
                                <h3 class="text-sm font-medium text-zinc-900 mb-1">No users found</h3>
                                <p class="text-sm text-zinc-500">Add your first user to get started</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">{{ $editMode ? 'Edit User' : 'Add New User' }}</h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <!-- Form -->
                <form wire:submit.prevent="save" class="p-6 space-y-6 overflow-y-auto flex-1">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <input 
                                type="text" 
                                wire:model="name" 
                                id="user_name"
                                class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                                placeholder="Name"
                            >
                            <label for="user_name" class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                                Full Name *
                            </label>
                            @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="relative">
                            <input 
                                type="email" 
                                wire:model="email" 
                                id="user_email"
                                class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                                placeholder="Email"
                            >
                            <label for="user_email" class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                                Email Address *
                            </label>
                            @error('email') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="relative">
                            <input 
                                type="password" 
                                wire:model="password" 
                                id="user_password"
                                class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                                placeholder="Password"
                            >
                            <label for="user_password" class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                                Password {{ $editMode ? '(leave blank to keep current)' : '*' }}
                            </label>
                            @error('password') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="relative">
                            <select 
                                wire:model="role" 
                                id="user_role"
                                class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all appearance-none"
                            >
                                <option value="" disabled selected class="text-zinc-400">Select Role</option>
                                <option value="owner">Owner</option>
                                <option value="manager">Manager</option>
                                <option value="cashier">Cashier</option>
                            </select>
                            <label for="user_role" class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                                Role *
                            </label>
                            @error('role') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="relative">
                        <select 
                            wire:model="branch_id" 
                            id="user_branch"
                            class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all appearance-none"
                        >
                            <option value="" selected>Select Branch (Optional)</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <label for="user_branch" class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                            Branch
                        </label>
                        @error('branch_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Menu Access Permissions -->
                    <div class="border-t border-zinc-200 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="text-sm font-semibold text-zinc-900">Menu Access Permissions</h4>
                                <p class="text-xs text-zinc-500 mt-0.5">Select which menus this user can access</p>
                            </div>
                            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full font-medium">{{ count($selectedMenus) }}/{{ count($availableMenus) }} selected</span>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($availableMenus as $key => $label)
                                <label class="relative flex items-center gap-3 p-3 rounded-lg border-2 transition-all cursor-pointer group {{ in_array($key, $selectedMenus) ? 'border-indigo-600 bg-indigo-50' : 'border-zinc-200 bg-white hover:border-indigo-300 hover:bg-indigo-50/50' }}">
                                    <input 
                                        type="checkbox" 
                                        wire:model="selectedMenus" 
                                        value="{{ $key }}"
                                        class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-zinc-300 rounded transition-colors"
                                    >
                                    <div class="flex-1 min-w-0">
                                        <span class="text-sm font-medium {{ in_array($key, $selectedMenus) ? 'text-indigo-900' : 'text-zinc-700' }}">{{ $label }}</span>
                                    </div>
                                    @if(in_array($key, $selectedMenus))
                                        <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <p class="text-xs text-blue-700 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Owners have access to all menus by default, regardless of selections
                            </p>
                        </div>
                    </div>
                </form>
                
                <!-- Footer -->
                <div class="bg-zinc-50 px-6 py-4 border-t border-zinc-200 flex gap-3 flex-shrink-0">
                    <button 
                        type="button" 
                        wire:click="closeModal" 
                        class="flex-1 border border-zinc-300 hover:bg-zinc-50 bg-white text-zinc-700 font-medium px-4 py-2.5 rounded-lg transition-colors disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="save"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-lg transition-colors disabled:opacity-75 flex items-center justify-center gap-2"
                        wire:loading.attr="disabled"
                        wire:target="save"
                    >
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24" wire:loading wire:target="save">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="save">{{ $editMode ? 'Update User' : 'Create User' }}</span>
                        <span wire:loading wire:target="save">{{ $editMode ? 'Updating...' : 'Creating...' }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function confirmDeleteUser(userId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            @this.call('delete', userId);
        }
    });
}
</script>
