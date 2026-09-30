@extends('layouts.dashboard')

@section('title', 'Bienvenido - Panel de Gestión')

@section('content')
    <div class="fade-in">
        @php
            $activeUsers = \App\Models\User::where('is_active', 1)->count();
            $totalProfesores = \App\Models\User::where('is_profesor', 1)->count();
            $activeProfesores = \App\Models\User::where('is_profesor', 1)->where('is_active', 1)->count();
            try {
                $totalNotifications = \Illuminate\Support\Facades\DB::table('notifications')->count();
                $readNotifications = \Illuminate\Support\Facades\DB::table('notifications')->whereNotNull('read_at')->count();
            } catch (\Throwable $e) {
                $totalNotifications = 0;
                $readNotifications = 0;
            }
            $sessionsPayload = \App\Events\ActiveSessionsUpdated::currentPayload();
            $activeSessionsInit = $sessionsPayload['active_sessions'];
            $authOnlineInit = $sessionsPayload['authenticated_online'];
            try {
                $onlineUsers = \App\Models\User::with('profile')
                    ->where('last_seen_at', '>=', now()->subMinutes(15))
                    ->orderByDesc('last_seen_at')
                    ->limit(30)
                    ->get();
            } catch (\Throwable $e) {
                $onlineUsers = collect();
            }
        @endphp

        <!-- Indicators Section -->
        <section aria-label="Indicadores principales">
            <!-- Fila 1: 4 indicator cards en una línea flex -->
            <div class="flex flex-col md:flex-row gap-6 mb-6">
                <!-- Card 1: Notificaciones Registradas/Leídas -->
                <div class="diagnostic-card flex-1 bg-emerald-500/10 border border-emerald-500/20 p-6 rounded-lg">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-emerald-300 text-sm font-medium">Notificaciones Registradas</p>
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                    <p class="text-white text-2xl font-extrabold">{{ number_format($totalNotifications) }}</p>
                    <p class="text-emerald-400/70 text-xs font-medium mt-1">{{ number_format($readNotifications) }} leídas · {{ number_format($totalNotifications - $readNotifications) }} sin leer</p>
                </div>
                <!-- Card 2: Usuarios Activos -->
                <div class="diagnostic-card flex-1 bg-blue-500/10 border border-blue-500/20 p-6 rounded-lg">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-blue-300 text-sm font-medium">Usuarios Activos</p>
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-white text-2xl font-extrabold">{{ number_format($activeUsers) }}</p>
                    <p class="text-blue-400/70 text-xs font-medium mt-1">Con estado activo</p>
                </div>
                <!-- Card 3: Profesores Activos/Registrados -->
                <div class="diagnostic-card flex-1 bg-purple-500/10 border border-purple-500/20 p-6 rounded-lg">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-purple-300 text-sm font-medium">Profesores Activos</p>
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                        </svg>
                    </div>
                    <p class="text-white text-2xl font-extrabold">{{ number_format($activeProfesores) }}</p>
                    <p class="text-purple-400/70 text-xs font-medium mt-1">De {{ number_format($totalProfesores) }} registrados</p>
                </div>
                <!-- Card 4: Sesiones Activas -->
                <div class="diagnostic-card flex-1 bg-amber-500/10 border border-amber-500/20 p-6 rounded-lg flex flex-col">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-amber-300 text-sm font-medium">Sesiones Activas</p>
                        <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                    </div>
                    <p class="text-white text-2xl font-extrabold"><span id="indicatorActiveSessions">{{ $activeSessionsInit }}</span></p>
                    <p class="text-amber-400/70 text-xs font-medium mt-1">Usuarios conectados ahora</p>
                    <div class="mt-auto pt-4 flex justify-end">
                        <button type="button" x-data
                            x-on:click="$dispatch('wireui:dialog:active-sessions', { options: { icon: 'info', close: 'Cerrar' }, componentId: 'admin-dashboard' })"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 rounded-lg border border-amber-500/20 transition-all duration-300 text-[11px] font-bold uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver usuarios
                        </button>
                    </div>
                </div>
            </div>

            <!-- Fila 2: chart ApexCharts online — usuarios conectados con sesión activa -->
            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg mb-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-white font-bold flex items-center gap-2">
                            <span id="sessionsWsDot" class="w-2.5 h-2.5 bg-gray-500 rounded-full"></span>
                            Usuarios conectados con sesión activa
                        </h3>
                        <p class="text-gray-400 text-xs mt-1">
                            <span id="sessionsWsStatus" class="font-bold text-gray-500">Conectando…</span>
                            <span class="text-gray-600"> · WebSocket (Reverb) · canal privado</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-extrabold text-emerald-400"><span id="chartLiveCount">{{ $activeSessionsInit }}</span></p>
                        <p class="text-[11px] uppercase tracking-widest text-gray-500 font-bold">En línea · <span id="chartAuthOnline">{{ $authOnlineInit }}</span> autenticados</p>
                    </div>
                </div>
                <div id="activeSessionsChart"></div>
            </div>
        </section>

        <!-- Dialog: usuarios con sesión activa (WireUI x-dialog) -->
        <x-dialog id="active-sessions" title="Usuarios con sesión activa" width="lg" blur="lg">
            <div class="text-left">
                <p class="text-xs text-gray-500 dark:text-slate-400 mb-4">
                    Actividad en los últimos 15 minutos · {{ $onlineUsers->count() }} usuario(s) · actualizado al cargar la página ({{ now()->format('H:i:s') }})
                </p>
                @if ($onlineUsers->isEmpty())
                    <div class="py-8 text-center">
                        <p class="text-sm font-medium text-gray-500 dark:text-slate-400">Sin usuarios conectados en este momento.</p>
                    </div>
                @else
                    <ul class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5 -mx-1 px-1">
                        @foreach ($onlineUsers as $onlineUser)
                            <li class="flex items-center gap-3 py-2.5">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-sm font-extrabold uppercase">
                                    {{ mb_substr($onlineUser->full_name ?? $onlineUser->username, 0, 1) }}
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block truncate text-sm font-bold text-gray-800 dark:text-slate-100">{{ $onlineUser->full_name ?? $onlineUser->username }}</span>
                                    <span class="block truncate text-xs text-gray-500 dark:text-slate-400">@@{{ $onlineUser->username }} · {{ $onlineUser->role_label }}</span>
                                </span>
                                <span class="shrink-0 text-right">
                                    <span class="flex items-center gap-1.5 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                        {{ $onlineUser->last_seen_at?->diffForHumans() }}
                                    </span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </x-dialog>

        <!-- Welcome Section -->
        <div class="mb-10">
            <h1 class="text-lg font-extrabold text-white mb-2">Hola, {{ Auth::user()->username }}</h1>
            <p class="text-emerald-400 font-medium">Bienvenido de nuevo al ecosistema administrativo de SAEFL.</p>
        </div>

        <h2 class="text-lg font-bold text-white mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                </path>
            </svg>
            Módulos Disponibles
        </h2>

        <!-- Modules Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

            <!-- Módulo de Competiciones Académicas -->
            <div
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 hover:border-emerald-500/30">
                <a href="{{ route('admin.educational.competition.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Competiciones Académicas</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Gestión de retos educativos, debates y control de
                        puntajes en vivo.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('admin.educational.competition.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                </path>
                            </svg>
                            Administrar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Módulo de Diagnóstico -->
            <div
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 hover:border-emerald-500/30">
                <a href="{{ route('diagnostico') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Diagnóstico</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Gestión y visualización del Diagnóstico
                        académico.</p>


                    @if (Auth::user()->isAdminOrDiagnostic())
                        <div class="mt-auto flex justify-end pointer-events-auto">
                            <a href="{{ route('admin.diagnostico.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                                <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Administrar
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Módulo de Censo -->
            <div
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 hover:border-blue-500/30">
                <a href="{{ route('census') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Censo</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Registro y control de la población estudiantil.
                    </p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="#"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded-lg border border-blue-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                                </path>
                            </svg>
                            Administrar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Módulo de Matrícula -->
            <div
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 hover:border-purple-500/30">
                <a href="{{ route('enrollment') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2M8 7H6">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Matrícula</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Procesos de inscripción y pagos de escolaridad.
                    </p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="#"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500/10 hover:bg-purple-500/20 text-purple-400 rounded-lg border border-purple-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2">
                                </path>
                            </svg>
                            Administrar
                        </a>
                    </div>
                </div>
            </div>

            @if (Auth::user()->isAdminOrDiagnostic())
                <!-- Módulo de Planificación -->
                <div
                    class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 hover:border-cyan-500/30">
                    <a href="{{ route('app.planning.index') }}" class="absolute inset-0 z-0"></a>
                    <div
                        class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                        <svg class="w-20 h-20 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                    </div>
                    <div class="relative z-10 flex flex-col h-full pointer-events-none">
                        <div
                            class="w-12 h-12 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Planificación</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-6">Gestión y organización de competiciones académicas y diagnóstico institucional.</p>

                        <div class="mt-auto flex justify-end pointer-events-auto">
                            <a href="{{ route('app.planning.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg border border-cyan-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                    </path>
                                </svg>
                                Ir a Planificación
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if (Auth::user()->is_admin || Auth::user()->is_diagnostic)
                <!-- Módulo de Votaciones -->
                <div
                    class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 hover:border-amber-500/30">
                    <a href="{{ route('admin.voting.dashboard') }}" class="absolute inset-0 z-0"></a>
                    <div
                        class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                        <svg class="w-20 h-20 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <div class="relative z-10 flex flex-col h-full pointer-events-none">
                        <div
                            class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Votaciones</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-6">Administración de encuestas y resultados en
                            tiempo real.</p>

                        <div class="mt-auto flex justify-end pointer-events-auto">
                            <a href="{{ route('admin.voting.dashboard') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-lg border border-amber-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                Panel Control
                            </a>
                        </div>
                    </div>
                </div>
            @endif

                <!-- Módulo de Usuarios -->
                <div
                    class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 hover:border-emerald-500/30">
                    <a href="{{ route('admin.users.index') }}" class="absolute inset-0 z-0"></a>
                    <div
                        class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                        <svg class="w-20 h-20 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="relative z-10 flex flex-col h-full pointer-events-none">
                        <div
                            class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Usuarios</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-6">Gestión de cuentas, roles y accesos al sistema.</p>

                        <div class="mt-auto flex justify-end pointer-events-auto">
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                    </path>
                                </svg>
                                Administrar
                            </a>
                        </div>
                    </div>
                </div>

        </div>

        @if (Auth::user()->is_admin)
            <h2 class="text-lg font-bold text-white mb-6 mt-12 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                    </path>
                </svg>
                Gestión de Servidor
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Database Card -->
                <div
                    class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 hover:border-blue-500/30">
                    <div
                        class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                        <svg class="w-20 h-20 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                            </path>
                        </svg>
                    </div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div
                            class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Base de Datos</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-6">Generación y descarga de respaldos completos en
                            formato SQL.</p>

                        <div class="mt-auto flex justify-end">
                            <a href="{{ route('admin.database.backup') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded-lg border border-blue-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Descargar Respaldo
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Reverb / Pulse Card -->
                <div
                    class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 hover:border-cyan-500/30">
                    <div
                        class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                        <svg class="w-20 h-20 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z">
                            </path>
                        </svg>
                    </div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div
                            class="w-12 h-12 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-2">Monitor Reverb</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-6">Métricas de WebSockets, conexiones activas y rendimiento del servidor.</p>

                        <div class="mt-auto flex justify-end">
                            <a href="{{ url('/pulse') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg border border-cyan-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Abrir Consola
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        @endif

        <!-- Extra Modules / Links -->
        <div class="mt-12 pt-8 border-t border-white/5">
            <h2 class="text-lg font-bold text-white mb-6">Herramientas del Sistema</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ url('/') }}"
                    class="bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white px-5 py-2.5 rounded-lg border border-white/5 transition-all duration-300 text-sm font-medium flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Ver Página Pública
                </a>
                @if (Auth::user()->is_admin)
                    <a href="{{ url('admin/logs') }}"
                        class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-5 py-2.5 rounded-lg border border-red-500/20 transition-all duration-300 text-sm font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        Auditoría de Logs
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    @parent
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.querySelector('#activeSessionsChart');
            if (!el || typeof ApexCharts === 'undefined') return;

            const MAX_POINTS = 20;
            const initial = parseInt(document.querySelector('#chartLiveCount')?.textContent ?? '0', 10) || 0;
            let seriesData = Array(MAX_POINTS).fill(initial);
            let labels = Array.from({ length: MAX_POINTS }, (_, i) => {
                const d = new Date(Date.now() - (MAX_POINTS - 1 - i) * 60000);
                return d.toLocaleTimeString('es-VE', { hour: '2-digit', minute: '2-digit' });
            });

            const options = {
                chart: { type: 'area', height: 280, animations: { enabled: true, easing: 'linear', dynamicAnimation: { speed: 500 } }, toolbar: { show: false }, zoom: { enabled: false }, foreColor: '#94a3b8' },
                series: [{ name: 'Sesiones activas', data: seriesData }],
                xaxis: { categories: labels, labels: { rotate: -30, style: { fontSize: '10px' } } },
                yaxis: { min: 0, forceNiceScale: true, labels: { formatter: (v) => Math.round(v) }, title: { text: 'Usuarios conectados' } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2, colors: ['#10b981'] },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 100] } },
                markers: { size: 0 },
                colors: ['#10b981'],
                grid: { borderColor: 'rgba(255,255,255,0.06)' },
                tooltip: { theme: 'dark', x: { show: true }, y: { title: { formatter: () => 'Conectados:' } } },
            };

            const chart = new ApexCharts(el, options);
            chart.render();

            function setWsStatus(online) {
                const status = document.querySelector('#sessionsWsStatus');
                const dot = document.querySelector('#sessionsWsDot');
                if (status) {
                    status.textContent = online ? 'En vivo' : 'Sin conexión';
                    status.classList.toggle('text-emerald-400', online);
                    status.classList.toggle('text-gray-500', !online);
                }
                if (dot) {
                    dot.classList.toggle('bg-emerald-500', online);
                    dot.classList.toggle('animate-pulse', online);
                    dot.classList.toggle('bg-gray-500', !online);
                }
            }

            function applySessionsUpdate(value, stamp, authOnline) {
                seriesData.push(value);
                labels.push(stamp);
                if (seriesData.length > MAX_POINTS) seriesData.shift();
                if (labels.length > MAX_POINTS) labels.shift();

                chart.updateOptions({ xaxis: { categories: [...labels] } }, false, false);
                chart.updateSeries([{ name: 'Sesiones activas', data: [...seriesData] }]);

                const live = document.querySelector('#chartLiveCount');
                if (live) live.textContent = value;
                const indicator = document.querySelector('#indicatorActiveSessions');
                if (indicator) indicator.textContent = value;
                if (typeof authOnline !== 'undefined' && authOnline !== null) {
                    const authEl = document.querySelector('#chartAuthOnline');
                    if (authEl) authEl.textContent = authOnline;
                }
            }

            // Tiempo real vía WebSocket (Reverb, canal privado admin.sessions).
            // Sin polling: el backend emite `sessions.updated` cada minuto
            // (scheduler) y al abrir el dashboard.
            if (window.Echo) {
                window.Echo.private('admin.sessions')
                    .listen('.sessions.updated', (e) => {
                        setWsStatus(true);
                        applySessionsUpdate(
                            parseInt(e.active_sessions ?? 0, 10) || 0,
                            e.timestamp ?? new Date().toLocaleTimeString(),
                            e.authenticated_online
                        );
                    });

                try {
                    const connection = window.Echo.connector?.pusher?.connection;
                    connection?.bind('connected', () => setWsStatus(true));
                    connection?.bind('disconnected', () => setWsStatus(false));
                    connection?.bind('error', () => setWsStatus(false));
                    if (connection?.state === 'connected') setWsStatus(true);
                } catch (e) {
                    // Silencioso: el estado queda en "Conectando…".
                }
            } else {
                setWsStatus(false);
            }
        });
    </script>
@endsection
