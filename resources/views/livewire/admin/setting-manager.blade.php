@section('header', 'Settings')

<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <!-- Local header removed -->
        </div>
    </div>

    <!-- Settings Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-zinc-100 overflow-hidden max-w-2xl">
        <div class="p-6 border-b border-zinc-100 bg-zinc-50/50">
            <h2 class="text-lg font-semibold text-zinc-800">General Configuration</h2>
            <p class="text-sm text-zinc-500">Update your business details and financial settings.</p>
        </div>
        
        <form wire:submit="save" class="p-6 space-y-6">
            <!-- Business Name -->
            <div>
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model="business_name" 
                        id="business_name"
                        class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                        placeholder="Business Name"
                    >
                    <label for="business_name" class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                        Business Name
                    </label>
                </div>
                <p class="text-xs text-zinc-500 mt-1.5">This name will be displayed on the dashboard and receipts.</p>
                @error('business_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Business Type -->
            <div>
                <label class="block text-sm font-medium text-zinc-700 mb-3">Business Type</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" :class="$wire.business_type === 'retail' ? 'border-indigo-600 ring-2 ring-indigo-600/20' : 'border-zinc-300'">
                        <input type="radio" wire:model="business_type" value="retail" class="sr-only">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-zinc-900">Retail Store</span>
                                <span class="mt-1 flex items-center text-sm text-zinc-500">Standard POS for retail businesses.</span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-indigo-600" :class="$wire.business_type === 'retail' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </label>

                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none" :class="$wire.business_type === 'restaurant' ? 'border-indigo-600 ring-2 ring-indigo-600/20' : 'border-zinc-300'">
                        <input type="radio" wire:model="business_type" value="restaurant" class="sr-only">
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-zinc-900">Restaurant</span>
                                <span class="mt-1 flex items-center text-sm text-zinc-500">Enables Kitchen Display System (KDS).</span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-indigo-600" :class="$wire.business_type === 'restaurant' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </label>
                </div>
                @error('business_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Tax Rate -->
            <div>
                <div class="relative">
                    <input 
                        type="number" 
                        step="0.01" 
                        wire:model="tax_rate" 
                        id="tax_rate"
                        class="peer w-full px-4 pt-6 pb-2 text-zinc-900 bg-white border border-zinc-300 rounded-lg focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 transition-all placeholder-transparent"
                        placeholder="Tax Rate"
                    >
                    <label for="tax_rate" class="absolute left-4 top-2 text-xs font-medium text-zinc-600 transition-all peer-placeholder-shown:text-base peer-placeholder-shown:top-4 peer-placeholder-shown:text-zinc-400 peer-focus:top-2 peer-focus:text-xs peer-focus:text-indigo-600">
                        Tax Rate (%)
                    </label>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none peer-focus:text-indigo-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM19 10a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                    </div>
                </div>
                <p class="text-xs text-zinc-500 mt-1.5">Percentage of tax applied to each transaction.</p>
                @error('tax_rate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Save Button -->
            <div class="pt-4 flex items-center justify-end border-t border-zinc-100 mt-6">
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl font-medium transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/50"
                >
                    <svg wire:loading class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>
