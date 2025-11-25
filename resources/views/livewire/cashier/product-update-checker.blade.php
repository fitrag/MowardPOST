<div wire:poll.10s="checkUpdates" class="absolute top-4 left-1/2 -translate-x-1/2 z-50 pointer-events-none">
    @if($hasNewUpdates)
        <button 
            wire:click="triggerRefresh" 
            wire:loading.attr="disabled"
            wire:loading.class.remove="animate-bounce"
            class="pointer-events-auto bg-indigo-600 text-white px-4 py-2 rounded-full shadow-lg flex items-center gap-2 text-sm font-medium animate-bounce hover:bg-indigo-700 transition-colors"
        >
            <span wire:loading.remove wire:target="triggerRefresh" class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                </span>
                New Updates Available
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </span>
            <span wire:loading wire:target="triggerRefresh" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Updating...
            </span>
        </button>
    @endif
</div>
