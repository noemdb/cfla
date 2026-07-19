@extends('profesors.layouts.app')

@section('title', 'Dashboard - ' . config('app.name', 'SAEFL'))

@section('navbar-info')
<div class="hidden lg:flex items-center gap-3 ml-6 px-2 py-1 bg-gray-900/30 backdrop-blur-md rounded-lg">
    {{-- Profesor --}}
    <div class="flex items-center gap-1.5 text-xs text-gray-400">
        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span class="text-white font-medium">{{ $profesor->name ?? '—' }}</span>
    </div>

    <span class="w-px h-4 bg-white/5"></span>

    {{-- Período --}}
    <div class="flex items-center gap-1.5 text-xs text-gray-400">
        <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <span>{{ $lapso_active->pescolar->name ?? $lapso_active->academic_year ?? '2026-2027' }}</span>
    </div>

    <span class="w-px h-4 bg-white/5"></span>

    {{-- Rol --}}
    <div class="flex items-center gap-1.5 text-xs">
        @if($esProfesorGuia)
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Profesor Guía
            </span>
        @else
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-500/10 text-gray-400 border border-white/5">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profesor
            </span>
        @endif
    </div>

    <span class="w-px h-4 bg-white/5"></span>

    {{-- Lapso activo --}}
    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
        {{ $lapso_active->name ?? '—' }}
    </span>
</div>
@endsection

