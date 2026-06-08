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
        <span>{{ $lapso_active->pescolar->name ?? $lapso_active->academic_year ?? '2025-2026' }}</span>
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
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white mb-2">
                Bienvenido, {{ $profesor->full_name ?? Auth::user()->username }}
            </h1>
            <p class="text-emerald-400 font-medium">Panel de rendimiento académico</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('app.profesors.users.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-emerald-300 rounded-xl border border-white/5 transition-all duration-300 text-xs font-bold">
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
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl overflow-hidden"
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
                        class="flex-1 px-6 py-4 text-xs font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap"
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
                    {{-- 6 KPI Boxes grid (3 columns like Planning indicator tabs) --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

                        {{-- 1. Planes de Evaluación --}}
                        <x-indicator-box
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
                            label="Planes de Evaluación"
                            value="{{ $ind['count_pevaluacions'] ?? 0 }}"
                            color="emerald"
                            subtext="Asignados en este lapso"
                        />

                        {{-- 2. Evaluaciones Registradas --}}
                        <x-indicator-box
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>'
                            label="Evaluaciones Registradas"
                            value="{{ $ind['count_evaluacions'] ?? 0 }}"
                            color="blue"
                            subtext="Total de evaluaciones aplicadas"
                        />

                        {{-- 3. Notas Registradas (custom card with progress bar) --}}
                        @php $porc = $ind['porc_notas_load'] ?? 0; @endphp
                        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-5 rounded-2xl transition-all duration-300 hover:border-purple-500/30 hover:shadow-lg hover:shadow-purple-500/5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="w-10 h-10 bg-purple-500/10 rounded-xl flex items-center justify-center text-purple-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-white mb-1">{{ $porc }}%</p>
                            <p class="text-[11px] font-medium text-purple-400 uppercase tracking-wider">Notas Registradas</p>
                            <div class="progress-bar-sm mt-2">
                                <div class="progress-bar-sm-fill {{ $porc >= 80 ? 'bg-emerald-400' : ($porc >= 50 ? 'bg-amber-400' : 'bg-red-400') }}"
                                     style="width:{{ min($porc,100) }}%"></div>
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1">Meta de carga de notas</p>
                        </div>

                        {{-- 4. Promedio General --}}
                        <x-indicator-box
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>'
                            label="Promedio General"
                            value="{{ $ind['promedio'] !== null ? number_format($ind['promedio'], 2) : '—' }}"
                            color="amber"
                            subtext="Calificación promedio del lapso"
                        />

                        {{-- 5. Porcentaje Aprobados --}}
                        @php $aprob = $ind['porc_aprobados'] ?? null; @endphp
                        <x-indicator-box
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                            label="Estudiantes Aprobados"
                            value="{{ $aprob !== null ? number_format($aprob, 1) . '%' : '—' }}"
                            color="rose"
                            subtext="Porcentaje de aprobación"
                        />

                        {{-- 6. IRE --}}
                        @php
                            $ireValue = null;
                            $ireCode  = null;
                            $pestudios = $profesor?->pevaluacions()
                                ->where('lapso_id', $lapsoItem->id)
                                ->get()
                                ->pluck('pensum.pestudio')
                                ->filter()
                                ->unique('id');
                            if ($pestudios && $pestudios->isNotEmpty()) {
                                $first   = $pestudios->first();
                                $ireValue = $profesor?->getProfesorIRE($first->id, $lapsoItem->id);
                                $ireCode  = $first->code;
                            }
                        @endphp
                        <x-indicator-box
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>'
                            label="Índice IRE"
                            value="{{ $ireValue !== null ? number_format($ireValue, 1) . '%' : '—' }}"
                            color="cyan"
                            subtext="{{ $ireCode ? 'Vs. pares — ' . $ireCode : 'Rendimiento vs. colegas' }}"
                        />

                    </div>

                    {{-- IRE Explanation Box --}}
                    @if($ireValue !== null)
                    <div class="mt-5 bg-cyan-500/5 border border-cyan-500/10 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-cyan-500/10 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-cyan-400">¿Qué es el IRE?</p>
                                <p class="text-xs text-gray-400 mt-1 leading-relaxed">
                                    Índice de Rendimiento en Evaluación. Mide tu eficiencia comparada con el promedio de tus colegas del mismo plan de estudio.
                                    <span class="text-cyan-400">Más de 100% = superior al promedio.</span>
                                </p>
                                <p class="text-[10px] text-gray-500 mt-1">
                                    <span class="text-cyan-500 font-medium">Fórmula:</span> IRE = (Notas cargadas / Promedio de pares) &times; 100
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @else
    {{-- Empty State (Planning pattern) --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl p-12 text-center">
        <svg class="w-16 h-16 text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

@if(isset($mostrarModalNotificacion) && $mostrarModalNotificacion)
    @include('profesors.partials.modal-notificacion-diag')
@endif
@endsection
