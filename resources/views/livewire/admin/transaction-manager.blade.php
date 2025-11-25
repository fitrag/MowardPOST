@section('header', 'Transactions')

<div class="space-y-6">
    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-lg border border-zinc-200 space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <!-- Date Range -->
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input 
                        type="date" 
                        wire:model.live="dateStart"
                        class="pl-3 pr-2 py-2 rounded-lg border-zinc-200 bg-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all text-sm w-36"
                        placeholder="Start Date"
                    >
                </div>
                <span class="text-zinc-400">-</span>
                <div class="relative">
                    <input 
                        type="date" 
                        wire:model.live="dateEnd"
                        class="pl-3 pr-2 py-2 rounded-lg border-zinc-200 bg-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all text-sm w-36"
                        placeholder="End Date"
                    >
                </div>
            </div>

            <!-- Branch Filter (Owner Only) -->
            @if(auth()->user()->hasRole('owner'))
                <div class="w-48">
                    <select 
                        wire:model.live="filterBranch"
                        class="w-full py-2 rounded-lg border-zinc-200 bg-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all text-sm"
                    >
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Reset Filters -->
            @if($dateStart || $dateEnd || $filterBranch || $search)
                <button 
                    wire:click="resetFilters"
                    class="text-sm text-red-600 hover:text-red-700 font-medium transition-colors flex items-center gap-1"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Reset
                </button>
            @endif
        </div>

        <!-- Search -->
        <div class="relative w-full md:w-64">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                class="w-full pl-10 pr-4 py-2 rounded-lg border-zinc-200 bg-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all placeholder-zinc-400 text-sm"
                placeholder="Search transactions..."
            >
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50/50">
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Transaction ID</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Date</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Branch</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Cashier</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Items</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Total</th>
                    <th class="px-6 py-3.5 text-center text-sm font-medium text-zinc-700">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($transactions as $transaction)
                    <tr wire:key="{{ $transaction->id }}" wire:dblclick="showDetails({{ $transaction->id }})" class="hover:bg-zinc-50/50 transition-colors cursor-pointer">
                        <td class="px-6 py-4">
                            <span class="text-sm font-mono text-zinc-600">#{{ $transaction->id }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-zinc-900">{{ $transaction->created_at->format('M d, Y') }}</span>
                                <span class="text-xs text-zinc-500">{{ $transaction->created_at->format('H:i') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                {{ $transaction->branch->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-zinc-100 flex items-center justify-center text-xs font-medium text-zinc-600">
                                    {{ substr($transaction->user->name ?? 'U', 0, 1) }}
                                </div>
                                <span class="text-sm text-zinc-700">{{ $transaction->user->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-zinc-600">{{ $transaction->items->sum('quantity') }} items</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-zinc-900">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 capitalize">
                                {{ $transaction->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                </div>
                                <h3 class="text-sm font-medium text-zinc-900 mb-1">No transactions found</h3>
                                <p class="text-sm text-zinc-500">Transactions will appear here once sales are made</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Load More Button -->
    @if($hasMore)
        <div class="flex justify-center mt-6">
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
                <span wire:loading.remove wire:target="loadMore">Load More Transactions</span>
                <span wire:loading wire:target="loadMore">Loading...</span>
            </button>
        </div>
    @endif

    <!-- Transaction Detail Modal -->
    @if($showModal && $selectedTransaction)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between flex-shrink-0">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Transaction Details</h3>
                        <p class="text-indigo-100 text-sm">#{{ $selectedTransaction->id }}</p>
                    </div>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div>
                            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider mb-1">Date & Time</p>
                            <p class="text-zinc-900 font-medium">{{ $selectedTransaction->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider mb-1">Branch</p>
                            <p class="text-zinc-900 font-medium">{{ $selectedTransaction->branch->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider mb-1">Cashier</p>
                            <p class="text-zinc-900 font-medium">{{ $selectedTransaction->user->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider mb-1">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 capitalize">
                                {{ $selectedTransaction->status }}
                            </span>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="border border-zinc-200 rounded-lg overflow-hidden mb-6">
                        <table class="w-full text-sm">
                            <thead class="bg-zinc-50 border-b border-zinc-200">
                                <tr>
                                    <th class="px-4 py-3 text-left font-medium text-zinc-700">Item</th>
                                    <th class="px-4 py-3 text-right font-medium text-zinc-700">Price</th>
                                    <th class="px-4 py-3 text-center font-medium text-zinc-700">Qty</th>
                                    <th class="px-4 py-3 text-right font-medium text-zinc-700">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($selectedTransaction->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-zinc-900">{{ $item->product->name ?? 'Unknown Product' }}</td>
                                        <td class="px-4 py-3 text-right text-zinc-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center text-zinc-600">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-zinc-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-zinc-50 border-t border-zinc-200">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-right font-medium text-zinc-600">Total Amount</td>
                                    <td class="px-4 py-3 text-right font-bold text-zinc-900 text-lg">Rp {{ number_format($selectedTransaction->total, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm text-zinc-500">Cash Paid</td>
                                    <td class="px-4 py-2 text-right text-sm font-medium text-zinc-900">Rp {{ number_format($selectedTransaction->cash, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm text-zinc-500">Change</td>
                                    <td class="px-4 py-2 text-right text-sm font-medium text-zinc-900">Rp {{ number_format($selectedTransaction->change, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-zinc-50 px-6 py-4 border-t border-zinc-200 flex justify-end">
                    <button wire:click="closeModal" class="bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-700 font-medium px-4 py-2 rounded-lg transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
