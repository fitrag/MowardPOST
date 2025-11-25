@section('header', 'Product Management')

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex-1 w-full sm:max-w-md">
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search products..."
                    class="w-full pl-10 pr-4 py-2.5 border border-zinc-200 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all"
                >
                <svg class="absolute left-3 top-3 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        @if(Auth::user()->canCreateProduct())
            <button wire:click="create" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2.5 rounded-lg transition-colors duration-150 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="create"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24" wire:loading wire:target="create">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="create">Add Product</span>
                <span wire:loading wire:target="create">Loading...</span>
            </button>
        @endif
    </div>

    <!-- Products Table (Desktop) -->
    <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden hidden md:block">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="border-b border-zinc-200 bg-zinc-50/50">
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Product</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">SKU</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Category</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Price</th>
                        <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Cost</th>
                        <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Status</th>
                        <th class="px-6 py-3.5 text-right text-sm font-medium text-zinc-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-zinc-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-zinc-100 flex-shrink-0">
                                        @if($product->image)
                                            <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                        @else
                                            <div class="w-full h-full bg-indigo-600 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <span class="font-medium text-zinc-900">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-zinc-600 font-mono bg-zinc-100 px-2 py-1 rounded">{{ $product->sku ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($product->category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-sm text-zinc-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-zinc-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-zinc-600">{{ $product->cost ? 'Rp ' . number_format($product->cost, 0, ',', '.') : '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($product->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(Auth::user()->canUpdateProduct())
                                        <button wire:click="edit({{ $product->id }})" class="text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 px-3 py-1.5 rounded-md text-sm font-medium transition-colors disabled:opacity-50" wire:loading.attr="disabled" wire:target="edit({{ $product->id }})">
                                            <span wire:loading.remove wire:target="edit({{ $product->id }})">Edit</span>
                                            <span wire:loading wire:target="edit({{ $product->id }})">Loading...</span>
                                        </button>
                                    @endif
                                    @if(Auth::user()->canDeleteProduct())
                                        <button onclick="confirmDelete({{ $product->id }})" class="text-red-600 hover:text-red-700 hover:bg-red-50 px-3 py-1.5 rounded-md text-sm font-medium transition-colors">
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    <h3 class="text-sm font-medium text-zinc-900 mb-1">No products found</h3>
                                    <p class="text-sm text-zinc-500">Add your first product to get started</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="space-y-4 md:hidden">
        @forelse($products as $product)
            <div class="bg-white rounded-xl border border-zinc-200 p-4 shadow-sm">
                <div class="flex items-start gap-4">
                    <!-- Image -->
                    <div class="w-20 h-20 rounded-lg overflow-hidden bg-zinc-100 flex-shrink-0">
                        @if($product->image)
                            <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                        @else
                            <div class="w-full h-full bg-indigo-600 flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold text-zinc-900 truncate pr-2">{{ $product->name }}</h3>
                                <p class="text-sm text-zinc-500 font-mono">{{ $product->sku ?: '-' }}</p>
                            </div>
                            @if($product->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-700">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-zinc-100 text-zinc-700">
                                    Inactive
                                </span>
                            @endif
                        </div>
                        
                        <div class="mt-2 flex items-center gap-2 text-sm">
                            @if($product->category)
                                <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium">
                                    {{ $product->category->name }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="mt-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-zinc-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                @if($product->cost)
                                    <p class="text-xs text-zinc-500">Cost: Rp {{ number_format($product->cost, 0, ',', '.') }}</p>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-2">
                                @if(Auth::user()->canUpdateProduct())
                                    <button wire:click="edit({{ $product->id }})" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                @endif
                                @if(Auth::user()->canDeleteProduct())
                                    <button onclick="confirmDelete({{ $product->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-zinc-200 p-8 text-center">
                <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="text-sm font-medium text-zinc-900 mb-1">No products found</h3>
                <p class="text-sm text-zinc-500">Add your first product to get started</p>
            </div>
        @endforelse
    </div>

    <!-- Load More Button -->
    @if($hasMore)
        <div class="flex justify-center">
            <button 
                wire:click="loadMore"
                class="bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-700 font-medium px-6 py-2.5 rounded-lg transition-colors flex items-center gap-2"
                wire:loading.attr="disabled"
                wire:target="loadMore"
            >
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24" wire:loading wire:target="loadMore">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="loadMore">Load More Products</span>
                <span wire:loading wire:target="loadMore">Loading...</span>
            </button>
        </div>
    @endif

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <!-- Modal Container -->
            <div class="bg-white rounded-xl shadow-2xl w-full h-full md:h-auto md:max-h-[92vh] max-w-6xl overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between flex-shrink-0">
                    <h3 class="text-lg font-semibold text-white">{{ $editMode ? 'Edit Product' : 'Add New Product' }}</h3>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <!-- Form Content -->
                <form wire:submit.prevent="save" class="flex-1 overflow-y-auto">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Left Column - Basic Info -->
                            <div class="md:col-span-2 space-y-5">
                                <div>
                                    <h4 class="text-sm font-semibold text-zinc-900 mb-4">Basic Information</h4>
                                    
                                    <!-- Product Name -->
                                    <div class="relative mb-4">
                                        <input 
                                            type="text" 
                                            wire:model="name" 
                                            id="product_name"
                                            class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                                            placeholder="Product name"
                                        >
                                        <label for="product_name" class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                                            Product Name *
                                        </label>
                                        @error('name') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- SKU -->
                                        <div class="relative">
                                            <input 
                                                type="text" 
                                                wire:model="sku" 
                                                id="product_sku"
                                                class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                                                placeholder="SKU"
                                            >
                                            <label for="product_sku" class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                                                SKU (Optional)
                                            </label>
                                            @error('sku') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <!-- Category -->
                                        <div class="relative">
                                            <select 
                                                wire:model="category_id" 
                                                id="product_category"
                                                class="w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all"
                                            >
                                                <option value="">None</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            <label for="product_category" class="absolute left-4 top-2 text-xs font-medium text-indigo-600">
                                                Category
                                            </label>
                                            @error('category_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-zinc-200 pt-5">
                                    <h4 class="text-sm font-semibold text-zinc-900 mb-4">Pricing</h4>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- Selling Price -->
                                        <div class="relative">
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500 font-medium text-sm pointer-events-none z-10">Rp</div>
                                            <input 
                                                type="text" 
                                                wire:model="price" 
                                                id="product_price"
                                                x-data="{}"
                                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                                class="peer w-full pl-12 pr-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                                                placeholder="Price"
                                            >
                                            <label for="product_price" class="absolute left-12 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                                                Selling Price *
                                            </label>
                                            @error('price') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>

                                        <!-- Cost Price -->
                                        <div class="relative">
                                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500 font-medium text-sm pointer-events-none z-10">Rp</div>
                                            <input 
                                                type="text" 
                                                wire:model="cost" 
                                                id="product_cost"
                                                x-data="{}"
                                                x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                                class="peer w-full pl-12 pr-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                                                placeholder="Cost"
                                            >
                                            <label for="product_cost" class="absolute left-12 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                                                Cost Price
                                            </label>
                                            @error('cost') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-zinc-200 pt-5">
                                    <h4 class="text-sm font-semibold text-zinc-900 mb-4">Product Image</h4>
                                    
                                    <input 
                                        type="file" 
                                        wire:model="image" 
                                        id="product_image"
                                        accept="image/*"
                                        class="w-full px-4 py-3 text-sm text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                    >
                                    @error('image') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Active Status -->
                                <div class="flex items-center gap-2 pt-2">
                                    <input 
                                        type="checkbox" 
                                        wire:model="is_active" 
                                        id="product_active"
                                        class="w-4 h-4 text-indigo-600 border-zinc-300 rounded focus:ring-indigo-600"
                                    >
                                    <label for="product_active" class="text-sm font-medium text-zinc-700">
                                        Product is active
                                    </label>
                                </div>
                            </div>

                            <!-- Right Column - Branch Stock -->
                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-zinc-900 mb-2">Branch Stock</h4>
                                    <p class="text-xs text-zinc-500 mb-4">Select branches and set quantities</p>
                                </div>
                                
                                <div class="space-y-2 max-h-[300px] md:max-h-[500px] overflow-y-auto pr-1">
                                    @foreach($branches as $branch)
                                        <div class="border border-zinc-200 rounded-lg p-3 hover:border-indigo-300 transition-colors bg-zinc-50/50" x-data="{ checked: {{ in_array($branch->id, $selectedBranches) ? 'true' : 'false' }} }">
                                            <div class="flex items-center gap-2 mb-2">
                                                <input 
                                                    type="checkbox" 
                                                    wire:model="selectedBranches" 
                                                    value="{{ $branch->id }}"
                                                    id="branch_{{ $branch->id }}"
                                                    class="w-4 h-4 text-indigo-600 border-zinc-300 rounded focus:ring-indigo-600"
                                                    x-model="checked"
                                                >
                                                <label for="branch_{{ $branch->id }}" class="flex-1 text-sm font-medium text-zinc-900 cursor-pointer">
                                                    {{ $branch->name }}
                                                </label>
                                            </div>
                                            <input 
                                                type="number" 
                                                wire:model="branchStocks.{{ $branch->id }}" 
                                                placeholder="Quantity"
                                                min="0"
                                                :disabled="!checked"
                                                class="w-full px-3 py-2 text-sm text-zinc-900 bg-white border border-zinc-300 rounded-md focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all disabled:bg-zinc-100 disabled:text-zinc-400 disabled:cursor-not-allowed"
                                            >
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="border-t border-zinc-200 px-6 py-4 bg-zinc-50 flex justify-end gap-3 flex-shrink-0">
                        <button 
                            type="button" 
                            wire:click="closeModal" 
                            class="border border-zinc-300 hover:bg-zinc-100 text-zinc-700 font-medium px-5 py-2 rounded-lg transition-colors disabled:opacity-50"
                            wire:loading.attr="disabled"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-5 py-2 rounded-lg transition-colors disabled:opacity-75 flex items-center gap-2"
                            wire:loading.attr="disabled"
                            wire:target="save"
                        >
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24" wire:loading wire:target="save">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="save">{{ $editMode ? 'Update Product' : 'Create Product' }}</span>
                            <span wire:loading wire:target="save">{{ $editMode ? 'Updating...' : 'Creating...' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script>
function confirmDelete(productId) {
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
            @this.call('delete', productId);
        }
    });
}
</script>
