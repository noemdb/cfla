<div class="fade-in" x-data="{
    modeObservation: @entangle('modeObservation'),
    modeComments: @entangle('modeComments'),
    modePreview: @entangle('modePreview'),
    commentStatus: @entangle('status')
}">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Plan de Actividades</h1>
            <p class="text-emerald-600 dark:text-emerald-400 font-medium">Revisión y control de calidad pedagógica de los planes de evaluación.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('app.planning.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-100 dark:bg-cyan-500/10 hover:bg-cyan-200 dark:hover:bg-cyan-500/20 text-cyan-700 dark:text-cyan-400 rounded-lg border border-cyan-200 dark:border-cyan-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Planificación
            </a>
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 dark:bg-white/5 hover:bg-gray-200 dark:hover:bg-white/10 text-gray-700 dark:text-gray-300 rounded-lg border border-gray-200 dark:border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Actualizar
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 p-5 rounded-lg mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Plan Estudio</label>
                <select wire:model.live="pestudio_id"
                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_pestudio as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Profesor</label>
                <select wire:model.live="profesor_id"
                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_profesors as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Grado/Año</label>
                <select wire:model.live="grado_id"
                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($list_grado as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Sección</label>
                <select wire:model.live="seccion_id"
                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todas</option>
                    @foreach($list_seccion as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Actividades</label>
                <select wire:model.live="status_activities"
                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todas</option>
                    <option value="SI">Con actividades</option>
                    <option value="NO">Sin actividades</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-gray-500 mb-1.5">Resultados</label>
                <select wire:model.live="paginate"
                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="9999">Todos</option>
                </select>
            </div>

            <div class="flex items-end">
                <button wire:click="$refresh"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-100 dark:bg-emerald-500/10 hover:bg-emerald-200 dark:hover:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 rounded-lg border border-emerald-200 dark:border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Filtrar
                </button>
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

    <!-- ===== TABBED CONTENT (Lapso tabs like profesor home) ===== -->
    <div class="bg-white dark:bg-gray-900/40 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden">

        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 dark:border-white/5">
            <nav class="flex overflow-x-auto">
                @foreach($tabsLapsos as $index => $lapsoItem)
                    @php $isActive = $lapsoItem->id == $lapso_id; @endphp
                    <button wire:click="selectLapso({{ $lapsoItem->id }})"
                        class="flex-1 px-6 py-2 text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap
                               {{ $isActive ? 'text-emerald-600 dark:text-emerald-400 border-emerald-500 bg-emerald-50 dark:bg-emerald-500/5' : 'text-gray-500 dark:text-gray-500 border-transparent hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}"
                    >
                        <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $lapsoItem->name }}
                        <span class="block text-[9px] font-normal text-gray-400 dark:text-gray-500 normal-case">{{ $lapsoItem->code }}</span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="space-y-6 p-6">
            @forelse($pevaluacions as $item)
                <div class="bg-white dark:bg-gray-900/60 backdrop-blur-md border border-gray-200 dark:border-white/5 rounded-lg overflow-hidden transition-all duration-300 hover:border-emerald-300 dark:hover:border-emerald-500/10"
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
                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                    <span>{{ $item->profesor?->lastname ?? '' }} {{ $item->profesor?->name ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-shrink-0">
                            <!-- Activity count -->
                            <div class="flex items-center gap-2">
                                @if($item->activities_count > 0)
                                    <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-bold rounded-lg border border-emerald-200 dark:border-emerald-500/20">
                                        {{ $item->activities_count }} Act.
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 text-xs font-bold rounded-lg border border-red-200 dark:border-red-500/20">
                                        Sin actividades
                                    </span>
                                @endif
                            </div>

                            <!-- Observation button -->
                            <button type="button" wire:click="createObservation({{ $item->id }})" stop
                                class="p-2 bg-gray-100 dark:bg-white/5 hover:bg-blue-100 dark:hover:bg-blue-500/10 rounded-lg border border-gray-200 dark:border-white/5 hover:border-blue-300 dark:hover:border-blue-500/20 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300"
                                title="Observaciones del coordinador">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>

                            <!-- PDF links -->
                            @if($item->activities_count > 0)
                                <a href="{{ route('app.profesors.activities.format', $item->id) }}" target="_blank"
                                    class="p-2 bg-gray-100 dark:bg-white/5 hover:bg-purple-100 dark:hover:bg-purple-500/10 rounded-lg border border-gray-200 dark:border-white/5 hover:border-purple-300 dark:hover:border-purple-500/20 text-gray-500 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-300"
                                    title="Formato completo (9 columnas)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('app.profesors.activities.resume', $item->id) }}" target="_blank"
                                    class="p-2 bg-gray-100 dark:bg-white/5 hover:bg-emerald-100 dark:hover:bg-emerald-500/10 rounded-lg border border-gray-200 dark:border-white/5 hover:border-emerald-300 dark:hover:border-emerald-500/20 text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all duration-300"
                                    title="Resumen ejecutivo (6 columnas)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </a>
                            @endif

                            <!-- Toggle -->
                            <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-white/5 flex items-center justify-center border border-gray-200 dark:border-white/5 transition-transform duration-300"
                                :class="open ? 'rotate-180 bg-emerald-100 dark:bg-emerald-500/10 border-emerald-300 dark:border-emerald-500/20 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-500'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Expanded Activities with Tabs -->
                    <div x-show="open" x-collapse x-cloak>
                        <div class="px-5 pb-5 pt-0 border-t border-gray-200 dark:border-white/5">
                            @if($item->activities_count > 0)
                                {{-- Activity Tab Bar (border-b-2 style like profesor home) --}}
                                <div class="border-b border-gray-200 dark:border-white/5 mt-4">
                                    <nav class="flex overflow-x-auto">
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
                                                class="flex-1 px-4 py-2 text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap"
                                                title="{{ \Carbon\Carbon::parse($act->finicial)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($act->ffinal)->format('d/m/Y') }}{{ $qualityTitle ? ' · ' . $qualityTitle : '' }}">
                                                <span class="flex items-center justify-center gap-1.5">
                                                    <span>Act. {{ $i + 1 }}</span>
                                                    @if($qualityIcon)
                                                        <span class="text-[10px] font-bold leading-none {{ $qualityColor }}">{{ $qualityIcon }}</span>
                                                    @endif
                                                    @if($act->status !== null)
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $act->status ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                                    @endif
                                                </span>
                                            </button>
                                        @endforeach
                                    </nav>
                                </div>

                                {{-- Activity Tab Content --}}
                                @foreach($item->activities as $i => $act)
                                    <div x-show="activeTab === {{ $i }}" x-cloak x-transition:enter.duration.200ms class="mt-4">
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
                                                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-white/5 hover:bg-sky-100 dark:hover:bg-sky-500/10 rounded-lg border border-gray-200 dark:border-white/5 hover:border-sky-300 dark:hover:border-sky-500/20 text-gray-500 dark:text-gray-400 hover:text-sky-600 dark:hover:text-sky-400 text-[10px] font-bold uppercase tracking-wider transition-all duration-300">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                    </svg>
                                                                    Vista Previa
                                                                </button>
                                                                <button type="button" wire:click="setModeComment({{ $act->id }})"
                                                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-white/5 hover:bg-emerald-100 dark:hover:bg-emerald-500/10 rounded-lg border border-gray-200 dark:border-white/5 hover:border-emerald-300 dark:hover:border-emerald-500/20 text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 text-[10px] font-bold uppercase tracking-wider transition-all duration-300">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                    </svg>
                                                                    {{ $act->comments ? 'Editar' : 'Agregar' }}
                                                                </button>
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
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-1">Observaciones del Coordinador</p>
                                    <p class="text-xs text-gray-700 dark:text-gray-200">{{ $item->observations }}</p>
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

    <!-- ===== MODAL: Observaciones del Coordinador ===== -->
    <x-modal-card title="Observaciones del Coordinador" blur wire:model="modeObservation" max-width="lg">
        <div class="space-y-4">
            @if($pevaluacion)
                <div class="bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-gray-200 dark:border-white/5">
                    <div class="text-xs text-gray-500 dark:text-gray-500 mb-1 font-bold uppercase tracking-wider">Plan de Evaluación</div>
                    <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $pevaluacion->pensum?->asignatura?->name ?? '' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $pevaluacion->seccion?->grado?->name ?? '' }} - Sección {{ $pevaluacion->seccion?->name ?? '' }}
                        · {{ $pevaluacion->profesor?->lastname ?? '' }} {{ $pevaluacion->profesor?->name ?? '' }}
                        · {{ $pevaluacion->lapso?->name ?? '' }}
                    </p>
                </div>
            @endif
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">Observaciones</label>
                <textarea wire:model="observations" rows="5"
                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-300 dark:border-white/10 text-gray-700 dark:text-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none resize-none transition-all"
                    placeholder="Escribe las observaciones del coordinador de evaluación..."></textarea>
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="modeObservation = false" />
                <x-button primary label="Guardar Observaciones" wire:click="saveObservation" spinner="saveObservation" />
            </div>
        </x-slot>
    </x-modal-card>

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
    <x-modal-card title="Vista Previa de la Actividad" blur="lg" wire:model="modePreview" width="max-w-[80vw]">
        @if($previewActivity)
            <div class="space-y-5" x-data="{ showTeaching: false }">

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

                    {{-- Teaching structured view --}}
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

                    {{-- Teaching raw view --}}
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
                    {{-- Achievements --}}
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

                    {{-- Observations / ODS --}}
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
            </div>
        @endif
        <x-slot name="footer">
            <div class="flex justify-end">
                <x-button flat label="Cerrar" x-on:click="modePreview = false" />
            </div>
        </x-slot>
    </x-modal-card>

    @script
    <script>
        // Fix for WireUI modal not closing properly
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('close-modal', () => {
                // WireUI handles modal closing via wire:model
            });
        });
    </script>
    @endscript
</div>
