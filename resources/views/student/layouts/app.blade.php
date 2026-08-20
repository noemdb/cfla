{{-- F1 · Modo lectura (franja 5–8): el layout aplica tipografía mayor y menos
     complejidad visual a TODO el área de estudiante cuando el alumno tiene
     edad 5–8. Criterio = edad (Estudiant::modo_lectura), la misma base que la
     mascota (C4): si la mascota y la fuente usaran bases distintas, un niño
     vería la mascota con tipografía adulta (o viceversa). Se lee una vez por
     render; la relación estudiant suele venir ya cargada del componente. --}}
@php $__modoLectura = (bool) (auth()->user()?->estudiant?->modo_lectura ?? false); @endphp
<!DOCTYPE html>
<html lang="es" x-data="{ dark: localStorage.getItem('theme') !== 'light' }">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@include('partials.title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    {{-- Tema flash-free: aplica 'dark' antes de cualquier render (por defecto oscuro) --}}
    <script>if(localStorage.getItem('theme')!=='light'){document.documentElement.classList.add('dark')}else{document.documentElement.classList.remove('dark')}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased min-h-screen flex flex-col {{ $__modoLectura ? 'modo-lectura' : '' }}">

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
                {{-- Próximamente: items por implementar, agrupados en un dropdown --}}
                <div class="relative" x-data="{ dropOpen: false }">
                    <button type="button"
                            @click="dropOpen = !dropOpen"
                            @click.outside="dropOpen = false"
                            :aria-expanded="dropOpen"
                            aria-haspopup="true"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors text-gray-400 hover:text-emerald-300 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2">
                        Próximamente
                        <svg class="w-3 h-3 transition-transform duration-200" :class="dropOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="dropOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         x-cloak
                         class="absolute right-0 mt-1.5 w-44 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1 z-50">
                        <span class="block px-3.5 py-2 text-sm text-gray-300 dark:text-gray-600 cursor-not-allowed select-none" aria-disabled="true" title="Próximamente">
                            Horario
                        </span>
                        <span class="block px-3.5 py-2 text-sm text-gray-300 dark:text-gray-600 cursor-not-allowed select-none" aria-disabled="true" title="Próximamente">
                            Diagnóstico
                        </span>
                        <span class="block px-3.5 py-2 text-sm text-gray-300 dark:text-gray-600 cursor-not-allowed select-none" aria-disabled="true" title="Próximamente">
                            Competiciones
                        </span>
                        <span class="block px-3.5 py-2 text-sm text-gray-300 dark:text-gray-600 cursor-not-allowed select-none" aria-disabled="true" title="Próximamente">
                            Kainos Lab Go
                        </span>
                    </div>
                </div>
            </nav>

            {{-- Right section: user + hamburger --}}
            <div class="flex items-center gap-2 ml-auto md:ml-0">
                {{-- Menú de usuario: avatar + Perfil/Académico/Salir --}}
                <div class="relative" x-data="{ userOpen: false }">
                    <button type="button"
                            @click="userOpen = !userOpen"
                            @click.outside="userOpen = false"
                            :aria-expanded="userOpen"
                            aria-haspopup="true"
                            aria-label="Menú de usuario"
                            class="flex items-center gap-2 rounded-full focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                        <x-lms.user-avatar :user="auth()->user()" size="sm" :ring="'ring-2 ring-emerald-500/30'" />
                        <span class="hidden sm:inline text-xs text-gray-400 max-w-[8rem] truncate">{{ auth()->user()->name }}</span>
                        <svg class="hidden sm:inline w-3 h-3 text-gray-400 transition-transform duration-200" :class="userOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="userOpen"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         x-cloak
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1 z-50">
                        <div class="px-3.5 py-2.5 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <a href="{{ route('student.lms.profile') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Perfil
                        </a>
                        <a href="{{ route('student.lms.academic') }}"
                           class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Académico
                        </a>
                        <div class="border-t border-gray-100 dark:border-gray-700 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Salir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Toggle modo claro/oscuro (E5) --}}
                <button type="button" @click="
                    dark = !dark;
                    document.documentElement.classList.toggle('dark', dark);
                    localStorage.setItem('theme', dark ? 'dark' : 'light');
                " :title="dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                    :aria-label="dark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                    class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors shrink-0 focus-visible:ring-2 ring-emerald-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-800">
                    <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>
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
             class="md:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 space-y-1 shadow-lg ">
            <a href="{{ route('student.lms.home') }}"
               class="block px-3 py-2 text-sm font-medium rounded-lg transition-colors
                      {{ request()->routeIs('student.lms.home') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                Inicio
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
            {{-- Próximamente: items por implementar, agrupados --}}
            <div class="border-t border-gray-100 dark:border-gray-700 pt-2 mt-2">
                <p class="px-3 pb-1 text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Próximamente</p>
                <span class="block px-3 py-2 text-sm font-medium rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed select-none" aria-disabled="true" title="Próximamente">
                    Horario
                </span>
                <span class="block px-3 py-2 text-sm font-medium rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed select-none" aria-disabled="true" title="Próximamente">
                    Diagnóstico
                </span>
                <span class="block px-3 py-2 text-sm font-medium rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed select-none" aria-disabled="true" title="Próximamente">
                    Competiciones
                </span>
                <span class="block px-3 py-2 text-sm font-medium rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed select-none" aria-disabled="true" title="Próximamente">
                    Kainos Lab Go
                </span>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 pt-2 mt-2">
                <span class="block px-3 py-1.5 text-xs text-gray-400 sm:hidden">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="sm:hidden">
                    @csrf
                    <button class="w-full text-left px-3 py-2 text-sm font-medium  rounded-lg text-gray-500 dark:text-gray-400 hover:text-emerald-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
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
            <div class="flex flex-col md:flex-row justify-between items-center gap-3 text-center md:text-left ">
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
        [x-data="wireui_notifications"] {
            z-index: 9999 !important;
        }
        {{-- Flotación suave de la mascota (C4) --}}
        @keyframes mascot-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-mascot-float {
            animation: mascot-float 3s ease-in-out infinite;
        }
        {{-- "Pop" de la racha al hacer login (C2): un solo disparo por carga. --}}
        @keyframes streak-pop {
            0% { transform: scale(0.85); opacity: 0; }
            60% { transform: scale(1.06); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-streak-pop {
            animation: streak-pop 0.5s cubic-bezier(0.22, 1, 0.36, 1);
        }
        @media (prefers-reduced-motion: reduce) {
            .animate-mascot-float { animation: none; }
            .animate-streak-pop { animation: none; }
        }

        {{-- F1 · Modo lectura (franja 5–8): escala tipográfica mayor para
             lectores iniciales. Las utilidades de texto de Tailwind son rem,
             no heredan el font-size del body, así que cada clase se sobrescribe
             con un selector de mayor especificidad (.modo-lectura .text-xs).
             Solo aplica cuando <body> lleva la clase modo-lectura. --}}
        .modo-lectura { font-size: 18px; }
        .modo-lectura .text-\[10px\] { font-size: 0.8125rem; }
        .modo-lectura .text-\[11px\] { font-size: 0.875rem; }
        .modo-lectura .text-\[13px\] { font-size: 0.9375rem; }
        .modo-lectura .text-\[15px\] { font-size: 1.0625rem; }
        .modo-lectura .text-xs { font-size: 0.9375rem; }
        .modo-lectura .text-sm { font-size: 1.0625rem; }
        .modo-lectura .text-base { font-size: 1.125rem; }
        .modo-lectura .text-lg { font-size: 1.1875rem; }
        .modo-lectura .text-xl { font-size: 1.375rem; }
        .modo-lectura .text-2xl { font-size: 1.75rem; }
        .modo-lectura .text-3xl { font-size: 2.125rem; }
        {{-- Cuerpo de la lección: más aire entre líneas y párrafos. --}}
        .modo-lectura .lms-content { font-size: 1.3125rem; line-height: 1.8; }
        .modo-lectura .lms-content p, .modo-lectura .lms-content li { margin-bottom: 1.125rem; }
    </style>
</body>
</html>
