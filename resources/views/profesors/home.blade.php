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
    <div class="mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">
                Bienvenido, {{ $profesor->full_name ?? Auth::user()->username }}
            </h1>
            <p class="text-emerald-400 font-medium">Panel de rendimiento académico</p>
        </div>
        <div class="flex items-center gap-3" x-data="{ showLegend: false }">
            <button @click="showLegend = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-emerald-300 rounded-lg border border-white/5 transition-all duration-300 text-xs font-bold"
                title="Leyenda de colores">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                <span class="hidden sm:inline">Leyenda</span>
            </button>
            <a href="{{ route('app.profesors.users.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-emerald-300 rounded-lg border border-white/5 transition-all duration-300 text-xs font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Mi Perfil
            </a>

            {{-- ═══ Legend Modal ═══ --}}
            <div x-show="showLegend" x-cloak
                 @keydown.escape.window="showLegend = false"
                 class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
                <div @click.away="showLegend = false"
                     class="bg-gray-900/90 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-500/10 rounded-lg">
                                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">Leyenda de Indicadores</h3>
                                <p class="text-xs text-emerald-400">Código de colores del dashboard</p>
                            </div>
                        </div>
                        <button @click="showLegend = false"
                            class="p-1.5 bg-white/10 hover:bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    {{-- Body --}}
                    <div class="px-6 py-5 space-y-4">
                        <p class="text-sm text-gray-400">Cada indicador tiene un borde superior de 4px que identifica visualmente su grupo:</p>
                        <div class="space-y-2">
                            {{-- Actividades --}}
                            <div class="flex items-center gap-3 bg-white/5 rounded-lg px-4 py-3 border border-white/5">
                                <div class="w-1 h-10 rounded-full bg-amber-500 shrink-0"></div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                        <p class="text-sm font-semibold text-white">Actividades</p>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">Enseñanza de Calidad (anchor 2×2)</p>
                                </div>
                            </div>
                            {{-- Pevaluacions --}}
                            <div class="flex items-center gap-3 bg-white/5 rounded-lg px-4 py-3 border border-white/5">
                                <div class="w-1 h-10 rounded-full bg-blue-500 shrink-0"></div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                        <p class="text-sm font-semibold text-white">Pevaluacions</p>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">Planes de Eval, Actividades Reg, Con Eval, Aprobadas</p>
                                </div>
                            </div>
                            {{-- Diagnóstico --}}
                            <div class="flex items-center gap-3 bg-white/5 rounded-lg px-4 py-3 border border-white/5">
                                <div class="w-1 h-10 rounded-full bg-indigo-500 shrink-0"></div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 shrink-0"></span>
                                        <p class="text-sm font-semibold text-white">Diagnóstico</p>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">Sesiones Totales, Completados, En Progreso, Progreso (custom)</p>
                                </div>
                            </div>
                            {{-- Lecciones --}}
                            <div class="flex items-center gap-3 bg-white/5 rounded-lg px-4 py-3 border border-white/5">
                                <div class="w-1 h-10 rounded-full bg-teal-500 shrink-0"></div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-teal-500 shrink-0"></span>
                                        <p class="text-sm font-semibold text-white">Lecciones</p>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-0.5">Lecciones Publicadas, Secciones de Contenido, Recursos LMS</p>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 italic">Los colores corresponden al borde superior (border-t-4) de cada tarjeta indicadora.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════════════
         FLUJO DE REGISTROS — Charts con date range toggle (Alpine.js)
         Replica los charts de /app/planning/indicators filtrados por profesor
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="mt-8 bg-white border border-gray-200 rounded-lg p-4 sm:p-5
                dark:bg-slate-800/40 dark:backdrop-blur-md dark:border-slate-700/60"
         x-data="{ flowRange: '7d' }"
         x-init="
             $nextTick(() => renderFlowCharts($el, flowRange));
             $watch('flowRange', value => renderFlowCharts($el, value));
         ">

        {{-- Header + Range Selector --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-cyan-100 rounded-lg flex items-center justify-center shrink-0 dark:bg-cyan-500/20">
                    <svg class="w-4 h-4 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white">Flujo de Registros</h2>
                <span class="text-[10px] text-gray-500 hidden sm:inline dark:text-slate-400">Actividades · Lecciones · Diagnósticos</span>
            </div>

            {{-- Range Buttons --}}
            <div class="flex items-center gap-1">
                <button @click="flowRange = '7d'"
                    :class="flowRange === '7d'
                        ? 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/30'
                        : 'bg-gray-100 text-gray-600 border-gray-200 hover:text-gray-900 hover:border-gray-400 dark:bg-white/5 dark:text-gray-500 dark:border-white/10 dark:hover:text-gray-300 dark:hover:border-gray-600'"
                    class="px-3 py-1.5 text-[10px] font-bold rounded-lg border transition-all min-w-[44px] min-h-[36px]">
                    7d
                </button>
                <button @click="flowRange = '30d'"
                    :class="flowRange === '30d'
                        ? 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/30'
                        : 'bg-gray-100 text-gray-600 border-gray-200 hover:text-gray-900 hover:border-gray-400 dark:bg-white/5 dark:text-gray-500 dark:border-white/10 dark:hover:text-gray-300 dark:hover:border-gray-600'"
                    class="px-3 py-1.5 text-[10px] font-bold rounded-lg border transition-all min-w-[44px] min-h-[36px]">
                    30d
                </button>
                <button @click="flowRange = '3m'"
                    :class="flowRange === '3m'
                        ? 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/30'
                        : 'bg-gray-100 text-gray-600 border-gray-200 hover:text-gray-900 hover:border-gray-400 dark:bg-white/5 dark:text-gray-500 dark:border-white/10 dark:hover:text-gray-300 dark:hover:border-gray-600'"
                    class="px-3 py-1.5 text-[10px] font-bold rounded-lg border transition-all min-w-[44px] min-h-[36px]">
                    3m
                </button>
                <button @click="flowRange = 'all'"
                    :class="flowRange === 'all'
                        ? 'bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/30'
                        : 'bg-gray-100 text-gray-600 border-gray-200 hover:text-gray-900 hover:border-gray-400 dark:bg-white/5 dark:text-gray-500 dark:border-white/10 dark:hover:text-gray-300 dark:hover:border-gray-600'"
                    class="px-3 py-1.5 text-[10px] font-bold rounded-lg border transition-all min-w-[44px] min-h-[36px]">
                    Todo
                </button>
            </div>
        </div>

        {{-- Charts Grid (3 columns on lg+) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4"
             data-flow-data='@json($registrationFlow ?? [])'>

            {{-- ── Activities ── --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 dark:bg-slate-800/60 dark:border-slate-700/60">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-purple-500 shrink-0 dark:bg-purple-400"></span>
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">Actividades</h3>
                </div>
                <div id="flow-activities-chart" class="w-full" style="min-height:200px"
                     data-flow-key="activities"
                     data-series-name="Actividades"
                     data-series-color="#8b5cf6"></div>
            </div>

            {{-- ── Lessons ── --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 dark:bg-slate-800/60 dark:border-slate-700/60">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500 shrink-0 dark:bg-sky-400"></span>
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">Lecciones</h3>
                </div>
                <div id="flow-lessons-chart" class="w-full" style="min-height:200px"
                     data-flow-key="lessons"
                     data-series-name="Lecciones"
                     data-series-color="#0ea5e9"></div>
            </div>

            {{-- ── Diagnostics ── --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 dark:bg-slate-800/60 dark:border-slate-700/60">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0 dark:bg-emerald-400"></span>
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400">Diagnósticos</h3>
                </div>
                <div id="flow-diagnostics-chart" class="w-full" style="min-height:200px"
                     data-flow-key="diagnostics"
                     data-series-name="Diagnósticos"
                     data-series-color="#10b981"></div>
            </div>
        </div>
    </div>


    {{-- Identity strip (mobile <lg) — info oculta por navbar-info --}}
    <div class="flex lg:hidden items-center gap-2 mb-4 flex-wrap">
        @if($esProfesorGuia)
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Profesor Guía
            </span>
        @endif
        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
            {{ $lapso_active->name ?? '—' }}
        </span>
        <span class="text-[10px] text-gray-500">{{ $lapso_active->pescolar->name ?? $lapso_active->academic_year ?? '2026-2027' }}</span>
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
                        class="flex-1 px-2 sm:px-3 lg:px-6 py-2 text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap"
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
        <div class="p-3 sm:p-4 lg:p-6">
            @foreach($lapsos as $lapsoItem)
                @php
                    $tabNum = $loop->iteration;
                    $ind = $indicadores->firstWhere('id', $lapsoItem->id);
                @endphp
                <template x-if="activeTab === {{ $tabNum }}">
                    <div>
                        @php
                            $diagTotal     = $ind['diag_total'] ?? 0;
                            $diagCompleted = $ind['diag_completed'] ?? 0;
                            $diagProgress  = $ind['diag_progress'] ?? 0;
                            $diagHasData   = $diagTotal > 0;
                        @endphp

                    {{-- ════════════════════════════════════════════════════
                         BENTO GRID — single unified dashboard grid
                         ════════════════════════════════════════════════════ --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-2" style="grid-auto-flow: dense;">

                        {{-- ═══ 1. Enseñanza de Calidad — anchor tile 2×2 ═══ --}}
                        <div class="col-span-1 sm:col-span-2 row-span-1 sm:row-span-2">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'
                                label="Enseñanza de Calidad"
                                value="{{ $ind['act_calidad_ens'] ?? 0 }}"
                                color="indigo"
                                topBorderColor="amber"
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
                                topBorderColor="blue"
                                subtext="Asignados en este lapso"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'
                                label="Actividades Registradas"
                                value="{{ $ind['act_total'] ?? 0 }}"
                                color="blue"
                                topBorderColor="blue"
                                subtext="En todos los planes"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                label="Con Evaluación"
                                value="{{ $ind['act_con_eval'] ?? 0 }}"
                                color="teal"
                                topBorderColor="blue"
                                subtext="Tienen actividad evaluativa"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                label="Aprobadas"
                                value="{{ $ind['act_aprobadas'] ?? 0 }}"
                                color="amber"
                                topBorderColor="blue"
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
                                topBorderColor="indigo"
                                subtext="Evaluaciones diagnósticas"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                label="Completados"
                                value="{{ $diagCompleted }}"
                                color="emerald"
                                topBorderColor="indigo"
                                subtext="{{ $diagHasData ? $diagProgress . '% de avance' : 'Sin datos' }}"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                                label="En Progreso"
                                value="{{ $ind['diag_en_progreso'] ?? 0 }}"
                                color="amber"
                                topBorderColor="indigo"
                                subtext="Sesiones activas pendientes"
                            />
                        </div>

                        <div class="col-span-1">
                            {{-- Custom progress card with bar --}}
                            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 border-t-4 border-t-indigo-500/60 p-4 sm:p-5 rounded-lg transition-all duration-300 hover:border-indigo-500/30 h-full flex flex-col">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-indigo-500/10 rounded-lg flex items-center justify-center text-indigo-400">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-base sm:text-lg font-bold text-white mb-1">{{ $diagProgress }}%</p>
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
                                topBorderColor="teal"
                                subtext="Visibles para estudiantes"
                            />
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>'
                                label="Secciones de Contenido"
                                value="{{ $ind['lms_sections'] ?? 0 }}"
                                color="teal"
                                topBorderColor="teal"
                                subtext="Estructuras de aprendizaje"
                            />
                        </div>

                        <div class="col-span-1">
                            <x-indicator-box
                                icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>'
                                label="Recursos LMS"
                                value="{{ $ind['lms_resources'] ?? 0 }}"
                                color="pink"
                                topBorderColor="teal"
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
                        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 sm:p-5">
                            <div class="flex items-start sm:items-center justify-between mb-3 gap-y-1 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-white">Actividades Registradas por Día</h3>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 ml-10 sm:ml-0">
                                    {{ count($ind['chart_activities'] ?? []) }} día(s) con actividad
                                </span>
                            </div>
                            <div id="chart-activities-{{ $lapsoItem->id }}" class="w-full" style="min-height: 250px;"
                                 data-series-name="Actividades"
                                 data-series-color="#10b981"
                                 data-chart-data='@json($ind['chart_activities'] ?? [])'></div>
                        </div>

                        {{-- ── Lessons per day ── --}}
                        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 sm:p-5">
                            <div class="flex items-start sm:items-center justify-between mb-3 gap-y-1 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-sky-500/20 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-white">Lecciones Registradas por Día</h3>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 ml-10 sm:ml-0">
                                    {{ count($ind['chart_lessons'] ?? []) }} día(s) con publicación
                                </span>
                            </div>
                            <div id="chart-lessons-{{ $lapsoItem->id }}" class="w-full" style="min-height: 250px;"
                                 data-series-name="Lecciones"
                                 data-series-color="#0ea5e9"
                                 data-chart-data='@json($ind['chart_lessons'] ?? [])'></div>
                        </div>

                        {{-- ── Scheduled per day ── --}}
                        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-4 sm:p-5">
                            <div class="flex items-start sm:items-center justify-between mb-3 gap-y-1 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-violet-500/20 rounded-lg flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-white">Publicaciones Programadas por Día</h3>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500 ml-10 sm:ml-0">
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
                </template>
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

    {{-- ═══════════════════════════════════════════════════════════════════
         FLOATING GUIDE BUTTON + XXL MODAL
         ═══════════════════════════════════════════════════════════════════ --}}
    <div x-data="{ showGuide: false }">
        {{-- Floating Action Button --}}
        <button @click="showGuide = true"
            class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/30 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/10 hover:shadow-emerald-500/25 transition-all duration-300 group"
            title="Guía de indicadores">
            <svg class="w-6 h-6 text-emerald-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
        </button>

        {{-- XXL Guide Modal --}}
        <div x-show="showGuide" x-cloak
             @keydown.escape.window="showGuide = false"
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div @click.away="showGuide = false"
                 class="bg-gray-900/95 backdrop-blur-md border border-white/10 rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] flex flex-col">
                {{-- ═══ Header ═══ --}}
                <div class="flex items-center justify-between px-8 py-5 border-b border-white/5 shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-11 h-11 bg-emerald-500/15 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Guía de Indicadores del Dashboard</h2>
                            <p class="text-sm text-emerald-400">Significado, origen e interpretación de cada indicador académico</p>
                        </div>
                    </div>
                    <button @click="showGuide = false"
                        class="p-2 bg-white/10 hover:bg-red-500/20 rounded-xl text-gray-400 hover:text-red-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- ═══ Scrollable Body ═══ --}}
                <div class="overflow-y-auto p-8 space-y-8">
                    {{-- ── 1. Actividades ── --}}
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-1.5 h-8 rounded-full bg-amber-500"></div>
                            <h3 class="text-lg font-bold text-white">Actividades</h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20 text-[10px] font-bold uppercase tracking-wider">Enseñanza de Calidad</span>
                        </div>
                        <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-4">
                            {{-- Indicator detail --}}
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-indigo-500/15 rounded-lg flex items-center justify-center text-indigo-400 shrink-0 mt-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                </div>
                                <div class="min-w-0 space-y-2">
                                    <h4 class="text-sm font-bold text-white">Enseñanza de Calidad</h4>
                                    <p class="text-sm text-gray-400 leading-relaxed">
                                        Cuenta las actividades cuya descripción del campo <strong class="text-gray-300">teaching</strong> contiene al menos <strong class="text-gray-300">10 palabras significativas</strong> (con más de 3 caracteres). Este indicador mide la profundidad pedagógica con la que el profesor describe su metodología de enseñanza.
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-1">
                                        <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Origen del dato</p>
                                            <p class="text-xs text-gray-300">Campo <code class="text-emerald-400">teaching</code> en tabla <code class="text-emerald-400">activities</code>. Filtro: <code class="text-emerald-400">teachingWordsMayorCount(3) ≥ 10</code></p>
                                        </div>
                                        <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                            <p class="text-xs text-gray-300">A mayor valor, mayor riqueza pedagógica en la planificación del docente. El mínimo deseable es 1 por cada plan de evaluación activo.</p>
                                        </div>
                                        <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Recomendación</p>
                                            <p class="text-xs text-gray-300">Si el valor es bajo, revisar las actividades registradas y enriquecer el campo "teaching" con descripciones metodológicas detalladas.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- ── 2. Pevaluacions (Carga Académica) ── --}}
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-1.5 h-8 rounded-full bg-blue-500"></div>
                            <h3 class="text-lg font-bold text-white">Pevaluacions — Carga Académica</h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] font-bold uppercase tracking-wider">4 indicadores</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Planes de Evaluación --}}
                            <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-emerald-500/15 rounded-lg flex items-center justify-center text-emerald-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">Planes de Evaluación</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    Total de <strong class="text-gray-300">planes de evaluación</strong> (Pevaluacion) asignados al profesor en el lapso activo. Cada plan corresponde a una combinación de asignatura, grado y sección.
                                </p>
                                <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                    <p class="text-xs text-gray-300">Representa la <strong class="text-gray-400">carga académica</strong> del profesor. Un número elevado indica mayor cantidad de secciones o asignaturas a cargo. Cada plan debe contener actividades registradas para cumplir con la planificación.</p>
                                </div>
                            </div>

                            {{-- Actividades Registradas --}}
                            <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-blue-500/15 rounded-lg flex items-center justify-center text-blue-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">Actividades Registradas</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    Número total de <strong class="text-gray-300">actividades</strong> creadas por el profesor <strong class="text-gray-300">en todos sus planes</strong> de evaluación durante el lapso activo.
                                </p>
                                <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                    <p class="text-xs text-gray-300">Mide el <strong class="text-gray-400">volumen total de planificación</strong>. Una cantidad adecuada depende de la carga académica (Planes de Evaluación) y del tipo de asignatura. Actividades variadas y bien distribuidas en el lapso indican una planificación saludable.</p>
                                </div>
                            </div>

                            {{-- Con Evaluación --}}
                            <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-teal-500/15 rounded-lg flex items-center justify-center text-teal-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">Con Evaluación</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    Actividades que tienen un <strong class="text-gray-300">criterio de evaluación definido</strong> (campo <code class="text-emerald-400">description</code> no vacío). No todas las actividades registradas incluyen necesariamente una descripción evaluativa.
                                </p>
                                <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                    <p class="text-xs text-gray-300">Un valor cercano al total de Actividades Registradas indica que <strong class="text-gray-400">casi todas las actividades tienen evaluación</strong>. Si es significativamente menor, hay actividades sin criterio de evaluación definido que deberían completarse.</p>
                                </div>
                            </div>

                            {{-- Aprobadas --}}
                            <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-amber-500/15 rounded-lg flex items-center justify-center text-amber-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">Aprobadas</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    Actividades cuyo <strong class="text-gray-300">estatus</strong> (<code class="text-emerald-400">status = true</code>) indica que han sido <strong class="text-gray-300">revisadas y aprobadas</strong> por la coordinación o dirección académica.
                                </p>
                                <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                    <p class="text-xs text-gray-300">Refleja el <strong class="text-gray-400">porcentaje de planificación validada</strong>. Idealmente, todas las actividades deberían pasar a estatus aprobado. Un número bajo puede indicar actividades pendientes de revisión administrativa o con observaciones por corregir.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- ── 3. Diagnóstico ── --}}
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-1.5 h-8 rounded-full bg-indigo-500"></div>
                            <h3 class="text-lg font-bold text-white">Diagnóstico</h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-[10px] font-bold uppercase tracking-wider">4 indicadores</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Sesiones Totales --}}
                            <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-blue-500/15 rounded-lg flex items-center justify-center text-blue-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">Sesiones Totales</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    Total de <strong class="text-gray-300">sesiones de evaluación diagnóstica</strong> (DiagSession) creadas para las asignaturas del profesor en el lapso activo. Cada sesión representa una evaluación diagnóstica asociada a un pensum específico.
                                </p>
                                <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                    <p class="text-xs text-gray-300">Define el <strong class="text-gray-400">universo de evaluaciones diagnósticas</strong> disponibles. Es la base sobre la cual se calculan los demás indicadores de diagnóstico. Cada sesión puede contener múltiples preguntas y ser aplicada a uno o varios estudiantes.</p>
                                </div>
                            </div>

                            {{-- Completados --}}
                            <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-emerald-500/15 rounded-lg flex items-center justify-center text-emerald-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">Completados</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    Sesiones donde el campo <strong class="text-gray-300">completado_at</strong> tiene una fecha registrada, indicando que la evaluación diagnóstica fue <strong class="text-gray-300">finalizada exitosamente</strong>.
                                </p>
                                <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                    <p class="text-xs text-gray-300">Mide el <strong class="text-gray-400">avance real</strong> del diagnóstico. Idealmente debe ser la mayor parte del total de sesiones. Un número bajo comparado con Sesiones Totales indica que hay diagnósticos pendientes por completar.</p>
                                </div>
                            </div>

                            {{-- En Progreso --}}
                            <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-amber-500/15 rounded-lg flex items-center justify-center text-amber-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">En Progreso</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    Sesiones activas (<code class="text-emerald-400">activo = true</code>) que <strong class="text-gray-300">aún no han sido completadas</strong> (<code class="text-emerald-400">completado_at IS NULL</code>). Son evaluaciones iniciadas pero no finalizadas.
                                </p>
                                <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                    <p class="text-xs text-gray-300">Indica <strong class="text-gray-400">evaluaciones en curso</strong> que requieren atención para ser completadas. Un número alto persistente puede señalar diagnósticos abandonados o estudiantes que no han finalizado la evaluación.</p>
                                </div>
                            </div>

                            {{-- Progreso --}}
                            <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-indigo-500/15 rounded-lg flex items-center justify-center text-indigo-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">Progreso</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    <strong class="text-gray-300">Porcentaje global</strong> de avance calculado como <code class="text-emerald-400">(Completados ÷ Sesiones Totales) × 100</code>. Representa el progreso consolidado de todas las evaluaciones diagnósticas del lapso activo.
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                    <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                        <p class="text-xs text-gray-300"><strong class="text-emerald-400">≥80%</strong> → Buen avance. <strong class="text-amber-400">50-79%</strong> → Progreso moderado, requiere impulso. <strong class="text-indigo-400">&lt;50%</strong> → Pendiente significativo, priorizar finalización.</p>
                                    </div>
                                    <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Detalle</p>
                                        <p class="text-xs text-gray-300">Muestra además la fracción <strong class="text-gray-400">Completados / Total</strong> debajo de la barra de progreso. Esta barra cambia de color según el nivel de avance (indigo → amber → emerald).</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- ── 4. Lecciones (LMS) ── --}}
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-1.5 h-8 rounded-full bg-teal-500"></div>
                            <h3 class="text-lg font-bold text-white">Lecciones — LMS</h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-teal-500/10 text-teal-400 border border-teal-500/20 text-[10px] font-bold uppercase tracking-wider">3 indicadores</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Lecciones Publicadas --}}
                            <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-violet-500/15 rounded-lg flex items-center justify-center text-violet-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">Lecciones Publicadas</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    Actividades que tienen una <strong class="text-gray-300">publicación LMS activa</strong> y visible para los estudiantes, a través del modelo <code class="text-emerald-400">LmsActivityPublication</code>. Solo cuentan las publicaciones cuyo rango de visibilidad está vigente (<code class="text-emerald-400">scopeVisibleNow</code>).
                                </p>
                                <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                    <p class="text-xs text-gray-300">Representa el <strong class="text-gray-400">contenido disponible</strong> que los estudiantes pueden ver y con el que pueden interactuar. Es el indicador principal de actividad LMS. Idealmente, cada actividad relevante debería tener su lección publicada.</p>
                                </div>
                            </div>

                            {{-- Secciones de Contenido --}}
                            <div class="bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-teal-500/15 rounded-lg flex items-center justify-center text-teal-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">Secciones de Contenido</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    Número de <strong class="text-gray-300">secciones</strong> (LmsActivitySection) creadas <strong class="text-gray-300">dentro</strong> de las lecciones publicadas. Cada sección es un bloque estructurado de contenido (ej. introducción, desarrollo, cierre, actividad práctica).
                                </p>
                                <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                    <p class="text-xs text-gray-300">Mide la <strong class="text-gray-400">estructura interna</strong> del contenido LMS. Cuantas más secciones, más organizada y completa es la lección. Una lección con 3-5 secciones bien definidas ofrece una experiencia de aprendizaje más rica.</p>
                                </div>
                            </div>

                            {{-- Recursos LMS (full width) --}}
                            <div class="md:col-span-2 bg-white/5 border border-white/5 rounded-xl p-5 space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-pink-500/15 rounded-lg flex items-center justify-center text-pink-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white">Recursos LMS</h4>
                                </div>
                                <p class="text-sm text-gray-400 leading-relaxed">
                                    Cantidad de <strong class="text-gray-300">materiales didácticos</strong> (LmsActivityResource) adjuntos a las lecciones: documentos PDF, videos, enlaces externos, imágenes, presentaciones, etc. Estos recursos enriquecen el contenido de las secciones.
                                </p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                                    <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Interpretación</p>
                                        <p class="text-xs text-gray-300">Refleja la <strong class="text-gray-400">riqueza multimedia</strong> del material didáctico. Un mayor número de recursos por lección indica contenido más variado y atractivo para los estudiantes. Se recomienda al menos 1-2 recursos por cada sección de contenido.</p>
                                    </div>
                                    <div class="bg-white/[0.03] border border-white/5 rounded-lg px-3.5 py-2.5">
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-600 mb-0.5">Consejo práctico</p>
                                        <p class="text-xs text-gray-300">Alternar formatos (video, texto, PDF, enlaces) mejora la <strong class="text-gray-400">experiencia de aprendizaje</strong> y atiende diferentes estilos de estudio. Incluir al menos un recurso visual por lección aumenta significativamente el engagement estudiantil.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- ── 5. Charts ── --}}
                    <section>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-1.5 h-8 rounded-full bg-emerald-500"></div>
                            <h3 class="text-lg font-bold text-white">Gráficos de Actividad</h3>
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[10px] font-bold uppercase tracking-wider">3 gráficos</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white/5 border border-white/5 rounded-xl p-4 space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0"></span>
                                    <h4 class="text-xs font-bold text-white">Actividades por Día</h4>
                                </div>
                                <p class="text-xs text-gray-400 leading-relaxed">Muestra la cantidad de actividades registradas en cada fecha del lapso. Picos indican días de alta carga de planificación. Valles pueden indicar períodos sin registro de actividad.</p>
                            </div>
                            <div class="bg-white/5 border border-white/5 rounded-xl p-4 space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-sky-400 shrink-0"></span>
                                    <h4 class="text-xs font-bold text-white">Lecciones por Día</h4>
                                </div>
                                <p class="text-xs text-gray-400 leading-relaxed">Distribución temporal de las publicaciones LMS. Ayuda a identificar la frecuencia con la que se libera contenido a los estudiantes. Idealmente debería haber publicación regular, no acumulada al final del lapso.</p>
                            </div>
                            <div class="bg-white/5 border border-white/5 rounded-xl p-4 space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-violet-400 shrink-0"></span>
                                    <h4 class="text-xs font-bold text-white">Programadas por Día</h4>
                                </div>
                                <p class="text-xs text-gray-400 leading-relaxed">Lecciones con fecha de publicación <strong class="text-gray-300">futura o programada</strong> (<code>publish_at</code> definido). Útil para visualizar el calendario editorial de contenido LMS y anticipar la carga de publicación.</p>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- ═══ Footer ═══ --}}
                <div class="flex items-center justify-between px-8 py-4 border-t border-white/5 shrink-0 bg-white/[0.02] rounded-b-2xl">
                    <p class="text-xs text-gray-500">Los datos mostrados varían según el lapso seleccionado en las pestañas superiores.</p>
                    <button @click="showGuide = false"
                        class="px-5 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-xs font-bold">
                        Cerrar guía
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
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

    /**
     * Render the Flujo de Registros charts (Activities, Lessons, Diagnostics)
     * for the selected date range. Called from Alpine x-data reactivity.
     */
    window.renderFlowCharts = async function(rootEl, range) {
        // Load ApexCharts dynamically
        if (window.loadApexCharts) await window.loadApexCharts();
        if (!window.ApexCharts) return;

        const dataContainer = rootEl.querySelector('[data-flow-data]');
        if (!dataContainer) return;

        const rawData = dataContainer.getAttribute('data-flow-data');
        let flowData = {};
        try { flowData = JSON.parse(rawData) || {}; } catch (e) { flowData = {}; }

        const rangeData = flowData[range];
        if (!rangeData) return;

        const chartEls = dataContainer.querySelectorAll('[data-flow-key]');
        chartEls.forEach(el => {
            const key = el.getAttribute('data-flow-key');
            const data = rangeData[key] || [];
            const name = el.getAttribute('data-series-name') || 'Serie';
            const color = el.getAttribute('data-series-color') || '#10b981';

            // Destroy previous instance
            if (el._flowChart) {
                el._flowChart.destroy();
                el._flowChart = null;
            }

            const config = {
                series: [{ name, data }],
                chart: {
                    type: 'area',
                    height: 200,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: 'Inter, system-ui, sans-serif',
                },
                colors: [color],
                stroke: { curve: 'smooth', width: 2 },
                markers: {
                    size: 3,
                    colors: [color],
                    strokeColors: color,
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
                    labels: { style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    labels: { style: { colors: '#9ca3af', fontSize: '10px', fontWeight: 600 } },
                    tickAmount: 4,
                    forceNiceScale: true,
                },
                grid: { borderColor: '#37415140', strokeDashArray: 4 },
                tooltip: { theme: 'dark' },
                noData: {
                    text: 'Sin datos para este período',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: { color: '#6b7280', fontSize: '12px' },
                },
            };

            el._flowChart = new window.ApexCharts(el, config);
            el._flowChart.render();
        });
    };
</script>

@if(isset($mostrarModalNotificacion) && $mostrarModalNotificacion)
    @include('profesors.partials.modal-notificacion-diag')
@endif
@endsection
