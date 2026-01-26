<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Prosecución Estudiantil || {{ config('app.name') }}</title>

    <!-- Livewire -->
    @livewireStyles
    @wireUiScripts
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')
</head>
<body class="bg-gradient-to-br from-gray-900 via-green-900 to-slate-900 min-h-screen">
    <x-notifications />

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header con Logo -->
            <div class="text-center mb-2">
                <div class="flex flex-col items-center mb-2">
                    <h3 class="text-4xl md:text-5xl font-bold text-white mb-2 drop-shadow-2xl uppercase">U.E. Colegio Fray Luis Amigó</h3>


                    <!-- Títulos -->
                    <h1 class="text-2xl md:text-5xl font-bold text-green-200 mb-2 drop-shadow-2xl">
                        Prosecución Estudiantil
                    </h1>

                    <!--
                    <p class="text-green-200 text-lg md:text-xl">
                        Período Escolar 2024 - 2025
                    </p>
                    -->


                    <!-- Logo -->
                    <div class="mb-2">
                        <img
                            src="{{ asset('image/brand/512.png') }}"
                            alt="{{ config('app.name') }} Logo"
                            class="w-24 h-24 md:w-32 md:h-32 rounded-2xl shadow-2xl p-2 transition-transform duration-300 hover:scale-105"
                        >
                    </div>

                </div>
            </div>

            @livewire('prosecucion-wizard')
        </div>
    </div>

    @livewireScripts
    @yield('scripts')
</body>
</html>