@section('content')
<div class="fade-in">

    {{-- ═══════════════════════════════════════════════════════════════════
         HEADER
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">
                Bienvenido, {{ $profesor->full_name ?? Auth::user()->username }}
            </h1>
            <p class="text-emerald-400 font-medium">Panel de rendimiento académico</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('app.profesors.users.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-emerald-300 rounded-lg border border-white/5 transition-all duration-300 text-xs font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Mi Perfil
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @includeIf('profesors.elements.messeges.oper_ok')

    {{-- ═══════════════════════════════════════════════════════════════════
         MAIN DASHBOARD — Lapso tabs with KPI boxes
         ═══════════════════════════════════════════════════════════════════ --}}
    @if(isset($indicadores) && $indicadores->isNotEmpty())
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden"
         x-data="{ activeTab: {{ $lapsos->first()->id === ($lapso_active->id ?? $lapsos->first()->id) ? 1 : 1 }} }">

        {{-- Tab Navigation (Alpine.js, border-b-2 pattern like Planning) --}}
        <div class="border-b border-white/5">
            <nav class="flex overflow-x-auto">
                @foreach($lapsos as $index => $lapsoItem)
                    @php $tabNum = $loop->iteration; @endphp
                    <button
                        @click="activeTab = {{ $tabNum }}"
                        :class="activeTab === {{ $tabNum }}
                            ? 'text-emerald-400 border-emerald-500 bg-emerald-500/5'
                            : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600'"
                        class="flex-1 px-6 py-2 text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap"
                    >
                        <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $lapsoItem->name }}
                        <span class="block text-[9px] font-normal text-gray-500 normal-case">{{ $lapsoItem->code }}</span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Tab Content Panels --}}
        <div class="p-6">
            @foreach($lapsos as $lapsoItem)
                @php
                    $tabNum = $loop->iteration;
                    $ind = $indicadores->firstWhere('id', $lapsoItem->id);
                @endphp
                <div x-show="activeTab === {{ $tabNum }}" x-cloak>
                    @php
                        $diagTotal     = $ind['diag_total'] ?? 0;
                        $diagCompleted = $ind['diag_completed'] ?? 0;
                        $diagProgress  = $ind['diag_progress'] ?? 0;
                        $diagHasData   = $diagTotal > 0;
                    @endphp

                    {{-- ════════════════════════════════════════════════════
                         BENTO GRID — single unified dashboard grid
                         ════════════════════════════════════════════════════ --}}
                    <div class="grid grid-cols-4 gap-2" style="grid-auto-flow: dense;">

                        {{-- ═══ 1. Enseñanza de Calidad — anchor tile 2×2 ═══ --}}
                        <div class="col-span-2 row-span-2">
                            <x-indicator-box
                                icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'
                                label="Enseñanza de Calidad"
                                value="{{ $ind['act_calidad_ens'] ?? 0 }}"
                                color="indigo"
                                subtext="Actividades con ≥10 palabras significativas"
                            />
                        </div>

                        {{-- ═══ Planificación: 4 tiles 1×1 ═══ --}}
                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                                label="Planes de Evaluación"
                                value="{{ $ind['count_pevaluacions'] ?? 0 }}"
                                color="emerald"
                                subtext="Asignados en este lapso"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'
                                label="Actividades Registradas"
                                value="{{ $ind['act_total'] ?? 0 }}"
                                color="blue"
                                subtext="En todos los planes"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                label="Con Evaluación"
                                value="{{ $ind['act_con_eval'] ?? 0 }}"
                                color="teal"
                                subtext="Tienen actividad evaluativa"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                label="Aprobadas"
                                value="{{ $ind['act_aprobadas'] ?? 0 }}"
                                color="amber"
                                subtext="Estatus = aprobado"
                            />
                        </div>

                        {{-- ═══ Diagnósticos: 4 tiles 1×1 ═══ --}}
                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'
                                label="Sesiones Totales"
                                value="{{ $diagTotal }}"
                                color="blue"
                                subtext="Evaluaciones diagnósticas"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                label="Completados"
                                value="{{ $diagCompleted }}"
                                color="emerald"
                                subtext="{{ $diagHasData ? $diagProgress . '% de avance' : 'Sin datos' }}"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                label="En Progreso"
                                value="{{ $ind['diag_en_progreso'] ?? 0 }}"
                                color="amber"
                                subtext="Sesiones activas pendientes"
                            />
                        </div>

                        <div class="col-span-1">
                            {{-- Custom progress card with bar --}}
                            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-5 rounded-lg transition-all duration-300 hover:border-indigo-500/30 h-full flex flex-col">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="w-10 h-10 bg-indigo-500/10 rounded-lg flex items-center justify-center text-indigo-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-lg font-bold text-white mb-1">{{ $diagProgress }}%</p>
                                <p class="text-[11px] font-medium text-indigo-400 uppercase tracking-wider">Progreso</p>
                                <div class="mt-auto pt-3">
                                    <div class="progress-bar-sm">
                                        <div class="progress-bar-sm-fill {{ $diagProgress >= 80 ? 'bg-emerald-400' : ($diagProgress >= 50 ? 'bg-amber-400' : 'bg-indigo-400') }}"
                                             style="width:{{ min($diagProgress,100) }}%"></div>
                                    </div>
                                    <p class="text-[10px] text-gray-500 mt-1">{{ $diagCompleted }}/{{ $diagTotal }} sesiones</p>
                                </div>
                            </div>
                        </div>

                        {{-- ═══ LMS / Lecciones: 1×1 + 2×1 + 1×1 ═══ --}}
                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                label="Lecciones Publicadas"
                                value="{{ $ind['lms_published'] ?? 0 }}"
                                color="violet"
                                subtext="Visibles para estudiantes"
                            />
                        </div>

                        <div class="col-span-2">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>'
                                label="Secciones de Contenido"
                                value="{{ $ind['lms_sections'] ?? 0 }}"
                                color="teal"
                                subtext="Estructuras de aprendizaje"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>'
                                label="Recursos LMS"
                                value="{{ $ind['lms_resources'] ?? 0 }}"
                                color="pink"
                                subtext="Materiales didácticos"
                            />
                        </div>

                    </div>

                    {{-- ════════════════════════════════════════════════════
                         CHARTS — Actividades / Lecciones / Programadas
                         Inicializados via IntersectionObserver
                         ════════════════════════════════════════════════════ --}}
                    <div class="mt-8 space-y-4 profesor-charts" data-lapso="{{ $lapsoItem->id }}">
                        {{-- ── Activities per day ── --}}
                        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-white">Actividades Registradas por Día</h3>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                    {{ count($ind['chart_activities'] ?? []) }} día(s) con actividad
                                </span>
                            </div>
                            <div id="chart-activities-{{ $lapsoItem->id }}" class="w-full" style="min-height: 250px;"
                                 data-series-name="Actividades"
                                 data-series-color="#10b981"
                                 data-chart-data='@json($ind['chart_activities'] ?? [])'></div>
                        </div>

                        {{-- ── Lessons per day ── --}}
                        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-sky-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-white">Lecciones Registradas por Día</h3>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                    {{ count($ind['chart_lessons'] ?? []) }} día(s) con publicación
                                </span>
                            </div>
                            <div id="chart-lessons-{{ $lapsoItem->id }}" class="w-full" style="min-height: 250px;"
                                 data-series-name="Lecciones"
                                 data-series-color="#0ea5e9"
                                 data-chart-data='@json($ind['chart_lessons'] ?? [])'></div>
                        </div>

                        {{-- ── Scheduled per day ── --}}
                        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-violet-500/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-white">Publicaciones Programadas por Día</h3>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                    {{ count($ind['chart_scheduled'] ?? []) }} día(s) con programación
                                </span>
                            </div>
                            <div id="chart-scheduled-{{ $lapsoItem->id }}" class="w-full" style="min-height: 250px;"
                                 data-series-name="Programadas"
                                 data-series-color="#8b5cf6"
                                 data-chart-data='@json($ind['chart_scheduled'] ?? [])'></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @else
    {{-- Empty State (Planning pattern) --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-12 text-center">
        <svg class="w-16 h-16 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        <p class="text-lg font-medium text-gray-400">Sin indicadores disponibles</p>
        <p class="text-sm text-gray-600 mt-1">No hay datos académicos para mostrar en este momento.</p>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════
         PROFESOR GUÍA SECTION (Planning pattern card)
         ═══════════════════════════════════════════════════════════════════ --}}
    @if(isset($esProfesorGuia) && $esProfesorGuia)
        @include('profesors.partials.card-profesor-guia')
    @endif

