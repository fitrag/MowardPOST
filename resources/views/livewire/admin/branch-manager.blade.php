@section('header', 'Branch Management')

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
            <span wire:loading.remove wire:target="create">Add Branch</span>
            <span wire:loading wire:target="create">Loading...</span>
        </button>
    </div>

    <!-- Branches Table -->
    <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50/50">
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Branch Name</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Address</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Phone</th>
                    <th class="px-6 py-3.5 text-right text-sm font-medium text-zinc-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($branches as $branch)
                    <tr class="hover:bg-zinc-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-medium text-sm">
                                    {{ strtoupper(substr($branch->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-zinc-900">{{ $branch->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm text-zinc-600">
                                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $branch->address ?: '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm text-zinc-600">
                                <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $branch->phone ?: '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="edit({{ $branch->id }})" class="text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 px-3 py-1.5 rounded-md text-sm font-medium transition-colors disabled:opacity-50" wire:loading.attr="disabled" wire:target="edit({{ $branch->id }})">
                                    <span wire:loading.remove wire:target="edit({{ $branch->id }})">Edit</span>
                                    <span wire:loading wire:target="edit({{ $branch->id }})">Loading...</span>
                                </button>
                                <button onclick="confirmDeleteBranch({{ $branch->id }})" class="text-red-600 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <h3 class="text-sm font-medium text-zinc-900 mb-1">No branches found</h3>
                                <p class="text-sm text-zinc-500">Get started by creating your first branch</p>
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
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden">
                <!-- Header -->
                <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">{{ $editMode ? 'Edit Branch' : 'Add New Branch' }}</h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <!-- Form -->
                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    <!-- Branch Name -->
                    <div class="relative">
                        <input 
                            type="text" 
                            wire:model="name" 
                            id="branch_name"
                            class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                            placeholder="Branch name"
                        >
                        <label 
                            for="branch_name"
                            class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600"
                        >
                            Branch Name *
                        </label>
                        @error('name') 
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="relative">
                        <textarea 
                            wire:model="address" 
                            id="branch_address"
                            rows="3"
                            class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent resize-none"
                            placeholder="Address"
                        ></textarea>
                        <label 
                            for="branch_address"
                            class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600"
                        >
                            Full Address (Optional)
                        </label>
                        @error('address') 
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="relative">
                        <input 
                            type="text" 
                            wire:model="phone" 
                            id="branch_phone"
                            class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                            placeholder="Phone"
                        >
                        <label 
                            for="branch_phone"
                            class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600"
                        >
                            Phone Number (Optional)
                        </label>
                        @error('phone') 
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-2">
                        <button 
                            type="button" 
                            wire:click="closeModal" 
                            class="flex-1 border border-zinc-300 hover:bg-zinc-50 text-zinc-700 font-medium px-4 py-2.5 rounded-lg transition-colors disabled:opacity-50"
                            wire:loading.attr="disabled"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2.5 rounded-lg transition-colors disabled:opacity-75 flex items-center justify-center gap-2"
                            wire:loading.attr="disabled"
                            wire:target="save"
                        >
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24" wire:loading wire:target="save">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="save">{{ $editMode ? 'Update Branch' : 'Create Branch' }}</span>
                            <span wire:loading wire:target="save">{{ $editMode ? 'Updating...' : 'Creating...' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
function confirmDeleteBranch(branchId) {
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
            @this.call('delete', branchId);
        }
    });
}
</script>
