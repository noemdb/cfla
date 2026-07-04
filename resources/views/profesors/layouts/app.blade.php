<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Panel del Profesor - ' . config('app.name', 'SAEFL'))</title>

    <!-- Favicon único SAEFL -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('image/brand/512.png') }}" sizes="512x512">
    <meta name="theme-color" content="#0d9488">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @wireUiScripts
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @yield('styles')
</head>

<body
    class="bg-[#020617] text-gray-100 font-sans antialiased min-h-screen bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-emerald-950/30 via-gray-950 to-black selection:bg-emerald-500 selection:text-white bg-fixed flex flex-col">

    <div class="relative z-[100]">
        <x-notifications />
        <x-dialog />
    </div>

    <!-- Navbar compartido para todos los roles -->
    <x-role-navbar subtitle="Panel del Profesor">
        <x-slot:navbarInfo>@yield('navbar-info')</x-slot:navbarInfo>
        @include('components.navbars.profesor-items')
        @include('components.navbars.admin-items')
        @include('components.navbars.planning-items')
    </x-role-navbar>

    <!-- Main Content -->
    <main class="flex-1 container-fluid w-full mx-auto  px-4 py-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900/50 backdrop-blur-md border-t border-white/5 mt-auto">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 text-center md:text-left">
                <p class="text-xs text-gray-500">
                    &copy; {{ date('Y') }} <strong>SAEFL</strong> | Sistema de Gestión y Planificación Académica.
                </p>
                <div class="flex space-x-6 text-xs text-emerald-500/60 font-medium">
                    <span>Panel del Profesor</span>
                    <span>Versión 2.0</span>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    @yield('script')

    <style>
        .diagnostic-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .diagnostic-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.1);
        }
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #020617; }
        ::-webkit-scrollbar-thumb { background: #064e3b; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #10b981; }

        #notifications,
        .wireui-notifications,
        [fixed] {
            z-index: 9999 !important;
        }

        .progress-bar-sm {
            height: 4px;
            border-radius: 2px;
            background: rgba(255,255,255,0.06);
            overflow: hidden;
        }
        .progress-bar-sm-fill {
            height: 100%;
            border-radius: 2px;
            transition: width 0.6s ease;
        }
    </style>
</body>

</html>
