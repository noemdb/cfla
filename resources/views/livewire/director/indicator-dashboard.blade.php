{{-- resources/views/livewire/director/indicator-dashboard.blade.php --}}
<div class="fade-in" x-data="{ helpOpen: false }" x-cloak>

    {{-- Cabecera --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white">Panel de Dirección</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Seguimiento institucional · modo solo lectura</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            @if($lapsoActive)
                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-sky-600 dark:text-sky-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                    Lapso activo: {{ $lapsoActive->name }}
                </span>
            @endif
            <select wire:model.live="selectedLapsoId"
                class="w-full sm:w-auto rounded-lg border border-gray-300 bg-white px-3.5 py-2 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 dark:border-white/10 dark:bg-gray-900 dark:text-gray-100">
                @foreach($lapsos ?? [] as $lapso)
                    <option value="{{ $lapso->id }}">{{ $lapso->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPIs globales (toda la institución) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Peducativos</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalPeducativos }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Pensums</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalPensums }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Lecciones</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalLessons }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Actividades</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalActivities }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Recursos</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalResources }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-white/5 dark:bg-gray-900">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Profesores</p>
            <p class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $totalProfesoresActivos }}</p>
        </div>
    </div>

    {{-- Indicadores por Peducativo --}}
    <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden dark:border-white/5 dark:bg-gray-900">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-white/5">
            <h2 class="text-sm font-bold text-gray-900 dark:text-white">Indicadores por Peducativo</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Carga, actividades y profesores por unidad educativa</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 border-b border-gray-200 dark:border-white/5">
                        <th class="px-5 py-3">Peducativo</th>
                        <th class="px-5 py-3">Pensums</th>
                        <th class="px-5 py-3">Actividades</th>
                        <th class="px-5 py-3">Profesores</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peducativoIndicators as $item)
                        <tr class="border-b border-gray-100 last:border-0 dark:border-white/5">
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $item->peducativo->name }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $item->pensums_count }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $item->activities_count }}</td>
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $item->profesores_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-6 text-center text-sm text-gray-400 dark:text-gray-500">Sin datos para el lapso seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══ Flujo de Registros (global, con rango de fechas) ═══ --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Flujo de Registros</h3>
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-white/5 rounded-lg p-0.5">
                @php $ranges = ['7d' => '7 días', '30d' => '30 días', '3m' => '3 meses', 'all' => 'Todo']; @endphp
                @foreach($ranges as $val => $label)
                    <button @click="$wire.set('registrationRange', '{{ $val }}')"
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

    {{-- ═══ Charts por Día (filtrados por lapso) ═══ --}}
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg p-4 sm:p-5 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">Actividad por Día</h3>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">Corresponde al lapso seleccionado</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" style="grid-auto-flow: dense;">

            {{-- Actividades Registradas por Día (2×1) --}}
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
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ count($chartActivitiesByDay) }} día(s) con actividad</span>
                </div>
                <div wire:ignore>
                    <div id="activities-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                </div>
            </div>

            {{-- Lecciones Registradas por Día (2×1) --}}
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
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ count($chartLessonsByDay['categories'] ?? []) }} día(s) con actividad</span>
                </div>
                <div wire:ignore>
                    <div id="lessons-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                </div>
            </div>

            {{-- Publicaciones Programadas por Día (4×1) --}}
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
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400">{{ count($chartScheduledByDay) }} día(s) con programación</span>
                </div>
                <div wire:ignore>
                    <div id="scheduled-per-day-chart" class="w-full" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- HELP BUTTON: Guía de estados de lecciones (contexto director) --}}
    {{-- ============================================================ --}}
    {{-- Botón flotante --}}
    <button @click="helpOpen = true"
            class="fixed bottom-6 right-6 z-40 w-12 h-12 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/30 hover:text-emerald-300 hover:scale-110 flex items-center justify-center shadow-lg backdrop-blur-sm transition-all duration-300 group"
            title="Guía de indicadores del panel de dirección"
            x-show="!helpOpen">
        <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c1.256 0 2.47.202 3.612.586a9.044 9.044 0 012.907 1.895 8.997 8.997 0 011.896 2.908A8.95 8.95 0 0121 12a8.95 8.95 0 01-.585 3.611 8.997 8.997 0 01-1.896 2.908 9.044 9.044 0 01-2.907 1.895A8.98 8.98 0 0112 21a8.98 8.98 0 01-3.612-.586 9.044 9.044 0 01-2.907-1.895 8.997 8.997 0 01-1.896-2.908A8.95 8.95 0 013 12a8.95 8.95 0 01.585-3.611 8.997 8.997 0 011.896-2.908 9.044 9.044 0 012.907-1.895A8.98 8.98 0 0112 3z"/>
        </svg>
    </button>

    {{-- Backdrop --}}
    <div x-show="helpOpen"
         x-transition:enter="transition-opacity duration-300"
         x-transition:leave="transition-opacity duration-200"
         @click="helpOpen = false"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm"></div>

    {{-- Slideover panel --}}
    <div x-show="helpOpen"
         x-transition:enter="transition-transform duration-300 ease-out"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform duration-200 ease-in"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         @keydown.escape.window="helpOpen = false"
         class="fixed top-0 right-0 z-50 h-full w-full max-w-2xl bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-white/10 shadow-2xl overflow-y-auto">

        {{-- Sticky header --}}
        <div class="sticky top-0 bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm border-b border-gray-200 dark:border-white/10 z-10">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M12 18h.01"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c1.256 0 2.47.202 3.612.586a9.044 9.044 0 012.907 1.895 8.997 8.997 0 011.896 2.908A8.95 8.95 0 0121 12a8.95 8.95 0 01-.585 3.611 8.997 8.997 0 01-1.896 2.908 9.044 9.044 0 01-2.907 1.895A8.98 8.98 0 0112 21a8.98 8.98 0 01-3.612-.586 9.044 9.044 0 01-2.907-1.895 8.997 8.997 0 01-1.896-2.908A8.95 8.95 0 013 12a8.95 8.95 0 01.585-3.611 8.997 8.997 0 011.896-2.908 9.044 9.044 0 012.907-1.895A8.98 8.98 0 0112 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Guía de Indicadores del Panel de Dirección</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Para el director · supervisión de toda la institución (solo lectura)</p>
                    </div>
                </div>
                <button @click="helpOpen = false"
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-6" x-data="{ tab: 'kpis' }">
            {{-- Intro text --}}
            <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                    Este <strong class="text-gray-900 dark:text-white">Panel de Dirección</strong> resume en una sola pantalla el
                    <strong class="text-sky-600 dark:text-sky-400">seguimiento institucional</strong> de <strong class="text-gray-900 dark:text-white">toda la
                    institución</strong> en <strong class="text-emerald-600 dark:text-emerald-500">modo solo lectura</strong>. Agrega los KPIs globales por
                    unidad educativa, el flujo de nuevos registros (actividades, lecciones y diagnósticos) en el tiempo y el detalle
                    diario de actividad por lapso. Usa esta guía para interpretar cada bloque e identificar tendencias, rezagos o
                    cuellos de botella sin modificar ningún dato.
                </p>
            </div>

            {{-- Tabs navigation --}}
            <div class="flex gap-1 bg-gray-100 dark:bg-white/5 rounded-lg p-1 mb-6 overflow-x-auto" role="tablist">
                <button @click="tab = 'kpis'"
                        :class="tab === 'kpis' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-500/30 shadow-sm' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'"
                        class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                    <span class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 12l3-3 3 3 4-4M8 21h4a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m2 4h.01M17 17l4-4m-4 4l4 4"/></svg>
                        KPIs globales
                    </span>
                </button>
                <button @click="tab = 'peducativos'"
                        :class="tab === 'peducativos' ? 'bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 border-sky-200 dark:border-sky-500/30 shadow-sm' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'"
                        class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                    <span class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Por Peducativo
                    </span>
                </button>
                <button @click="tab = 'flujo'"
                        :class="tab === 'flujo' ? 'bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300 border-violet-200 dark:border-violet-500/30 shadow-sm' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'"
                        class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                    <span class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        Flujo de registros
                    </span>
                </button>
                <button @click="tab = 'diario'"
                        :class="tab === 'diario' ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-500/30 shadow-sm' : 'text-gray-500 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border-transparent'"
                        class="flex-1 px-3 py-2.5 text-xs font-bold uppercase tracking-wider rounded-md border transition-all duration-200">
                    <span class="flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Actividad por día
                    </span>
                </button>
            </div>

            {{-- ─── TAB: KPIs GLOBALES ─────────────────────────── --}}
            <div x-show="tab === 'kpis'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/15 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21h4a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m2 4h.01M17 17l4-4m-4 4l4 4"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué representan las 6 tarjetas superiores?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Visión global de la institución</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Son los contadores de toda la institución para el lapso seleccionado en la parte superior:</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Peducativos</strong> — total de unidades educativas (escuelas) de la institución.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Pensums</strong> — total de planes de estudio / mallas académicas registradas.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Lecciones</strong> — total de lecciones LMS creadas en la institución.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Actividades</strong> — total de actividades académicas registradas.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Recursos</strong> — total de recursos didácticos cargados al LMS.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Profesores</strong> — total de docentes activos en el periodo.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Significado para la dirección</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">¿Qué debes observar?</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Los KPIs te dan un pulso rápido de cobertura y actividad institucional. Al compararlos con el lapso seleccionado puedes:</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Detectar desbalances</strong>: si hay muchos docentes y pocas lecciones publicadas, quizá hay contenidos sin avanzar.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Verificar cobertura</strong>: cruza Pensums vs. Lecciones para confirmar que cada plan de estudio tenga contenido digital.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-emerald-600 dark:text-emerald-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Monitorear crecimiento</strong>: los totales deben crecer a lo largo del lapso; variaciones bruscas merecen revisión.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo de lectura</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4">
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-white/10 p-3 space-y-1.5">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-emerald-400"></span><span><strong class="text-gray-800 dark:text-gray-200">3 Peducativos</strong> · <strong class="text-gray-800 dark:text-gray-200">12 Pensums</strong> · <strong class="text-gray-800 dark:text-gray-200">180 Lecciones</strong> → cobertura digital positiva.</span></div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>Si <strong class="text-gray-800 dark:text-gray-200">Profesores</strong> es alto pero <strong class="text-gray-800 dark:text-gray-200">Lecciones</strong> es bajo, indaga si hay docentes sin publicar.</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── TAB: POR PEDUCATIVO ────────────────────────── --}}
            <div x-show="tab === 'peducativos'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-sky-50 dark:bg-sky-500/15 border border-sky-200 dark:border-sky-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué muestra la tabla «Indicadores por Peducativo»?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Desglose por unidad educativa</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Desglosa, para cada unidad educativa, tres métricas del lapso activo: <strong class="text-gray-900 dark:text-white">Pensums</strong>, <strong class="text-gray-900 dark:text-white">Actividades</strong> y <strong class="text-gray-900 dark:text-white">Profesores</strong>.</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20">✓ Permite comparar unidades entre sí</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20">✓ Se filtra por lapso seleccionado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Significado para la dirección</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">¿Qué debes observar?</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">La comparación por unidad educativa te permite:</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Identificar unidades más/menos activas</strong> y priorizar acompañamiento pedagógico.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Detectar disparidades</strong>: una unidad con muchos profesores y pocas actividades puede tener baja producción.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-sky-600 dark:text-sky-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Detectar unidades sin datos</strong>: la fila «Sin datos para el lapso seleccionado» indica unidad sin actividad registrada.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo de lectura</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4">
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-white/10 p-3 space-y-1.5">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-sky-400"></span><span>Comparas la columna <strong class="text-gray-800 dark:text-gray-200">Profesores</strong> de dos unidades: una concentra el personal — candidata a revisar su plan de trabajo.</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── TAB: FLUJO DE REGISTROS ────────────────────── --}}
            <div x-show="tab === 'flujo'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué muestran los 3 gráficos de «Flujo de Registros»?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Series de tiempo globales</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Tres líneas/áreas que acumulan los registros nuevos por día: <strong class="text-violet-600 dark:text-violet-400">Actividades</strong> (morado), <strong class="text-sky-600 dark:text-sky-400">Lecciones</strong> (azul) y <strong class="text-emerald-600 dark:text-emerald-400">Diagnósticos</strong> (verde). El selector superior (7 días / 30 días / 3 meses / Todo) define la ventana que se grafica.</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-violet-50 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-500/20">✓ Rango configurable por el director</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-violet-50 dark:bg-violet-500/10 text-violet-700 dark:text-violet-400 border border-violet-200 dark:border-violet-500/20">✓ No depende del lapso (es temporal)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Significado para la dirección</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">¿Qué debes observar?</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Este bloque es clave para ver el <strong class="text-gray-900 dark:text-white">ritmo de trabajo</strong> de la institución:</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Picos inesperados</strong>: un día con mucha actividad acumulada puede indicar actualizaciones masivas o el cierre de lapso.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Mesetas planas</strong>: períodos sin registros nuevos sugieren baja producción y merecen seguimiento.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-violet-600 dark:text-violet-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Comparar líneas</strong>: si lecciones superan ampliamente a diagnósticos, quizá faltan evaluaciones de diagnóstico.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo de lectura</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4">
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-white/10 p-3 space-y-1.5">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-violet-400"></span><span>Seleccionas <strong class="text-gray-800 dark:text-gray-200">30 días</strong> y ves una meseta plana de <strong class="text-gray-800 dark:text-gray-200">Diagnósticos</strong> → señala a la coordinación que no se están aplicando evaluaciones.</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── TAB: ACTIVIDAD POR DÍA ─────────────────────── --}}
            <div x-show="tab === 'diario'" x-transition:enter="transition-opacity duration-200">
                <div class="space-y-3">
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: true }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-amber-50 dark:bg-amber-500/15 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">¿Qué muestran los gráficos de «Actividad por Día»?</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Detalle diario por lapso</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">Tres gráficos, <strong class="text-gray-900 dark:text-white">filtrados por el lapso seleccionado</strong> en la cabecera: <strong class="text-emerald-600 dark:text-emerald-400">Actividades Registradas por Día</strong>, <strong class="text-sky-600 dark:text-sky-400">Lecciones Registradas por Día</strong> y <strong class="text-violet-600 dark:text-violet-400">Publicaciones Programadas por Día</strong>. Cada barra muestra cuántos registros se produjeron en cada fecha del lapso.</p>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">✓ Respeta el lapso activo</span>
                                    <span class="px-2 py-1 rounded text-[10px] font-medium bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">✓ «Programadas» anticipa contenido futuro</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-500/15 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Significado para la dirección</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">¿Qué debes observar?</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4 space-y-3">
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">En la vista diaria puedes:</p>
                                <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                    <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Ver la constancia</strong>: días consecutivos con registros indican actividad sostenida; vacíos totales, semanas sin trabajo.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Planificación futura</strong>: «Publicaciones Programadas» muestra el contenido ya agendado, clave para anticipar el calendario.</span></li>
                                    <li class="flex items-start gap-2"><span class="text-amber-600 dark:text-amber-400 mt-0.5">▸</span><span><strong class="text-gray-800 dark:text-gray-200">Cruzar con el lapso</strong>: al cambiar el lapso, todos los gráficos del bloque se recalculan para ese periodo.</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 text-left hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-full bg-violet-50 dark:bg-violet-500/15 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Ejemplo de lectura</h3>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">Caso práctico</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-500 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition-all duration-200">
                            <div class="px-4 pb-4">
                                <div class="bg-white dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-white/10 p-3 space-y-1.5">
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400"><span class="w-2 h-2 rounded-full bg-amber-400"></span><span>En <strong class="text-gray-800 dark:text-gray-200">Publicaciones Programadas</strong> ves alta actividad de programación para la próxima semana → la institución tiene contenido planificado.</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── FOOTER: nota read-only ─────────────────────── --}}
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-white/10">
                <div class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-white/10 rounded-lg p-4">
                    <div class="flex items-start gap-2 mb-3">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">Modo solo lectura</h3>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed mt-1">
                                Este panel de la Dirección es de <strong class="text-emerald-600 dark:text-emerald-500">solo lectura</strong>:
                                observas, supervisas y auditas los indicadores de toda la institución, pero <strong class="text-gray-800 dark:text-gray-200">no modificas</strong> ni registros ni datos.
                                Los contenidos (lecciones, actividades, diagnósticos) los generan los docentes y Planificación desde sus módulos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
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
            series: [{ name: 'Actividades', data: rawData }],
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: window.flowChartColors.chartBackground,
            },
            colors: ['#a855f7'],
            stroke: { curve: 'smooth', width: 2 },
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
                gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] },
            },
            xaxis: {
                type: 'category',
                labels: { style: window.flowChartColors.labelStyle },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: window.flowChartColors.labelStyle },
                tickAmount: 4,
                forceNiceScale: true,
            },
            grid: { borderColor: window.flowChartColors.gridColor, strokeDashArray: 4 },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                y: { formatter: function(val) { return val + ' actividad(es)'; } },
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
            series: [{ name: 'Lecciones', data: rawData }],
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: window.flowChartColors.chartBackground,
            },
            colors: ['#0ea5e9'],
            stroke: { curve: 'smooth', width: 2 },
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
                gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] },
            },
            xaxis: {
                type: 'category',
                labels: { style: window.flowChartColors.labelStyle },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: window.flowChartColors.labelStyle },
                tickAmount: 4,
                forceNiceScale: true,
            },
            grid: { borderColor: window.flowChartColors.gridColor, strokeDashArray: 4 },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                y: { formatter: function(val) { return val + ' lección(es)'; } },
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
            series: [{ name: 'Diagnósticos', data: rawData }],
            chart: {
                type: 'area',
                height: 200,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: window.flowChartColors.chartBackground,
            },
            colors: ['#10b981'],
            stroke: { curve: 'smooth', width: 2 },
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
                gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] },
            },
            xaxis: {
                type: 'category',
                labels: { style: window.flowChartColors.labelStyle },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: window.flowChartColors.labelStyle },
                tickAmount: 4,
                forceNiceScale: true,
            },
            grid: { borderColor: window.flowChartColors.gridColor, strokeDashArray: 4 },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                y: { formatter: function(val) { return val + ' diagnóstico(s)'; } },
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

