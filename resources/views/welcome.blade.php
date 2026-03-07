<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Teaching Hospital Jaffna - Anesthesia Database</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-800">
        
        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0">
            
            <!-- LOGO / IMAGE SECTION -->
            <div class="mb-8">
                <!-- Make sure you added the image to public/images/hospital_logo.png -->
                <!-- If you don't have an image yet, this placeholder will show -->
                <img src="{{ asset('images/hospital_logo.png') }}" 
                     alt="Teaching Hospital Jaffna Logo" 
                     class="h-32 w-auto mx-auto drop-shadow-lg rounded-md"
                     onerror="this.style.display='none'"> 
            </div>

            <!-- TITLE SECTION -->
            <div class="text-center space-y-2 mb-10">
                <h1 class="text-4xl md:text-5xl font-bold text-blue-900 tracking-tight">
                    Teaching Hospital Jaffna
                </h1>
                <h2 class="text-2xl md:text-3xl font-semibold text-gray-600">
                    Anesthesia Audit System
                </h2>
                <div class="w-24 h-1 bg-blue-500 mx-auto mt-4 rounded"></div>
            </div>

            <!-- LOGIN BUTTON SECTION -->
            <div class="w-full sm:max-w-md px-6">
                @if (Route::has('login'))
                    <div class="flex flex-col items-center gap-4">
                        @auth
                            <!-- If user is already logged in, show 'Go to Dashboard' -->
                            <a href="{{ url('/dashboard') }}" 
                               class="w-full text-center px-8 py-4 bg-green-600 hover:bg-green-700 text-black text-xl font-bold rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1">
                                Go to Dashboard
                            </a>
                            <div class="text-gray-500 text-sm">
                                You are logged in as: <span class="font-bold">{{ Auth::user()->name }}</span>
                            </div>
                        @else
                            <!-- If user is NOT logged in, show Big Login Button -->
                            <a href="{{ route('login') }}" 
                               class="w-full text-center px-8 py-4 bg-blue-800 hover:bg-blue-900 text-black text-xl font-bold rounded-lg shadow-lg transition duration-300 ease-in-out transform hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-blue-300">
                                Login to System
                            </a>
                            
                            <p class="text-sm text-gray-500 mt-4 text-center">
                                Authorized Personnel Only
                            </p>
                        @endauth
                    </div>
                @endif
            </div>

            <!-- FOOTER -->
            <footer class="mt-16 text-gray-400 text-sm">
                &copy; {{ date('Y') }} Teaching Hospital Jaffna. All rights reserved.
            </footer>

        </div>
    </body>
</html>