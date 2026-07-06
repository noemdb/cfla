@if(Auth::user()->is_admin || Auth::user()->is_diagnostic || Auth::user()->is_planner)
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @click.outside="open = false"
            class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {{ request()->routeIs('planning.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300 hover:bg-white/5' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            Planificación
            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
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
                <a href="{{ route('planning.indicators.index') }}"
                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('planning.indicators.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                    <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Indicadores
                </a>
                <a href="{{ route('planning.activities.index') }}"
                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('planning.activities.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                    <svg class="w-4 h-4 text-cyan-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Actividades
                </a>
                <a href="{{ route('planning.pevaluacions.index') }}"
                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('planning.pevaluacions.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                    <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Carga Académica
                </a>
                <a href="{{ route('planning.lapsos.index') }}"
                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('planning.lapsos.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                    <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Lapsos Académicos
                </a>
            </div>

            {{-- Columna 2: Estructura Académica --}}
            <div class="space-y-0.5">
                <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-400/60 px-3 py-1.5">Estructura Académica</div>
                <a href="{{ route('planning.peducativos.index') }}"
                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('planning.peducativos.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                    <svg class="w-4 h-4 text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Programas Educativos
                </a>
                <a href="{{ route('planning.pestudios.index') }}"
                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('planning.pestudios.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                    <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Planes de Estudio
                </a>
                <a href="{{ route('planning.asignaturas.index') }}"
                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('planning.asignaturas.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                    <svg class="w-4 h-4 text-pink-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    Asignaturas
                </a>
                <a href="{{ route('planning.grados.index') }}"
                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('planning.grados.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                    <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Grados
                </a>
                <a href="{{ route('planning.secciones.index') }}"
                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('planning.secciones.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                    <svg class="w-4 h-4 text-lime-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Secciones
                </a>
                <a href="{{ route('planning.pensums.index') }}"
                    class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('planning.pensums.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                    <svg class="w-4 h-4 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Pensums
                </a>
            </div>

            {{-- Footer: herramientas transversales --}}
            <div class="col-span-2 mt-2 pt-3 border-t border-white/5 flex items-center gap-4 px-3">
                <a href="{{ route('planning.diagnostico.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-300 transition-colors {{ request()->routeIs('planning.diagnostico.*') && !request()->routeIs('planning.diagnostico.referents.*') ? 'text-emerald-400' : '' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Diagnóstico
                </a>
                <a href="{{ route('planning.diagnostico.referents.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-300 transition-colors {{ request()->routeIs('planning.diagnostico.referents.*') ? 'text-emerald-400' : '' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Referentes
                </a>
                <a href="{{ route('planning.profesors.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-300 transition-colors {{ request()->routeIs('planning.profesors.*') ? 'text-emerald-400' : '' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Profesores
                </a>
                <a href="{{ route('planning.educational.competition.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-300 transition-colors {{ request()->routeIs('planning.educational.*') ? 'text-emerald-400' : '' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Competiciones
                </a>
                {{-- <a href="{{ route('planning.lms.monitor') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-emerald-300 transition-colors {{ request()->routeIs('planning.lms.*') ? 'text-emerald-400' : '' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Contenido LMS
                </a> --}}
            </div>
        </div>
    </div>
@endif
