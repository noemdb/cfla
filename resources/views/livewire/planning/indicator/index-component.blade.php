<div class="fade-in">
    {{-- Loading overlay --}}
    <div wire:loading
         class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm transition-all duration-300">
        <div class="bg-gray-900/90 border border-white/10 rounded-2xl px-10 py-8 shadow-2xl shadow-cyan-500/5 flex flex-col items-center gap-4">
            {{-- Animated spinner --}}
            <div class="relative w-14 h-14">
                <div class="absolute inset-0 rounded-full border-4 border-white/5"></div>
                <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-cyan-400 animate-spin"></div>
                <div class="absolute inset-2 rounded-full border-4 border-transparent border-t-emerald-400 animate-spin" style="animation-duration: 0.8s; animation-direction: reverse;"></div>
            </div>
            <span class="text-sm font-bold text-gray-300 tracking-widest uppercase">Cargando</span>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Indicadores de Planificación</h1>
            <p class="text-cyan-400 font-medium">Dashboard institucional con KPIs por programa educativo y período académico.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('app.planning.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-xl border border-cyan-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Planificación
            </a>
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl border border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refrescar
            </button>
        </div>
    </div>

    <!-- Global KPI Boxes -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <x-indicator-box
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
            label="Actividades" value="{{ number_format($totalActivities) }}" color="purple" />
        <x-indicator-box
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            label="Diagnósticos" value="{{ number_format($totalDiagActive) }}" color="emerald" />
        <x-indicator-box
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
            label="Profesores Activos" value="{{ number_format($totalProfesoresActivos) }}" color="amber" />
    </div>

    <!-- Filters: Lapso + Peducativo + Pestudio + Profesor -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-2 rounded-xl mb-4">
        {{-- Lapso selector --}}
        <div class="flex flex-wrap gap-1 mb-2 pb-2 border-b border-white/5">
            <span class="text-[10px] font-bold uppercase tracking-widest text-cyan-400 w-full mb-1">Período:</span>
            @foreach($lapsos as $lapso)
                <button wire:click="$set('selectedLapsoId', {{ $lapso->id }})"
                    class="flex-1 min-w-0 px-2 py-1.5 rounded-lg text-xs font-bold text-center transition-all duration-200 {{ $selectedLapsoId == $lapso->id ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30' : 'bg-white/5 text-gray-400 border border-white/5 hover:bg-white/10' }}">
                    <span>{{ $lapso->name }}</span>
                    <span class="text-[9px] text-gray-500 ml-1">{{ $lapso->finicial?->format('d/m') }} – {{ $lapso->ffinal?->format('d/m') }}</span>
                </button>
            @endforeach
        </div>

        {{-- 3 filter dropdowns --}}
        <div class="flex flex-wrap items-center gap-2">
            {{-- Peducativo filter --}}
            <div class="flex items-center gap-1.5">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">P.Educativo:</span>
                <select wire:model.live="selectedPeducativoId"
                    class="bg-gray-800 text-gray-200 text-xs rounded-lg border border-white/5 px-2 py-1.5 focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 outline-none">
                    <option value="">Todos</option>
                    @foreach($peducativos as $ped)
                        <option value="{{ $ped->id }}">{{ $ped->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Pestudio filter --}}
            <div class="flex items-center gap-1.5">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">P.Estudio:</span>
                <select wire:model.live="selectedPestudioId"
                    class="bg-gray-800 text-gray-200 text-xs rounded-lg border border-white/5 px-2 py-1.5 focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 outline-none">
                    <option value="">Todos</option>
                    @foreach($pestudios as $pest)
                        <option value="{{ $pest->id }}">{{ $pest->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Grado filter --}}
            <div class="flex items-center gap-1.5">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Grado:</span>
                <select wire:model.live="selectedGradoId"
                    class="bg-gray-800 text-gray-200 text-xs rounded-lg border border-white/5 px-2 py-1.5 focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 outline-none">
                    <option value="">Todos</option>
                    @foreach($gradosOptions as $grd)
                        <option value="{{ $grd->id }}">{{ $grd->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Profesor filter --}}
            <div class="flex items-center gap-1.5">
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Profesor:</span>
                <select wire:model.live="selectedProfesorId"
                    class="bg-gray-800 text-gray-200 text-xs rounded-lg border border-white/5 px-2 py-1.5 focus:border-cyan-500/30 focus:ring-1 focus:ring-cyan-500/20 outline-none">
                    <option value="">Todos</option>
                    @foreach($profesoresOptions as $prof)
                        <option value="{{ $prof->id }}">{{ $prof->lastname }}, {{ $prof->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Main Tabs -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl overflow-hidden" x-data="{ activeTab: {{ $activeTab }} }">
        <div class="border-b border-white/5">
            <nav class="flex overflow-x-auto">
                <button @click="activeTab = 1" :class="activeTab === 1 ? 'text-cyan-400 border-cyan-500 bg-cyan-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                    class="flex-1 px-6 py-4 text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Indicadores Principales
                </button>
                <button @click="activeTab = 2" :class="activeTab === 2 ? 'text-cyan-400 border-cyan-500 bg-cyan-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                    class="flex-1 px-6 py-4 text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path></svg>
                    Profesores
                </button>
                <button @click="activeTab = 3" :class="activeTab === 3 ? 'text-cyan-400 border-cyan-500 bg-cyan-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                    class="flex-1 px-6 py-4 text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    Actividades
                </button>
                <button @click="activeTab = 4" :class="activeTab === 4 ? 'text-emerald-400 border-emerald-500 bg-emerald-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                    class="flex-1 px-6 py-4 text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Diagnóstico
                </button>
            </nav>
        </div>

        <div class="p-6">

            {{-- ═══════════════════════════════════════════════════════════════════
                 TAB 1: Indicadores Principales — grouped by Peducativo
                 ═══════════════════════════════════════════════════════════════════ --}}
            <div x-show="activeTab === 1" x-cloak>
                <div class="space-y-8">
                    @forelse($peducativoMainIndicators as $item)
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center">
                                    <span class="text-cyan-400 text-xs font-bold">{{ $item->peducativo?->code ?? '' }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-white">{{ $item->peducativo?->name ?? '' }}</h3>
                                <span class="text-xs text-gray-500">[{{ $item->peducativo?->code ?? '' }}]</span>
                                <span class="ml-auto text-[10px] text-gray-500">{{ $item->pestudios->count() }} plan(es)</span>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <x-indicator-box
                                    icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
                                    label="Actividades Registradas"
                                    value="{{ number_format($item->activities_count) }}"
                                    color="purple"
                                />
                                <x-indicator-box
                                    icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
                                    label="Profesores con Carga"
                                    value="{{ $item->profesores_count }}"
                                    color="amber"
                                />
                            </div>
                        </div>
                        @if(!$loop->last)<hr class="border-white/5 my-6">@endif
                    @empty
                        <div class="text-center py-16">
                            <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-gray-500 font-medium">No hay programas educativos activos</p>
                            <p class="text-gray-600 text-sm mt-1">Activa un programa educativo con planificación para ver indicadores.</p>
                        </div>
                    @endforelse
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
                    <div class="border-b border-white/5 mb-4">
                        <nav class="flex overflow-x-auto -mb-px">
                            @foreach($peducativos as $peducativo)
                                @php $ieePROM = $tab2Data[$lapsoId][$peducativo->id]['ieePROM'] ?? 0; @endphp
                                <button @click="activePeducativo = {{ $peducativo->id }}"
                                    :class="activePeducativo === {{ $peducativo->id }} ? 'text-violet-400 border-violet-500 bg-violet-500/5' : 'text-gray-500 border-transparent hover:text-gray-400'"
                                    class="flex-1 px-4 py-2 text-xs font-bold transition-all duration-200 border-b-2 whitespace-nowrap">
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
                                                <th class="text-left px-3 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Profesor</th>
                                                <th class="text-center px-3 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">N. Actividades</th>
                                                <th class="text-center px-3 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500" title="Porcentaje de notas cargadas">IEE</th>
                                                <th class="text-center px-3 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500" title="Porcentaje de notas cargadas para el corte de notas">IEE-CN</th>
                                                <th class="text-center px-3 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500" title="Índice Relativo de Rendimiento en Evaluación">IRE</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/5">
                                            @foreach($profesors as $teacher)
                                                <tr class="hover:bg-white/[0.02] transition-colors">
                                                    <td class="px-3 py-3">
                                                        <span class="text-sm text-white font-medium">{{ $teacher->full_name }}</span>
                                                        <span class="block text-[10px] text-gray-500">{{ $teacher->ci_profesor }}</span>
                                                    </td>
                                                    <td class="px-3 py-3 text-center">
                                                        <span class="text-xs font-mono text-gray-300 px-2 py-1 bg-white/5 rounded-lg">
                                                            {{ $teacher->activities_count }}
                                                            @if($teacher->activities_count > 0)
                                                                <small class="text-gray-500">[{{ $teacher->approval_rate }}%]</small>
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-3 text-center">
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
                                                    <td class="px-3 py-3 text-center">
                                                        <span class="text-xs font-mono {{ ($teacher->iee_cn ?? 0) >= 70 ? 'text-emerald-400' : (($teacher->iee_cn ?? 0) >= 40 ? 'text-amber-400' : 'text-red-400') }}">
                                                            {{ $teacher->iee_cn }}%
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-3 text-center">
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
                                <div class="bg-white/5 rounded-xl p-6 text-center">
                                    <p class="text-gray-500 text-sm">No hay profesores con carga académica en este programa educativo.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="bg-white/5 rounded-xl p-6 text-center">
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
                    <div class="border-b border-white/5 mb-4">
                        <nav class="flex overflow-x-auto -mb-px">
                            @foreach($peducativos as $peducativo)
                                @php
                                    $tab3Item = $tab3Data[$lapsoId][$peducativo->id] ?? null;
                                    $ieePROM = $tab3Item ? ($tab2Data[$lapsoId][$peducativo->id]['ieePROM'] ?? 0) : 0;
                                @endphp
                                <button @click="activePeducativo = {{ $peducativo->id }}"
                                    :class="activePeducativo === {{ $peducativo->id }} ? 'text-amber-400 border-amber-500 bg-amber-500/5' : 'text-gray-500 border-transparent hover:text-gray-400'"
                                    class="flex-1 px-4 py-2 text-xs font-bold transition-all duration-200 border-b-2 whitespace-nowrap">
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
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    <x-indicator-box
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
                                        label="Total de actividades planificadas"
                                        value="{{ number_format($indicators->total_activities) }}"
                                        color="cyan"
                                    />
                                    <x-indicator-box
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>'
                                        label="Indicador de Cobertura Curricular"
                                        subtext="Promedio de actividades por Área de Formación"
                                        value="{{ $indicators->cobertura_curricular }}"
                                        color="emerald"
                                    />
                                    <x-indicator-box
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"></path></svg>'
                                        label="Indicador de Participación"
                                        subtext="% Docentes con Planificaciones Activas"
                                        value="{{ $indicators->participacion }}%"
                                        color="blue"
                                    />
                                    <x-indicator-box
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>'
                                        label="Indicador de Seguimiento"
                                        subtext="Tasa de Comentarios en Actividades"
                                        value="{{ $indicators->seguimiento }}%"
                                        color="purple"
                                    />
                                    <x-indicator-box
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                                        label="Indicador de Aprobación"
                                        subtext="% de Actividades Aprobadas"
                                        value="{{ $indicators->aprobacion }}%"
                                        color="emerald"
                                    />
                                    <x-indicator-box
                                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'
                                        label="Indicador de Supervisión"
                                        subtext="Tasa de Observaciones en Áreas de Formación"
                                        value="{{ $indicators->supervision }}%"
                                        color="rose"
                                    />
                                </div>
                            @else
                                <div class="bg-white/5 rounded-xl p-6 text-center">
                                    <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    <p class="text-gray-500 text-sm mb-1">Sin actividades registradas</p>
                                    <p class="text-gray-600 text-xs">No hay actividades planificadas para este período en {{ $peducativo->name }}.</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="bg-white/5 rounded-xl p-6 text-center">
                        <p class="text-gray-500 text-sm">No hay datos de actividades para el período seleccionado.</p>
                    </div>
                @endif
            </div>

            {{-- ═══════════════════════════════════════════════════════════════════
                 TAB 4: Diagnóstico — aggregate indicators
                 ═══════════════════════════════════════════════════════════════════ --}}
            <div x-show="activeTab === 4" x-cloak>
                <div class="space-y-6">
                    {{-- Diagnostic KPI row --}}
                    @php
                        $diagTotalSessions = $tab4DiagData->sum('total_sessions');
                        $diagCompletedSessions = $tab4DiagData->sum('completed_sessions');
                        $diagAvgPrecision = $diagTotalSessions > 0
                            ? round($tab4DiagData->avg('avg_precision') ?? 0, 1) : 0;
                        $diagCompletionRate = $diagTotalSessions > 0
                            ? round(($diagCompletedSessions / $diagTotalSessions) * 100, 1) : 0;
                        $diagStudents = $tab4DiagData->sum('students_evaluated');
                    @endphp

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <x-indicator-box
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>'
                            label="Total Sesiones" value="{{ number_format($diagTotalSessions) }}" color="cyan" />
                        <x-indicator-box
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                            label="Completadas" value="{{ number_format($diagCompletedSessions) }}" color="emerald" />
                        <x-indicator-box
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path></svg>'
                            label="Precisión Prom." value="{{ $diagAvgPrecision }}%" color="amber" />
                        <x-indicator-box
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>'
                            label="Estudiantes Eval." value="{{ number_format($diagStudents) }}" color="purple" />
                    </div>

                    {{-- Question-level indicators --}}
                    <div class="bg-gray-800/30 border border-white/5 rounded-xl p-5">
                        <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Resumen de Preguntas</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-800/50 border border-white/5 rounded-lg p-4 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Preguntas Cargadas</p>
                                    <p class="text-lg font-bold text-white">{{ number_format($diagTotalQuestions) }}</p>
                                </div>
                            </div>
                            <div class="bg-gray-800/50 border border-white/5 rounded-lg p-4 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Con Opciones de Respuesta</p>
                                    <p class="text-lg font-bold text-white">{{ number_format($diagQuestionsWithOptions) }}</p>
                                </div>
                            </div>
                            <div class="bg-gray-800/50 border border-white/5 rounded-lg p-4 flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-violet-500/10 border border-violet-500/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Cobertura Pensum</p>
                                    <p class="text-lg font-bold text-white">{{ $diagPensumCoveragePct }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Completion progress bar --}}
                    <div class="bg-gray-800/30 border border-white/5 rounded-xl p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Tasa de Finalización</h4>
                            <span class="text-xs text-gray-400">{{ $diagCompletionRate }}%</span>
                        </div>
                        <div class="w-full bg-gray-700/50 rounded-full h-2.5">
                            <div class="bg-emerald-500 h-2.5 rounded-full transition-all duration-500"
                                 style="width: {{ $diagCompletionRate }}%"></div>
                        </div>
                    </div>

                    {{-- Per-diagnostic breakdown table --}}
                    <div class="bg-gray-800/30 border border-white/5 rounded-xl overflow-hidden">
                        <div class="px-5 py-4 border-b border-white/5">
                            <h4 class="text-xs font-bold text-white uppercase tracking-wider">Desempeño por Diagnóstico</h4>
                        </div>
                        @if($tab4DiagData->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="border-b border-white/5">
                                            <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500">Diagnóstico</th>
                                            <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Sesiones</th>
                                            <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Completadas</th>
                                            <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Estudiantes</th>
                                            <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-center">Precisión</th>
                                            <th class="py-3 px-4 text-[10px] font-bold uppercase tracking-widest text-gray-500 text-right">Progreso</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach($tab4DiagData as $item)
                                            @php $pct = $item->total_sessions > 0 ? round(($item->completed_sessions / $item->total_sessions) * 100) : 0; @endphp
                                            <tr class="hover:bg-white/[0.02] transition-colors">
                                                <td class="py-3 px-4">
                                                    <span class="text-xs font-medium text-white">{{ $item->diag_main->name }}</span>
                                                </td>
                                                <td class="py-3 px-4 text-center text-xs text-gray-400">{{ number_format($item->total_sessions) }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-medium text-emerald-400">{{ number_format($item->completed_sessions) }}</span>
                                                </td>
                                                <td class="py-3 px-4 text-center text-xs text-gray-400">{{ number_format($item->students_evaluated) }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <span class="text-xs font-medium {{ ($item->avg_precision ?? 0) >= 70 ? 'text-emerald-400' : (($item->avg_precision ?? 0) >= 40 ? 'text-amber-400' : 'text-red-400') }}">
                                                        {{ $item->avg_precision }}%
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <div class="w-20 bg-gray-700/50 rounded-full h-1.5">
                                                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                                        </div>
                                                        <span class="text-[10px] text-gray-500 w-8 text-right">{{ $pct }}%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-8 text-center">
                                <svg class="w-12 h-12 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <p class="text-gray-500 text-sm mb-1">Sin diagnósticos activos</p>
                                <p class="text-gray-600 text-xs">No hay diagnósticos activos para el período seleccionado.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
