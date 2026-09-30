<div class="fade-in" x-data="{
    modeObservation: @entangle('modeObservation'),
    modeComments: @entangle('modeComments'),
    modePreview: @entangle('modePreview'),
    modeCopy: @entangle('modeCopy'),
    commentStatus: @entangle('status')
}">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Plan de Actividades</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium">Revisión y control de calidad pedagógica de los planes de evaluación.</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openCopyWizard"
                class="inline-flex items-center gap-2 min-h-[44px] px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-lg shadow-emerald-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                </svg>
                Copiar actividades
            </button>
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 min-h-[44px] px-5 py-2.5 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Actualizar
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 p-2 sm:p-5 rounded-lg mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Plan Estudio</label>
                <select wire:model.live="pestudio_id"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_pestudio as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest mb-1.5 transition-colors {{ !$pestudio_id ? 'text-gray-400 dark:text-gray-600' : 'text-gray-500 dark:text-gray-500' }}">
                    Grado/Año
                    @if(!$pestudio_id)
                        <span class="normal-case font-normal text-[9px] tracking-normal">— seleccione Plan</span>
                    @endif
                </label>
                <select wire:model.live="grado_id" @disabled(!$pestudio_id)
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed {{ !$pestudio_id ? 'opacity-60 cursor-not-allowed' : '' }}">
                    @if(!$pestudio_id)
                        <option value="">Seleccione Plan Estudio primero</option>
                    @else
                        <option value="">Todos</option>
                        @foreach($list_grado as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest mb-1.5 transition-colors {{ !$grado_id ? 'text-gray-400 dark:text-gray-600' : 'text-gray-500 dark:text-gray-500' }}">
                    Sección
                    @if(!$pestudio_id)
                        <span class="normal-case font-normal text-[9px] tracking-normal">— requiere Plan</span>
                    @elseif(!$grado_id)
                        <span class="normal-case font-normal text-[9px] tracking-normal">— seleccione Grado</span>
                    @endif
                </label>
                <select wire:model.live="seccion_id" @disabled(!$grado_id)
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all disabled:opacity-50 disabled:cursor-not-allowed {{ !$grado_id ? 'opacity-60 cursor-not-allowed' : '' }}">
                    @if(!$grado_id)
                        <option value="">{{ !$pestudio_id ? 'Seleccione Plan Estudio' : 'Seleccione Grado primero' }}</option>
                    @else
                        <option value="">Todas</option>
                        @foreach($list_seccion as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Profesor</label>
                <select wire:model.live="profesor_id"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_profesors as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Actividades/Lecciones</label>
                <select wire:model.live="status_activities"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todas</option>
                    <option value="SI">Con actividades</option>
                    <option value="NO">Sin actividades</option>
                    <option value="SI_LE">Con lecciones</option>
                    <option value="NO_LE">Sin lecciones</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Estado</label>
                <select wire:model.live="filter_status"
                    class="w-full min-h-[44px] bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todas</option>
                    <option value="approved">Aprobada</option>
                    <option value="pending">En revisión</option>
                </select>
            </div>

            <div class="flex items-end gap-4">
                <label class="relative inline-flex items-center gap-2 cursor-pointer min-h-[44px] select-none">
                    <input type="checkbox" wire:model.live="filter_observations" class="sr-only peer">
                    <div class="relative w-10 h-6 rounded-full transition-all duration-300 peer-checked:bg-blue-500 bg-gray-300 dark:bg-white/10 peer-checked:shadow-sm peer-checked:shadow-blue-500/30 after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:shadow-sm after:border after:border-gray-200 dark:after:border-white/10"></div>
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-colors duration-300">
                        <svg class="w-3.5 h-3.5 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Observaciones
                    </span>
                    <span wire:loading wire:target="filter_observations" class="w-3 h-3">
                        <svg class="w-3 h-3 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </span>
                </label>
            </div>
        </div>
    </div>

    <!-- Global Indicator (solo cuando hay profesor seleccionado) -->
    @if($profesor_id && $pevaluacions->total() > 0)
        @php
            $totalActivities = $pevaluacions->sum('activities_count');
            $aboveAvg = 0;
            foreach($pevaluacions as $peva) {
                foreach($peva->activities as $act) {
                    $avr = $act->activities_avr;
                    $count = $act->teachingWordsMayorCount();
                    if ($avr !== null && $count > $avr) $aboveAvg++;
                }
            }
            $pct = $totalActivities > 0 ? round(($aboveAvg / $totalActivities) * 100) : 0;
            $level = $pct >= 50 ? 'success' : ($pct >= 25 ? 'warning' : 'danger');
            $message = $pct >= 50 ? 'Buen desempeño: la mayoría de las actividades superan el promedio de palabras esperado.'
                : ($pct >= 25 ? 'Desempeño moderado: una parte de las actividades alcanza el promedio.'
                : 'Atención: pocas actividades superan el promedio de palabras esperado.');
            $colors = ['success' => 'emerald', 'warning' => 'amber', 'danger' => 'red'];
            $c = $colors[$level];
        @endphp

        <div class="bg-{{ $c }}-50 dark:bg-{{ $c }}-500/10 border border-{{ $c }}-200 dark:border-{{ $c }}-500/20 p-5 rounded-lg mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-{{ $c }}-100 dark:bg-{{ $c }}-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-{{ $c }}-600 dark:text-{{ $c }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-{{ $c }}-700 dark:text-{{ $c }}-300 text-sm font-bold uppercase tracking-wider">Indicador — Actividades sobre el Promedio</p>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mt-0.5">{{ $totalActivities }} actividades, {{ $aboveAvg }} superan el promedio (&gt;3 palabras)</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 px-4 py-2 bg-{{ $c }}-100 dark:bg-{{ $c }}-500/10 border border-{{ $c }}-200 dark:border-{{ $c }}-500/20 rounded-lg">
                    <span class="text-{{ $c }}-700 dark:text-{{ $c }}-400 text-xs font-bold uppercase">{{ $level === 'success' ? 'Buen desempeño' : ($level === 'warning' ? 'Moderado' : 'Atención') }}</span>
                    <span class="text-gray-900 dark:text-white text-lg font-black">{{ $pct }}%</span>
                </div>
            </div>
            <p class="text-gray-500 dark:text-gray-500 text-xs mt-3">{{ $message }}</p>
        </div>
    @endif

    <!-- ===== VIEW MODE TOGGLE (Grid / Table) ===== -->
    <div class="flex items-center gap-2 mb-4"
         x-data="{ mode: localStorage.getItem('planning-activities-view-mode') || 'table' }"
         x-init="$watch('mode', val => {
             localStorage.setItem('planning-activities-view-mode', val);
             window.dispatchEvent(new CustomEvent('planning-activities-view-mode-changed', { detail: { mode: val } }))
         })">
        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mr-1">Vista</span>
        <button @click="mode = 'grid'"
            :class="mode === 'grid' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-white/5 hover:text-gray-700 dark:hover:text-gray-300'"
            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border transition-all duration-200 text-[10px] font-bold">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="hidden sm:inline">Grid</span>
        </button>
        <button @click="mode = 'table'"
            :class="mode === 'table' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-white/5 hover:text-gray-700 dark:hover:text-gray-300'"
            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border transition-all duration-200 text-[10px] font-bold">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
            <span class="hidden sm:inline">Tabla</span>
        </button>
    </div>

    <!-- ===== CONTENT: View container ===== -->
    <div x-cloak
         x-data="{ mode: localStorage.getItem('planning-activities-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('planning-activities-view-mode')) localStorage.setItem('planning-activities-view-mode', 'table') }"
         x-on:planning-activities-view-mode-changed.window="mode = $event.detail.mode">

        {{-- ═══════════════════════════════════════════════════════════════ --}}
        {{-- BENTO GRID MODE (bento-grid-modile pattern)                  --}}
        {{-- ═══════════════════════════════════════════════════════════════ --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             wire:key="tab-content-grid-{{ $lapso_id }}-{{ $pestudio_id ?? 'all' }}-{{ $filter_status ?: 'all' }}-{{ $filter_observations ? 'obs' : 'all' }}-{{ $status_activities ?? 'all' }}">
            <div class="bg-gray-900/60 border border-white/5 rounded-2xl p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @forelse($pevaluacions as $item)
                        <div class="rounded-2xl border border-white/5 bg-gray-900 hover:border-emerald-500/30 transition-all duration-200 flex flex-col overflow-hidden h-full {{ $item->activities && $item->activities->where('status', 0)->isNotEmpty() ? 'ring-1 ring-amber-500/20' : '' }}">

                            {{-- Header: Asignatura + Code Badge --}}
                            <div class="flex items-start justify-between px-4 pt-4 pb-3 border-b border-white/5 gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-white truncate line-clamp-2" title="{{ $item->pensum?->asignatura?->name ?? 'Sin asignatura' }}">
                                        {{ $item->pensum?->asignatura?->name ?? 'Sin asignatura' }}
                                    </h3>
                                </div>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/12 text-purple-400 border border-purple-500/20 shrink-0">
                                    {{ $item->pensum?->asignatura?->code ?? '' }}
                                </span>
                            </div>

                            {{-- Body: Sección, Profesor, Actividades, Observaciones --}}
                            <div class="px-4 py-3 space-y-2.5 flex-1">
                                {{-- Grado · Sección --}}
                                <div class="flex items-center gap-2 text-[11px]">
                                    <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span class="text-gray-400 truncate">
                                        {{ $item->seccion?->grado?->name ?? '' }}
                                        <span class="text-gray-600">· Sección</span> {{ $item->seccion?->name ?? '' }}
                                    </span>
                                </div>

                                {{-- Grupo Estable --}}
                                @if($item->grupo_estable_id)
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-amber-500/80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span class="text-amber-400/90 truncate" title="Grupo Estable">
                                            <span class="text-gray-600">Grupo</span> {{ $item->grupoEstable?->name ?? '—' }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Profesor --}}
                                <div class="flex items-center gap-2 text-[11px]">
                                    <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-gray-400 truncate" title="{{ $item->profesor?->lastname ?? '' }} {{ $item->profesor?->name ?? '' }}">
                                        {{ $item->profesor?->lastname ?? '' }} {{ $item->profesor?->name ?? '' }}
                                    </span>
                                </div>

                                {{-- Actividades count + Status --}}
                                <div class="flex items-center gap-2">
                                    @php $hasPending = $item->activities && $item->activities->where('status', 0)->isNotEmpty(); @endphp
                                    @if($item->activities_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20 rounded-md">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            {{ $item->activities_count }} {{ $item->activities_count === 1 ? 'Actividad' : 'Actividades' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold bg-red-500/12 text-red-400 border border-red-500/20 rounded-md">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            Sin actividades
                                        </span>
                                    @endif
                                    @if($hasPending)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20 rounded-md">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            Revisión
                                        </span>
                                    @endif
                                </div>

                                {{-- Observations (inline en el card) --}}
                                @if($item->observations)
                                    <div class="flex items-start gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span class="text-gray-500 line-clamp-2 leading-relaxed" title="{{ $item->observations }}">
                                            {{ $item->observations }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Lapso --}}
                                <div class="flex items-center gap-2 text-[11px]">
                                    <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-gray-500 font-mono text-[10px]">{{ $item->lapso?->name ?? '—' }}</span>
                                </div>
                            </div>

                            {{-- Footer Stats: Total · Aprobadas · En revisión --}}
                            <div class="px-4 py-2.5 border-t border-white/5 bg-white/[0.03] flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-[10px] font-bold bg-blue-500/12 text-blue-400">
                                        {{ $item->activities_count }}
                                    </span>
                                    <span class="text-[10px] text-gray-500 font-medium">total</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if($item->activities_approved_count > 0)
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[9px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20 rounded-md">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            {{ $item->activities_approved_count }}
                                        </span>
                                    @endif
                                    @if($item->activities_revision_count > 0)
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[9px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20 rounded-md">
                                            <span class="w-1 h-1 rounded-full bg-amber-400"></span>
                                            {{ $item->activities_revision_count }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions: btnGroup (bento-grid-modile pattern) --}}
                            <div class="px-4 pb-4 pt-2.5 border-t border-white/5 flex items-center gap-2"
                                 x-data="{ actionsOpen: false }"
                                 @click.away="actionsOpen = false">

                                {{-- Primary: Observation (siempre visible) --}}


                                {{-- Desktop group (sm+) --}}
                                <div class="hidden sm:flex items-center gap-2">
                                    <a href="{{ route('app.planning.activities.resume', $item->id) }}" target="_blank"
                                        class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-xs font-bold bg-sky-500/12 text-sky-400 hover:bg-sky-500/20 border border-sky-500/20 transition-all duration-200"
                                        title="Resumen PDF">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('app.planning.activities.format', $item->id) }}" target="_blank"
                                        class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-xs font-bold bg-purple-500/12 text-purple-400 hover:bg-purple-500/20 border border-purple-500/20 transition-all duration-200"
                                        title="Plan Completo PDF">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                </div>

                                {{-- Mobile dropdown (<sm) --}}
                                <div class="relative sm:hidden">
                                    <button @click="actionsOpen = !actionsOpen"
                                        class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/30 hover:bg-gray-200 dark:hover:bg-slate-600/50 border border-gray-200 dark:border-slate-600/30 transition-all"
                                        title="Más acciones">
                                        <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4z"/>
                                            <path d="M10 12a2 2 0 110-4 2 2 0 010 4z"/>
                                            <path d="M10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>
                                    <div x-show="actionsOpen"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute right-0 z-50 mt-1 min-w-[180px] bg-gray-800 border border-white/10 rounded-lg shadow-xl py-1"
                                         @click="actionsOpen = false">
                                        <a href="{{ route('app.planning.activities.resume', $item->id) }}" target="_blank"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                            <svg class="w-4 h-4 shrink-0 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            Resumen PDF
                                        </a>
                                        <a href="{{ route('app.planning.activities.format', $item->id) }}" target="_blank"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                            <svg class="w-4 h-4 shrink-0 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Plan Completo PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 font-medium mb-2">No se encontraron planes de evaluación</p>
                            <p class="text-gray-600 text-sm">Ajusta los filtros o verifica que existan planes de evaluación con el módulo de planificación activo.</p>
                        </div>
                    @endforelse
                </div>

                @if($pevaluacions->hasPages())
                    <div class="mt-6">
                        {{ $pevaluacions->links('vendor.pagination.custom-tailwind') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- TABLE MODE (legacy — unchanged) --}}
        <div x-show="mode === 'table'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <!-- ===== TABBED CONTENT (Lapso tabs like profesor home) ===== -->
            <div wire:key="tab-content-{{ $lapso_id }}-{{ $pestudio_id ?? 'all' }}-{{ $filter_status ?: 'all' }}-{{ $filter_observations ? 'obs' : 'all' }}-{{ $status_activities ?? 'all' }}" class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden">

        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 dark:border-white/5">
            <nav class="flex overflow-x-auto [&::-webkit-scrollbar]:h-1" style="scrollbar-width: thin;">
                @foreach($tabsLapsos as $index => $lapsoItem)
                    @php $isActive = $lapsoItem->id == $lapso_id; @endphp
                    <button wire:click="selectLapso({{ $lapsoItem->id }})"
                        title="{{ $lapsoItem->name }}"
                        class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap
                               {{ $isActive ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500 bg-emerald-50 dark:bg-emerald-500/5' : 'text-gray-500 dark:text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}"
                    >
                        <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="hidden sm:inline">{{ $lapsoItem->name }}</span>
                        <span class="hidden sm:block text-[9px] font-normal text-gray-400 dark:text-gray-500 normal-case">{{ $lapsoItem->code }}</span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="space-y-6 p-2 sm:p-4 lg:p-6">
            @forelse($pevaluacions as $item)
                <div class="bg-white dark:bg-gray-900/60 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden transition-all duration-300 hover:border-emerald-300 dark:hover:border-emerald-500/10 @if($item->activities && $item->activities->where('status', 0)->isNotEmpty()) border-t-4 border-t-amber-500 @endif"
                    wire:key="peva-{{ $item->id }}"
                    x-data="{ open: false, activeTab: 0 }">

                    <!-- Header Row -->
                    <div @click="open = !open"
                        class="flex items-center justify-between p-5 cursor-pointer hover:bg-gray-50/80 dark:hover:bg-white/[0.02] transition-colors group">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-11 h-11 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->pensum?->asignatura?->name ?? 'Sin asignatura' }}</span>
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-white/5 text-[10px] font-bold text-gray-500 dark:text-gray-400 rounded-md border border-gray-200 dark:border-white/5">{{ $item->pensum?->asignatura?->code ?? '' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    <span>{{ $item->seccion?->grado?->name ?? '' }} - Sección {{ $item->seccion?->name ?? '' }}</span>
                                    @if($item->grupo_estable_id)
                                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                        <span class="text-amber-500/90">Grupo: {{ $item->grupoEstable?->name ?? '—' }}</span>
                                    @endif
                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                    <span>{{ $item->profesor?->lastname ?? '' }} {{ $item->profesor?->name ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            <!-- Activity counts: total · aprobadas · en revisión -->
                            <div class="flex items-center gap-1.5">
                                @if($item->activities_count > 0)
                                    <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 text-[10px] font-bold rounded-lg border border-blue-200 dark:border-blue-500/20">
                                        {{ $item->activities_count }} total
                                    </span>
                                    @if($item->activities_approved_count > 0)
                                        <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-lg border border-emerald-200 dark:border-emerald-500/20">
                                            <svg class="w-3 h-3 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            {{ $item->activities_approved_count }} aprob.
                                        </span>
                                    @endif
                                    @if($item->activities_revision_count > 0)
                                        <span class="px-2 py-1 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-lg border border-amber-200 dark:border-amber-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span>
                                            {{ $item->activities_revision_count }} revisión
                                        </span>
                                    @endif
                                @else
                                    <span class="px-3 py-1 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg border border-red-200 dark:border-red-500/20">
                                        Sin actividades
                                    </span>
                                @endif
                                @if($item->activities_lessons_count > 0)
                                    <span class="px-2.5 py-1 bg-violet-100 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400 text-[10px] font-bold rounded-lg border border-violet-200 dark:border-violet-500/20">
                                        <svg class="w-3 h-3 inline -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        {{ $item->activities_lessons_count }} {{ $item->activities_lessons_count === 1 ? 'lección' : 'lecciones' }}
                                    </span>
                                @endif
                            </div>

                            <!-- Button Group: Observación + PDFs -->
                            <div class="inline-flex items-center rounded-lg overflow-hidden border border-gray-200 dark:border-white/5 divide-x divide-gray-200 dark:divide-white/5" role="group">
                                <!-- PDF Resume -->
                                <a href="{{ route('app.planning.activities.resume', $item->id) }}" target="_blank" title="Resumen PDF"
                                    class="p-2 min-w-[36px] min-h-[36px] bg-gray-100 dark:bg-white/5 hover:bg-sky-100 dark:hover:bg-sky-500/10 text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </a>
                                <!-- PDF Format -->
                                <a href="{{ route('app.planning.activities.format', $item->id) }}" target="_blank" title="Plan Completo PDF"
                                    class="p-2 min-w-[36px] min-h-[36px] bg-gray-100 dark:bg-white/5 hover:bg-purple-100 dark:hover:bg-purple-500/10 text-gray-500 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            </div>

                            <!-- Toggle -->
                            <div class="min-w-[44px] min-h-[44px] w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center border border-gray-200 dark:border-white/5 transition-transform duration-300"
                                :class="open ? 'rotate-180 bg-emerald-100 dark:bg-emerald-500/10 border-emerald-300 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-500'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Expanded Activities with Tabs -->
                    <div :class="open ? '' : '!hidden'">
                        <div class="px-5 pb-5 pt-0 border-t border-gray-200 dark:border-white/5">
                            @if($item->activities_count > 0)
                                {{-- Activity Tab Bar --}}
                                <div class="border-b border-gray-200 dark:border-white/5 mt-4">
                                    <nav class="flex overflow-x-auto [&::-webkit-scrollbar]:h-1" style="scrollbar-width: thin;">
                                        @foreach($item->activities as $i => $act)
                                            @php
                                                $wordCount = $act->teachingWordsMayorCount();
                                                $avr = $act->activities_avr;
                                                $qualityIcon = null;
                                                $qualityColor = null;
                                                $qualityTitle = null;
                                                if ($avr !== null) {
                                                    if ($wordCount > $avr) {
                                                        $qualityIcon = '↑';
                                                        $qualityColor = 'text-emerald-400';
                                                        $qualityTitle = "Palabras ({$wordCount}) por encima del promedio ({$avr})";
                                                    } elseif ($wordCount == $avr) {
                                                        $qualityIcon = '−';
                                                        $qualityColor = 'text-blue-400';
                                                        $qualityTitle = "Palabras ({$wordCount}) igual al promedio ({$avr})";
                                                    } else {
                                                        $qualityIcon = '↓';
                                                        $qualityColor = 'text-amber-400';
                                                        $qualityTitle = "Palabras ({$wordCount}) por debajo del promedio ({$avr})";
                                                    }
                                                }
                                            @endphp
                                            <button type="button" @click="activeTab = {{ $i }}"
                                                :class="activeTab === {{ $i }}
                                                    ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500 bg-emerald-50 dark:bg-emerald-500/5'
                                                    : 'text-gray-500 dark:text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                                                class="flex-1 px-4 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap"
                                                title="{{ \Carbon\Carbon::parse($act->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($act->ffinal)->format('d/m/Y') }}{{ $qualityTitle ? ' · ' . $qualityTitle : '' }}">
                                                <span class="flex items-center justify-center gap-1.5">
                                                    <span>Act. {{ $i + 1 }}</span>
                                                    @if($qualityIcon)
                                                        <span class="text-[10px] font-bold leading-none {{ $qualityColor }}">{{ $qualityIcon }}</span>
                                                    @endif
                                                    @if($act->status !== null)
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $act->status ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                                    @endif
                                                    @if($act->supplement && (!empty(trim($act->supplement->text ?? '')) || !empty($act->supplement->image_url)))
                                                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400" title="Tiene información complementaria"></span>
                                                    @endif
                                                </span>
                                            </button>
                                        @endforeach
                                    </nav>
                                </div>

                                {{-- Activity Tab Content --}}
                                @foreach($item->activities as $i => $act)
                                    <div :class="activeTab === {{ $i }} ? 'mt-4' : '!hidden'" class="transition-all duration-200">
                                        <div class="bg-gray-50 dark:bg-white/[0.03] p-4 rounded-lg border border-gray-200 dark:border-white/5">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex-1 min-w-0">
                                                    <!-- Fechas -->
                                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500 mb-2">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span class="font-medium">{{ \Carbon\Carbon::parse($act->finicial)->format('d/m/Y') }}</span>
                                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                                        <span class="font-medium">{{ \Carbon\Carbon::parse($act->ffinal)->format('d/m/Y') }}</span>
                                                    </div>

                                                    <!-- Topic -->
                                                    <p class="text-sm text-gray-800 dark:text-gray-100 font-medium mb-1">{{ $act->topic }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-300 line-clamp-2">{{ $act->teaching }}</p>

                                                    <!-- Señalización (E / Ev / suplemento) -->
                                                    <div class="flex items-center gap-1.5 mt-2">
                                                        @if(!empty($act->teaching))
                                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[8px] font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20" title="Contiene planificación de enseñanza">
                                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                                </svg>
                                                                <span>E</span>
                                                            </span>
                                                        @endif
                                                        @if(!empty($act->description))
                                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[8px] font-medium bg-sky-500/10 text-sky-400 border border-sky-500/20" title="Contiene actividad evaluativa">
                                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                                <span>Ev</span>
                                                            </span>
                                                        @endif
                                                        @if($act->supplement && !empty(trim($act->supplement->text ?? '')))
                                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-[10px] font-bold bg-cyan-500/15 text-cyan-400 border border-cyan-500/25" title="Tiene texto complementario">T</span>
                                                        @endif
                                                        @if($act->supplement && !empty($act->supplement->image_url))
                                                            <span class="inline-flex items-center justify-center min-w-[30px] px-1 rounded-md text-[9px] font-bold bg-violet-500/15 text-violet-400 border border-violet-500/25" title="Tiene imagen complementaria">
                                                                <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                                <span>IMG</span>
                                                            </span>
                                                        @endif
                                                        @if($act->supplement && (!empty(trim($act->supplement->text ?? '')) || !empty($act->supplement->image_url)))
                                                            <button type="button" wire:click="openSupplementModal({{ $act->id }})"
                                                                title="Información complementaria"
                                                                class="inline-flex items-center justify-center min-w-[44px] min-h-[44px] w-8 h-8 rounded-lg text-xs font-bold bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all duration-200">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <!-- Word quality indicator -->
                                                    @php
                                                        $wordCount = $act->teachingWordsMayorCount();
                                                        $avr = $act->activities_avr;
                                                        $quality = null;
                                                        if ($avr !== null) {
                                                            $quality = $wordCount > $avr ? 'above' : ($wordCount === $avr ? 'at' : 'below');
                                                        }
                                                    @endphp
                                                    @if($quality)
                                                        <div class="flex items-center gap-1.5 mt-2">
                                                            <span class="text-[10px] text-gray-500 dark:text-gray-500 font-medium">Calidad:</span>
                                                            @if($quality === 'above')
                                                                <span class="flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                                                    {{ $wordCount }} > {{ $avr }}
                                                                </span>
                                                            @elseif($quality === 'at')
                                                                <span class="flex items-center gap-1 px-2 py-0.5 bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 text-[10px] font-bold rounded-md border border-blue-200 dark:border-blue-500/20">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                                    {{ $wordCount }} = {{ $avr }}
                                                                </span>
                                                            @else
                                                                <span class="flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md border border-amber-200 dark:border-amber-500/20">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                                                    {{ $wordCount }} < {{ $avr }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <!-- Comments section -->
                                                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-white/5">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500">Comentario [Jefe Área]</span>
                                                            <div class="flex items-center gap-2">
                                                                <button type="button" wire:click="showPreview({{ $act->id }})"
                                                                    class="flex items-center gap-1.5 px-3 py-1.5 min-h-[44px] bg-gray-100 dark:bg-white/5 hover:bg-sky-100 dark:hover:bg-sky-500/10 rounded-lg border border-gray-200 dark:border-white/5 hover:border-sky-300 dark:hover:border-sky-500/20 text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 text-[10px] font-bold uppercase tracking-wider transition-all duration-300">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                    </svg>
                                                                    Vista Previa
                                                                </button>
                                                                <button type="button" wire:click="setModeComment({{ $act->id }})"
                                                                    class="flex items-center gap-1.5 px-3 py-1.5 min-h-[44px] bg-gray-100 dark:bg-white/5 hover:bg-emerald-100 dark:hover:bg-emerald-500/10 rounded-lg border border-gray-200 dark:border-white/5 hover:border-emerald-300 dark:hover:border-emerald-500/20 text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 text-[10px] font-bold uppercase tracking-wider transition-all duration-300">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                    </svg>
                                                                    {{ $act->comments ? 'Editar' : 'Agregar' }}
                                                                </button>
                                                                @if(!$act->status)
                                                                    <button type="button"
                                                                        @click="$dispatch('confirm-delete-activity', { id: {{ $act->id }}, message: '¿Eliminar la actividad «{{ addslashes(preg_replace('/\s+/', ' ', $act->topic ?? '')) }}»? Esta acción no se puede deshacer.' })"
                                                                        class="flex items-center gap-1.5 px-3 py-1.5 min-h-[44px] bg-gray-100 dark:bg-white/5 hover:bg-red-100 dark:hover:bg-red-500/10 rounded-lg border border-gray-200 dark:border-white/5 hover:border-red-300 dark:hover:border-red-500/20 text-gray-500 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 text-[10px] font-bold uppercase tracking-wider transition-all duration-300"
                                                                        title="Eliminar actividad">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                        </svg>
                                                                        Eliminar
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @if($act->comments)
                                                            <p class="text-xs text-gray-700 dark:text-gray-200 mt-1 italic">"{{ $act->comments }}"</p>
                                                        @else
                                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 italic">Sin comentarios</p>
                                                        @endif
                                                        @if($act->status !== null)
                                                            <div class="mt-1">
                                                                @if($act->status)
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                        Aprobado
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md border border-amber-200 dark:border-amber-500/20">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                        En revisión
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Lección LMS: estado + Ver lección + Publicar --}}
                                    <x-lms-lesson-block :activity="$act" />
                                @endforeach
                            @else
                                <div class="flex items-center justify-center py-8 text-center">
                                    <div>
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-500 text-sm">No hay actividades registradas en este plan de evaluación.</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Observations -->
                            @if($item->observations)
                                <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-500/5 border border-blue-200 dark:border-blue-500/10 rounded-lg">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-1">Observaciones del Coordinador</p>
                                            <p class="text-xs text-gray-700 dark:text-gray-200">{{ $item->observations }}</p>
                                        </div>
                                        <button type="button"
                                            @click="$dispatch('confirm-delete-observation', { id: {{ $item->id }}, message: '¿Eliminar las observaciones de «{{ addslashes(preg_replace('/\s+/', ' ', $item->pensum->asignatura->name ?? '')) }}»?' })"
                                            class="shrink-0 inline-flex items-center gap-1 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 rounded-md border border-red-200 dark:border-red-500/20 transition-all"
                                            title="Eliminar observaciones">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 dark:bg-gray-900/20 border border-gray-200 dark:border-white/5 rounded-lg py-16 text-center">
                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-500 font-medium mb-2">No se encontraron planes de evaluación</p>
                    <p class="text-gray-400 dark:text-gray-600 text-sm">Ajusta los filtros o verifica que existan planes de evaluación con el módulo de planificación activo.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($pevaluacions->hasPages())
                <div class="mt-6">
                    {{ $pevaluacions->links('vendor.pagination.custom-tailwind') }}
                </div>
            @endif
        </div>
    </div>
        </div>
    </div>

    <!-- ===== MODAL: Observaciones del Coordinador ===== -->
    <div x-show="modeObservation" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        @keydown.escape.window="modeObservation = false">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-gray-950/70 backdrop-blur-sm" @click="modeObservation = false"></div>

        {{-- Panel --}}
        <div x-show="modeObservation" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative w-full max-w-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl shadow-2xl overflow-hidden">

            {{-- Top accent bar --}}
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-emerald-500"></div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100 dark:border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/20 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Observaciones del Coordinador</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Supervisión pedagógica del plan de evaluación</p>
                    </div>
                </div>
                <button type="button" @click="modeObservation = false"
                    class="p-1.5 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg transition-all shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">
                @if($pevaluacion)
                <div class="bg-gray-50 dark:bg-white/[0.03] p-4 rounded-lg border border-gray-200 dark:border-white/5 space-y-3">
                    {{-- Section label --}}
                    <div class="flex items-center gap-2 text-[10px] text-gray-500 dark:text-gray-500 font-bold uppercase tracking-widest">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Plan de Evaluación
                    </div>
                    {{-- Asignatura --}}
                    <p class="text-sm text-gray-900 dark:text-white font-semibold">{{ $pevaluacion->pensum?->asignatura?->name ?? '—' }}</p>
                    {{-- Metadata row --}}
                    <div class="flex flex-wrap gap-x-5 gap-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $pevaluacion->seccion?->grado?->name ?? '—' }} · Sección {{ $pevaluacion->seccion?->name ?? '—' }}
                        </span>
                        @if($pevaluacion->grupo_estable_id)
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Grupo: {{ $pevaluacion->grupoEstable?->name ?? '—' }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $pevaluacion->profesor?->lastname ?? '—' }} {{ $pevaluacion->profesor?->name ?? '—' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $pevaluacion->lapso?->name ?? '—' }}
                        </span>
                    </div>
                </div>
                @endif

                {{-- Textarea con contador --}}
                <div x-data="{ obsCount: {{ strlen($observations ?? '') }} }">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Observaciones</label>
                        <span class="text-[10px] tabular-nums text-gray-400" x-text="`${obsCount}/65535 caracteres`"></span>
                    </div>
                    <textarea wire:model="observations" rows="5" maxlength="65535"
                        x-on:input="obsCount = $el.value.length"
                        class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none resize-none transition-all placeholder:text-gray-400 dark:placeholder:text-gray-600 @error('observations') border-red-300 dark:border-red-500/50 focus:ring-red-500/50 focus:border-red-500/50 @enderror"
                        placeholder="Escribe las observaciones del coordinador de evaluación..."></textarea>
                    @error('observations')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400 flex items-center gap-1.5 font-medium">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-white/5">
                <div class="flex items-center gap-2">
                    @if($pevaluacion && $pevaluacion->observations)
                        <span class="inline-flex items-center gap-1 text-emerald-500 not-italic font-medium text-[10px]">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Tiene observaciones previas
                        </span>
                    @else
                        <span class="text-[10px] text-gray-400 italic">Sin observaciones previas</span>
                    @endif
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="modeObservation = false"
                        class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 rounded-lg border border-gray-200 dark:border-white/5 transition-all">
                        Cancelar
                    </button>
                    <button type="button" wire:click="saveObservation" wire:loading.attr="disabled"
                        class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-white bg-gradient-to-r from-blue-600 to-emerald-600 hover:from-blue-700 hover:to-emerald-700 rounded-lg shadow-lg shadow-emerald-500/10 disabled:opacity-50 transition-all">
                        <span wire:loading.remove wire:target="saveObservation">
                            <span class="hidden sm:inline">Guardar </span>Observaciones
                        </span>
                        <span wire:loading wire:target="saveObservation" class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MODAL: Comentario del Jefe de Área ===== -->
    <x-modal-card title="Comentario del Jefe de Área" blur wire:model="modeComments" max-width="lg">
        <div class="space-y-4">
            @if($activity)
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500 mb-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ \Carbon\Carbon::parse($activity->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($activity->ffinal)->format('d/m/Y') }}
                    </div>
                    <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $activity->topic }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-300 mt-1 line-clamp-2">{{ $activity->teaching }}</p>
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Estado de Aprobación</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" wire:model="status" value="1"
                            class="w-4 h-4 text-emerald-500 bg-white dark:bg-white/5 border-gray-300 dark:border-white/10 focus:ring-emerald-500/50 focus:ring-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">Aprobado</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="radio" wire:model="status" value="0"
                            class="w-4 h-4 text-amber-500 bg-white dark:bg-white/5 border-gray-300 dark:border-white/10 focus:ring-amber-500/50 focus:ring-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">En revisión</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Comentario</label>
                <textarea wire:model="comments" rows="4"
                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none resize-none transition-all"
                    placeholder="Escribe tu comentario como jefe de área..."></textarea>
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="modeComments = false" />
                <x-button primary label="Guardar Comentario" wire:click="saveComent" spinner="saveComent" />
            </div>
        </x-slot>
    </x-modal-card>

    <!-- ===== MODAL: Vista Previa de Actividad ===== -->
    <x-modal-card title="Vista Previa de la Actividad" blur="lg" wire:model="modePreview" width="max-w-[80vw]" class="border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-gray-900">
        @if($previewActivity)
            <div class="space-y-5" x-data="{ showTeaching: false, previewTab: 0, previewLightbox: false }" x-init="$watch('$wire.previewActivity', () => { previewTab = 0; showTeaching = false; previewLightbox = false; })">

                {{-- Fechas --}}
                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-white/5 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-white/5">
                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-medium">
                        {{ \Carbon\Carbon::parse($previewActivity->finicial)->format('d/m/Y') }}
                        —
                        {{ \Carbon\Carbon::parse($previewActivity->ffinal)->format('d/m/Y') }}
                    </span>
                    @if($previewActivity->status !== null)
                        <span class="ml-auto">
                            @if($previewActivity->status)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-200 dark:border-emerald-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Aprobado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-[10px] font-bold rounded-md border border-amber-200 dark:border-amber-500/20">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    En revisión
                                </span>
                            @endif
                        </span>
                    @endif
                </div>

                {{-- Tab Navigation: Actividad | Complementaria --}}
                <div class="border-b border-gray-200 dark:border-white/5">
                    <nav class="flex gap-1">
                        <button type="button" @click="previewTab = 0"
                            :class="previewTab === 0
                                ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500 bg-emerald-50 dark:bg-emerald-500/5'
                                : 'text-gray-500 dark:text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="flex-1 px-4 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                            Actividad
                        </button>
                        <button type="button" @click="previewTab = 1"
                            :class="previewTab === 1
                                ? 'text-cyan-600 dark:text-cyan-400 border-cyan-500 bg-cyan-50 dark:bg-cyan-500/5'
                                : 'text-gray-500 dark:text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600'"
                            class="flex-1 px-4 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                            <span class="inline-flex items-center justify-center gap-1.5">
                                Complementaria
                                @if($previewActivity->supplement && (!empty(trim($previewActivity->supplement->text ?? '')) || !empty($previewActivity->supplement->image_url)))
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                @endif
                            </span>
                        </button>
                    </nav>
                </div>

                {{-- TAB 1: Actividad (contenido actual) --}}
                <div x-show="previewTab === 0" class="space-y-5">

                {{-- Topic --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Tema generador / Énfasis</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->topic ?? '—' }}</p>
                </div>

                {{-- Topic --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Tema generador / Énfasis</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->topic ?? '—' }}</p>
                </div>

                {{-- Thematic --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Tejido temático / Tema Indispensable</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->thematic ?? '—' }}</p>
                </div>

                {{-- References --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Referentes teórico-prácticos y Éticos</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->references ?? '—' }}</p>
                </div>

                {{-- Teaching --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Enseñanza / Actividad Globalizada</span>
                        @if($previewActivity->hasTeachingStructure())
                            <button @click="showTeaching = !showTeaching"
                                class="text-[10px] font-bold uppercase tracking-wider text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 transition-colors">
                                <span x-show="!showTeaching">Ver estructura</span>
                                <span x-show="showTeaching">Ver completo</span>
                            </button>
                        @endif
                    </div>

                    @php $sections = $previewActivity->getTeachingSections(); @endphp
                    @if(!empty($sections))
                        <div x-show="showTeaching" x-cloak x-transition:enter.duration.200ms>
                            <div class="space-y-3">
                                <div class="bg-cyan-50 dark:bg-cyan-500/5 border border-cyan-200 dark:border-cyan-500/10 rounded-lg p-3">
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-1">INICIO</div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ $sections['INICIO'] ?? '' }}</p>
                                </div>
                                <div class="bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-200 dark:border-emerald-500/10 rounded-lg p-3">
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1">DESARROLLO</div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ $sections['DESARROLLO'] ?? '' }}</p>
                                </div>
                                <div class="bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/10 rounded-lg p-3">
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400 mb-1">CIERRE</div>
                                    <p class="text-sm text-gray-700 dark:text-gray-200">{{ $sections['CIERRE'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div x-show="!showTeaching">
                        <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{{ $previewActivity->teaching ?? '—' }}</p>
                    </div>
                </div>

                {{-- Learning --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Aprendizaje</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->learning ?? '—' }}</p>
                </div>

                {{-- Description --}}
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Actividad Evaluativa</div>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $previewActivity->description ?? '—' }}</p>
                </div>

                {{-- Grid: Achievements + ODS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">Indicadores de Logro</div>
                        @if($previewActivity->achievements->isNotEmpty())
                            <ul class="space-y-1.5">
                                @foreach($previewActivity->achievements as $ach)
                                    <li class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $ach->name }}</span>
                                        @if($ach->weighting)
                                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/10 px-1.5 py-0.5 rounded-md">[{{ $ach->weighting }}]</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-400 dark:text-gray-500 italic">Sin indicadores de logro</p>
                        @endif
                    </div>

                    <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1.5">ODS / Sistematización</div>
                        <p class="text-sm text-gray-700 dark:text-gray-200">{{ $previewActivity->observations ?? '—' }}</p>
                    </div>
                </div>

                {{-- Comments --}}
                @if($previewActivity->comments)
                    <div class="bg-amber-50 dark:bg-amber-500/5 border border-amber-200 dark:border-amber-500/10 p-4 rounded-lg">
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-amber-600 dark:text-amber-400">Comentario del Jefe de Área</span>
                        </div>
                        <p class="text-sm text-amber-800 dark:text-amber-200 italic">"{{ $previewActivity->comments }}"</p>
                    </div>
                @endif

                {{-- Teaching word count indicator --}}
                @php
                    $wordCountPrev = $previewActivity->teachingWordsMayorCount();
                    $avrPrev = $previewActivity->activities_avr;
                @endphp
                @if($avrPrev !== null)
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-500 bg-gray-50 dark:bg-white/5 px-4 py-2 rounded-lg border border-gray-200 dark:border-white/5">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span>Indicador de calidad:</span>
                        @if($wordCountPrev > $avrPrev)
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $wordCountPrev }} &gt; {{ $avrPrev }}</span>
                            <span class="text-emerald-600/70 dark:text-emerald-400/70">(Supera el promedio)</span>
                        @elseif($wordCountPrev === $avrPrev)
                            <span class="text-blue-600 dark:text-blue-400 font-bold">{{ $wordCountPrev }} = {{ $avrPrev }}</span>
                            <span class="text-blue-600/70 dark:text-blue-400/70">(En el promedio)</span>
                        @else
                            <span class="text-amber-600 dark:text-amber-400 font-bold">{{ $wordCountPrev }} &lt; {{ $avrPrev }}</span>
                            <span class="text-amber-600/70 dark:text-amber-400/70">(Debajo del promedio)</span>
                        @endif
                    </div>
                @endif
                </div>{{-- /TAB 1 --}}

                {{-- TAB 2: Información Complementaria (ActivitySupplement) --}}
                <div x-show="previewTab === 1" x-cloak class="space-y-5">
                    <div class="bg-gray-50 dark:bg-white/5 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-white/5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-1">Actividad</div>
                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $previewActivity->topic ?? '—' }}</p>
                    </div>

                    @if(!empty(trim($previewActivity->supplement?->text ?? '')))
                        <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-1.5">Texto complementario</div>
                            <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{{ $previewActivity->supplement->text }}</p>
                        </div>
                    @endif

                    @if(!empty($previewActivity->supplement?->image_url))
                        @php $previewSuppImg = str_starts_with($previewActivity->supplement->image_url, 'http') ? $previewActivity->supplement->image_url : \Illuminate\Support\Facades\Storage::url($previewActivity->supplement->image_url); @endphp
                        <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400">Imagen complementaria</div>
                                <button type="button" @click="previewLightbox = true"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 min-h-[36px] bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 rounded-lg border border-cyan-500/20 text-[10px] font-bold uppercase tracking-wider transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M15 11a4 4 0 11-8 0 4 4 0 018 0zm-4-7v3m0 8v3M4 11h3m8 0h3"></path>
                                    </svg>
                                    Tamaño original
                                </button>
                            </div>
                            <img src="{{ $previewSuppImg }}"
                                alt="Imagen complementaria" class="rounded-lg border border-gray-200 dark:border-white/10 max-h-96 w-auto mx-auto cursor-zoom-in" @click="previewLightbox = true">
                        </div>

                        {{-- Lightbox: imagen en dimensiones originales --}}
                        <div x-show="previewLightbox" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="fixed inset-0 z-[90] overflow-auto bg-gray-950/90 p-4 sm:p-8"
                            @keydown.escape.window="previewLightbox = false">
                            <div class="absolute inset-0" @click="previewLightbox = false"></div>
                            <div class="relative min-h-full flex items-center justify-center">
                                <img src="{{ $previewSuppImg }}" alt="Imagen complementaria en tamaño original" class="max-w-none rounded-lg border border-white/20 shadow-2xl">
                            </div>
                            <button type="button" @click="previewLightbox = false"
                                class="fixed top-4 right-4 inline-flex items-center gap-1.5 px-4 py-2 min-h-[44px] bg-white/10 hover:bg-white/20 text-white rounded-lg border border-white/20 text-xs font-bold uppercase tracking-widest backdrop-blur transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cerrar
                            </button>
                        </div>
                    @endif

                    @if(empty(trim($previewActivity->supplement?->text ?? '')) && empty($previewActivity->supplement?->image_url))
                        <p class="text-sm text-gray-500 dark:text-slate-400 text-center py-6">Esta actividad no tiene información complementaria registrada.</p>
                    @endif
                </div>
            </div>
        @endif
        <x-slot name="footer">
            <div class="flex justify-end">
                <x-button flat label="Cerrar" x-on:click="modePreview = false" />
            </div>
        </x-slot>
    </x-modal-card>

    <!-- ===== MODAL: Información Complementaria (solo lectura) ===== -->
    <x-modal-card title="Información Complementaria" blur="lg" wire:model="modeSupplement" width="max-w-2xl" class="border border-gray-200 dark:border-white/10 rounded-xl bg-white dark:bg-gray-900">
        @if($supplementActivity)
            <div class="space-y-5" x-data="{ supplementLightbox: false }">
                <div class="bg-gray-50 dark:bg-white/5 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-1">Actividad</div>
                    <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $supplementActivity->topic ?? '—' }}</p>
                </div>

                @if(!empty(trim($supplementActivity->supplement?->text ?? '')))
                    <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                        <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400 mb-1.5">Texto complementario</div>
                        <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{{ $supplementActivity->supplement->text }}</p>
                    </div>
                @endif

                @if(!empty($supplementActivity->supplement?->image_url))
                    @php $suppModalImg = str_starts_with($supplementActivity->supplement->image_url, 'http') ? $supplementActivity->supplement->image_url : \Illuminate\Support\Facades\Storage::url($supplementActivity->supplement->image_url); @endphp
                    <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <div class="text-[10px] font-bold uppercase tracking-widest text-cyan-600 dark:text-cyan-400">Imagen complementaria</div>
                            <button type="button" @click="supplementLightbox = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 min-h-[36px] bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-600 dark:text-cyan-400 rounded-lg border border-cyan-500/20 text-[10px] font-bold uppercase tracking-wider transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M15 11a4 4 0 11-8 0 4 4 0 018 0zm-4-7v3m0 8v3M4 11h3m8 0h3"></path>
                                </svg>
                                Tamaño original
                            </button>
                        </div>
                        <img src="{{ $suppModalImg }}"
                            alt="Imagen complementaria" class="rounded-lg border border-gray-200 dark:border-white/10 max-h-96 w-auto mx-auto cursor-zoom-in" @click="supplementLightbox = true">
                    </div>

                    {{-- Lightbox: imagen en dimensiones originales --}}
                    <div x-show="supplementLightbox" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-[90] overflow-auto bg-gray-950/90 p-4 sm:p-8"
                        @keydown.escape.window="supplementLightbox = false">
                        <div class="absolute inset-0" @click="supplementLightbox = false"></div>
                        <div class="relative min-h-full flex items-center justify-center">
                            <img src="{{ $suppModalImg }}" alt="Imagen complementaria en tamaño original" class="max-w-none rounded-lg border border-white/20 shadow-2xl">
                        </div>
                        <button type="button" @click="supplementLightbox = false"
                            class="fixed top-4 right-4 inline-flex items-center gap-1.5 px-4 py-2 min-h-[44px] bg-white/10 hover:bg-white/20 text-white rounded-lg border border-white/20 text-xs font-bold uppercase tracking-widest backdrop-blur transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cerrar
                        </button>
                    </div>
                @endif

                @if(empty(trim($supplementActivity->supplement?->text ?? '')) && empty($supplementActivity->supplement?->image_url))
                    <p class="text-sm text-gray-500 dark:text-slate-400 text-center py-6">Esta actividad no tiene información complementaria registrada.</p>
                @endif
            </div>
        @endif
        <x-slot name="footer">
            <div class="flex justify-end">
                <x-button flat label="Cerrar" wire:click="closeSupplementModal" />
            </div>
        </x-slot>
    </x-modal-card>

    <!-- ===== MODAL: Wizard copiar actividades (activity:copy) ===== -->
    <x-modal-card title="Copiar actividades entre planes" blur="lg" wire:model="modeCopy" width="max-w-[90vw]">
        {{-- Stepper --}}
        <div class="flex items-center gap-1.5 mb-5">
            @foreach(['1' => 'Origen', '2' => 'Destino', '3' => 'Vista previa', '4' => 'Resultado'] as $n => $label)
                <button type="button" wire:click="copyGoToStep({{ $n }})"
                    class="flex-1 flex items-center justify-center gap-1.5 px-2 py-2 rounded-lg border text-[10px] font-bold uppercase tracking-widest transition-all
                    {{ $copyStep == $n ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border-emerald-500/30' : ($copyStep > $n ? 'bg-emerald-500/5 text-emerald-600 dark:text-emerald-500 border-emerald-500/20' : 'bg-gray-50 dark:bg-white/5 text-gray-400 border-gray-200 dark:border-white/5') }}">
                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[10px] {{ $copyStep >= $n ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-white/10 text-gray-500' }}">{{ $n }}</span>
                    <span class="hidden sm:inline">{{ $label }}</span>
                </button>
                @if($n < 4)
                    <div class="w-2 h-px {{ $copyStep > $n ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-white/10' }}"></div>
                @endif
            @endforeach
        </div>

        {{-- PASO 1: Origen --}}
        @if($copyStep === 1)
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Fuente de datos</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition-all {{ $copySource === '2' ? 'border-emerald-500/40 bg-emerald-500/5' : 'border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5' }}">
                            <input type="radio" wire:model.live="copySource" value="2" class="w-4 h-4 text-emerald-500 focus:ring-emerald-500/50">
                            <span class="text-sm text-gray-700 dark:text-gray-200"><strong>S2526</strong> <span class="text-xs text-gray-500">· período anterior</span></span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition-all {{ $copySource === '1' ? 'border-emerald-500/40 bg-emerald-500/5' : 'border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5' }}">
                            <input type="radio" wire:model.live="copySource" value="1" class="w-4 h-4 text-emerald-500 focus:ring-emerald-500/50">
                            <span class="text-sm text-gray-700 dark:text-gray-200"><strong>Actual</strong> <span class="text-xs text-gray-500">· base en uso</span></span>
                        </label>
                    </div>
                    @error('copySource')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Pevaluación origen (en la fuente)</label>
                    <div class="space-y-2 mb-2">
                        <x-select wire:model.live="copyPestudioId" placeholder="Plan Estudio: todos" searchable clearable>
                            @foreach($copyPestudioOptions as $opt)
                                <x-select.option :label="$opt['label']" :value="$opt['id']" :description="$opt['description']" />
                            @endforeach
                        </x-select>
                        <x-select wire:model.live="copyGradoId" placeholder="Grado/Año: todos" searchable clearable>
                            @foreach($copyGradoOptions as $opt)
                                <x-select.option :label="$opt['label']" :value="$opt['id']" :description="$opt['description']" />
                            @endforeach
                        </x-select>
                        <x-select wire:model.live="copyLapsoId" placeholder="Momento: todos" searchable clearable>
                            @foreach($copyLapsoOptions as $opt)
                                <x-select.option :label="$opt['label']" :value="$opt['id']" :description="$opt['description']" />
                            @endforeach
                        </x-select>
                    </div>
                    <x-input wire:model.live.debounce.300ms="copyFromSearch" placeholder="Buscar por materia, profesor, sección o ID..." />
                    <div class="mt-2">
                        <x-select wire:model.live="copyFromId" placeholder="Selecciona el plan origen..." searchable>
                            @foreach($copyFromOptions as $opt)
                                <x-select.option :label="$opt['label']" :value="$opt['id']" :description="$opt['description']" />
                            @endforeach
                        </x-select>
                    </div>
                    @error('copyFromId')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>@enderror
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-500">El origen nunca se modifica. Solo se copian <strong>actividades + indicadores</strong>; no se copian relaciones LMS ni comentarios de aprobación (<code>comments</code> queda en NULL).</p>
            </div>
        @endif

        {{-- PASO 2: Destino --}}
        @if($copyStep === 2)
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Pevaluación destino (base actual)</label>
                    <div class="space-y-2 mb-2">
                        <x-select wire:model.live="copyToPestudioId" placeholder="Plan Estudio: todos" searchable clearable>
                            @foreach($copyToPestudioOptions as $opt)
                                <x-select.option :label="$opt['label']" :value="$opt['id']" :description="$opt['description']" />
                            @endforeach
                        </x-select>
                        <x-select wire:model.live="copyToGradoId" placeholder="Grado/Año: todos" searchable clearable>
                            @foreach($copyToGradoOptions as $opt)
                                <x-select.option :label="$opt['label']" :value="$opt['id']" :description="$opt['description']" />
                            @endforeach
                        </x-select>
                        <x-select wire:model.live="copyToLapsoId" placeholder="Momento: todos" searchable clearable>
                            @foreach($copyToLapsoOptions as $opt)
                                <x-select.option :label="$opt['label']" :value="$opt['id']" :description="$opt['description']" />
                            @endforeach
                        </x-select>
                    </div>
                    <x-input wire:model.live.debounce.300ms="copyToSearch" placeholder="Buscar por materia, profesor, sección o ID..." />
                    <div class="mt-2">
                        <x-select wire:model.live="copyToId" placeholder="Selecciona el plan destino..." searchable>
                            @foreach($copyToOptions as $opt)
                                <x-select.option :label="$opt['label']" :value="$opt['id']" :description="$opt['description']" />
                            @endforeach
                        </x-select>
                    </div>
                    @error('copyToId')<p class="mt-1.5 text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
        @endif

        {{-- PASO 3: Vista previa --}}
        @if($copyStep === 3)
            <div class="space-y-4">
                @if($copyPreviewError)
                    <div class="p-3 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg text-xs text-red-700 dark:text-red-300 font-medium">{{ $copyPreviewError }}</div>
                @endif
                @if($copyPreview)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="p-3 rounded-lg border border-gray-200 dark:border-white/5 bg-gray-50 dark:bg-white/[0.03]">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1">Origen #{{ $copyPreview['from_id'] }} <span class="normal-case font-normal">({{ $copyPreview['sourceConnection'] }})</span></p>
                            <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $copyPreview['from_name'] }}</p>
                        </div>
                        <div class="p-3 rounded-lg border border-emerald-500/20 bg-emerald-500/5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-1">Destino #{{ $copyPreview['to_id'] }} <span class="normal-case font-normal">({{ $copyPreview['targetConnection'] }})</span></p>
                            <p class="text-xs font-bold text-gray-900 dark:text-white">{{ $copyPreview['to_name'] }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 text-[11px] font-bold">
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">{{ count($copyPreview['toCopy']) }} se copiarían</span>
                        <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">{{ $copyPreview['achievementsToCopy'] }} indicadores</span>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">{{ count($copyPreview['skipped']) }} ya existen (skip)</span>
                    </div>
                    <div class="max-h-64 overflow-y-auto border border-gray-200 dark:border-white/5 rounded-lg divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($copyPreview['toCopy'] as $row)
                            <div class="flex items-start gap-2 px-3 py-2">
                                <span class="mt-0.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold shrink-0">○</span>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-900 dark:text-white truncate">act {{ $row['id'] }} · {{ $row['topic'] ?? '—' }}</p>
                                    <p class="text-[11px] text-gray-500">{{ $row['finicial'] ?? '—' }} → {{ $row['ffinal'] ?? '—' }} · {{ $row['achievements'] }} ind.</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 text-center py-4">Nada que copiar: todo ya existe en el destino.</p>
                        @endforelse
                        @foreach($copyPreview['skipped'] as $row)
                            <div class="flex items-start gap-2 px-3 py-2 opacity-70">
                                <span class="mt-0.5 inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-500/15 text-amber-600 dark:text-amber-400 text-[10px] font-bold shrink-0">→</span>
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">act {{ $row['id'] }} · {{ $row['topic'] ?? '—' }} (ya existe)</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <label class="flex items-start gap-2 p-3 rounded-lg border border-amber-300 dark:border-amber-500/30 bg-amber-50 dark:bg-amber-500/5 cursor-pointer">
                        <input type="checkbox" wire:model="copyConfirm" class="mt-0.5 w-4 h-4 text-emerald-500 rounded focus:ring-emerald-500/50">
                        <span class="text-xs text-amber-800 dark:text-amber-200">Entiendo que la copia es idempotente (omite duplicados por huella topic+thematic+fechas) y que <strong>no hay deshacer automático</strong>.</span>
                    </label>
                    @error('copyConfirm')<p class="text-xs text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>@enderror
                @endif
            </div>
        @endif

        {{-- PASO 4: Resultado --}}
        @if($copyStep === 4 && $copyResult)
            <div class="space-y-4">
                <div class="p-4 rounded-lg border border-emerald-500/20 bg-emerald-500/5 text-center">
                    <p class="text-sm font-black text-emerald-600 dark:text-emerald-400">¡Copia completada!</p>
                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ $copyResult['to_name'] }}</p>
                    <div class="flex flex-wrap justify-center gap-2 mt-3 text-[11px] font-bold">
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">{{ $copyResult['copiedActivities'] }} actividades</span>
                        <span class="px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">{{ $copyResult['copiedAchievements'] }} indicadores</span>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">{{ $copyResult['skippedActivities'] }} omitidas</span>
                    </div>
                </div>
                <div class="max-h-48 overflow-y-auto border border-gray-200 dark:border-white/5 rounded-lg divide-y divide-gray-100 dark:divide-white/5">
                    @foreach($copyResult['details'] as $detail)
                        <div class="px-3 py-1.5 text-xs {{ $detail['status'] === 'copied' ? 'text-gray-700 dark:text-gray-200' : 'text-gray-400' }}">
                            @if($detail['status'] === 'copied')
                                ✓ act {{ $detail['id'] }} → {{ $detail['new_id'] }}: {{ $detail['topic'] }}
                            @else
                                → act {{ $detail['id'] }} omitida (ya existía): {{ $detail['topic'] }}
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <x-slot name="footer">
            <div class="flex justify-between gap-3">
                <x-button flat label="Cerrar" wire:click="closeCopyWizard" />
                <div class="flex gap-2">
                    @if($copyStep === 1)
                        <x-button primary label="Siguiente →" wire:click="copyGoToStep(2)" />
                    @elseif($copyStep === 2)
                        <x-button flat label="← Atrás" wire:click="copyGoToStep(1)" />
                        <x-button primary label="Vista previa" wire:click="loadCopyPreview" spinner="loadCopyPreview" />
                    @elseif($copyStep === 3)
                        <x-button flat label="← Atrás" wire:click="copyGoToStep(2)" />
                        <x-button primary label="Copiar ahora" wire:click="runCopy" spinner="runCopy" />
                    @elseif($copyStep === 4)
                        <x-button primary label="Listo" wire:click="closeCopyWizard" />
                    @endif
                </div>
            </div>
        </x-slot>
    </x-modal-card>

    <x-confirm-modal
        name="delete-observation"
        title="Eliminar observaciones"
        message="Esta acción no se puede deshacer."
        confirm-text="Sí, eliminar"
        cancel-text="Cancelar"
        type="danger"
        action="deleteObservation"
    />

    <x-confirm-modal
        name="delete-activity"
        title="Eliminar actividad"
        message="Esta acción no se puede deshacer."
        confirm-text="Sí, eliminar"
        cancel-text="Cancelar"
        type="danger"
        action="deleteActivity"
    />

    @script
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-modal', () => {
                // WireUI handles modal closing via wire:model
            });
        });
    </script>
    @endscript

    {{-- Modales LMS: Vista de Lección + Publicar --}}
    <x-lms-lesson-modals
        :show-lesson-preview="$showLessonPreview"
        :preview-data="$previewData"
        :show-publish-modal="$showPublishModal"
        :publish-activity-title="$publishActivityTitle"
        :publish-publish-at="$publishPublishAt" />

    @include('leadership.help-activities')

    <!-- Floating Loading State — anidado filters (transparencia backdrop-blur) -->
    <div wire:loading.flex
         class="fixed bottom-6 right-6 z-[80] hidden items-center gap-2.5 px-5 py-3 bg-white/65 dark:bg-gray-900/40 backdrop-blur-xl border border-gray-200/40 dark:border-white/10 rounded-full shadow-2xl shadow-black/20 dark:shadow-black/40 opacity-95">
        <svg class="w-4 h-4 animate-spin text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span class="text-xs font-bold uppercase tracking-widest text-gray-700 dark:text-gray-200">Cargando ...</span>
    </div>
</div>
