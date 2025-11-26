<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - POS</title>

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
    <body class="font-sans antialiased bg-zinc-50 text-zinc-800 overflow-hidden" 
          x-data="{ 
              sidebarOpen: false, 
              sidebarMinimized: localStorage.getItem('sidebarMinimized') === 'true' 
          }"
          x-init="$watch('sidebarMinimized', value => localStorage.setItem('sidebarMinimized', value))">
        <div class="h-screen flex">
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" style="display: none;" @click="sidebarOpen = false" x-transition.opacity 
                 class="fixed inset-0 z-20 bg-black/50 lg:hidden"></div>

            <!-- Sidebar -->
            <aside :class="{ 
                        'translate-x-0': sidebarOpen, 
                        '-translate-x-full lg:translate-x-0': !sidebarOpen,
                        'w-64': !sidebarMinimized,
                        'w-20': sidebarMinimized
                   }"
                   class="fixed inset-y-0 left-0 z-30 bg-white border-r border-zinc-200 transition-all duration-300 ease-in-out lg:block overflow-hidden">
                @include('layouts.navigation')
            </aside>

            <!-- Main Content -->
            <main class="flex-1 h-full flex flex-col overflow-hidden transition-all duration-300 ease-in-out"
                  :class="sidebarMinimized ? 'lg:ml-20' : 'lg:ml-64'">
                <!-- Mobile Header -->
                <div class="lg:hidden bg-white border-b border-zinc-200 px-4 py-3 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="text-zinc-600 hover:bg-zinc-100 p-2 rounded-lg -ml-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <span class="font-bold text-lg text-zinc-800">POS</span>
                    </div>
                </div>

                <div class="flex-1 overflow-hidden p-4 lg:p-6">
                    {{ $slot }}
                </div>
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
