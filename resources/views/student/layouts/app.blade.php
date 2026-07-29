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
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30"
         x-data="{ mobileOpen: false }">
        <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between gap-2">
            {{-- Logo --}}
            <a href="{{ route('student.lms.home') }}"
               class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('image/logo/logo1x1.png') }}" alt="Logo" class="w-8 h-8 rounded-lg">
                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ config('app.name') }}</span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('student.lms.home') }}"
                   class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                          {{ request()->routeIs('student.lms.home') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
                    Inicio
                </a>
                <a href="{{ route('student.lms.profile') }}"
                   class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                          {{ request()->routeIs('student.lms.profile') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
                    Perfil
                </a>
                <a href="{{ route('student.lms.academic') }}"
                   class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                          {{ request()->routeIs('student.lms.academic') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
                    Académica
                </a>
                <a href="{{ route('student.lms.lessons') }}"
                   class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                          {{ request()->routeIs('student.lms.lessons') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
                    Lecciones
                </a>
                <a href="{{ route('student.lms.resources') }}"
                   class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                          {{ request()->routeIs('student.lms.resources') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
                    Recursos
                </a>
            </nav>

            {{-- Right section: user + hamburger --}}
            <div class="flex items-center gap-2 ml-auto md:ml-0">
                <span class="hidden sm:inline text-xs text-gray-400">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-xs text-gray-500 hover:text-gray-800 dark:hover:text-white transition-colors hidden sm:inline">
                        Salir
                    </button>
                </form>
                {{-- Hamburger --}}
                <button type="button" @click="mobileOpen = !mobileOpen"
                        class="md:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        aria-label="Menú">
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.outside="mobileOpen = false"
             class="md:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 space-y-1 shadow-lg">
            <a href="{{ route('student.lms.home') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors
                      {{ request()->routeIs('student.lms.home') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                Inicio
            </a>
            <a href="{{ route('student.lms.profile') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors
                      {{ request()->routeIs('student.lms.profile') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                Perfil
            </a>
            <a href="{{ route('student.lms.academic') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors
                      {{ request()->routeIs('student.lms.academic') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                Académica
            </a>
            <a href="{{ route('student.lms.lessons') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors
                      {{ request()->routeIs('student.lms.lessons') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                Lecciones
            </a>
            <a href="{{ route('student.lms.resources') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors
                      {{ request()->routeIs('student.lms.resources') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                Recursos
            </a>
            <div class="border-t border-gray-100 dark:border-gray-700 pt-2 mt-2">
                <span class="block px-3 py-1.5 text-xs text-gray-400 sm:hidden">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="sm:hidden">
                    @csrf
                    <button class="w-full text-left px-3 py-2 text-sm font-medium rounded-lg text-gray-500 dark:text-gray-400 hover:text-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
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

    {{-- Footer --}}
    <footer class="bg-gray-100/80 backdrop-blur-md border-t border-gray-200 dark:bg-gray-900/50 dark:border-white/5 mt-auto">
        <div class="max-w-4xl mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-3 text-center md:text-left">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} <strong class="text-gray-700 dark:text-gray-300">{{ config('app.name') }}</strong> | Portal Estudiante
                </p>
                <div class="flex items-center gap-4 text-xs text-emerald-500/60 dark:text-emerald-400/60 font-medium">
                    <span>{{ config('app.name') }} LMS</span>
                    <span>Versión 1.0</span>
                </div>
            </div>
        </div>
    </footer>

    <x-notifications />

    @wireUiScripts
    @livewireScripts

    <style>
        #notifications,
        .wireui-notifications,
        [fixed] {
            z-index: 9999 !important;
        }
    </style>
</body>
</html>
