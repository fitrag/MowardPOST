@section('header', 'Kitchen Display System')

<div class="space-y-6" wire:poll.5s>
    @if($orders->isEmpty())
        <div class="text-center py-12">
            <div class="bg-zinc-50 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            </div>
            <h3 class="text-lg font-medium text-zinc-900">No Active Orders</h3>
            <p class="text-zinc-500">New orders will appear here automatically.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-xl shadow-sm border border-zinc-200 overflow-hidden flex flex-col h-full 
                    {{ $order->kitchen_status === 'ready' ? 'ring-2 ring-emerald-500' : '' }}">
                    
                    <!-- Header -->
                    <div class="p-4 border-b border-zinc-100 flex justify-between items-start 
                        {{ $order->kitchen_status === 'pending' ? 'bg-yellow-50' : ($order->kitchen_status === 'preparing' ? 'bg-blue-50' : 'bg-emerald-50') }}">
                        <div>
                            <h3 class="font-bold text-lg text-zinc-900">Order #{{ $order->id }}</h3>
                            <p class="text-xs text-zinc-500">{{ $order->created_at->format('H:i') }} ({{ $order->created_at->diffForHumans() }})</p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs font-bold uppercase tracking-wide
                            {{ $order->kitchen_status === 'pending' ? 'bg-yellow-100 text-yellow-700' : ($order->kitchen_status === 'preparing' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                            {{ $order->kitchen_status }}
                        </span>
                    </div>

                    <!-- Items -->
                    <div class="p-4 flex-1 overflow-y-auto max-h-[300px]">
                        <ul class="space-y-3">
                            @foreach($order->items as $item)
                                <li class="flex justify-between items-start">
                                    <div class="flex gap-3">
                                        <span class="font-bold text-zinc-900 w-6">{{ $item->quantity }}x</span>
                                        <span class="text-zinc-700">{{ $item->product->name }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        @if($order->note)
                            <div class="mt-4 p-3 bg-zinc-50 rounded-lg border border-zinc-100">
                                <p class="text-xs font-bold text-zinc-500 uppercase mb-1">Note:</p>
                                <p class="text-sm text-zinc-700 italic">{{ $order->note }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="p-4 border-t border-zinc-100 bg-zinc-50">
                        @if($order->kitchen_status === 'pending')
                            <button wire:click="updateStatus({{ $order->id }}, 'preparing')" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Start Preparing
                            </button>
                        @elseif($order->kitchen_status === 'preparing')
                            <button wire:click="updateStatus({{ $order->id }}, 'ready')" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Mark Ready
                            </button>
                        @elseif($order->kitchen_status === 'ready')
                            <button wire:click="updateStatus({{ $order->id }}, 'served')" class="w-full bg-zinc-800 hover:bg-zinc-900 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                Complete / Served
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
