@section('header', 'Activity Logs')

<div class="space-y-6">
    <!-- Header with Filters -->
    <div class="flex items-center justify-between gap-4">
        <!-- Search -->
        <div class="flex-1 max-w-md">
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search activities..."
                    class="w-full pl-10 pr-4 py-2.5 border border-zinc-200 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all"
                >
                <svg class="absolute left-3 top-3 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Action Filter -->
        <div class="bg-white rounded-lg border border-zinc-200 px-4 py-2.5">
            <label class="text-sm font-medium text-zinc-700 mr-2">Action:</label>
            <select 
                wire:model.live="filterAction" 
                class="border-0 bg-transparent text-zinc-900 font-medium focus:ring-0 pr-8 py-0"
            >
                <option value="">All Actions</option>
                <option value="login">Login</option>
                <option value="logout">Logout</option>
                <option value="created">Created</option>
                <option value="updated">Updated</option>
                <option value="deleted">Deleted</option>
            </select>
        </div>

        <!-- Export PDF -->
        <button 
            wire:click="exportPdf"
            wire:loading.attr="disabled"
            class="flex items-center gap-2 px-4 py-2.5 bg-white border border-zinc-200 rounded-lg text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 transition-all"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="text-sm font-medium">Export PDF</span>
        </button>

        <!-- Live Toggle -->
        <button 
            wire:click="toggleLive"
            class="flex items-center gap-2 px-4 py-2.5 rounded-lg border transition-all {{ $isLive ? 'bg-red-50 border-red-200 text-red-700' : 'bg-white border-zinc-200 text-zinc-600 hover:bg-zinc-50' }}"
        >
            <div class="relative flex h-3 w-3">
                @if($isLive)
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                @endif
                <span class="relative inline-flex rounded-full h-3 w-3 {{ $isLive ? 'bg-red-500' : 'bg-zinc-400' }}"></span>
            </div>
            <span class="text-sm font-medium">Live</span>
        </button>
    </div>

    @if($isLive)
        <div wire:poll.2s="loadLogs"></div>
    @endif

    <!-- Activity Logs Table -->
    <div class="bg-white rounded-lg border border-zinc-200 overflow-hidden relative">
        <div wire:loading.flex wire:target="search, filterAction, loadLogs" class="absolute inset-0 bg-white/50 backdrop-blur-[1px] z-10 items-center justify-center hidden">
            <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b border-zinc-200 bg-zinc-50/50">
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Time</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">User</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Action</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Description</th>
                    <th class="px-6 py-3.5 text-left text-sm font-medium text-zinc-700">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                @forelse($logs as $log)
                    <tr 
                        class="hover:bg-zinc-50 transition-colors duration-150 cursor-pointer" 
                        ondblclick="Livewire.dispatch('showLogDetail', { logId: {{ $log->id }} })"
                        wire:dblclick="showLogDetail({{ $log->id }})"
                    >
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-900">{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-zinc-500">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-zinc-900">{{ $log->user?->name ?? 'System' }}</div>
                            <div class="text-xs text-zinc-500">{{ $log->user?->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->action === 'login')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    Login
                                </span>
                            @elseif($log->action === 'logout')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700">
                                    Logout
                                </span>
                            @elseif($log->action === 'created')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    Created
                                </span>
                            @elseif($log->action === 'updated')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                    Updated
                                </span>
                            @elseif($log->action === 'deleted')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    Deleted
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                    {{ ucfirst($log->action) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-zinc-900">{{ $log->description }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->properties)
                                <div class="text-xs space-y-1">
                                    @if(isset($log->properties['old_quantity']) && isset($log->properties['new_quantity']))
                                        <div class="text-zinc-600">
                                            <span class="font-medium">Quantity:</span> 
                                            <span class="text-red-600">{{ $log->properties['old_quantity'] }}</span> 
                                            → 
                                            <span class="text-green-600">{{ $log->properties['new_quantity'] }}</span>
                                        </div>
                                    @endif
                                    @if(isset($log->properties['changes']))
                                        @foreach($log->properties['changes'] as $field => $change)
                                            <div class="text-zinc-600">
                                                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                                @if($field === 'price' || $field === 'cost')
                                                    <span class="text-red-600">Rp {{ number_format($change['old'] ?? 0, 0, ',', '.') }}</span>
                                                    →
                                                    <span class="text-green-600">Rp {{ number_format($change['new'] ?? 0, 0, ',', '.') }}</span>
                                                @elseif($field === 'is_active')
                                                    <span class="text-red-600">{{ $change['old'] ? 'Active' : 'Inactive' }}</span>
                                                    →
                                                    <span class="text-green-600">{{ $change['new'] ? 'Active' : 'Inactive' }}</span>
                                                @else
                                                    <span class="text-red-600">{{ $change['old'] ?? '-' }}</span>
                                                    →
                                                    <span class="text-green-600">{{ $change['new'] ?? '-' }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                    @if(isset($log->properties['added']) && !empty($log->properties['added']))
                                        <div class="text-green-700">
                                            <span class="font-medium">+</span> {{ implode(', ', $log->properties['added']) }}
                                        </div>
                                    @endif
                                    @if(isset($log->properties['removed']) && !empty($log->properties['removed']))
                                        <div class="text-red-700">
                                            <span class="font-medium">-</span> {{ implode(', ', $log->properties['removed']) }}
                                        </div>
                                    @endif
                                    @if(isset($log->properties['menus']) && !empty($log->properties['menus']))
                                        <div class="text-blue-700">
                                            {{ implode(', ', $log->properties['menus']) }}
                                        </div>
                                    @endif
                                    @if(isset($log->properties['items']) && !empty($log->properties['items']))
                                        <div class="text-zinc-600">
                                            <span class="font-medium">Items:</span>
                                            <div class="ml-2 text-xs">
                                                @foreach($log->properties['items'] as $item)
                                                    <div>• {{ $item }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="text-zinc-600">
                                            <span class="font-medium">Total:</span> Rp {{ number_format($log->properties['total'] ?? 0, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-zinc-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-zinc-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-medium text-zinc-900 mb-1">No activity logs found</h3>
                                <p class="text-sm text-zinc-500">Activity logs will appear here as users interact with the system</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
                <span wire:loading.remove wire:target="loadMore">Load More Logs</span>
                <span wire:loading wire:target="loadMore">Loading...</span>
            </button>
        </div>
    @endif

    <!-- Detail Modal -->
    @if($showModal && $selectedLog)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:click="closeModal">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden" wire:click.stop>
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-zinc-200 flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50">
                    <h3 class="text-lg font-semibold text-zinc-900">Activity Log Details</h3>
                    <button wire:click="closeModal" class="text-zinc-400 hover:text-zinc-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-8rem)] space-y-4">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">User</label>
                            <div class="mt-1 text-sm font-medium text-zinc-900">{{ $selectedLog->user?->name ?? 'System' }}</div>
                            <div class="text-xs text-zinc-500">{{ $selectedLog->user?->email }}</div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">Action</label>
                            <div class="mt-1">
                                @if($selectedLog->action === 'login')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Login</span>
                                @elseif($selectedLog->action === 'logout')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-700">Logout</span>
                                @elseif($selectedLog->action === 'created')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Created</span>
                                @elseif($selectedLog->action === 'updated')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Updated</span>
                                @elseif($selectedLog->action === 'deleted')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Deleted</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ ucfirst($selectedLog->action) }}</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">Date & Time</label>
                            <div class="mt-1 text-sm text-zinc-900">{{ $selectedLog->created_at->format('M d, Y H:i:s') }}</div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">IP Address</label>
                            <div class="mt-1 text-sm text-zinc-900">{{ $selectedLog->ip_address ?? '-' }}</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="text-xs font-medium text-zinc-500 uppercase">Description</label>
                        <div class="mt-1 text-sm text-zinc-900 bg-zinc-50 p-3 rounded-lg">{{ $selectedLog->description }}</div>
                    </div>

                    <!-- Model Info -->
                    @if($selectedLog->model_type)
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-zinc-500 uppercase">Model Type</label>
                                <div class="mt-1 text-sm text-zinc-900">{{ class_basename($selectedLog->model_type) }}</div>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-zinc-500 uppercase">Model ID</label>
                                <div class="mt-1 text-sm text-zinc-900">{{ $selectedLog->model_id }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- Properties/Details -->
                    @if($selectedLog->properties)
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">Details</label>
                            <div class="mt-2 bg-zinc-50 p-4 rounded-lg space-y-2">
                                @if(isset($selectedLog->properties['changes']))
                                    <div class="font-medium text-sm text-zinc-700 mb-2">Changes:</div>
                                    @foreach($selectedLog->properties['changes'] as $field => $change)
                                        <div class="flex items-center gap-2 text-sm">
                                            <span class="font-medium text-zinc-600">{{ ucfirst(str_replace('_', ' ', $field)) }}:</span>
                                            <span class="text-red-600">{{ is_bool($change['old'] ?? null) ? ($change['old'] ? 'Yes' : 'No') : ($change['old'] ?? '-') }}</span>
                                            <span class="text-zinc-400">→</span>
                                            <span class="text-green-600">{{ is_bool($change['new'] ?? null) ? ($change['new'] ? 'Yes' : 'No') : ($change['new'] ?? '-') }}</span>
                                        </div>
                                    @endforeach
                                @endif

                                @if(isset($selectedLog->properties['old_quantity']) && isset($selectedLog->properties['new_quantity']))
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="font-medium text-zinc-600">Quantity:</span>
                                        <span class="text-red-600">{{ $selectedLog->properties['old_quantity'] }}</span>
                                        <span class="text-zinc-400">→</span>
                                        <span class="text-green-600">{{ $selectedLog->properties['new_quantity'] }}</span>
                                    </div>
                                    @if(isset($selectedLog->properties['product']))
                                        <div class="text-sm text-zinc-600">Product: <span class="font-medium">{{ $selectedLog->properties['product'] }}</span></div>
                                    @endif
                                    @if(isset($selectedLog->properties['branch']))
                                        <div class="text-sm text-zinc-600">Branch: <span class="font-medium">{{ $selectedLog->properties['branch'] }}</span></div>
                                    @endif
                                @endif

                                @if(isset($selectedLog->properties['added']) && !empty($selectedLog->properties['added']))
                                    <div class="text-sm">
                                        <span class="font-medium text-green-700">Added:</span>
                                        <span class="text-zinc-700">{{ implode(', ', $selectedLog->properties['added']) }}</span>
                                    </div>
                                @endif

                                @if(isset($selectedLog->properties['removed']) && !empty($selectedLog->properties['removed']))
                                    <div class="text-sm">
                                        <span class="font-medium text-red-700">Removed:</span>
                                        <span class="text-zinc-700">{{ implode(', ', $selectedLog->properties['removed']) }}</span>
                                    </div>
                                @endif

                                @if(isset($selectedLog->properties['menus']) && !empty($selectedLog->properties['menus']))
                                    <div class="text-sm">
                                        <span class="font-medium text-blue-700">Menus:</span>
                                        <span class="text-zinc-700">{{ implode(', ', $selectedLog->properties['menus']) }}</span>
                                    </div>
                                @endif

                                @if(isset($selectedLog->properties['items']) && !empty($selectedLog->properties['items']))
                                    <div class="border-t border-zinc-200 pt-3 mt-3">
                                        <div class="font-medium text-sm text-zinc-700 mb-2">Transaction Details:</div>
                                        
                                        @if(isset($selectedLog->properties['branch']))
                                            <div class="mb-3 text-sm">
                                                <span class="text-zinc-600">Branch:</span>
                                                <span class="font-medium text-indigo-600">{{ $selectedLog->properties['branch'] }}</span>
                                            </div>
                                        @endif
                                        
                                        <div class="space-y-1 mb-3">
                                            <div class="font-medium text-xs text-zinc-500 uppercase">Items Purchased:</div>
                                            @foreach($selectedLog->properties['items'] as $item)
                                                <div class="text-sm text-zinc-700">• {{ $item }}</div>
                                            @endforeach
                                        </div>

                                        <div class="grid grid-cols-2 gap-2 text-sm bg-zinc-100 p-3 rounded">
                                            <div>
                                                <span class="text-zinc-600">Subtotal:</span>
                                                <span class="font-medium">Rp {{ number_format($selectedLog->properties['subtotal'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div>
                                                <span class="text-zinc-600">Tax:</span>
                                                <span class="font-medium">Rp {{ number_format($selectedLog->properties['tax'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div>
                                                <span class="text-zinc-600">Total:</span>
                                                <span class="font-medium text-indigo-600">Rp {{ number_format($selectedLog->properties['total'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div>
                                                <span class="text-zinc-600">Cash:</span>
                                                <span class="font-medium">Rp {{ number_format($selectedLog->properties['cash'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-zinc-600">Change:</span>
                                                <span class="font-medium text-green-600">Rp {{ number_format($selectedLog->properties['change'] ?? 0, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- User Agent -->
                    @if($selectedLog->user_agent)
                        <div>
                            <label class="text-xs font-medium text-zinc-500 uppercase">User Agent</label>
                            <div class="mt-1 text-xs text-zinc-600 bg-zinc-50 p-3 rounded-lg break-all">{{ $selectedLog->user_agent }}</div>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-zinc-200 bg-zinc-50 flex justify-end">
                    <button wire:click="closeModal" class="px-4 py-2 bg-zinc-600 hover:bg-zinc-700 text-white rounded-lg transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
