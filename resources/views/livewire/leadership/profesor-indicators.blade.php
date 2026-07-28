<div class="fade-in space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Indicadores de Profesores</h1>
        <p class="text-amber-600 dark:text-amber-400 font-medium text-sm">
            {{ count($profesores) }} profesor(es) en tus áreas asignadas
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- ─── Sidebar colapsable horizontalmente ─── --}}
        <div x-data="{ open: true }"
             class="rounded-2xl border border-white/5 bg-gray-900 p-3 transition-all duration-300 ease-in-out shrink-0"
             :class="open ? 'lg:w-[300px]' : 'lg:w-[68px]'">
            {{-- Toggle button --}}
            <button @click="open = !open" type="button"
                class="w-full flex items-center justify-center lg:justify-end px-1 py-1 text-gray-500 hover:text-amber-400 transition-colors mb-1">
                <svg x-show="open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
                <svg x-show="!open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- ─── EXPANDIDO ─── --}}
            <div x-show="open" x-cloak>
                <div class="space-y-3">
                    {{-- Header --}}
                    <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60 px-1 pb-2 border-b border-white/5">
                        Profesores
                    </div>

                    {{-- Search --}}
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input wire:model.live.debounce.300ms="searchProfesor" type="text" placeholder="Buscar profesor..."
                            class="w-full pl-9 pr-8 py-2 text-sm bg-white/5 border border-white/10 rounded-lg text-gray-300 placeholder-gray-600 outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500/40 transition-all">
                        @if($searchProfesor)
                            <button wire:click="$set('searchProfesor', '')" type="button"
                                class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-500 hover:text-gray-300 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>

                    @if($searchProfesor)
                        <div class="text-[10px] text-gray-500 px-1">{{ count($filteredProfesores) }} resultado(s)</div>
                    @endif

                    {{-- Lista --}}
                    <div class="space-y-1 max-h-[48vh] overflow-y-auto">
                        @forelse($filteredProfesores as $prof)
                            <button wire:key="prof-{{ $prof->id }}" wire:click="selectProfesor({{ $prof->id }})"
                                class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors flex items-center gap-3 {{ $selectedProfesorId == $prof->id ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'text-gray-300 hover:text-amber-300 hover:bg-white/5' }}">
                                <span class="w-7 h-7 rounded-lg bg-gray-800 flex items-center justify-center text-[10px] font-bold text-gray-500 shrink-0">{{ strtoupper(substr($prof->name, 0, 1)) }}{{ strtoupper(substr($prof->lastname, 0, 1)) }}</span>
                                <span class="font-medium truncate">{{ $prof->lastname }}, {{ $prof->name }}</span>
                            </button>
                        @empty
                            <p class="text-gray-500 text-sm px-3 py-4 text-center">
                                @if($searchProfesor) Ningún profesor coincide con tu búsqueda. @else No hay profesores asignados a tus áreas. @endif
                            </p>
                        @endforelse
                    </div>

                    {{-- Lapso selector --}}
                    <div class="pt-3 border-t border-white/5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-500 block mb-1.5">Filtrar por lapso</label>
                        <select wire:model.live="selectedLapsoId"
                            class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none transition-all">
                            <option value="">Todos los lapsos</option>
                            @foreach($lapsos as $l)
                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ─── COLAPSADO (solo íconos) ─── --}}
            <div x-show="!open" class="flex flex-col items-center gap-2 py-2" x-cloak>
                @forelse($filteredProfesores as $prof)
                    <button wire:key="prof-icon-{{ $prof->id }}" wire:click="selectProfesor({{ $prof->id }})"
                        class="w-10 h-10 rounded-xl flex items-center justify-center text-[11px] font-bold transition-all duration-200 {{ $selectedProfesorId == $prof->id ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30 shadow-sm shadow-amber-500/10' : 'bg-white/5 text-gray-500 hover:bg-white/10 hover:text-amber-300 border border-transparent' }}"
                        title="{{ $prof->lastname }}, {{ $prof->name }}">
                        {{ strtoupper(substr($prof->name, 0, 1)) }}{{ strtoupper(substr($prof->lastname, 0, 1)) }}
                    </button>
                @empty
                    <span class="text-gray-600 text-[9px] text-center leading-tight">Sin<br>prof.</span>
                @endforelse
            </div>
        </div>

        {{-- ─── Detalle del profesor seleccionado ─── --}}
        <div class="flex-1 min-w-0 space-y-4">
            @if($profesor && $kpi)

                {{-- ── D) INFORMACIÓN GENERAL ── --}}
                <div class="rounded-2xl border border-white/5 bg-gray-900 p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4 min-w-0">
                            {{-- Avatar placeholder --}}
                            <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/10 border border-amber-500/20 flex items-center justify-center shrink-0">
                                <span class="text-xl font-black text-amber-400">
                                    {{ strtoupper(substr($profesor->name, 0, 1)) }}{{ strtoupper(substr($profesor->lastname, 0, 1)) }}
                                </span>
                            </div>
                            <div class="min-w-0 space-y-1">
                                <h2 class="text-base font-bold text-white truncate">{{ $profesor->full_name }}</h2>
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-400">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11a3 3 0 10-4 2.83M15 11c1.306 0 2.417.835 2.83 2"/></svg>
                                        {{ $profesorInfo['ci'] }}
                                    </span>
                                    @if($profesorInfo['email'])
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            {{ $profesorInfo['email'] }}
                                        </span>
                                    @endif
                                    @if($profesorInfo['phone'])
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            {{ $profesorInfo['phone'] }}
                                        </span>
                                    @endif
                                    @if($profesorInfo['cellphone'])
                                        <span class="inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            {{ $profesorInfo['cellphone'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- Status badge --}}
                        <span @class([
                            'shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border',
                            'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' => $profesorInfo['status_active'] === 'true',
                            'bg-red-500/10 text-red-400 border-red-500/20' => $profesorInfo['status_active'] !== 'true',
                        ])>
                            <span class="w-1.5 h-1.5 rounded-full {{ $profesorInfo['status_active'] === 'true' ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                            {{ $profesorInfo['status_active'] === 'true' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>

                {{-- ── KPIs EXISTENTES (IEE, IRE, Real Notas, Carga) ── --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    {{-- IEE --}}
                    <div class="rounded-2xl border border-white/5 bg-gray-900 p-4 space-y-1.5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">IEE</div>
                        <div class="text-lg font-black {{ ($kpi['iee'] ?? 0) < 70 ? 'text-red-400' : 'text-emerald-400' }}">
                            {{ $kpi['iee'] ?? '—' }}<span class="text-xs font-medium text-gray-600">%</span>
                        </div>
                    </div>
                    {{-- IRE --}}
                    <div class="rounded-2xl border border-white/5 bg-gray-900 p-4 space-y-1.5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">IRE</div>
                        <div class="text-lg font-black {{ ($kpi['ire'] ?? 0) < 70 ? 'text-red-400' : 'text-emerald-400' }}">
                            {{ $kpi['ire'] ?? '—' }}<span class="text-xs font-medium text-gray-600">%</span>
                        </div>
                    </div>
                    {{-- Notas reales --}}
                    <div class="rounded-2xl border border-white/5 bg-gray-900 p-4 space-y-1.5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Notas (Real)</div>
                        <div class="text-lg font-black text-white">{{ $kpi['real_notas'] ?? 0 }}</div>
                        <div class="text-[10px] text-gray-600">
                            Meta: <span class="font-semibold text-gray-400">{{ $kpi['goal_notas'] ?? 0 }}</span>
                        </div>
                    </div>
                    {{-- Carga (Pevas) --}}
                    <div class="rounded-2xl border border-white/5 bg-gray-900 p-4 space-y-1.5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Carga</div>
                        <div class="text-lg font-black text-white">{{ $kpi['total_pevas'] ?? 0 }}</div>
                        <div class="text-[10px] text-gray-600">Plan. Evaluación</div>
                    </div>
                </div>

                {{-- ── B) CARGA ACADÉMICA ── --}}
                <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-300">Carga Académica</h3>
                        @if($selectedLapsoId)
                            <span class="text-[10px] text-gray-600">(filtrado por lapso)</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Asignaturas --}}
                        <div class="space-y-2">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Asignaturas</div>
                            @forelse($cargaAcademica['asignaturas'] ?? [] as $asig)
                                <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white/[0.03] border border-white/5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500/60 shrink-0"></span>
                                    <span class="text-sm text-gray-300 font-medium">{{ $asig['name'] }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-gray-600 italic">Sin asignaturas asignadas.</p>
                            @endforelse
                        </div>

                        {{-- Secciones Guía --}}
                        <div class="space-y-2">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Guía/Tutor de</div>
                            @forelse($cargaAcademica['seccion_guias'] ?? [] as $seccion)
                                <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white/[0.03] border border-white/5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500/60 shrink-0"></span>
                                    <span class="text-sm text-gray-300 font-medium">{{ $seccion->name ?? $seccion['name'] ?? '' }}</span>
                                    @php $lapsoName = $seccion->lapso_name ?? $seccion['lapso_name'] ?? null; @endphp
                                    @if($lapsoName)
                                        <span class="text-[10px] text-gray-600 ml-auto">{{ $lapsoName }}</span>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-gray-600 italic">Sin secciones como guía.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ── C) RESUMEN DE ACTIVIDADES ── --}}
                <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-300">Actividades de Planificación</h3>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-white/[0.03] border border-white/5 p-3.5 text-center space-y-1">
                            <div class="text-2xl font-black text-white">{{ $resumenActividades['total'] }}</div>
                            <div class="text-[10px] font-medium text-gray-500">Totales</div>
                        </div>
                        <div class="rounded-xl bg-emerald-500/5 border border-emerald-500/10 p-3.5 text-center space-y-1">
                            <div class="text-2xl font-black text-emerald-400">{{ $resumenActividades['approved'] }}</div>
                            <div class="text-[10px] font-medium text-emerald-400/60">Aprobadas</div>
                        </div>
                        <div class="rounded-xl bg-amber-500/5 border border-amber-500/10 p-3.5 text-center space-y-1">
                            <div class="text-2xl font-black text-amber-400">{{ $resumenActividades['pending'] }}</div>
                            <div class="text-[10px] font-medium text-amber-400/60">Pendientes</div>
                        </div>
                    </div>

                    {{-- Barra de progreso --}}
                    @if($resumenActividades['total'] > 0)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">Progreso de aprobación</span>
                                <span class="font-bold text-gray-300">{{ $resumenActividades['progress_pct'] }}%</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-white/5 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r from-amber-500 to-emerald-400 transition-all duration-500"
                                     style="width: {{ $resumenActividades['progress_pct'] }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ── E) RESUMEN DE LECCIONES LMS ── --}}
                <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-300">Lecciones LMS</h3>
                    </div>

                    @if($resumenLecciones->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            {{-- Draft --}}
                            <div class="rounded-xl bg-white/[0.03] border border-white/5 p-3.5 text-center space-y-1">
                                <div class="text-xl font-black text-gray-400">{{ $resumenLecciones['DRAFT'] ?? 0 }}</div>
                                <div class="text-[10px] font-medium text-gray-600">Borradores</div>
                            </div>
                            {{-- Scheduled --}}
                            <div class="rounded-xl bg-amber-500/5 border border-amber-500/10 p-3.5 text-center space-y-1">
                                <div class="text-xl font-black text-amber-400">{{ $resumenLecciones['SCHEDULED'] ?? 0 }}</div>
                                <div class="text-[10px] font-medium text-amber-400/60">Programadas</div>
                            </div>
                            {{-- Published --}}
                            <div class="rounded-xl bg-emerald-500/5 border border-emerald-500/10 p-3.5 text-center space-y-1">
                                <div class="text-xl font-black text-emerald-400">{{ $resumenLecciones['PUBLISHED'] ?? 0 }}</div>
                                <div class="text-[10px] font-medium text-emerald-400/60">Publicadas</div>
                            </div>
                            {{-- Archived --}}
                            <div class="rounded-xl bg-red-500/5 border border-red-500/10 p-3.5 text-center space-y-1">
                                <div class="text-xl font-black text-red-400">{{ $resumenLecciones['ARCHIVED'] ?? 0 }}</div>
                                <div class="text-[10px] font-medium text-red-400/60">Archivadas</div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center py-8 text-center">
                            <div>
                                <svg class="w-10 h-10 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                </svg>
                                <p class="text-sm text-gray-600">Sin lecciones LMS en este lapso.</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ── F) CHART: FLUJO DE ACTIVIDADES ── --}}
                <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Flujo de Actividades</h3>
                        <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/5 rounded-lg p-0.5">
                            @foreach(['7d' => '7 días', '30d' => '30 días', '3m' => '3 meses', 'all' => 'Todo'] as $val => $label)
                                <button wire:click="$set('activityRange', '{{ $val }}')"
                                    class="px-3 py-1.5 min-h-[36px] text-[10px] font-bold uppercase tracking-wider rounded-md transition-all duration-200 whitespace-nowrap
                                    {{ $activityRange === $val ? 'bg-amber-500/20 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/5' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                        {{-- Chart 1: Actividades --}}
                        <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-7 h-7 bg-amber-500/20 rounded-md flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Actividades</span>
                                <span class="ml-auto text-[9px] text-gray-500 dark:text-gray-500">{{ count($chartActivitiesFlow) }} día(s)</span>
                            </div>
                            <div wire:ignore>
                                <div id="pi-activities-flow-chart" class="w-full" style="min-height: 200px;"></div>
                            </div>
                        </div>
                        {{-- Chart 2: Lecciones --}}
                        <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-7 h-7 bg-sky-500/20 rounded-md flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Lecciones</span>
                                <span class="ml-auto text-[9px] text-gray-500 dark:text-gray-500">{{ count($chartLessonsFlow) }} día(s)</span>
                            </div>
                            <div wire:ignore>
                                <div id="pi-lessons-flow-chart" class="w-full" style="min-height: 200px;"></div>
                            </div>
                        </div>
                        {{-- Chart 3: Estado (aprobadas vs pendientes) --}}
                        <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-7 h-7 bg-emerald-500/20 rounded-md flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Aprobadas vs Pendientes</span>
                                <span class="ml-auto text-[9px] text-gray-500 dark:text-gray-500">{{ count($chartStatusFlow['categories'] ?? []) }} día(s)</span>
                            </div>
                            <div wire:ignore>
                                <div id="pi-status-flow-chart" class="w-full" style="min-height: 200px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                {{-- ── Placeholder (sin profesor seleccionado) ── --}}
                <div class="rounded-2xl border border-white/5 bg-gray-900 p-12 text-center">
                    <svg class="w-14 h-14 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-gray-500 font-medium mb-1">Selecciona un profesor</p>
                    <p class="text-gray-600 text-sm">para ver sus indicadores y carga académica.</p>
                </div>
            @endif
        </div>
    </div>
    @include('leadership.help-profesores')
    </div>{{-- /root --}}

{{-- ═══ ApexCharts scripts ═══ --}}
<script>
(function() {
    'use strict';

    var CHARTS = [
        {
            id: 'pi-activities-flow-chart',
            varName: 'piActivitiesFlowChart',
            dataProp: 'chartActivitiesFlow',
            build: function(rawData) {
                return {
                    series: [{ name: 'Actividades', data: rawData || [] }],
                    chart: { type: 'area', height: 200, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, system-ui, sans-serif', background: 'transparent' },
                    colors: ['#f59e0b'],
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 3, colors: ['#f59e0b'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 5 } },
                    dataLabels: { enabled: false },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] } },
                    xaxis: { type: 'category', labels: { style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 } }, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 } }, tickAmount: 4, forceNiceScale: true },
                    grid: { borderColor: '#37415140', strokeDashArray: 4 },
                    tooltip: { theme: 'dark', y: { formatter: function(v) { return v + ' actividad(es)'; } } },
                    noData: { text: 'Sin datos para los filtros seleccionados', align: 'center', verticalAlign: 'middle', style: { color: '#6b7280', fontSize: '13px' } },
                };
            },
        },
        {
            id: 'pi-lessons-flow-chart',
            varName: 'piLessonsFlowChart',
            dataProp: 'chartLessonsFlow',
            build: function(rawData) {
                return {
                    series: [{ name: 'Lecciones', data: rawData || [] }],
                    chart: { type: 'area', height: 200, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, system-ui, sans-serif', background: 'transparent' },
                    colors: ['#0ea5e9'],
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 3, colors: ['#0ea5e9'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 5 } },
                    dataLabels: { enabled: false },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] } },
                    xaxis: { type: 'category', labels: { style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 } }, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 } }, tickAmount: 4, forceNiceScale: true },
                    grid: { borderColor: '#37415140', strokeDashArray: 4 },
                    tooltip: { theme: 'dark', y: { formatter: function(v) { return v + ' lección(es)'; } } },
                    noData: { text: 'Sin datos para los filtros seleccionados', align: 'center', verticalAlign: 'middle', style: { color: '#6b7280', fontSize: '13px' } },
                };
            },
        },
        {
            id: 'pi-status-flow-chart',
            varName: 'piStatusFlowChart',
            dataProp: 'chartStatusFlow',
            build: function(rawData) {
                var d = rawData || {};
                return {
                    series: [
                        { name: 'Aprobadas', data: (d.approved || []).map(function(v, i) { return { x: (d.categories || [])[i] || '', y: v }; }) },
                        { name: 'Pendientes', data: (d.pending || []).map(function(v, i) { return { x: (d.categories || [])[i] || '', y: v }; }) },
                    ],
                    chart: { type: 'area', height: 200, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, system-ui, sans-serif', background: 'transparent' },
                    colors: ['#10b981', '#f59e0b'],
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 3, hover: { size: 5 } },
                    dataLabels: { enabled: false },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.4, opacityTo: 0, stops: [0, 90, 100] } },
                    xaxis: { type: 'category', labels: { style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 } }, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 } }, tickAmount: 4, forceNiceScale: true, min: 0 },
                    grid: { borderColor: '#37415140', strokeDashArray: 4 },
                    tooltip: { theme: 'dark', shared: true, intersect: false },
                    legend: { position: 'top', horizontalAlign: 'right', labels: { colors: '#9ca3af' }, markers: { width: 10, height: 10, radius: 2 } },
                    noData: { text: 'Sin datos para los filtros seleccionados', align: 'center', verticalAlign: 'middle', style: { color: '#6b7280', fontSize: '13px' } },
                };
            },
        },
    ];

    var instances = {};

    function destroyChart(varName) {
        if (instances[varName]) {
            try { instances[varName].destroy(); } catch(e) {}
            instances[varName] = null;
        }
    }

    function loadApex() {
        if (window.loadApexCharts) {
            return window.loadApexCharts().then(function() { return true; });
        }
        return Promise.resolve(!!window.ApexCharts);
    }

    function initSingleChart($wire, cfg) {
        var el = document.getElementById(cfg.id);
        if (!el || !window.ApexCharts) return;
        destroyChart(cfg.varName);

        var rawData = $wire[cfg.dataProp];
        if (rawData === undefined) rawData = null;

        try {
            instances[cfg.varName] = new window.ApexCharts(el, cfg.build(rawData));
            instances[cfg.varName].render();
        } catch(e) {
            console.warn('Chart ' + cfg.id + ' error:', e);
        }
    }

    function initAllCharts($wire) {
        loadApex().then(function(loaded) {
            if (!loaded) return;
            for (var i = 0; i < CHARTS.length; i++) {
                initSingleChart($wire, CHARTS[i]);
            }

            for (var j = 0; j < CHARTS.length; j++) {
                (function(cfg) {
                    try {
                        $wire.$watch(cfg.dataProp, function() {
                            setTimeout(function() { initSingleChart($wire, cfg); }, 100);
                        });
                    } catch(e) {
                        console.warn('$watch error for ' + cfg.id, e);
                    }
                })(CHARTS[j]);
            }

            // Also re-render when activityRange changes
            try {
                $wire.$watch('activityRange', function() {
                    setTimeout(function() {
                        for (var k = 0; k < CHARTS.length; k++) {
                            initSingleChart($wire, CHARTS[k]);
                        }
                    }, 150);
                });
            } catch(e) {}

            // Re-render when selectedProfesorId changes
            try {
                $wire.$watch('selectedProfesorId', function() {
                    setTimeout(function() {
                        for (var k = 0; k < CHARTS.length; k++) {
                            initSingleChart($wire, CHARTS[k]);
                        }
                    }, 200);
                });
            } catch(e) {}
        });
    }

    function bootstrap() {
        var el = document.querySelector('div.fade-in[wire\\:id]');
        if (!el) { setTimeout(bootstrap, 100); return; }
        var componentId = el.getAttribute('wire:id');
        if (!componentId) { setTimeout(bootstrap, 100); return; }
        if (typeof window.Livewire === 'undefined' || typeof window.Livewire.find !== 'function') {
            setTimeout(bootstrap, 100);
            return;
        }
        var $wire = window.Livewire.find(componentId);
        if (!$wire) { setTimeout(bootstrap, 100); return; }
        initAllCharts($wire);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }
})();
</script>
