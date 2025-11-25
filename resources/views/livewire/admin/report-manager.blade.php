@section('header', 'Reports & Analytics')

<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg border border-zinc-200 space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <!-- Date Range -->
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input 
                        type="date" 
                        wire:model.live="dateStart"
                        class="pl-3 pr-2 py-2 rounded-lg border-zinc-200 bg-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all text-sm w-36"
                    >
                </div>
                <span class="text-zinc-400">-</span>
                <div class="relative">
                    <input 
                        type="date" 
                        wire:model.live="dateEnd"
                        class="pl-3 pr-2 py-2 rounded-lg border-zinc-200 bg-white focus:border-indigo-500 focus:ring-indigo-500/20 transition-all text-sm w-36"
                    >
                </div>
            </div>

            <!-- Branch Filter (Owner Only) -->
            @if(auth()->user()->hasRole('owner'))
                <div class="bg-white rounded-lg border border-zinc-200 px-4 py-2">
                    <select 
                        wire:model.live="filterBranch"
                        class="border-0 bg-transparent text-zinc-900 font-medium focus:ring-0 pr-8 py-0 text-sm"
                    >
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Report Type -->
            <div class="bg-zinc-100 p-1 rounded-lg flex items-center">
                <button 
                    wire:click="$set('reportType', 'daily')"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-all {{ $reportType === 'daily' ? 'bg-white text-indigo-600 shadow-sm' : 'text-zinc-500 hover:text-zinc-700' }}"
                >
                    Daily
                </button>
                <button 
                    wire:click="$set('reportType', 'monthly')"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-all {{ $reportType === 'monthly' ? 'bg-white text-indigo-600 shadow-sm' : 'text-zinc-500 hover:text-zinc-700' }}"
                >
                    Monthly
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
        <!-- Total Revenue -->
        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-indigo-50 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-medium text-zinc-500 bg-zinc-50 px-2 py-1 rounded-full">Revenue</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
            <p class="text-sm text-zinc-500 mt-1">Total earnings</p>
        </div>

        <!-- Total Profit -->
        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-medium text-zinc-500 bg-zinc-50 px-2 py-1 rounded-full">Profit</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">Rp {{ number_format($stats['total_profit'], 0, ',', '.') }}</h3>
            <p class="text-sm text-zinc-500 mt-1">Net profit</p>
        </div>

        <!-- Net Margin -->
        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <span class="text-xs font-medium text-zinc-500 bg-zinc-50 px-2 py-1 rounded-full">Margin</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">{{ number_format($stats['margin'], 1) }}%</h3>
            <p class="text-sm text-zinc-500 mt-1">Profit margin</p>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-50 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
                <span class="text-xs font-medium text-zinc-500 bg-zinc-50 px-2 py-1 rounded-full">Transactions</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">{{ number_format($stats['total_transactions']) }}</h3>
            <p class="text-sm text-zinc-500 mt-1">Completed orders</p>
        </div>

        <!-- Avg Transaction Value -->
        <div class="bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-50 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <span class="text-xs font-medium text-zinc-500 bg-zinc-50 px-2 py-1 rounded-full">Avg. Value</span>
            </div>
            <h3 class="text-2xl font-bold text-zinc-900">Rp {{ number_format($stats['avg_transaction'], 0, ',', '.') }}</h3>
            <p class="text-sm text-zinc-500 mt-1">Per transaction</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-zinc-200 shadow-sm">
            <h3 class="text-lg font-semibold text-zinc-900 mb-6">Sales Overview</h3>
            <div class="relative h-80 w-full" wire:ignore>
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-zinc-100">
                <h3 class="text-lg font-semibold text-zinc-900">Top Products</h3>
            </div>
            <div class="overflow-y-auto max-h-80">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-zinc-500 uppercase bg-zinc-50 sticky top-0">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            <th class="px-6 py-3 text-right">Qty</th>
                            <th class="px-6 py-3 text-right">Revenue</th>
                            <th class="px-6 py-3 text-right">Profit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @forelse($topProducts as $product)
                            <tr class="bg-white hover:bg-zinc-50">
                                <td class="px-6 py-4 font-medium text-zinc-900">{{ $product->name }}</td>
                                <td class="px-6 py-4 text-right text-zinc-600">{{ $product->total_qty }}</td>
                                <td class="px-6 py-4 text-right font-medium text-indigo-600">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right font-medium text-emerald-600">Rp {{ number_format($product->total_profit, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-zinc-500">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed Transactions -->
    <div class="bg-white rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-zinc-100 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-zinc-900">Transaction Details</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-zinc-500 uppercase bg-zinc-50">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Transaction ID</th>
                        <th class="px-6 py-3">Branch</th>
                        <th class="px-6 py-3">Cashier</th>
                        <th class="px-6 py-3 text-center">Items</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-right">Profit</th>
                        <th class="px-6 py-3 text-right">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse($transactions as $transaction)
                        @php
                            $profit = $transaction->profit;
                            $margin = $transaction->total > 0 ? ($profit / $transaction->total) * 100 : 0;
                        @endphp
                        <tr wire:key="transaction-{{ $transaction->id }}" class="bg-white hover:bg-zinc-50 cursor-pointer" wire:dblclick="showDetails({{ $transaction->id }})">
                            <td class="px-6 py-4 text-zinc-600">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 font-medium text-zinc-900">#{{ $transaction->id }}</td>
                            <td class="px-6 py-4 text-zinc-600">{{ $transaction->branch->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-zinc-600">{{ $transaction->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-zinc-600">{{ $transaction->items->count() }}</td>
                            <td class="px-6 py-4 text-right font-medium text-zinc-900">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium text-emerald-600">Rp {{ number_format($profit, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-medium text-blue-600">{{ number_format($margin, 1) }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-zinc-500">No transactions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-zinc-100">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Transaction Detail Modal -->
    @if($showModal && $selectedTransaction)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
                <!-- Header -->
                <div class="bg-indigo-600 px-6 py-4 flex items-center justify-between flex-shrink-0">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Transaction Details</h3>
                        <p class="text-indigo-100 text-sm">#{{ $selectedTransaction->id }} - {{ $selectedTransaction->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <button wire:click="closeModal" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Info Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                        <div>
                            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider mb-1">Branch</p>
                            <p class="text-zinc-900 font-medium">{{ $selectedTransaction->branch->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider mb-1">Cashier</p>
                            <p class="text-zinc-900 font-medium">{{ $selectedTransaction->user->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider mb-1">Payment</p>
                            <p class="text-zinc-900 font-medium capitalize">{{ $selectedTransaction->payment_method }}</p>
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
                                    <th class="px-4 py-3 text-right font-medium text-zinc-700">Cost</th>
                                    <th class="px-4 py-3 text-center font-medium text-zinc-700">Qty</th>
                                    <th class="px-4 py-3 text-right font-medium text-zinc-700">Subtotal</th>
                                    <th class="px-4 py-3 text-right font-medium text-zinc-700">Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100">
                                @foreach($selectedTransaction->items as $item)
                                    @php
                                        $itemCost = $item->cost ?? ($item->product->cost ?? 0);
                                        $itemProfit = ($item->price - $itemCost) * $item->quantity;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-zinc-900">{{ $item->product->name ?? 'Unknown Product' }}</td>
                                        <td class="px-4 py-3 text-right text-zinc-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right text-zinc-500">Rp {{ number_format($itemCost, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center text-zinc-600">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-zinc-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-medium text-emerald-600">Rp {{ number_format($itemProfit, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-zinc-50 border-t border-zinc-200">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-right font-medium text-zinc-600">Total Amount</td>
                                    <td colspan="2" class="px-4 py-3 text-right font-bold text-zinc-900 text-lg">Rp {{ number_format($selectedTransaction->total, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-right text-sm font-medium text-emerald-700">Total Profit</td>
                                    <td colspan="2" class="px-4 py-2 text-right text-sm font-bold text-emerald-600">Rp {{ number_format($selectedTransaction->profit, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-right text-sm font-medium text-blue-700">Profit Margin</td>
                                    <td colspan="2" class="px-4 py-2 text-right text-sm font-bold text-blue-600">{{ number_format(($selectedTransaction->profit / $selectedTransaction->total) * 100, 1) }}%</td>
                                </tr>
                                <tr class="border-t border-zinc-300">
                                    <td colspan="4" class="px-4 py-2 text-right text-sm text-zinc-500">Cash Paid</td>
                                    <td colspan="2" class="px-4 py-2 text-right text-sm font-medium text-zinc-900">Rp {{ number_format($selectedTransaction->cash, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-right text-sm text-zinc-500">Change</td>
                                    <td colspan="2" class="px-4 py-2 text-right text-sm font-medium text-zinc-900">Rp {{ number_format($selectedTransaction->change, 0, ',', '.') }}</td>
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

@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endassets

<script>
// Use window object to avoid redeclaration issues
if (!window.salesChart) {
    window.salesChart = null;
}

function initSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (window.salesChart && typeof window.salesChart.destroy === 'function') {
        window.salesChart.destroy();
    }
    window.salesChart = null;

    // Get chart data from the page
    const chartDataElement = document.getElementById('chartData');
    if (!chartDataElement) return;
    
    const data = JSON.parse(chartDataElement.textContent);

    window.salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Revenue',
                    data: data.revenue,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'Profit',
                    data: data.profit,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    initSalesChart();
    
    // Listen for chart updates
    document.addEventListener('livewire:init', () => {
        Livewire.on('update-chart', (event) => {
            const chartData = event.data || event; 
            
            if (window.salesChart && chartData) {
                window.salesChart.data.labels = chartData.labels || [];
                if (window.salesChart.data.datasets[0]) {
                    window.salesChart.data.datasets[0].data = chartData.revenue || [];
                }
                if (window.salesChart.data.datasets[1]) {
                    window.salesChart.data.datasets[1].data = chartData.profit || [];
                }
                window.salesChart.update();
            }
        });
    });
});

// Re-initialize on Livewire navigation
document.addEventListener('livewire:navigated', initSalesChart);
</script>

<!-- Hidden element to store chart data -->
<script type="application/json" id="chartData">
    @json($chartData)
</script>
