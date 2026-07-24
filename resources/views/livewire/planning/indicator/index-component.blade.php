<div class="fade-in">
    {{-- Loading sutil (bottom-right) --}}
    <x-loading-simple />

    <!-- Header -->
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Indicadores de Planificación</h1>
            <p class="text-cyan-400 font-medium">Dashboard institucional con KPIs por programa educativo y período académico.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('app.planning.index') }}"
                class="inline-flex items-center gap-2 px-3 py-2 sm:px-5 sm:py-2.5 min-h-[44px] bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg border border-cyan-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="hidden sm:inline">Planificación</span>
                <span class="sm:hidden">Volver</span>
            </a>
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 px-3 py-2 sm:px-5 sm:py-2.5 min-h-[44px] min-w-[44px] bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="hidden sm:inline">Refrescar</span>
            </button>
        </div>
    </div>

    <!-- Global KPI Boxes -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <x-indicator-box
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
            label="Actividades" value="{{ number_format($totalActivities) }}" color="purple" />
        <x-indicator-box
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            label="Diagnósticos" value="{{ number_format($totalDiagActive) }}" color="emerald" />
        <x-indicator-box
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
            label="Profesores Activos" value="{{ number_format($totalProfesoresActivos) }}" color="amber" />

        {{-- Lessons Card (global, not affected by lapso) --}}
        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-4 sm:p-5 rounded-lg transition-all duration-300 hover:border-sky-500/30 hover:shadow-lg hover:shadow-sky-500/5">
            <div class="flex items-start justify-between mb-2">
                <div class="w-9 h-9 sm:w-10 sm:h-10 bg-sky-500/10 rounded-lg flex items-center justify-center text-sky-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <p class="text-base sm:text-lg font-bold text-white mb-1">{{ number_format($lessonTotal) }}</p>
            <p class="text-[11px] font-medium text-sky-400 uppercase tracking-wider">Lecciones</p>
            <div class="mt-3 pt-3 border-t border-white/10 flex items-center justify-between text-[10px] sm:text-[11px] text-gray-500">
                <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Programadas: <strong class="text-sky-400 font-bold">{{ $lessonScheduled }}</strong>
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Publicadas: <strong class="text-emerald-400 font-bold">{{ $lessonPublished }}</strong>
                </span>
            </div>
        </div>
    </div>

    {{-- ═══ Registration Flow Charts (global, with date range filter) ═══ --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5 mb-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Flujo de Registros</h3>
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/5 rounded-lg p-0.5">
                @php $ranges = ['7d' => '7 días', '30d' => '30 días', '3m' => '3 meses', 'all' => 'Todo']; @endphp
                @foreach($ranges as $val => $label)
                    <button wire:click="$set('registrationRange', '{{ $val }}')"
                        class="px-3 py-1.5 min-h-[36px] text-[10px] font-bold uppercase tracking-wider rounded-md transition-all duration-200 whitespace-nowrap
                               {{ $registrationRange === $val ? 'bg-sky-500/20 text-sky-600 dark:text-sky-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/5' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            {{-- Activities Flow --}}
            <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-purple-500/20 rounded-md flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Actividades</span>
                    <span class="ml-auto text-[9px] text-gray-500 dark:text-gray-500">{{ count($chartActivitiesFlow) }} día(s)</span>
                </div>
                <div wire:ignore>
                    <div id="activities-flow-chart" class="w-full" style="min-height: 200px;"></div>
                </div>
            </div>

            {{-- Lessons Flow --}}
            <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-sky-500/20 rounded-md flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Lecciones</span>
                    <span class="ml-auto text-[9px] text-gray-500 dark:text-gray-500">{{ count($chartLessonsFlow) }} día(s)</span>
                </div>
                <div wire:ignore>
                    <div id="lessons-flow-chart" class="w-full" style="min-height: 200px;"></div>
                </div>
            </div>

            {{-- Diagnostics Flow --}}
            <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-emerald-500/20 rounded-md flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Diagnósticos</span>
                    <span class="ml-auto text-[9px] text-gray-500 dark:text-gray-500">{{ count($chartDiagnosticsFlow) }} día(s)</span>
                </div>
                <div wire:ignore>
                    <div id="diagnostics-flow-chart" class="w-full" style="min-height: 200px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lapso NavTabs + Filters -->
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden mb-4">
        {{-- Lapso navtabs (replica el patrón de profesors/activities) --}}
        <nav class="flex overflow-x-auto gap-0.5 snap-x snap-mandatory border-b border-white/5">
            @foreach($lapsos as $lapso)
                <button wire:click="$set('selectedLapsoId', {{ $lapso->id }})"
                    class="shrink-0 sm:flex-1 px-2 sm:px-4 py-2 min-h-[44px] text-[11px] font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap {{ $selectedLapsoId == $lapso->id ? 'text-cyan-400 border-cyan-500 bg-cyan-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600' }}">
                    <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5 hidden sm:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $lapso->name }}
                    <span class="block text-[8px] font-normal text-gray-500 normal-case">{{ $lapso->finicial?->format('d/m') }} – {{ $lapso->ffinal?->format('d/m') }}</span>
                </button>
            @endforeach
        </nav>

        {{-- 4 filters en grid de ancho completo --}}
        <div class="px-2 py-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
            <select wire:model.live="selectedPeducativoId"
                class="bg-gray-800 text-gray-200 text-[11px] rounded-lg border border-white/5 px-2 py-1.5 min-h-[44px] focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 outline-none appearance-none cursor-pointer w-full">
                <option value="">P.Educativo: Todos</option>
                @foreach($peducativos as $ped)
                    <option value="{{ $ped->id }}">{{ $ped->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedPestudioId"
                class="bg-gray-800 text-gray-200 text-[11px] rounded-lg border border-white/5 px-2 py-1.5 min-h-[44px] focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 outline-none appearance-none cursor-pointer w-full">
                <option value="">P.Estudio: Todos</option>
                @foreach($pestudios as $pest)
                    <option value="{{ $pest->id }}">{{ $pest->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedGradoId"
                class="bg-gray-800 text-gray-200 text-[11px] rounded-lg border border-white/5 px-2 py-1.5 min-h-[44px] focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 outline-none appearance-none cursor-pointer w-full">
                <option value="">Grado: Todos</option>
                @foreach($gradosOptions as $grd)
                    <option value="{{ $grd->id }}">{{ $grd->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedProfesorId"
                class="bg-gray-800 text-gray-200 text-[11px] rounded-lg border border-white/5 px-2 py-1.5 min-h-[44px] focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 outline-none appearance-none cursor-pointer w-full">
                <option value="">Profesor: Todos</option>
                @foreach($profesoresOptions as $prof)
                    <option value="{{ $prof->id }}">{{ $prof->lastname }}, {{ $prof->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Main Tabs -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden" x-data="{ activeTab: {{ $activeTab }} }">
        <div class="border-b border-white/5">
            <nav class="flex w-full overflow-x-auto gap-0.5 snap-x snap-mandatory [&::-webkit-scrollbar]:h-1" style="scrollbar-width: thin;">
                <button @click="activeTab = 1" title="Indicadores Principales" :class="activeTab === 1 ? 'text-cyan-400 border-cyan-500 bg-cyan-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                    class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                    <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="hidden sm:inline">Indicadores Principales</span>
                </button>
                <button @click="activeTab = 2" title="Profesores" :class="activeTab === 2 ? 'text-cyan-400 border-cyan-500 bg-cyan-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                    class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                    <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                    <span class="hidden sm:inline">Profesores</span>
                </button>
                <button @click="activeTab = 3" title="Actividades" :class="activeTab === 3 ? 'text-cyan-400 border-cyan-500 bg-cyan-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                    class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                    <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span class="hidden sm:inline">Actividades</span>
                </button>
            </nav>
        </div>

        <div class="p-2 sm:p-4 lg:p-6">

            {{-- ═══════════════════════════════════════════════════════════════════
                 TAB 1: Indicadores Principales — grouped by Peducativo
                 ═══════════════════════════════════════════════════════════════════ --}}
            <div x-show="activeTab === 1" x-cloak>
                <div class="space-y-8">
                    @forelse($peducativoMainIndicators as $item)
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center">
                                    <span class="text-cyan-400 text-xs font-bold">{{ $item->peducativo?->code ?? '' }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-white">{{ $item->peducativo?->name ?? '' }}</h3>
                                <span class="text-xs text-gray-500">[{{ $item->peducativo?->code ?? '' }}]</span>
                                <span class="ml-auto text-[10px] text-gray-500">{{ $item->pestudios->count() }} plan(es)</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2" style="grid-auto-flow: dense;">
                                <div class="col-span-1 sm:col-span-2">
                                    <x-indicator-box
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
                                        label="Actividades Registradas"
                                        value="{{ number_format($item->activities_count) }}"
                                        color="purple"
                                    />
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <x-indicator-box
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
                                        label="Profesores con Carga"
                                        value="{{ $item->profesores_count }}"
                                        color="amber"
                                    />
                                </div>
                            </div>
                        </div>
                        @if(!$loop->last)<hr class="border-white/5 my-6">@endif
                    @empty
                        <div class="text-center py-16">
                            <svg class="w-16 h-16 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-gray-500 font-medium">No hay programas educativos activos</p>
                            <p class="text-gray-600 text-sm mt-1">Activa un programa educativo con planificación para ver indicadores.</p>
                        </div>
                    @endforelse
                </div>

                    {{-- ═══ Charts Bento Grid (4-col) ═══ --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 mt-2" style="grid-auto-flow: dense;">
                        {{-- ── Actividades por Día (2×1) ── --}}
                        <div class="col-span-1 sm:col-span-2 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Actividades Registradas por Día</h3>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                    {{ count($chartActivitiesByDay) }} día(s) con actividad
                                </span>
                            </div>
                            <div wire:ignore>
                                <div id="activities-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                            </div>
                        </div>

                        {{-- ── Lecciones por Día (2×1) ── --}}
                        <div class="col-span-1 sm:col-span-2 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-sky-100 dark:bg-sky-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Lecciones Registradas por Día</h3>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                    {{ count($chartLessonsByDay['categories'] ?? []) }} día(s) con actividad
                                </span>
                            </div>
                            <div wire:ignore>
                                <div id="lessons-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                            </div>
                        </div>

                        {{-- ── Publicaciones Programadas por Día (4×1) ── --}}
                        <div class="col-span-1 sm:col-span-2 lg:col-span-4 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-violet-100 dark:bg-violet-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Publicaciones Programadas por Día</h3>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                    {{ count($chartScheduledByDay) }} día(s) con programación
                                </span>
                            </div>
                            <div wire:ignore>
                                <div id="scheduled-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                            </div>
                        </div>
                    </div>
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
                 TAB 2: Profesores  — Peducativo tabs → DataTable (uses top lapso selector)
                 ═══════════════════════════════════════════════════════════════════ --}}
            <div x-show="activeTab === 2" x-cloak
                 x-data="{ activePeducativo: {{ $peducativos->first()?->id ?? 0 }} }">

                @php $lapsoId = $selectedLapsoId; @endphp

                @if(isset($tab2Data[$lapsoId]) && count($tab2Data[$lapsoId]) > 0)
                    <!-- Peducativo nav-tabs (uses selected lapso only) -->
                    <div class="border-b border-white/5 mb-2">
                        <nav class="flex overflow-x-auto gap-0.5 snap-x snap-mandatory -mb-px">
                            @foreach($peducativos as $peducativo)
                                @php $ieePROM = $tab2Data[$lapsoId][$peducativo->id]['ieePROM'] ?? 0; @endphp
                                <button @click="activePeducativo = {{ $peducativo->id }}"
                                    :class="activePeducativo === {{ $peducativo->id }} ? 'text-violet-400 border-violet-500 bg-violet-500/5' : 'text-gray-500 border-transparent hover:text-gray-400'"
                                    class="shrink-0 sm:flex-1 px-2 sm:px-4 py-2 min-h-[44px] text-xs font-bold transition-all duration-200 border-b-2 whitespace-nowrap">
                                    {{ $peducativo->name }}
                                    <span class="block text-[9px] font-normal text-gray-500 normal-case" title="Cantidad promedio de notas por profesor">
                                        Prom.Notas[{{ round($ieePROM, 2) }}]
                                    </span>
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Peducativo content panels -->
                    @foreach($peducativos as $peducativo)
                        @php
                            $data = $tab2Data[$lapsoId][$peducativo->id] ?? null;
                            $profesors = $data['profesors'] ?? collect();
                        @endphp
                        <div x-show="activePeducativo === {{ $peducativo->id }}" x-cloak>
                            @if($profesors->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-white/5">
                                                <th class="text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">Profesor</th>
                                                <th class="text-center px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">N. Actividades</th>
                                                <th class="text-center px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500" title="Porcentaje de notas cargadas">IEE</th>
                                                <th class="text-center px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500" title="Porcentaje de notas cargadas para el corte de notas">IEE-CN</th>
                                                <th class="text-center px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500" title="Índice Relativo de Rendimiento en Evaluación">IRE</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/5">
                                            @foreach($profesors as $teacher)
                                                <tr class="hover:bg-white/[0.02] transition-colors">
                                                    <td class="px-3 py-2">
                                                        <span class="text-sm text-white font-medium">{{ $teacher->full_name }}</span>
                                                        <span class="block text-[10px] text-gray-500">{{ $teacher->ci_profesor }}</span>
                                                    </td>
                                                    <td class="px-3 py-2 text-center">
                                                        <span class="text-xs font-mono text-gray-300 px-2 py-1 bg-white/5 rounded-lg">
                                                            {{ $teacher->activities_count }}
                                                            @if($teacher->activities_count > 0)
                                                                <small class="text-gray-500">[{{ $teacher->approval_rate }}%]</small>
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-2 text-center">
                                                        <div class="flex items-center justify-center gap-2">
                                                            <div class="w-16 bg-white/5 rounded-full h-1.5">
                                                                <div class="h-1.5 rounded-full {{ ($teacher->iee ?? 0) >= 70 ? 'bg-emerald-500' : (($teacher->iee ?? 0) >= 40 ? 'bg-amber-500' : 'bg-red-500') }}"
                                                                     style="width: {{ min(100, $teacher->iee ?? 0) }}%"></div>
                                                            </div>
                                                            <span class="text-xs font-mono {{ ($teacher->iee ?? 0) >= 70 ? 'text-emerald-400' : (($teacher->iee ?? 0) >= 40 ? 'text-amber-400' : 'text-red-400') }}">
                                                                {{ $teacher->iee }}%
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-2 text-center">
                                                        <span class="text-xs font-mono {{ ($teacher->iee_cn ?? 0) >= 70 ? 'text-emerald-400' : (($teacher->iee_cn ?? 0) >= 40 ? 'text-amber-400' : 'text-red-400') }}">
                                                            {{ $teacher->iee_cn }}%
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-2 text-center">
                                                        <span class="text-xs font-mono {{ ($teacher->ire ?? 0) >= 100 ? 'text-emerald-400' : (($teacher->ire ?? 0) >= 70 ? 'text-amber-400' : 'text-red-400') }}">
                                                            {{ $teacher->ire }}%
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="bg-white/5 rounded-lg p-4 sm:p-6 text-center">
                                    <p class="text-gray-500 text-sm">No hay profesores con carga académica en este programa educativo.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="bg-white/5 rounded-lg p-4 sm:p-6 text-center">
                        <p class="text-gray-500 text-sm">No hay datos de profesores para el período seleccionado.</p>
                    </div>
                @endif
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
                 TAB 3: Actividades  — Peducativo tabs → 6 boxes (uses top lapso selector)
                 ═══════════════════════════════════════════════════════════════════ --}}
            <div x-show="activeTab === 3" x-cloak
                 x-data="{ activePeducativo: {{ $peducativos->first()?->id ?? 0 }} }">

                @php $lapsoId = $selectedLapsoId; @endphp

                @if(isset($tab3Data[$lapsoId]) && count($tab3Data[$lapsoId]) > 0)
                    <!-- Peducativo nav-tabs (uses selected lapso only) -->
                    <div class="border-b border-white/5 mb-2">
                        <nav class="flex overflow-x-auto gap-0.5 snap-x snap-mandatory -mb-px">
                            @foreach($peducativos as $peducativo)
                                @php
                                    $tab3Item = $tab3Data[$lapsoId][$peducativo->id] ?? null;
                                    $ieePROM = $tab3Item ? ($tab2Data[$lapsoId][$peducativo->id]['ieePROM'] ?? 0) : 0;
                                @endphp
                                <button @click="activePeducativo = {{ $peducativo->id }}"
                                    :class="activePeducativo === {{ $peducativo->id }} ? 'text-amber-400 border-amber-500 bg-amber-500/5' : 'text-gray-500 border-transparent hover:text-gray-400'"
                                    class="shrink-0 sm:flex-1 px-2 sm:px-4 py-2 min-h-[44px] text-xs font-bold transition-all duration-200 border-b-2 whitespace-nowrap">
                                    {{ $peducativo->name }}
                                    <span class="block text-[9px] font-normal text-gray-500 normal-case" title="Cantidad promedio de notas por profesor">
                                        Prom.Notas[{{ round($ieePROM, 2) }}]
                                    </span>
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    <!-- Peducativo content panels -->
                    @foreach($peducativos as $peducativo)
                        @php
                            $tab3Item = $tab3Data[$lapsoId][$peducativo->id] ?? null;
                            $indicators = $tab3Item->indicators ?? null;
                        @endphp
                        <div x-show="activePeducativo === {{ $peducativo->id }}" x-cloak>
                            @if($indicators && $indicators->total_activities > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2" style="grid-auto-flow: dense;">
                                    <div class="col-span-1 sm:col-span-2">
                                        <x-indicator-box
                                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
                                            label="Total de actividades planificadas"
                                            value="{{ number_format($indicators->total_activities) }}"
                                            color="cyan"
                                        />
                                    </div>
                                    <div class="col-span-1">
                                        <x-indicator-box
                                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>'
                                            label="Indicador de Cobertura Curricular"
                                            subtext="Promedio de actividades por Área de Formación"
                                            value="{{ $indicators->cobertura_curricular }}"
                                            color="emerald"
                                        />
                                    </div>
                                    <div class="col-span-1">
                                        <x-indicator-box
                                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"></path></svg>'
                                            label="Indicador de Participación"
                                            subtext="% Docentes con Planificaciones Activas"
                                            value="{{ $indicators->participacion }}%"
                                            color="blue"
                                        />
                                    </div>
                                    <div class="col-span-1">
                                        <x-indicator-box
                                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>'
                                            label="Indicador de Seguimiento"
                                            subtext="Tasa de Comentarios en Actividades"
                                            value="{{ $indicators->seguimiento }}%"
                                            color="purple"
                                        />
                                    </div>
                                    <div class="col-span-1">
                                        <x-indicator-box
                                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                                            label="Indicador de Aprobación"
                                            subtext="% de Actividades Aprobadas"
                                            value="{{ $indicators->aprobacion }}%"
                                            color="emerald"
                                        />
                                    </div>
                                    <div class="col-span-1 sm:col-span-2">
                                        <x-indicator-box
                                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'
                                            label="Indicador de Supervisión"
                                            subtext="Tasa de Observaciones en Áreas de Formación"
                                            value="{{ $indicators->supervision }}%"
                                            color="rose"
                                        />
                                    </div>
                                </div>
                            @else
                                <div class="bg-white/5 rounded-lg p-4 sm:p-6 text-center">
                                    <svg class="w-12 h-12 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    <p class="text-gray-500 text-sm mb-1">Sin actividades registradas</p>
                                    <p class="text-gray-600 text-xs">No hay actividades planificadas para este período en {{ $peducativo->name }}.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="bg-white/5 rounded-lg p-4 sm:p-6 text-center">
                        <p class="text-gray-500 text-sm">No hay datos de actividades para el período seleccionado.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

@script
<script>
    let activitiesChart = null;

    async function initActivitiesChart() {
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const el = document.getElementById('activities-per-day-chart');
        if (!el) return;

        if (activitiesChart) activitiesChart.destroy();

        // Use $wire.get() to get the raw value (avoid Proxy wrapping issues)
        const rawData = await $wire.get('chartActivitiesByDay') ?? [];

        activitiesChart = new window.ApexCharts(el, {
            series: [{
                name: 'Actividades',
                data: rawData,
            }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
            },
            colors: ['#10b981'],
            stroke: {
                curve: 'smooth',
                width: 2,
            },
            markers: {
                size: 4,
                colors: ['#10b981'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 6 },
            },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100],
                },
            },
            xaxis: {
                type: 'category',
                labels: {
                    style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 },
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: {
                    style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 },
                },
                tickAmount: 5,
                forceNiceScale: true,
            },
            grid: {
                borderColor: '#37415140',
                strokeDashArray: 4,
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function(val) {
                        return val + ' actividad(es)';
                    },
                },
            },
            noData: {
                text: 'Sin datos para los filtros seleccionados',
                align: 'center',
                verticalAlign: 'middle',
                style: { color: '#6b7280', fontSize: '13px' },
            },
        });

        activitiesChart.render();
    }

    // This script block runs after Livewire mounts — $wire is available
    initActivitiesChart();
    $wire.$watch('chartActivitiesByDay', () => initActivitiesChart());
</script>
@endscript

@script
<script>
    let lessonsChart = null;

    async function initLessonsChart() {
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const el = document.getElementById('lessons-per-day-chart');
        if (!el) return;

        if (lessonsChart) lessonsChart.destroy();

        const rawData = await $wire.get('chartLessonsByDay') ?? [];
        if (!rawData || !rawData.series) return;

        lessonsChart = new window.ApexCharts(el, {
            series: rawData.series,
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                dropShadow: {
                    enabled: true,
                    color: '#000',
                    top: 10,
                    left: 5,
                    blur: 8,
                    opacity: 0.3,
                },
            },
            colors: ['#10b981', '#0ea5e9', '#f59e0b'],
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#e2e8f0', '#e2e8f0'],
                    fontSize: '10px',
                    fontWeight: 600,
                },
                background: {
                    enabled: true,
                    foreColor: '#0f172a',
                    padding: 4,
                    borderRadius: 4,
                    borderWidth: 0,
                },
                dropShadow: { enabled: false },
            },
            stroke: {
                curve: 'smooth',
                width: 2,
            },
            markers: {
                size: 4,
                hover: { size: 6 },
            },
            xaxis: {
                categories: rawData.categories,
                type: 'category',
                labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } },
                tickAmount: 5,
                forceNiceScale: true,
                min: 0,
            },
            grid: { borderColor: '#37415140', strokeDashArray: 4 },
            tooltip: {
                theme: 'dark',
                shared: true,
                intersect: false,
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                labels: { colors: '#9ca3af' },
                markers: { width: 10, height: 10, radius: 2 },
            },
            noData: {
                text: 'Sin datos para los filtros seleccionados',
                align: 'center',
                verticalAlign: 'middle',
                style: { color: '#6b7280', fontSize: '13px' },
            },
        });

        lessonsChart.render();
    }

    initLessonsChart();
    $wire.$watch('chartLessonsByDay', () => initLessonsChart());
</script>
@endscript

@script
<script>
    let scheduledChart = null;

    async function initScheduledChart() {
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const el = document.getElementById('scheduled-per-day-chart');
        if (!el) return;

        if (scheduledChart) scheduledChart.destroy();

        const rawData = await $wire.get('chartScheduledByDay') ?? [];

        scheduledChart = new window.ApexCharts(el, {
            series: [{ name: 'Programadas', data: rawData }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
            },
            colors: ['#8b5cf6'],
            stroke: { curve: 'smooth', width: 2 },
            markers: {
                size: 4,
                colors: ['#8b5cf6'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 6 },
            },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100],
                },
            },
            xaxis: {
                type: 'category',
                labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } },
                tickAmount: 5,
                forceNiceScale: true,
            },
            grid: { borderColor: '#37415140', strokeDashArray: 4 },
            tooltip: {
                theme: 'dark',
                y: { formatter: function(val) { return val + ' programación(es)'; } },
            },
            noData: {
                text: 'Sin datos para los filtros seleccionados',
                align: 'center',
                verticalAlign: 'middle',
                style: { color: '#6b7280', fontSize: '13px' },
            },
        });

        scheduledChart.render();
    }

    initScheduledChart();
    $wire.$watch('chartScheduledByDay', () => initScheduledChart());
