@extends('layouts.dashboard')

@section('title', 'Bienvenido - Panel de Gestión')

@section('content')
<div class="fade-in">
    <!-- Welcome Section -->
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-white mb-2">Hola, {{ Auth::user()->username }}</h1>
        <p class="text-emerald-400 font-medium">Bienvenido de nuevo al ecosistema administrativo de SAEFL.</p>
    </div>

    <!-- Quick Stats / Overview (Mockup for now) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="diagnostic-card bg-emerald-500/10 border border-emerald-500/20 p-6 rounded-2xl">
            <p class="text-emerald-300 text-sm font-medium mb-1">Sesión Activa</p>
            <p class="text-white text-2xl font-bold">{{ now()->format('H:i A') }}</p>
        </div>
        <div class="diagnostic-card bg-blue-500/10 border border-blue-500/20 p-6 rounded-2xl">
            <p class="text-blue-300 text-sm font-medium mb-1">Estado del Sistema</p>
            <p class="text-white text-2xl font-bold flex items-center">
                <span class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                Operacional
            </p>
        </div>
        <div class="diagnostic-card bg-purple-500/10 border border-purple-500/20 p-6 rounded-2xl">
            <p class="text-purple-300 text-sm font-medium mb-1">Nivel de Acceso</p>
            <p class="text-white text-2xl font-bold">{{ Auth::user()->is_admin ? 'Administrador' : 'Usuario Estándar' }}</p>
        </div>
    </div>

    <h2 class="text-xl font-bold text-white mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
        </svg>
        Módulos Disponibles
    </h2>

    <!-- Modules Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        <!-- Módulo de Diagnóstico -->
        <div class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl overflow-hidden transition-all duration-300 hover:border-emerald-500/30">
            <a href="{{ route('diagnostico') }}" class="absolute inset-0 z-0"></a>
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                <svg class="w-20 h-20 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <div class="relative z-10 flex flex-col h-full pointer-events-none">
                <div class="w-12 h-12 bg-emerald-500/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Diagnóstico</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">Gestión y visualización de evaluaciones académicas.</p>
                
                <div class="mt-auto flex justify-end pointer-events-auto">
                    <a href="{{ route('admin.diagnostico.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-xl border border-emerald-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                        <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Administrar
                    </a>
                </div>
            </div>
        </div>

        <!-- Módulo de Censo -->
        <div class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl overflow-hidden transition-all duration-300 hover:border-blue-500/30">
            <a href="{{ route('census') }}" class="absolute inset-0 z-0"></a>
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                <svg class="w-20 h-20 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="relative z-10 flex flex-col h-full pointer-events-none">
                <div class="w-12 h-12 bg-blue-500/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Censo</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">Registro y control de la población estudiantil.</p>
                
                <div class="mt-auto flex justify-end pointer-events-auto">
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded-xl border border-blue-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        Administrar
                    </a>
                </div>
            </div>
        </div>

        <!-- Módulo de Matrícula -->
        <div class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl overflow-hidden transition-all duration-300 hover:border-purple-500/30">
            <a href="{{ route('enrollment') }}" class="absolute inset-0 z-0"></a>
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                <svg class="w-20 h-20 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="relative z-10 flex flex-col h-full pointer-events-none">
                <div class="w-12 h-12 bg-purple-500/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2M8 7H6"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Matrícula</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">Procesos de inscripción y pagos de escolaridad.</p>
                
                <div class="mt-auto flex justify-end pointer-events-auto">
                    <a href="#" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500/10 hover:bg-purple-500/20 text-purple-400 rounded-xl border border-purple-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2"></path>
                        </svg>
                        Administrar
                    </a>
                </div>
            </div>
        </div>

        @if(Auth::user()->is_admin)
            <!-- Módulo de Votaciones (Solo Admin) -->
            <div class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-3xl overflow-hidden transition-all duration-300 hover:border-amber-500/30">
                <a href="{{ route('admin.voting.dashboard') }}" class="absolute inset-0 z-0"></a>
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div class="w-12 h-12 bg-amber-500/20 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Votaciones</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Administración de encuestas y resultados en tiempo real.</p>
                    
                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('admin.voting.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-xl border border-amber-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Panel Control
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>

    <!-- Extra Modules / Links -->
    <div class="mt-12 pt-8 border-t border-white/5">
        <h2 class="text-xl font-bold text-white mb-6">Herramientas del Sistema</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ url('/') }}" class="bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white px-5 py-2.5 rounded-xl border border-white/5 transition-all duration-300 text-sm font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Ver Página Pública
            </a>
            @if(Auth::user()->is_admin)
                <a href="{{ url('admin/logs') }}" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-5 py-2.5 rounded-xl border border-red-500/20 transition-all duration-300 text-sm font-medium flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Auditoría de Logs
                </a>
            @endif
        </div>
    </div>
</div>
@endsection

