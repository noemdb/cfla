<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', config('app.name') . ' · Estudiante')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
            <a href="{{ route('student.lms.home') }}"
               class="flex items-center gap-2">
                <img src="{{ asset('image/logo/logo1x1.png') }}" alt="Logo" class="w-8 h-8 rounded-lg">
                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ config('app.name') }}</span>
            </a>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-xs text-gray-500 hover:text-gray-800 dark:hover:text-white transition-colors">
                        Salir
                    </button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Contenido --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