</script>
@endscript

{{-- Registration flow charts (activities/lessons/diagnostics by created_at) --}}

{{-- Light mode support: detect dark class and expose theme-aware chart colors --}}
<script>
    window.flowChartColors = (function() {
        var isDark = document.documentElement.classList.contains('dark');
        return {
            isDark: isDark,
            tooltip: { theme: isDark ? 'dark' : 'light' },
            chartBackground: isDark ? 'transparent' : '#ffffff',
            gridColor: isDark ? '#37415140' : '#e5e7eb',
            labelStyle: { colors: isDark ? '#9ca3af' : '#6b7280', fontSize: '10px', fontWeight: 600 },
        };
    })();
</script>

@script
<script>
    let activitiesFlowChart = null;

    async function initActivitiesFlowChart() {
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const el = document.getElementById('activities-flow-chart');
        if (!el) return;

        if (activitiesFlowChart) activitiesFlowChart.destroy();

        const rawData = await $wire.get('chartActivitiesFlow') ?? [];

        activitiesFlowChart = new window.ApexCharts(el, {
            series: [{
                name: 'Actividades',
                data: rawData,
            }],
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: window.flowChartColors.chartBackground,
            },
            colors: ['#a855f7'],
            stroke: {
                curve: 'smooth',
                width: 2,
            },
            markers: {
                size: 3,
                colors: ['#a855f7'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 5 },
            },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100],
                },
            },
            xaxis: {
                type: 'category',
                labels: {
                    style: window.flowChartColors.labelStyle,
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: {
                    style: window.flowChartColors.labelStyle,
                },
                tickAmount: 4,
                forceNiceScale: true,
            },
            grid: {
                borderColor: window.flowChartColors.gridColor,
                strokeDashArray: 4,
            },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                y: {
                    formatter: function(val) {
                        return val + ' actividad(es)';
                    },
                },
            },
            noData: {
                text: 'Sin datos para los filtros seleccionados',
                align: 'center',
                verticalAlign: 'middle',
                style: { color: '#6b7280', fontSize: '13px' },
            },
        });

        activitiesFlowChart.render();
    }

    initActivitiesFlowChart();
    $wire.$watch('chartActivitiesFlow', () => initActivitiesFlowChart());
    $wire.$watch('registrationRange', () => { setTimeout(() => initActivitiesFlowChart(), 100); });
