@section('header', 'Stock Management')

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <!-- Local header removed -->
        </div>
        <div class="flex items-center gap-3">
            <!-- Search -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search products..."
                    class="pl-10 pr-4 py-2.5 border border-zinc-200 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all w-64"
                >
                <svg class="absolute left-3 top-3 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            
            <!-- Branch Selector -->
            <div class="bg-white rounded-lg border border-zinc-200 px-4 py-2.5">
                <label class="text-sm font-medium text-zinc-700 mr-3">Branch:</label>
                <select wire:model.live="selectedBranchId" class="border-0 bg-transparent text-zinc-900 font-medium focus:ring-0 pr-8">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50/50">
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Product</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">SKU</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Current Stock</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Status</th>
                    <th class="px-6 py-3.5 text-right text-sm font-medium text-zinc-700">Update Quantity</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-zinc-50 transition-colors duration-150
                            @if($stock->quantity == 0) bg-red-50/30
                            @elseif($stock->quantity < 10) bg-amber-50/30
                            @endif">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-zinc-800">{{ $stock->product->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-zinc-600 font-mono bg-zinc-100 px-2 py-1 rounded">{{ $stock->product->sku ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-2xl font-bold
                                    @if($stock->quantity == 0) text-red-600
                                    @elseif($stock->quantity < 10) text-amber-600
                                    @else text-emerald-600
                                    @endif">
                                    {{ $stock->quantity }}
                                </span>
                                <span class="text-xs text-zinc-500 ml-1">units</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($stock->quantity == 0) bg-red-100 text-red-800
                                    @elseif($stock->quantity < 10) bg-amber-100 text-amber-800
                                    @else bg-emerald-100 text-emerald-800
                                    @endif">
                                    @if($stock->quantity == 0) Out of Stock
                                    @elseif($stock->quantity < 10) Low Stock
                                    @else In Stock
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <div class="relative">
                                        <input 
                                            type="number" 
                                            wire:model.defer="quantities.{{ $stock->id }}" 
                                            class="w-28 px-3 py-2 text-sm text-zinc-800 bg-zinc-50 border-2 border-zinc-200 rounded-lg focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all duration-200 text-center font-semibold" 
                                            placeholder="{{ $stock->quantity }}"
                                            min="0"
                                        >
                                    </div>
                                    <button 
                                        wire:click="updateStock({{ $stock->id }})" 
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors disabled:opacity-75"
                                        wire:loading.attr="disabled"
                                        wire:target="updateStock({{ $stock->id }})"
                                    >
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" wire:loading.remove wire:target="updateStock({{ $stock->id }})"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        <svg class="animate-spin w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" wire:loading wire:target="updateStock({{ $stock->id }})">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="updateStock({{ $stock->id }})">Update</span>
                                        <span wire:loading wire:target="updateStock({{ $stock->id }})">Updating...</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    </div>
                                    <h3 class="text-sm font-medium text-zinc-900 mb-1">No stock records</h3>
                                    <p class="text-sm text-zinc-500">Add products to start managing inventory</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Load More Button -->
        @if($hasMore)
            <div class="mt-4 flex justify-center">
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
    </div>
</div>
