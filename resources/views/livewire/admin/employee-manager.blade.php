@section('header', 'Employee Management')

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-zinc-500">Manage product permissions for employees in your branch</p>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50/50">
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Employee</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Email</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Create</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Read</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Update</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Delete</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($employees as $employee)
                    <tr class="hover:bg-zinc-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="font-semibold text-zinc-800">{{ $employee->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-600">{{ $employee->email }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label class="inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:click="togglePermission({{ $employee->id }}, 'can_create_product')"
                                    {{ $employee->productPermissions?->can_create_product ? 'checked' : '' }}
                                    class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-zinc-300 rounded transition-colors cursor-pointer"
                                >
                            </label>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label class="inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:click="togglePermission({{ $employee->id }}, 'can_read_product')"
                                    {{ $employee->productPermissions?->can_read_product ? 'checked' : '' }}
                                    class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-zinc-300 rounded transition-colors cursor-pointer"
                                >
                            </label>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label class="inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:click="togglePermission({{ $employee->id }}, 'can_update_product')"
                                    {{ $employee->productPermissions?->can_update_product ? 'checked' : '' }}
                                    class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-zinc-300 rounded transition-colors cursor-pointer"
                                >
                            </label>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <label class="inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:click="togglePermission({{ $employee->id }}, 'can_delete_product')"
                                    {{ $employee->productPermissions?->can_delete_product ? 'checked' : '' }}
                                    class="w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-zinc-300 rounded transition-colors cursor-pointer"
                                >
                            </label>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-medium text-zinc-900 mb-1">No employees found</h3>
                                <p class="text-sm text-zinc-500">There are no cashiers assigned to your branch</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-blue-900 mb-1">Product Permissions</h4>
                <p class="text-sm text-blue-700">
                    Grant or revoke product management permissions for employees in your branch. 
                    <strong>Create</strong> allows adding new products, 
                    <strong>Read</strong> allows viewing products, 
                    <strong>Update</strong> allows editing products, and 
                    <strong>Delete</strong> allows removing products.
                </p>
            </div>
        </div>
    </div>
</div>