</script>
@endscript

@script
<script>
    let lessonsFlowChart = null;

    async function initLessonsFlowChart() {
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const el = document.getElementById('lessons-flow-chart');
        if (!el) return;

        if (lessonsFlowChart) lessonsFlowChart.destroy();

        const rawData = await $wire.get('chartLessonsFlow') ?? [];

        lessonsFlowChart = new window.ApexCharts(el, {
            series: [{
                name: 'Lecciones',
                data: rawData,
            }],
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: window.flowChartColors.chartBackground,
            },
            colors: ['#0ea5e9'],
            stroke: {
                curve: 'smooth',
                width: 2,
            },
            markers: {
                size: 3,
                colors: ['#0ea5e9'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 5 },
            },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100],
                },
            },
            xaxis: {
                type: 'category',
                labels: {
                    style: window.flowChartColors.labelStyle,
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: {
                    style: window.flowChartColors.labelStyle,
                },
                tickAmount: 4,
                forceNiceScale: true,
            },
            grid: {
                borderColor: window.flowChartColors.gridColor,
                strokeDashArray: 4,
            },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                y: {
                    formatter: function(val) {
                        return val + ' lección(es)';
                    },
                },
            },
            noData: {
                text: 'Sin datos para los filtros seleccionados',
                align: 'center',
                verticalAlign: 'middle',
                style: { color: '#6b7280', fontSize: '13px' },
            },
        });

        lessonsFlowChart.render();
    }

    initLessonsFlowChart();
    $wire.$watch('chartLessonsFlow', () => initLessonsFlowChart());
    $wire.$watch('registrationRange', () => { setTimeout(() => initLessonsFlowChart(), 100); });
