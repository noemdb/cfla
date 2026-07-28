<div class="fade-in">
    <x-loading-simple />

    {{-- Header --}}
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Panel de Seguimiento · Indicadores</h1>
            <p class="text-amber-600 dark:text-amber-400 font-medium text-sm">
                {{ \Illuminate\Support\Facades\Auth::user()->username }} ·
                {{ count($asignaturaIds) }} asignatura(s) bajo tu supervisión
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 px-3 py-2 sm:px-5 sm:py-2.5 min-h-[44px] min-w-[44px] bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="hidden sm:inline">Refrescar</span>
            </button>
            <button wire:click="openAreasPensumsModal"
                class="inline-flex items-center gap-2 px-3 py-2 sm:px-5 sm:py-2.5 min-h-[44px] min-w-[44px] bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 hover:text-indigo-200 rounded-lg border border-indigo-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span class="hidden sm:inline">Áreas</span>
            </button>
        </div>
    </div>

    {{-- Global KPI Boxes --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <x-indicator-box
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
            label="Actividades" value="{{ number_format($totalActivities) }}" color="amber" />
        <x-indicator-box
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            label="Diagnósticos" value="{{ number_format($totalDiagActive) }}" color="emerald" />
        <x-indicator-box
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
            label="Profesores Activos" value="{{ number_format($totalProfesoresActivos) }}" color="amber" />

        {{-- Lessons Card --}}
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

    {{-- Registration Flow Charts --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5 mb-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Flujo de Registros</h3>
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/5 rounded-lg p-0.5">
                @php $ranges = ['7d' => '7 días', '30d' => '30 días', '3m' => '3 meses', 'all' => 'Todo']; @endphp
                @foreach($ranges as $val => $label)
                    <button wire:click="$set('registrationRange', '{{ $val }}')"
                        class="px-3 py-1.5 min-h-[36px] text-[10px] font-bold uppercase tracking-wider rounded-md transition-all duration-200 whitespace-nowrap
                               {{ $registrationRange === $val ? 'bg-amber-500/20 text-amber-600 dark:text-amber-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/5' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <div class="bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 bg-amber-500/20 rounded-md flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Actividades</span>
                    <span class="ml-auto text-[9px] text-gray-500 dark:text-gray-500">{{ count($chartActivitiesFlow) }} día(s)</span>
                </div>
                <div wire:ignore>
                    <div id="ld-activities-flow-chart" class="w-full" style="min-height: 200px;"></div>
                </div>
            </div>
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
                    <div id="ld-lessons-flow-chart" class="w-full" style="min-height: 200px;"></div>
                </div>
            </div>
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
                    <div id="ld-diagnostics-flow-chart" class="w-full" style="min-height: 200px;"></div>
                </div>
            </div>
        </div>
    </div>

    @if(count($asignaturaIds) > 0 || $isAdmin)
    {{-- Lapso NavTabs + Filters --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden mb-4">
        <nav class="flex overflow-x-auto gap-0.5 snap-x snap-mandatory border-b border-white/5">
            @foreach($lapsos as $lapso)
                <button wire:click="$set('selectedLapsoId', {{ $lapso->id }})"
                    class="shrink-0 sm:flex-1 px-2 sm:px-4 py-2 min-h-[44px] text-[11px] font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap {{ $selectedLapsoId == $lapso->id ? 'text-amber-400 border-amber-500 bg-amber-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600' }}">
                    <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5 hidden sm:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $lapso->name }}
                    <span class="block text-[8px] font-normal text-gray-500 normal-case">{{ $lapso->finicial?->format('d/m') }} – {{ $lapso->ffinal?->format('d/m') }}</span>
                </button>
            @endforeach
        </nav>

        <div class="px-2 py-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
            <select wire:model.live="selectedPeducativoId"
                class="bg-gray-800 text-gray-200 text-[11px] rounded-lg border border-white/5 px-2 py-1.5 min-h-[44px] focus:border-amber-500/30 focus:ring-1 focus:ring-amber-500/20 outline-none appearance-none cursor-pointer w-full">
                <option value="">P.Educativo: Todos</option>
                @foreach($peducativos as $ped)
                    <option value="{{ $ped->id }}">{{ $ped->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedPestudioId"
                class="bg-gray-800 text-gray-200 text-[11px] rounded-lg border border-white/5 px-2 py-1.5 min-h-[44px] focus:border-amber-500/30 focus:ring-1 focus:ring-amber-500/20 outline-none appearance-none cursor-pointer w-full">
                <option value="">P.Estudio: Todos</option>
                @foreach($filteredPestudios as $pest)
                    <option value="{{ $pest->id }}">{{ $pest->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedGradoId"
                class="bg-gray-800 text-gray-200 text-[11px] rounded-lg border border-white/5 px-2 py-1.5 min-h-[44px] focus:border-amber-500/30 focus:ring-1 focus:ring-amber-500/20 outline-none appearance-none cursor-pointer w-full">
                <option value="">Grado: Todos</option>
                @foreach($gradosOptions as $grd)
                    <option value="{{ $grd->id }}">{{ $grd->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedSeccionId"
                class="bg-gray-800 text-gray-200 text-[11px] rounded-lg border border-white/5 px-2 py-1.5 min-h-[44px] focus:border-amber-500/30 focus:ring-1 focus:ring-amber-500/20 outline-none appearance-none cursor-pointer w-full">
                <option value="">Sección: Todas</option>
                @foreach($seccionesOptions as $sec)
                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Main Tabs --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden" x-data="{ activeTab: {{ $activeTab }} }">
        <div class="border-b border-white/5">
            <nav class="flex w-full overflow-x-auto gap-0.5 snap-x snap-mandatory [&::-webkit-scrollbar]:h-1" style="scrollbar-width: thin;">
                <button @click="activeTab = 1" title="Indicadores Principales" :class="activeTab === 1 ? 'text-amber-400 border-amber-500 bg-amber-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                    class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                    <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="hidden sm:inline">Indicadores Principales</span>
                </button>
                <button @click="activeTab = 2" title="Profesores" :class="activeTab === 2 ? 'text-amber-400 border-amber-500 bg-amber-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                    class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                    <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                    <span class="hidden sm:inline">Profesores</span>
                </button>
                <button @click="activeTab = 3" title="Actividades" :class="activeTab === 3 ? 'text-amber-400 border-amber-500 bg-amber-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                    class="flex-1 px-2 sm:px-3 lg:px-6 py-2 min-h-[44px] text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                    <svg class="w-4 h-4 inline sm:mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span class="hidden sm:inline">Actividades</span>
                </button>
            </nav>
        </div>

        <div class="p-2 sm:p-4 lg:p-6">

            {{-- ═══ TAB 1: Indicadores Principales ═══ --}}
            <div x-show="activeTab === 1" x-cloak>
                <div class="space-y-8">
                    @forelse($peducativoMainIndicators as $item)
                        <div>
                            <div class="flex items-start gap-3 mb-2">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-bold text-white">{{ $item->peducativo?->name ?? '' }}</h3>
                                    @if($item->peducativo?->description)
                                    <p class="text-xs text-gray-400 mt-0.5 leading-relaxed">{{ $item->peducativo->description }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="text-[10px] text-gray-500 whitespace-nowrap">{{ $item->grados_count }} grado(s)</span>
                                    <span class="text-[10px] text-gray-500 whitespace-nowrap">{{ $item->pensums_count }} pensum(s)</span>
                                    <span class="text-[10px] text-gray-500 whitespace-nowrap">{{ $item->pestudios->count() }} plan(es)</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2" style="grid-auto-flow: dense;">
                                <div class="col-span-1 sm:col-span-2">
                                    <x-indicator-box
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
                                        label="Actividades Registradas"
                                        value="{{ number_format($item->activities_count) }}"
                                        color="amber"
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
                                <div class="col-span-1 sm:col-span-2">
                                    <x-indicator-box
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>'
                                        label="Lecciones Registradas"
                                        value="{{ number_format($item->lessons_count) }}"
                                        color="sky"
                                    />
                                </div>
                            </div>
                        </div>
                        @if(!$loop->last)<hr class="border-white/5 my-6">@endif
                    @empty
                        <div class="text-center py-16">
                            <svg class="w-16 h-16 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-gray-500 font-medium">No hay programas educativos activos bajo tu supervisión</p>
                            <p class="text-gray-600 text-sm mt-1">Las áreas que supervisas no tienen programas educativos con planificación activa.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Charts Bento Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 mt-2" style="grid-auto-flow: dense;">
                    <div class="col-span-1 sm:col-span-2 bg-white dark:bg-gray-800/30 border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-amber-100 dark:bg-amber-500/20 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Actividades Registradas por Día</h3>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">{{ count($chartActivitiesByDay) }} día(s)</span>
                        </div>
                        <div wire:ignore>
                            <div id="ld-activities-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                        </div>
                    </div>
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
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">{{ count($chartLessonsByDay['categories'] ?? []) }} día(s)</span>
                        </div>
                        <div wire:ignore>
                            <div id="ld-lessons-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                        </div>
                    </div>
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
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">{{ count($chartScheduledByDay) }} día(s)</span>
                        </div>
                        <div wire:ignore>
                            <div id="ld-scheduled-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ TAB 2: Profesores ═══ --}}
            <div x-show="activeTab === 2" x-cloak
                 x-data="{ activePeducativo: {{ $peducativos->first()?->id ?? 0 }} }">
                @php $lapsoId = $selectedLapsoId; @endphp

                @if(isset($tab2Data[$lapsoId]) && count($tab2Data[$lapsoId]) > 0)
                    <div class="border-b border-white/5 mb-2">
                        <nav class="flex overflow-x-auto gap-0.5 snap-x snap-mandatory -mb-px">
                            @foreach($peducativos as $peducativo)
                                @php $ieePROM = $tab2Data[$lapsoId][$peducativo->id]['ieePROM'] ?? 0; @endphp
                                <button @click="activePeducativo = {{ $peducativo->id }}"
                                    :class="activePeducativo === {{ $peducativo->id }} ? 'text-violet-400 border-violet-500 bg-violet-500/5' : 'text-gray-500 border-transparent hover:text-gray-400'"
                                    class="shrink-0 sm:flex-1 px-2 sm:px-4 py-2 min-h-[44px] text-xs font-bold transition-all duration-200 border-b-2 whitespace-nowrap">
                                    {{ $peducativo->name }}
                                    <span class="block text-[9px] font-normal text-gray-500 normal-case">Prom.Notas[{{ round($ieePROM, 2) }}]</span>
                                </button>
                            @endforeach
                        </nav>
                    </div>

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
                                    <p class="text-gray-500 text-sm">No hay profesores con carga académica en este programa educativo bajo tu supervisión.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="bg-white/5 rounded-lg p-4 sm:p-6 text-center">
                        <p class="text-gray-500 text-sm">No hay datos de profesores para el período seleccionado en tus áreas asignadas.</p>
                    </div>
                @endif
            </div>

            {{-- ═══ TAB 3: Actividades ═══ --}}
            <div x-show="activeTab === 3" x-cloak
                 x-data="{ activePeducativo: {{ $peducativos->first()?->id ?? 0 }} }">
                @php $lapsoId = $selectedLapsoId; @endphp

                @if(isset($tab3Data[$lapsoId]) && count($tab3Data[$lapsoId]) > 0)
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
                                    <span class="block text-[9px] font-normal text-gray-500 normal-case">Prom.Notas[{{ round($ieePROM, 2) }}]</span>
                                </button>
                            @endforeach
                        </nav>
                    </div>

                    @foreach($peducativos as $peducativo)
                        @php
                            $tab3Item = $tab3Data[$lapsoId][$peducativo->id] ?? null;
                            $indicators = $tab3Item->indicators ?? null;
                        @endphp
                        <div x-show="activePeducativo === {{ $peducativo->id }}" x-cloak>
                            @if($indicators && $indicators->total_activities > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2" style="grid-auto-flow: dense;">
                                    <div class="col-span-1 sm:col-span-2">
                                        <x-indicator-box icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
                                            label="Total de actividades planificadas" value="{{ number_format($indicators->total_activities) }}" color="amber" />
                                    </div>
                                    <div class="col-span-1">
                                        <x-indicator-box icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>'
                                            label="Cobertura Curricular" subtext="Promedio de actividades por Área de Formación" value="{{ $indicators->cobertura_curricular }}" color="emerald" />
                                    </div>
                                    <div class="col-span-1">
                                        <x-indicator-box icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"></path></svg>'
                                            label="Participación" subtext="% Docentes con Planificaciones Activas" value="{{ $indicators->participacion }}%" color="blue" />
                                    </div>
                                    <div class="col-span-1">
                                        <x-indicator-box icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>'
                                            label="Seguimiento" subtext="Tasa de Comentarios en Actividades" value="{{ $indicators->seguimiento }}%" color="purple" />
                                    </div>
                                    <div class="col-span-1">
                                        <x-indicator-box icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                                            label="Aprobación" subtext="% de Actividades Aprobadas" value="{{ $indicators->aprobacion }}%" color="emerald" />
                                    </div>
                                    <div class="col-span-1 sm:col-span-2">
                                        <x-indicator-box icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'
                                            label="Supervisión" subtext="Tasa de Observaciones en Áreas de Formación" value="{{ $indicators->supervision }}%" color="rose" />
                                    </div>
                                </div>
                            @else
                                <div class="bg-white/5 rounded-lg p-4 sm:p-6 text-center">
                                    <svg class="w-12 h-12 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    <p class="text-gray-500 text-sm mb-1">Sin actividades registradas</p>
                                    <p class="text-gray-600 text-xs">No hay actividades planificadas para este período en {{ $peducativo->name }} bajo tu supervisión.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="bg-white/5 rounded-lg p-4 sm:p-6 text-center">
                        <p class="text-gray-500 text-sm">No hay datos de actividades para el período seleccionado en tus áreas asignadas.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
    @else
    {{-- Empty state: no areas assigned --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-12 text-center">
        <svg class="w-14 h-14 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <p class="text-gray-500 font-medium mb-2">No tienes áreas asignadas</p>
        <p class="text-gray-600 text-sm">Contacta al administrador para asignarte como líder de áreas de conocimiento.</p>
    </div>
    @endif

    {{-- ═══ Áreas de Conocimiento XXL Modal ═══ --}}
    <x-modal-card title="Áreas de Conocimiento"
        wire:model="showAreasPensumsModal"
        width="max-w-[90vw]">
        <div class="max-h-[80vh] overflow-y-auto space-y-4 px-1">

            @forelse($areasPensumsData as $area)
                <div class="border border-white/10 rounded-lg overflow-hidden">
                    {{-- Area header --}}
                    <div class="flex items-start justify-between gap-3 bg-white/5 px-4 py-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-white">{{ $area['name'] }}</h3>
                                @if($area['code'])
                                    <span class="text-[10px] font-mono text-gray-500 bg-white/5 px-1.5 py-0.5 rounded">{{ $area['code'] }}</span>
                                @endif
                            </div>
                            @if($area['description'])
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ $area['description'] }}</p>
                            @endif
                        </div>
                        <div class="shrink-0 text-right">
                            @if($area['peducativo'] ?? false)
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400/70 bg-amber-500/10 px-2 py-1 rounded">
                                    {{ $area['peducativo']['name'] ?? '' }}
                                </span>
                            @endif
                            <span class="block text-[10px] text-gray-500 mt-0.5">
                                {{ count($area['campo_conocimientos'] ?? []) }} asignatura(s)
                            </span>
                        </div>
                    </div>

                    {{-- Asignaturas list --}}
                    @if(!empty($area['campo_conocimientos']))
                        <div class="divide-y divide-white/5">
                            @foreach($area['campo_conocimientos'] as $cc)
                                @php $asignatura = $cc['asignatura'] ?? null; @endphp
                                @if($asignatura)
                                    <div class="px-4 py-2.5 hover:bg-white/[0.02] transition-colors">
                                        <div class="flex items-center gap-2 mb-1">
                                            <svg class="w-3.5 h-3.5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-200">{{ $asignatura['name'] }}</span>
                                            @if($asignatura['code'])
                                                <span class="text-[10px] font-mono text-gray-500">({{ $asignatura['code'] }})</span>
                                            @endif
                                        </div>

                                        {{-- Pensums --}}
                                        @if(!empty($asignatura['pensums']))
                                            <div class="ml-5 mt-1.5 flex flex-wrap gap-1.5">
                                                @foreach($asignatura['pensums'] as $pensum)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-white/5 text-gray-300 border border-white/5">
                                                        <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                        </svg>
                                                        {{ $pensum['grado']['name'] ?? '?' }}
                                                        <span class="text-gray-600">·</span>
                                                        {{ $pensum['pestudio']['name'] ?? '?' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="ml-5 text-[10px] text-gray-600 mt-1">Sin pensums asociados</p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="px-4 py-3 text-center">
                            <p class="text-xs text-gray-500">Sin asignaturas asociadas</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="py-16 text-center">
                    <svg class="w-12 h-12 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-gray-500 font-medium">No hay áreas de conocimiento asignadas</p>
                    <p class="text-gray-600 text-sm mt-1">No tienes áreas de conocimiento bajo tu supervisión.</p>
                </div>
            @endforelse

        </div>

        <x-slot:footer>
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-500">{{ count($areasPensumsData) }} área(s) de conocimiento</span>
                <x-button xs wire:click="$set('showAreasPensumsModal', false)" label="Cerrar" class="font-bold" />
            </div>
        </x-slot:footer>
    </x-modal-card>
</div>

{{-- ═══ ApexCharts scripts ═══ --}}
<script>
    window.ldFlowChartColors = (function() {
        var isDark = document.documentElement.classList.contains('dark');
        return {
            isDark,
            tooltip: { theme: isDark ? 'dark' : 'light' },
            chartBackground: isDark ? 'transparent' : '#ffffff',
            gridColor: isDark ? '#37415140' : '#e5e7eb',
            labelStyle: { colors: isDark ? '#9ca3af' : '#6b7280', fontSize: '10px', fontWeight: 600 },
        };
    })();
</script>

<script>
(function() {
    'use strict';

    // Chart configuration for all 6 ApexCharts
    var CHARTS = [
        {
            id: 'ld-activities-per-day-chart',
            varName: 'ldActivitiesChart',
            dataProp: 'chartActivitiesByDay',
            build: function(rawData) {
                return {
                    series: [{ name: 'Actividades', data: rawData || [] }],
                    chart: { type: 'area', height: 300, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, system-ui, sans-serif' },
                    colors: ['#f59e0b'],
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 4, colors: ['#f59e0b'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 6 } },
                    dataLabels: { enabled: false },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] } },
                    xaxis: { type: 'category', labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } }, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } }, tickAmount: 5, forceNiceScale: true },
                    grid: { borderColor: '#37415140', strokeDashArray: 4 },
                    tooltip: { theme: 'dark', y: { formatter: function(v) { return v + ' actividad(es)'; } } },
                    noData: { text: 'Sin datos para los filtros seleccionados', align: 'center', verticalAlign: 'middle', style: { color: '#6b7280', fontSize: '13px' } },
                };
            },
        },
        {
            id: 'ld-lessons-per-day-chart',
            varName: 'ldLessonsChart',
            dataProp: 'chartLessonsByDay',
            build: function(rawData) {
                var d = rawData || {};
                return {
                    series: d.series || [],
                    chart: { type: 'line', height: 300, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, system-ui, sans-serif', dropShadow: { enabled: true, color: '#000', top: 10, left: 5, blur: 8, opacity: 0.3 } },
                    colors: ['#10b981', '#0ea5e9', '#f59e0b'],
                    dataLabels: { enabled: true, style: { colors: ['#e2e8f0', '#e2e8f0'], fontSize: '10px', fontWeight: 600 }, background: { enabled: true, foreColor: '#0f172a', padding: 4, borderRadius: 4, borderWidth: 0 }, dropShadow: { enabled: false } },
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 4, hover: { size: 6 } },
                    xaxis: { categories: d.categories || [], type: 'category', labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } }, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } }, tickAmount: 5, forceNiceScale: true, min: 0 },
                    grid: { borderColor: '#37415140', strokeDashArray: 4 },
                    tooltip: { theme: 'dark', shared: true, intersect: false },
                    legend: { position: 'top', horizontalAlign: 'right', labels: { colors: '#9ca3af' }, markers: { width: 10, height: 10, radius: 2 } },
                    noData: { text: 'Sin datos para los filtros seleccionados', align: 'center', verticalAlign: 'middle', style: { color: '#6b7280', fontSize: '13px' } },
                };
            },
        },
        {
            id: 'ld-scheduled-per-day-chart',
            varName: 'ldScheduledChart',
            dataProp: 'chartScheduledByDay',
            build: function(rawData) {
                return {
                    series: [{ name: 'Programadas', data: rawData || [] }],
                    chart: { type: 'area', height: 300, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, system-ui, sans-serif' },
                    colors: ['#8b5cf6'],
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 4, colors: ['#8b5cf6'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 6 } },
                    dataLabels: { enabled: false },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] } },
                    xaxis: { type: 'category', labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } }, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 600 } }, tickAmount: 5, forceNiceScale: true },
                    grid: { borderColor: '#37415140', strokeDashArray: 4 },
                    tooltip: { theme: 'dark', y: { formatter: function(v) { return v + ' programación(es)'; } } },
                    noData: { text: 'Sin datos para los filtros seleccionados', align: 'center', verticalAlign: 'middle', style: { color: '#6b7280', fontSize: '13px' } },
                };
            },
        },
        // Flow charts
        {
            id: 'ld-activities-flow-chart',
            varName: 'ldActivitiesFlowChart',
            dataProp: 'chartActivitiesFlow',
            flow: true,
            build: function(rawData) {
                var c = window.ldFlowChartColors || {};
                return {
                    series: [{ name: 'Actividades', data: rawData || [] }],
                    chart: { type: 'area', height: 200, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, system-ui, sans-serif', background: c.chartBackground },
                    colors: ['#f59e0b'],
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 3, colors: ['#f59e0b'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 5 } },
                    dataLabels: { enabled: false },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] } },
                    xaxis: { type: 'category', labels: { style: c.labelStyle }, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { style: c.labelStyle }, tickAmount: 4, forceNiceScale: true },
                    grid: { borderColor: c.gridColor, strokeDashArray: 4 },
                    tooltip: { theme: c.tooltip ? c.tooltip.theme : 'dark', y: { formatter: function(v) { return v + ' actividad(es)'; } } },
                    noData: { text: 'Sin datos para los filtros seleccionados', align: 'center', verticalAlign: 'middle', style: { color: '#6b7280', fontSize: '13px' } },
                };
            },
        },
        {
            id: 'ld-lessons-flow-chart',
            varName: 'ldLessonsFlowChart',
            dataProp: 'chartLessonsFlow',
            flow: true,
            build: function(rawData) {
                var c = window.ldFlowChartColors || {};
                return {
                    series: [{ name: 'Lecciones', data: rawData || [] }],
                    chart: { type: 'area', height: 200, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, system-ui, sans-serif', background: c.chartBackground },
                    colors: ['#0ea5e9'],
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 3, colors: ['#0ea5e9'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 5 } },
                    dataLabels: { enabled: false },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] } },
                    xaxis: { type: 'category', labels: { style: c.labelStyle }, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { style: c.labelStyle }, tickAmount: 4, forceNiceScale: true },
                    grid: { borderColor: c.gridColor, strokeDashArray: 4 },
                    tooltip: { theme: c.tooltip ? c.tooltip.theme : 'dark', y: { formatter: function(v) { return v + ' lección(es)'; } } },
                    noData: { text: 'Sin datos para los filtros seleccionados', align: 'center', verticalAlign: 'middle', style: { color: '#6b7280', fontSize: '13px' } },
                };
            },
        },
        {
            id: 'ld-diagnostics-flow-chart',
            varName: 'ldDiagnosticsFlowChart',
            dataProp: 'chartDiagnosticsFlow',
            flow: true,
            build: function(rawData) {
                var c = window.ldFlowChartColors || {};
                return {
                    series: [{ name: 'Diagnósticos', data: rawData || [] }],
                    chart: { type: 'area', height: 200, toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, system-ui, sans-serif', background: c.chartBackground },
                    colors: ['#10b981'],
                    stroke: { curve: 'smooth', width: 2 },
                    markers: { size: 3, colors: ['#10b981'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 5 } },
                    dataLabels: { enabled: false },
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] } },
                    xaxis: { type: 'category', labels: { style: c.labelStyle }, axisBorder: { show: false }, axisTicks: { show: false } },
                    yaxis: { labels: { style: c.labelStyle }, tickAmount: 4, forceNiceScale: true },
                    grid: { borderColor: c.gridColor, strokeDashArray: 4 },
                    tooltip: { theme: c.tooltip ? c.tooltip.theme : 'dark', y: { formatter: function(v) { return v + ' diagnóstico(s)'; } } },
                    noData: { text: 'Sin datos para los filtros seleccionados', align: 'center', verticalAlign: 'middle', style: { color: '#6b7280', fontSize: '13px' } },
                };
            },
        },
    ];

    // Store chart instances
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

        // In Livewire 3, $wire.property reads directly from the reactive data
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

            // Set up watches via $wire.$watch (public Livewire API)
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

            // Extra watch on registrationRange for flow charts
            try {
                $wire.$watch('registrationRange', function() {
                    setTimeout(function() {
                        for (var k = 0; k < CHARTS.length; k++) {
                            if (CHARTS[k].flow) initSingleChart($wire, CHARTS[k]);
                        }
                    }, 150);
                });
            } catch(e) {}
        });
    }

    // Poll for Livewire component and $wire availability
    function bootstrap() {
        var el = document.querySelector('div.fade-in[wire\\:id]');
        if (!el) { setTimeout(bootstrap, 100); return; }
        var componentId = el.getAttribute('wire:id');
        if (!componentId) { setTimeout(bootstrap, 100); return; }
        if (typeof window.Livewire === 'undefined' || typeof window.Livewire.find !== 'function') {
            setTimeout(bootstrap, 100);
            return;
        }
        // Livewire.find(id) returns $wire directly (see livewire.esm.js:9229)
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
