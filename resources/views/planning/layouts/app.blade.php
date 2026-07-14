<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Planificación - ' . config('app.name', 'SAEFL'))</title>

    <!-- Favicon único SAEFL -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

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

    <!-- Navbar -->
    <header class="bg-gray-900/50 backdrop-blur-md border-b border-white/5 sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('image/logo/logo1x1.png') }}" alt="Logo" class="w-10 h-10 rounded-lg">
                    <div class="hidden md:block">
                        <h2 class="text-lg font-bold text-white tracking-tight">SAEFL</h2>
                        <p class="text-xs text-emerald-400 font-medium">Planificación Académica</p>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="hidden lg:flex items-center space-x-1 mr-auto mx-2">
                    {{-- Dashboard link --}}
                    <a href="{{ route('app.planning.index') }}"
                        class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {{ request()->routeIs('app.planning.index') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300 hover:bg-white/5' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    {{-- Dropdown: Planificación (mega-dropdown con cascada) --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false"
                            class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {{ !request()->routeIs('app.planning.index') && request()->routeIs('app.planning.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300 hover:bg-white/5' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            Planificación
                            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        {{-- Mega-dropdown panel: columnas en cascada --}}
                        <div x-show="open" x-cloak
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute left-0 mt-1 w-[650px] bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-black/50 p-4 z-50 grid grid-cols-2 gap-x-2 gap-y-0">

                            {{-- Columna 1: Evaluación --}}
                            <div class="space-y-0.5">
                                <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400/60 px-3 py-1.5">Evaluación</div>
                                <a href="{{ route('app.planning.indicators.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.indicators.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Indicadores
                                </a>
                                <a href="{{ route('app.planning.activities.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.activities.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-cyan-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    Actividades
                                </a>
                                <a href="{{ route('app.planning.pevaluacions.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.pevaluacions.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Carga Académica
                                </a>
                                <a href="{{ route('app.planning.lapsos.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.lapsos.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Lapsos Académicos
                                </a>
                            </div>

                            {{-- Columna 2: Estructura Académica --}}
                            <div class="space-y-0.5">
                                <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400/60 px-3 py-1.5">Estructura Académica</div>
                                <a href="{{ route('app.planning.peducativos.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.peducativos.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    Programas Educativos
                                </a>
                                <a href="{{ route('app.planning.pestudios.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.pestudios.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    Planes de Estudio
                                </a>
                                <a href="{{ route('app.planning.asignaturas.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.asignaturas.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-pink-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                                    Asignaturas
                                </a>
                                <a href="{{ route('app.planning.grados.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.grados.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    Grados
                                </a>
                                <a href="{{ route('app.planning.secciones.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.secciones.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-lime-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    Secciones
                                </a>
                                <a href="{{ route('app.planning.pensums.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.pensums.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Pensums
                                </a>
                                <a href="{{ route('app.planning.area-conocimientos.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.planning.area-conocimientos.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4 text-violet-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    Áreas de Conocimiento
                                </a>
                            </div>

                            {{-- Footer del mega-dropdown: herramientas transversales --}}
                            <div class="col-span-2 mt-2 pt-3 border-t border-white/5 flex items-center gap-4 px-3">
                                <a href="{{ route('app.planning.diagnostico.index') }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-300 transition-colors {{ request()->routeIs('app.planning.diagnostico.*') ? 'text-emerald-400' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    Diagnóstico
                                </a>
                                <a href="{{ route('app.planning.diagnostico.referents.index') }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-300 transition-colors {{ request()->routeIs('app.planning.diagnostico.referents.*') ? 'text-emerald-400' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    Referentes
                                </a>
                                <a href="{{ route('app.planning.profesors.index') }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-300 transition-colors {{ request()->routeIs('app.planning.profesors.*') ? 'text-emerald-400' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Profesores
                                </a>
                                <a href="{{ route('app.planning.educational.competition.index') }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-300 transition-colors {{ request()->routeIs('app.planning.educational.*') ? 'text-emerald-400' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    Competiciones
                                </a>
                                <a href="{{ route('app.planning.lms.monitor') }}"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-300 transition-colors {{ request()->routeIs('app.planning.lms.*') ? 'text-emerald-400' : '' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    Contenido LMS
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown: Profesor (solo si tiene rol) --}}
                    @if(Auth::user()->isProfesor())
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.outside="open = false"
                                class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {{ request()->routeIs('app.profesors.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300 hover:bg-white/5' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profesor
                                <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                @click.outside="open = false"
                                class="absolute left-0 mt-1 w-48 bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-xl shadow-2xl shadow-black/50 py-1.5 z-50">
                                <a href="{{ route('app.profesors.home') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors {{ request()->routeIs('app.profesors.home') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('app.profesors.activities.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors {{ request()->routeIs('app.profesors.activities.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    Actividades
                                </a>
                                <a href="{{ route('app.profesors.diagnostics.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors {{ request()->routeIs('app.profesors.diagnostics.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    Diagnósticos
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Dropdown: Admin (solo si tiene permisos) --}}
                    @if(Auth::user()->is_admin || Auth::user()->is_diagnostic)
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.outside="open = false"
                                class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {{ request()->routeIs('admin.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300 hover:bg-white/5' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                                Admin
                                <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                @click.outside="open = false"
                                class="absolute left-0 mt-1 w-48 bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-xl shadow-2xl shadow-black/50 py-1.5 z-50">
                                <a href="{{ route('admin.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors {{ request()->routeIs('admin.index') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Dashboard
                                </a>
                                @if(Auth::user()->is_admin)
                                    <a href="{{ route('admin.voting.dashboard') }}"
                                        class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Votaciones
                                    </a>
                                    <a href="{{ url('admin/logs') }}"
                                        class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Logs
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </nav>

                <!-- Navbar Info -->
                @yield('navbar-info')

                <!-- Right section: role links + user -->
                <div class="flex items-center space-x-3">
                    {{-- Dropdown: Ir a --}}
                    {{-- <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false"
                            class="p-2 text-gray-400 hover:text-emerald-300 bg-white/5 hover:bg-emerald-500/20 rounded-xl border border-white/5 transition-all duration-300 group">
                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak
                            @click.outside="open = false"
                            class="absolute right-0 mt-1 w-48 bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-xl shadow-2xl shadow-black/50 py-1.5 z-50">
                            @if(Auth::user()->is_admin)
                                <a href="{{ route('admin.index') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                    Panel Admin
                                </a>
                            @endif
                            @if(Auth::user()->isProfesor())
                                <a href="{{ route('app.profesors.home') }}"
                                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Panel Profesor
                                </a>
                            @endif
                            <a href="{{ url('/') }}"
                                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                Sitio Público
                            </a>
                        </div>
                    </div> --}}

                    {{-- User info --}}
                    <div class="flex items-center space-x-4 px-2 py-1 bg-gray-900/30 backdrop-blur-md rounded-lg">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-white">{{ Auth::user()->username }}</p>
                            <p class="text-xs text-emerald-500">{{ Auth::user()->role_label }}</p>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="p-2 text-gray-400 hover:text-white bg-white/5 hover:bg-emerald-500/20 rounded-xl border border-white/5 transition-all duration-300 group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 container-fluid w-full mx-auto px-4 py-8">
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
                    <span>Planificación Académica</span>
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
    </style>
</body>

</html>