</script>
@endscript

@script
<script>
    let diagnosticsFlowChart = null;

    async function initDiagnosticsFlowChart() {
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const el = document.getElementById('diagnostics-flow-chart');
        if (!el) return;

        if (diagnosticsFlowChart) diagnosticsFlowChart.destroy();

        const rawData = await $wire.get('chartDiagnosticsFlow') ?? [];

        diagnosticsFlowChart = new window.ApexCharts(el, {
            series: [{
                name: 'Diagnósticos',
                data: rawData,
            }],
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: window.flowChartColors.chartBackground,
            },
            colors: ['#10b981'],
            stroke: {
                curve: 'smooth',
                width: 2,
            },
            markers: {
                size: 3,
                colors: ['#10b981'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 5 },
            },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.5,
                    opacityTo: 0,
                    stops: [0, 90, 100],
                },
            },
            xaxis: {
                type: 'category',
                labels: {
                    style: window.flowChartColors.labelStyle,
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: {
                    style: window.flowChartColors.labelStyle,
                },
                tickAmount: 4,
                forceNiceScale: true,
            },
            grid: {
                borderColor: window.flowChartColors.gridColor,
                strokeDashArray: 4,
            },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                y: {
                    formatter: function(val) {
                        return val + ' diagnóstico(s)';
                    },
                },
            },
            noData: {
                text: 'Sin datos para los filtros seleccionados',
                align: 'center',
                verticalAlign: 'middle',
                style: { color: '#6b7280', fontSize: '13px' },
            },
        });

        diagnosticsFlowChart.render();
    }

    initDiagnosticsFlowChart();
    $wire.$watch('chartDiagnosticsFlow', () => initDiagnosticsFlowChart());
    $wire.$watch('registrationRange', () => { setTimeout(() => initDiagnosticsFlowChart(), 100); });
</script>
@endscript
