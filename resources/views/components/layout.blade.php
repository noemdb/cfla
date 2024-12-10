<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Innovative App') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    @vite('resources/css/app.css')

    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="bg-gray-100 text-gray-900 antialiased">

    <!-- Main Layout -->
    <div id="app" class="flex flex-col min-h-screen">
        
        <!-- Header -->
        <header class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <a href="{{ route('home') }}" class="text-lg font-bold text-blue-600">
                        {{ config('app.name', 'Innovative App') }}
                    </a>
                    
                    <button x-data x-on:click="alert('Menu!')" class="md:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-white shadow-inner">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-4 text-center text-gray-500 text-sm">
                    © {{ date('Y') }} {{ config('app.name', 'Innovative App') }}. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Alpine.js -->
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}

    <!-- GSAP -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}

    <!-- Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            gsap.from('header', { opacity: 0, y: -20, duration: 0.5 });
            gsap.from('footer', { opacity: 0, y: 20, duration: 0.5 });
        });
    </script>
</body>
</html>
