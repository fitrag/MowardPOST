<div>
    <style>
        /* Custom Scrollbar Styling */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f4f4f5;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #818cf8 0%, #6366f1 100%);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #6366f1 0%, #4f46e5 100%);
        }

        /* Firefox */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #818cf8 #f4f4f5;
        }
    </style>

    <div class="h-[calc(100vh-8rem)]">
        <livewire:cashier.product-update-checker :last-loaded-at="$lastLoadedAt" />

        <div class="flex gap-6 h-full">
            <!-- Left Panel - Products -->
            <div class="flex-1 bg-white rounded-2xl shadow-sm border border-zinc-100 flex flex-col" 
                x-data="{ 
                    columnCount: parseInt(localStorage.getItem('posColumns')) || 4,
                    setColumns(count) {
                        this.columnCount = count;
                        localStorage.setItem('posColumns', count);
                    }
                }"
            >
                <!-- Search & Filter -->
                <div class="p-6 border-b border-zinc-100">
                    <div class="flex gap-4 items-center">
                        <!-- Desktop Sidebar Toggle -->
                        <button @click="sidebarMinimized = !sidebarMinimized" class="hidden lg:block text-zinc-500 hover:bg-zinc-100 p-2 rounded-lg transition-colors flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="search" 
                                placeholder="Search products..." 
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl border-zinc-200 bg-zinc-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all placeholder-zinc-400"
                            >
                        </div>
                        <div class="w-48">
                            <select 
                                wire:model.live="selectedCategory" 
                                class="w-full py-2.5 rounded-xl border-zinc-200 bg-zinc-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all text-zinc-600"
                            >
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-40">
                            <select 
                                wire:model.live="stockFilter" 
                                class="w-full py-2.5 rounded-xl border-zinc-200 bg-zinc-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all text-zinc-600"
                            >
                                <option value="all">All Stock</option>
                                <option value="in_stock">In Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                        <!-- Column Switcher -->
                        <div class="flex bg-zinc-100 rounded-xl p-1 border border-zinc-200">
                            <button @click="setColumns(4)" :class="columnCount === 4 ? 'bg-white text-indigo-600 shadow-sm' : 'text-zinc-400 hover:text-zinc-600'" class="p-1.5 rounded-lg transition-all" title="4 Columns">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            </button>
                            <button @click="setColumns(5)" :class="columnCount === 5 ? 'bg-white text-indigo-600 shadow-sm' : 'text-zinc-400 hover:text-zinc-600'" class="p-1.5 rounded-lg transition-all" title="5 Columns">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                            </button>
                            <button @click="setColumns(6)" :class="columnCount === 6 ? 'bg-white text-indigo-600 shadow-sm' : 'text-zinc-400 hover:text-zinc-600'" class="p-1.5 rounded-lg transition-all" title="6 Columns">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="flex-1 overflow-y-auto p-6 relative custom-scrollbar">
                    <!-- Global Loading Overlay for Search/Filter -->
                    <div wire:loading.flex wire:target="search, selectedCategory, stockFilter, loadProducts" class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 items-center justify-center hidden">
                        <div class="flex flex-col items-center">
                            <svg class="animate-spin h-10 w-10 text-indigo-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm font-medium text-indigo-600">Loading products...</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4"
                        :class="{
                            'xl:grid-cols-4': columnCount === 4,
                            'xl:grid-cols-5': columnCount === 5,
                            'xl:grid-cols-6': columnCount === 6
                        }"
                    >
                        @foreach($products as $product)
                            @php
                                $stock = $product->stocks->first();
                                $quantity = $stock ? $stock->quantity : 0;
                                $isOutOfStock = $quantity <= 0;
                            @endphp
                            <button 
                                wire:click="addToCart({{ $product->id }})" 
                                @if($isOutOfStock) disabled @endif
                                class="group bg-white border border-zinc-200 rounded-xl p-3 hover:border-indigo-500 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 text-left flex flex-col h-full {{ $isOutOfStock ? 'opacity-60 cursor-not-allowed grayscale' : '' }} relative overflow-hidden"
                            >
                                <!-- Loading Overlay for Specific Product -->
                                <div wire:loading.flex wire:target="addToCart({{ $product->id }})" class="absolute inset-0 bg-white/60 backdrop-blur-[1px] z-10 items-center justify-center hidden">
                                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                                <div class="relative aspect-square mb-3 overflow-hidden rounded-lg bg-zinc-100">
                                    @if($product->image)
                                        <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-zinc-300">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    
                                    @if($isOutOfStock)
                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                            <span class="text-white font-bold text-sm px-2 py-1 bg-red-500 rounded-md">Out of Stock</span>
                                        </div>
                                    @else
                                        <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-md text-xs font-medium text-zinc-600 shadow-sm">
                                            Stock: {{ $quantity }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 flex flex-col">
                                    <h3 class="font-medium text-zinc-900 text-sm mb-1 line-clamp-2 group-hover:text-indigo-600 transition-colors">{{ $product->name }}</h3>
                                    <p class="text-xs text-zinc-500 mb-2 font-mono">{{ $product->sku }}</p>
                                    <div class="mt-auto flex items-center justify-between">
                                        <p class="text-sm font-bold text-zinc-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        @if(!$isOutOfStock)
                                            <div class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                    
                    @if($products->count() < $totalCount)
                        <div class="mt-6 text-center">
                            <button 
                                wire:click="loadMore" 
                                wire:loading.attr="disabled"
                                class="bg-white border border-zinc-200 text-zinc-600 hover:text-indigo-600 hover:border-indigo-600 px-6 py-2.5 rounded-xl font-medium transition-all shadow-sm hover:shadow-md flex items-center justify-center gap-2 mx-auto"
                            >
                                <span wire:loading.remove wire:target="loadMore">Load More Products</span>
                                <span wire:loading wire:target="loadMore" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Loading...
                                </span>
                            </button>
                            <p class="text-xs text-zinc-400 mt-2">Showing {{ $products->count() }} of {{ $totalCount }} products</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Panel - Cart & Checkout -->
            <div class="w-96 bg-white rounded-2xl shadow-sm border border-zinc-100 flex flex-col">
                <!-- Cart Header -->
                <div class="p-6 border-b border-zinc-100">
                    <h2 class="text-lg font-semibold text-zinc-800">Current Order</h2>
                </div>

                <!-- Customer Selection -->
                <div class="px-6 pt-4 pb-2 border-b border-zinc-100">
                    @if($selectedCustomer)
                        <!-- Selected Customer -->
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-3 relative">
                            <button wire:click="clearCustomer" class="absolute top-2 right-2 text-zinc-400 hover:text-zinc-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-zinc-900">{{ $selectedCustomer->name }}</p>
                                    <p class="text-xs text-zinc-600">{{ $selectedCustomer->tier_badge }}</p>
                                    <p class="text-xs text-indigo-600 font-medium">{{ number_format($selectedCustomer->total_points) }} points</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Customer Search -->
                        <div class="relative">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="customerSearch"
                                placeholder="Search customer..."
                                class="w-full pl-10 pr-4 py-2 rounded-lg border-zinc-200 bg-zinc-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all placeholder-zinc-400 text-sm"
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            
                            <!-- Customer Search Results -->
                            @if($customerSearch && count($customers) > 0)
                                <div class="absolute z-10 w-full mt-1 bg-white border border-zinc-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                                    @foreach($customers as $customer)
                                        <button 
                                            wire:click="selectCustomer({{ $customer->id }})"
                                            class="w-full px-3 py-2 text-left hover:bg-zinc-50 flex items-center gap-2 border-b border-zinc-100 last:border-0"
                                        >
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-zinc-900">{{ $customer->name }}</p>
                                                <p class="text-xs text-zinc-500">{{ $customer->phone }} • {{ $customer->tier_badge }}</p>
                                            </div>
                                            <span class="text-xs text-indigo-600">{{ number_format($customer->total_points) }} pts</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-6 space-y-3 relative custom-scrollbar">
                    <!-- Cart Loading Overlay -->
                    <div wire:loading.flex wire:target="updateQuantity, removeFromCart" class="absolute inset-0 bg-white/50 z-20 items-center justify-center hidden">
                        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    @forelse($cart as $item)
                        <div class="flex items-center gap-3 p-3 bg-zinc-50 rounded-lg">
                            @if($item['image'])
                                <img src="{{ asset('storage/'.$item['image']) }}" class="w-12 h-12 object-cover rounded">
                            @else
                                <div class="w-12 h-12 bg-zinc-200 rounded flex items-center justify-center">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-zinc-900 truncate">{{ $item['name'] }}</h4>
                                <p class="text-xs text-zinc-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" class="w-6 h-6 rounded bg-zinc-200 hover:bg-zinc-300 flex items-center justify-center">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                </button>
                                <span class="w-8 text-center text-sm font-medium">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" class="w-6 h-6 rounded bg-zinc-200 hover:bg-zinc-300 flex items-center justify-center">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                </button>
                            </div>
                            <button wire:click="removeFromCart({{ $item['id'] }})" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-12 text-zinc-400">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <p class="text-sm">Cart is empty</p>
                        </div>
                    @endforelse
                </div>

                <!-- Checkout Section -->
                <div class="p-6 border-t border-zinc-100 space-y-4">
                    <!-- Points Redemption (if customer selected) -->
                    @if($selectedCustomer && $selectedCustomer->total_points > 0)
                        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg p-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        wire:model.live="usePoints"
                                        class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500"
                                    >
                                    <span class="text-sm font-medium text-emerald-900">Use Points</span>
                                </label>
                                <span class="text-xs text-emerald-700">Available: {{ number_format($selectedCustomer->total_points) }} pts</span>
                            </div>
                            
                            @if($usePoints)
                                <div>
                                    <label class="block text-xs font-medium text-emerald-700 mb-1">Points to Use</label>
                                    <input 
                                        type="number" 
                                        wire:model.live="pointsToUse"
                                        min="0"
                                        max="{{ $selectedCustomer->total_points }}"
                                        class="w-full px-3 py-1.5 text-sm rounded border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500/20"
                                        placeholder="Enter points (100 pts = Rp 10,000)"
                                    >
                                    <p class="text-xs text-emerald-600 mt-1">Max 50% of total • 100 pts = Rp 10,000</p>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Totals -->
                    <div class="space-y-2 text-sm relative">
                        <div wire:loading.flex wire:target="updatedCash, calculateChange" class="absolute inset-0 bg-white/50 z-10 items-center justify-center hidden">
                            <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <div class="flex justify-between text-zinc-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-zinc-600">
                            <span>Tax ({{ \App\Models\Setting::getValue('tax_rate', 10) }}%)</span>
                            <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($pointsDiscount > 0)
                            <div class="flex justify-between text-emerald-600 font-medium">
                                <span>Points Discount (-{{ $pointsToUse }} pts)</span>
                                <span>- Rp {{ number_format($pointsDiscount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        
                        <div class="flex justify-between text-lg font-bold text-zinc-900 pt-2 border-t border-zinc-200">
                            <span>Total</span>
                            <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($selectedCustomer && $pointsEarned > 0)
                            <div class="flex justify-between text-xs text-indigo-600 bg-indigo-50 px-2 py-1.5 rounded">
                                <span>🎁 Points to Earn</span>
                                <span class="font-semibold">+{{ $pointsEarned }} pts</span>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Input -->
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 mb-1">Cash Received</label>
                        <div class="relative" x-data="{ 
                            inputValue: '',
                            format(value) {
                                if (!value) return '';
                                return value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                            }
                        }"
                        @transaction-completed.window="inputValue = ''"
                        >
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <span class="text-zinc-500 font-medium">Rp</span>
                            </div>
                            <input 
                                type="text" 
                                x-model="inputValue"
                                x-on:input="inputValue = format($el.value); $wire.set('cash', inputValue.replace(/\./g, ''))"
                                class="w-full rounded-xl border-zinc-200 pl-12 py-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500/20 text-lg font-medium" 
                                placeholder="0"
                            >
                        </div>
                    </div>

                    <!-- Change -->
                    @if($change > 0)
                        <div class="bg-emerald-50 rounded-lg p-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-emerald-700">Change</span>
                                <span class="text-lg font-bold text-emerald-700">Rp {{ number_format($change, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Complete Button -->
                    <button 
                        wire:click="completeTransaction" 
                        @if(empty($cart) || $cash < $total) disabled @endif 
                        class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-zinc-300 disabled:cursor-not-allowed text-white font-medium py-3 px-4 rounded-xl transition-colors flex items-center justify-center gap-2"
                        wire:loading.attr="disabled"
                        wire:target="completeTransaction"
                    >
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24" wire:loading wire:target="completeTransaction">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="completeTransaction">Complete Transaction</span>
                        <span wire:loading wire:target="completeTransaction">Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