</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ═══ ApexCharts — Profesor Dashboard Charts ═══ --}}
<script>
    /**
     * Initialize all profesor chart containers using IntersectionObserver.
     * Defers rendering until the container becomes visible (tab is active).
     */
    (function initProfesorCharts() {
        const containers = document.querySelectorAll('[id^="chart-"]');
        if (!containers.length) return;

        // Shared ApexCharts area config
        function makeChartConfig(seriesData, name, color) {
            return {
                series: [{ name: name, data: seriesData }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: 'Inter, system-ui, sans-serif',
                },
                colors: [color],
                stroke: { curve: 'smooth', width: 2 },
                markers: {
                    size: 4,
                    colors: [color],
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
                    y: { formatter: function(val) { return val + ' ' + name.toLowerCase() + '(es)'; } },
                },
                noData: {
                    text: 'Sin datos para este período',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: { color: '#6b7280', fontSize: '13px' },
                },
            };
        }

        /**
         * Initialize a single chart container.
         */
        async function renderChart(el) {
            if (!el || el.dataset.chartInited) return;

            // Load ApexCharts dynamically
            if (window.loadApexCharts) await window.loadApexCharts();
            if (!window.ApexCharts) return;

            const rawData = el.getAttribute('data-chart-data');
            let data = [];
            try { data = JSON.parse(rawData) || []; } catch (e) { data = []; }

            const name = el.getAttribute('data-series-name') || 'Serie';
            const color = el.getAttribute('data-series-color') || '#10b981';

            const chart = new window.ApexCharts(el, makeChartConfig(data, name, color));
            chart.render();
            el.dataset.chartInited = '1';

            // Store reference for potential cleanup
            window._profCharts = window._profCharts || {};
            window._profCharts[el.id] = chart;
        }

        // Use IntersectionObserver: charts render only when visible in viewport
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        renderChart(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.01 });

            containers.forEach(el => observer.observe(el));
        } else {
            // Fallback: render all immediately
            containers.forEach(el => renderChart(el));
        }
    })();
</script>

@if(isset($mostrarModalNotificacion) && $mostrarModalNotificacion)
    @include('profesors.partials.modal-notificacion-diag')
@endif
@endsection
