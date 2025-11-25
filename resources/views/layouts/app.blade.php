<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="font-sans antialiased bg-zinc-50 text-zinc-800">
        <div class="min-h-screen flex">
            <!-- Sidebar -->
            <aside class="w-64 bg-white border-r border-zinc-200 fixed h-full z-10 hidden md:block">
                @include('layouts.navigation')
            </aside>

            <!-- Main Content -->
            <main class="flex-1 md:ml-64 p-8">
                <!-- Top Header (Mobile Toggle & Profile) -->
                <header class="flex justify-between items-center mb-8">
                    <h1 class="text-2xl font-bold text-zinc-800">
                        @yield('header', 'Dashboard')
                    </h1>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-zinc-500">
                            {{ Auth::user()->name }} 
                            <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full ml-1">{{ ucfirst(Auth::user()->role) }}</span>
                            @if((Auth::user()->hasRole('manager') || Auth::user()->hasRole('cashier')) && Auth::user()->branch)
                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full ml-1">
                                    <svg class="w-3 h-3 inline-block -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    {{ Auth::user()->branch->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </header>

                {{ $slot }}
            </main>
        </div>
        
        @livewireScripts
        
        <script>
            // Toast notification configuration
            window.Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Listen for Livewire events
            window.addEventListener('success', event => {
                window.Toast.fire({
                    icon: 'success',
                    title: event.detail[0] || event.detail.message || 'Success!'
                });
            });

            window.addEventListener('error', event => {
                window.Toast.fire({
                    icon: 'error',
                    title: event.detail[0] || event.detail.message || 'Error!'
                });
            });

            window.addEventListener('info', event => {
                window.Toast.fire({
                    icon: 'info',
                    title: event.detail[0] || event.detail.message || 'Info'
                });
            });

            window.addEventListener('warning', event => {
                window.Toast.fire({
                    icon: 'warning',
                    title: event.detail[0] || event.detail.message || 'Warning!'
                });
            });
        </script>
    </body>
</html>
