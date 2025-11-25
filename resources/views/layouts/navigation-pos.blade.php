<div class="h-full flex flex-col justify-between w-full items-center">
    <div class="w-full flex flex-col items-center">
        <!-- Logo -->
        <div class="h-16 flex items-center justify-center w-full px-6 border-b border-zinc-100">
            <a href="{{ route('dashboard') }}" wire:navigate class="flex flex-col items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-lg shadow-lg hover:shadow-indigo-500/30 transition-all duration-300">
                    {{ substr(\App\Models\Setting::getValue('business_name', 'POS Pro'), 0, 1) }}
                </div>
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-1 w-full p-4">
            @if(Auth::user()->hasMenuAccess('dashboard'))
                <a href="{{ route('dashboard') }}" wire:navigate title="Dashboard" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/50' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </a>
            @endif

            @if(Auth::user()->hasMenuAccess('pos'))
                <a href="{{ route('pos') }}" wire:navigate title="Point of Sale" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-all duration-200 {{ request()->routeIs('pos') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg shadow-indigo-500/50' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </a>
            @endif

            @if(Auth::user())
                <div class="w-full h-px bg-zinc-200 my-2"></div>

                @if(Auth::user()->hasMenuAccess('branches'))
                    <a href="{{ route('branches') }}" wire:navigate title="Branches" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('branches') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </a>
                @endif

                @if(Auth::user()->hasMenuAccess('categories'))
                    <a href="{{ route('categories') }}" wire:navigate title="Categories" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('categories') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </a>
                @endif

                @if(Auth::user()->hasMenuAccess('products'))
                    <a href="{{ route('products') }}" wire:navigate title="Products" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('products') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </a>
                @endif

                @if(Auth::user()->hasMenuAccess('stock'))
                    <a href="{{ route('stock') }}" wire:navigate title="Inventory" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('stock') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </a>
                @endif

                @if(Auth::user()->hasMenuAccess('transactions'))
                    <a href="{{ route('transactions') }}" wire:navigate title="Transactions" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('transactions') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </a>
                @endif

                @if(Auth::user()->hasMenuAccess('customers'))
                    <a href="{{ route('customers') }}" wire:navigate title="Customers" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('customers') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </a>
                @endif

                @if(Auth::user()->hasMenuAccess('reports'))
                    <a href="{{ route('reports') }}" wire:navigate title="Reports" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('reports') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </a>
                @endif

                @if(Auth::user()->hasMenuAccess('users'))
                    <a href="{{ route('users') }}" wire:navigate title="Users" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('users') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </a>
                @endif

                @if(Auth::user()->hasRole('manager'))
                    <a href="{{ route('employees') }}" wire:navigate title="Employees" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('employees') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </a>
                @endif


                @if(Auth::user()->hasRole('owner'))
                    <a href="{{ route('activity-logs') }}" wire:navigate title="Activity Logs" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('activity-logs') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </a>
                @endif

                @if(Auth::user()->hasMenuAccess('settings'))
                    <a href="{{ route('settings') }}" wire:navigate title="Settings" class="flex items-center justify-center w-full aspect-square rounded-xl p-2 transition-colors {{ request()->routeIs('settings') ? 'bg-indigo-50 text-indigo-600' : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-900' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </a>
                @endif
            @endif
        </nav>
    </div>

    <!-- User Profile / Logout -->
    <div class="p-2 border-t border-zinc-100 w-full">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Sign Out" class="w-full flex items-center justify-center aspect-square rounded-xl p-2 text-red-500 hover:bg-red-50 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </div>
</div>
