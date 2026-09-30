<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Teaching Hospital Jaffna - Anaesthesia Audit System') }}</title>

        <!-- Inline Theme Script: detect browser theme by default and carry to system -->
        <script>
            (function() {
                const storedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                // If stored theme exists, respect it. If not, detect browser's preferred theme and carry to system
                if (storedTheme === 'dark' || (!storedTheme && prefersDark)) {
                    document.documentElement.classList.add('dark');
                    if (!storedTheme) {
                        localStorage.setItem('theme', 'dark');
                    }
                } else {
                    document.documentElement.classList.remove('dark');
                    if (!storedTheme) {
                        localStorage.setItem('theme', 'light');
                    }
                }
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-[#080d1a] selection:bg-indigo-500 selection:text-white transition-colors duration-300">
        <div x-data="{
                darkMode: document.documentElement.classList.contains('dark'),
                setTheme(mode) {
                    this.darkMode = (mode === 'dark');
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    }
                },
                toggleTheme() {
                    this.setTheme(this.darkMode ? 'light' : 'dark');
                }
            }"
            class="min-h-screen flex flex-col justify-between relative overflow-hidden bg-gradient-to-br from-slate-50 via-slate-100/80 to-indigo-50/40 dark:from-[#080d1a] dark:via-[#0b132b] dark:to-[#071324] transition-colors duration-300">
            
            <!-- Top Navigation Bar: Brand + Theme Sliding Selector -->
            <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex items-center justify-between z-20">
                <a href="/" class="flex items-center gap-3 group focus:outline-none">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 dark:bg-indigo-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm sm:text-base text-slate-900 dark:text-white tracking-tight leading-tight">
                            Teaching Hospital Jaffna
                        </div>
                        <div class="text-[0.65rem] sm:text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">
                            Department of Anaesthesia
                        </div>
                    </div>
                </a>

                <!-- Theme Slider Switch -->
                <div class="flex items-center">
                    <button type="button" 
                            role="switch" 
                            :aria-checked="darkMode" 
                            @click="toggleTheme()" 
                            @keydown.space.prevent="toggleTheme()" 
                            @keydown.enter.prevent="toggleTheme()"
                            class="relative inline-flex h-8 w-16 items-center rounded-full p-1 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 cursor-pointer shadow-inner select-none"
                            :class="darkMode 
                                ? 'bg-slate-800/90 border border-slate-700/80 shadow-[inset_0_2px_4px_rgba(0,0,0,0.5)]' 
                                : 'bg-slate-200/90 border border-slate-300/80 shadow-[inset_0_2px_4px_rgba(0,0,0,0.08)]'"
                            :title="darkMode ? 'Theme: Dark (Click to slide to Light)' : 'Theme: Light (Click to slide to Dark)'"
                            aria-label="Theme Mode Slider">

                        <!-- Embedded Track Icons (Sun on Left, Moon on Right) -->
                        <span class="absolute inset-0 flex items-center justify-between px-2.5 pointer-events-none">
                            <!-- Left Sun Icon in Track (Visible in Dark mode to hint Light mode toggle) -->
                            <svg class="w-3.5 h-3.5 transition-opacity duration-300"
                                 :class="darkMode ? 'text-amber-400/60 opacity-100' : 'opacity-0'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <!-- Right Moon Icon in Track (Visible in Light mode to hint Dark mode toggle) -->
                            <svg class="w-3.5 h-3.5 transition-opacity duration-300"
                                 :class="!darkMode ? 'text-indigo-400/60 opacity-100' : 'opacity-0'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </span>

                        <!-- Sliding Thumb / Knob -->
                        <span class="inline-flex h-6 w-6 transform items-center justify-center rounded-full transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] shadow-md z-10"
                              :class="darkMode 
                                ? 'translate-x-8 bg-slate-900 border border-indigo-500/60 shadow-indigo-500/40' 
                                : 'translate-x-0 bg-white border border-amber-300 shadow-amber-500/20'">
                            <!-- Glowing Sun Icon inside knob when Light -->
                            <svg x-show="!darkMode" 
                                 class="w-3.5 h-3.5 text-amber-500 transition-transform duration-300 rotate-0"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <!-- Glowing Crescent Moon Icon inside knob when Dark -->
                            <svg x-show="darkMode" 
                                 class="w-3.5 h-3.5 text-indigo-400 transition-transform duration-300 -rotate-12"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </header>

            <!-- Main Content Container with Ambient Lighting -->
            <main class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-6 sm:py-10 relative z-10">
                <!-- Background Ambient Glows -->
                <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-500/10 dark:bg-indigo-600/15 rounded-full blur-3xl pointer-events-none -z-10"></div>
                <div class="absolute bottom-10 right-1/4 w-80 h-80 bg-teal-500/10 dark:bg-teal-500/10 rounded-full blur-3xl pointer-events-none -z-10"></div>

                {{ $slot }}
            </main>

            <!-- Minimal Footer -->
            <footer class="w-full py-4 text-center text-xs text-slate-500 dark:text-slate-500 z-10">
                <p>&copy; {{ date('Y') }} Teaching Hospital Jaffna • Department of Anaesthesia. All rights reserved.</p>
            </footer>
        </div>
    </body>
</html>