@script
<script>
    let activitiesChart = null;

    async function initActivitiesChart() {
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const el = document.getElementById('activities-per-day-chart');
        if (!el) return;

        if (activitiesChart) activitiesChart.destroy();

        const rawData = await $wire.get('chartActivitiesByDay') ?? [];

        activitiesChart = new window.ApexCharts(el, {
            series: [{ name: 'Actividades', data: rawData }],
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Inter, system-ui, sans-serif',
                background: window.flowChartColors.chartBackground,
            },
            colors: ['#10b981'],
            stroke: { curve: 'smooth', width: 2 },
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
                gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] },
            },
            xaxis: {
                type: 'category',
                labels: { style: window.flowChartColors.labelStyle },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: window.flowChartColors.labelStyle },
                tickAmount: 5,
                forceNiceScale: true,
            },
            grid: { borderColor: window.flowChartColors.gridColor, strokeDashArray: 4 },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                y: { formatter: function(val) { return val + ' actividad(es)'; } },
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

    initActivitiesChart();
    $wire.$watch('chartActivitiesByDay', () => initActivitiesChart());
    $wire.$watch('selectedLapsoId', () => { setTimeout(() => initActivitiesChart(), 100); });
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
                background: window.flowChartColors.chartBackground,
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
            stroke: { curve: 'smooth', width: 2 },
            markers: { size: 4, hover: { size: 6 } },
            xaxis: {
                categories: rawData.categories,
                type: 'category',
                labels: { style: window.flowChartColors.labelStyle },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: window.flowChartColors.labelStyle },
                tickAmount: 5,
                forceNiceScale: true,
                min: 0,
            },
            grid: { borderColor: window.flowChartColors.gridColor, strokeDashArray: 4 },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
                shared: true,
                intersect: false,
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                labels: { colors: window.flowChartColors.labelStyle.colors },
                fontSize: '11px',
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
    $wire.$watch('selectedLapsoId', () => { setTimeout(() => initLessonsChart(), 100); });
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
                background: window.flowChartColors.chartBackground,
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
                gradient: { shadeIntensity: 1, inverseColors: false, opacityFrom: 0.5, opacityTo: 0, stops: [0, 90, 100] },
            },
            xaxis: {
                type: 'category',
                labels: { style: window.flowChartColors.labelStyle },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                labels: { style: window.flowChartColors.labelStyle },
                tickAmount: 5,
                forceNiceScale: true,
            },
            grid: { borderColor: window.flowChartColors.gridColor, strokeDashArray: 4 },
            tooltip: {
                theme: window.flowChartColors.tooltip.theme,
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
    $wire.$watch('selectedLapsoId', () => { setTimeout(() => initScheduledChart(), 100); });
</script>
@endscript
