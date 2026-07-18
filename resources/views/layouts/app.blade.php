<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            
            <!-- Sidebar Navigation -->
            @include('layouts.navigation')

            <!-- Main Content Area (Offset for Desktop Sidebar) -->
            <div class="md:pl-64 lg:pl-72 flex flex-col min-h-screen transition-all duration-300">
                
                <!-- Mobile Top Bar Header -->
                <div class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-slate-900 border-b border-slate-800 md:hidden text-white shadow-md">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" type="button" class="p-2 rounded-md hover:bg-slate-800 text-slate-300 hover:text-white focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <span class="font-bold text-sm tracking-wide">Anaesthesia Audit System</span>
                    </div>
                    <div class="text-xs font-semibold px-2.5 py-1 bg-indigo-600 rounded-full">
                        {{ strtoupper(auth()->user()->role) }}
                    </div>
                </div>

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                        <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Main Slot Content -->
                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>

        </div>
    </body>
</html>
