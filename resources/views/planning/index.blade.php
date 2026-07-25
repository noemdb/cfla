@extends('planning.layouts.app')

@section('title', 'Planificación - Panel de Gestión')

@section('navbar-info')
<div class="hidden lg:flex items-center gap-3 ml-4">
    {{-- Fecha --}}
    <div class="flex items-center gap-1.5 text-xs text-gray-400">
        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <span>{{ now()->isoFormat('DD [de] MMMM, YYYY') }}</span>
    </div>

    <span class="w-px h-4 bg-white/5"></span>

    {{-- Período --}}
    <div class="flex items-center gap-1.5 text-xs text-gray-400">
        <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        <span>2026-2027</span>
    </div>

    <span class="w-px h-4 bg-white/5"></span>
    
</div>
@endsection

@section('content')
    <div class="fade-in">
        <!-- Welcome Section -->
        <div class="mb-10">
            <h1 class="text-lg font-extrabold text-white mb-2">Planificación Académica</h1>
            <p class="text-emerald-400 font-medium">Gestión y organización de actividades académicas institucionales.</p>
        </div>

        <div x-data="{ search: '', filterCategory: '', totalCards: 16, showLegend: false }">
            <h2 class="text-lg font-bold text-white mb-6 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                    Módulos de Planificación
                </span>
                <template x-if="search.length > 0">
                    <span class="text-xs text-gray-500 font-normal mr-2" x-text="`${document.querySelectorAll('[data-card]').length} resultados`"></span>
                </template>
            </h2>

            {{-- Search filter --}}
            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-5 rounded-lg mb-8">
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar Módulo</label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" x-model="search" placeholder="Buscar por nombre del módulo..."
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg pl-10 pr-12 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        <button x-show="search.length > 0" @click="search = ''"
                            class="absolute right-1.5 top-1/2 -translate-y-1/2 inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-md border border-emerald-500/20 transition-all duration-200 text-xs font-bold">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Limpiar
                        </button>
                    </div>
                    <select x-model="filterCategory"
                        class="shrink-0 bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-xs font-bold uppercase tracking-wider focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all appearance-none cursor-pointer min-w-[130px]">
                        <option value="" class="bg-gray-900 text-gray-400">Todas</option>
                        <option value="Monitoreo" class="bg-gray-900 text-gray-300">📊 Monitoreo</option>
                        <option value="Evaluación" class="bg-gray-900 text-gray-300">📝 Evaluación</option>
                        <option value="Estructura" class="bg-gray-900 text-gray-300">🏛️ Estructura</option>
                        <option value="Académico" class="bg-gray-900 text-gray-300">🎯 Académico</option>
                        <option value="Docentes" class="bg-gray-900 text-gray-300">👨‍🏫 Docentes</option>
                    </select>
                    <button @click="showLegend = true"
                        class="shrink-0 inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-lg border border-amber-500/20 hover:border-amber-500/40 transition-all duration-200 text-xs font-bold uppercase tracking-wider group/legend-btn"
                        title="Ver leyenda de clasificación">
                        <svg class="w-4 h-4 transition-transform group-hover/legend-btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Leyenda
                    </button>
                </div>
            </div>

            {{-- Modal: Leyenda de clasificación --}}
            <template x-teleport="body">
                <div x-show="showLegend" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                    @click.self="showLegend = false">
                    {{-- Backdrop --}}
                    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

                    {{-- Panel --}}
                    <div x-show="showLegend" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                        class="relative w-full max-w-lg bg-gray-900 border border-white/10 rounded-xl shadow-2xl overflow-hidden">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-white/5">
                            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Leyenda de Clasificación
                            </h3>
                            <button @click="showLegend = false"
                                class="p-1.5 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Body: legend items --}}
                        <div class="px-6 py-5 space-y-3">
                            {{-- Monitoreo --}}
                            <div class="flex items-center gap-3 p-3 bg-cyan-950/30 rounded-lg border border-cyan-500/10">
                                <div class="w-1 self-stretch bg-cyan-500 rounded-full shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">Monitoreo</span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate">Indicadores de Planificación</p>
                                </div>
                            </div>

                            {{-- Evaluación --}}
                            <div class="flex items-center gap-3 p-3 bg-emerald-950/30 rounded-lg border border-emerald-500/10">
                                <div class="w-1 self-stretch bg-emerald-500 rounded-full shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Evaluación</span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate">Actividades de Planificación · Carga Académica · Lapsos Académicos</p>
                                </div>
                            </div>

                            {{-- Estructura --}}
                            <div class="flex items-center gap-3 p-3 bg-amber-950/30 rounded-lg border border-amber-500/10">
                                <div class="w-1 self-stretch bg-amber-500 rounded-full shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20">Estructura</span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate">Programas Educativos · Planes de Estudio · Áreas de Conocimiento · Asignaturas · Grados · Secciones · Pensums</p>
                                </div>
                            </div>

                            {{-- Académico --}}
                            <div class="flex items-center gap-3 p-3 bg-blue-950/30 rounded-lg border border-blue-500/10">
                                <div class="w-1 self-stretch bg-blue-500 rounded-full shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20">Académico</span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate">Competiciones Académicas · Diagnóstico · Referentes</p>
                                </div>
                            </div>

                            {{-- Docentes --}}
                            <div class="flex items-center gap-3 p-3 bg-violet-950/30 rounded-lg border border-violet-500/10">
                                <div class="w-1 self-stretch bg-violet-500 rounded-full shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-violet-500/10 text-violet-400 border border-violet-500/20">Docentes</span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate">Profesores · Contenido LMS</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Planning Modules Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

            <!-- Indicadores de Planificación -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-cyan-500/20 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-cyan-500 hover:border-cyan-500/40 hover:shadow-lg hover:shadow-cyan-500/5">
                <a href="{{ route('app.planning.indicators.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 mb-3">Monitoreo</span>
                    <h3 class="text-lg font-bold text-white mb-2">Indicadores de Planificación</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Dashboard institucional con KPIs: inscritos, evaluaciones, actividades y profesores, segmentado por plan de estudio y lapso.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.indicators.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg border border-cyan-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                            </svg>
                            Ver Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Competiciones Académicas -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-blue-500 hover:border-blue-500/30">
                <a href="{{ route('app.planning.educational.competition.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-3">Académico</span>
                    <h3 class="text-lg font-bold text-white mb-2">Competiciones Académicas</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Planificación de retos educativos, debates y control de
                        puntajes en vivo.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.educational.competition.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                </path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Diagnóstico -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-blue-500 hover:border-blue-500/30">
                <a href="{{ route('app.planning.diagnostico.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012-2">
                            </path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-3">Académico</span>
                    <h3 class="text-lg font-bold text-white mb-2">Diagnóstico</h3>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.diagnostico.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 rounded-lg border border-blue-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                </path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Actividades de Planificación -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-emerald-500 hover:border-emerald-500/30">
                <a href="{{ route('app.planning.activities.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">Evaluación</span>
                    <h3 class="text-lg font-bold text-white mb-2">Actividades de Planificación</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Revisión y control de calidad pedagógica de los planes de evaluación por actividad.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.activities.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg border border-cyan-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                </path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Programas Educativos -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-amber-500 hover:border-amber-500/30">
                <a href="{{ route('app.planning.peducativos.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">Estructura</span>
                    <h3 class="text-lg font-bold text-white mb-2">Programas Educativos</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Gestión de los programas educativos (Inicial, Primaria, Media General) con su equipo directivo y configuración.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.peducativos.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500/10 hover:bg-orange-500/20 text-orange-400 rounded-lg border border-orange-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                </path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Planes de Estudio -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-amber-500 hover:border-amber-500/30">
                <a href="{{ route('app.planning.pestudios.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">Estructura</span>
                    <h3 class="text-lg font-bold text-white mb-2">Planes de Estudio</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Gestión de los programas educativos y su configuración académica, fechas de cierre y promoción.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.pestudios.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500/10 hover:bg-purple-500/20 text-purple-400 rounded-lg border border-purple-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                </path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Áreas de Conocimiento -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-amber-500 hover:border-amber-500/30">
                <a href="{{ route('app.planning.area-conocimientos.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">Estructura</span>
                    <h3 class="text-lg font-bold text-white mb-2">Áreas de Conocimiento</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Catálogo de áreas de conocimiento y asignaturas adscritas, con gestión de jefes de área y códigos.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.area-conocimientos.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500/10 hover:bg-yellow-500/20 text-yellow-400 rounded-lg border border-yellow-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Asignaturas -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-amber-500 hover:border-amber-500/30">
                <a href="{{ route('app.planning.asignaturas.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                            </path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">Estructura</span>
                    <h3 class="text-lg font-bold text-white mb-2">Asignaturas</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Catálogo de asignaturas del pensum académico con sus horas, créditos, prelaciones y configuración.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.asignaturas.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-pink-500/10 hover:bg-pink-500/20 text-pink-400 rounded-lg border border-pink-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                </path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Grados -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-amber-500 hover:border-amber-500/30">
                <a href="{{ route('app.planning.grados.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                            </path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">Estructura</span>
                    <h3 class="text-lg font-bold text-white mb-2">Grados</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Administración de grados y años académicos con configuración de horas sociales y secciones.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.grados.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-lg border border-amber-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                </path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Secciones -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-amber-500 hover:border-amber-500/30">
                <a href="{{ route('app.planning.secciones.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4">
                            </path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">Estructura</span>
                    <h3 class="text-lg font-bold text-white mb-2">Secciones</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Gestión de secciones (aulas) por grado académico con capacidad, inscripciones y control de estado.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.secciones.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-lime-500/10 hover:bg-lime-500/20 text-lime-400 rounded-lg border border-lime-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                </path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Lapsos -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-emerald-500 hover:border-emerald-500/30">
                <a href="{{ route('app.planning.lapsos.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">Evaluación</span>
                    <h3 class="text-lg font-bold text-white mb-2">Lapsos Académicos</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Períodos académicos del año escolar con fechas de inicio, fin, censo y configuración de pre-cierre.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.lapsos.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 rounded-lg border border-indigo-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                                </path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pensums -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-amber-500 hover:border-amber-500/30">
                <a href="{{ route('app.planning.pensums.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-amber-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-3">Estructura</span>
                    <h3 class="text-lg font-bold text-white mb-2">Pensums</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Pivote central del sistema: vincula planes de estudio, grados y asignaturas para definir el pensum académico.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.pensums.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 rounded-lg border border-rose-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Carga Académica -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-emerald-500 hover:border-emerald-500/30">
                <a href="{{ route('app.planning.pevaluacions.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mb-3">Evaluación</span>
                    <h3 class="text-lg font-bold text-white mb-2">Carga Académica</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Asignación de profesores a áreas de formación por sección y lapso. Control de planes de evaluación.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.pevaluacions.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-teal-500/10 hover:bg-teal-500/20 text-teal-400 rounded-lg border border-teal-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profesores -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-violet-500 hover:border-violet-500/30">
                <a href="{{ route('app.planning.profesors.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-violet-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-violet-500/10 text-violet-400 border border-violet-500/20 mb-3">Docentes</span>
                    <h3 class="text-lg font-bold text-white mb-2">Profesores</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Gestión de profesores con creación automática de usuario, perfil y rol. Asignación de cargas académicas por lapso.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.profesors.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-violet-500/10 hover:bg-violet-500/20 text-violet-400 rounded-lg border border-violet-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Referentes -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-blue-500 hover:border-blue-500/30">
                <a href="{{ route('app.planning.diagnostico.referents.index') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-3">Académico</span>
                    <h3 class="text-lg font-bold text-white mb-2">Referentes</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Gestión de referentes teóricos y estándares asociados a las áreas de formación para el diagnóstico académico.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.diagnostico.referents.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 rounded-lg border border-sky-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contenido LMS -->
            <div data-card
                x-show="(!search || $el.textContent.toLowerCase().includes(search.toLowerCase())) && (!filterCategory || $el.textContent.toLowerCase().includes(filterCategory.toLowerCase()))"
                class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-violet-500 hover:border-violet-500/30">
                <a href="{{ route('app.planning.lms.monitor') }}" class="absolute inset-0 z-0"></a>
                <div
                    class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                    <svg class="w-20 h-20 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="relative z-10 flex flex-col h-full pointer-events-none">
                    <div
                        class="w-12 h-12 bg-violet-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-violet-500/10 text-violet-400 border border-violet-500/20 mb-3">Docentes</span>
                    <h3 class="text-lg font-bold text-white mb-2">Contenido LMS</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">Monitor y auditoría de contenidos educativos publicados por los profesores en el sistema de aprendizaje.</p>

                    <div class="mt-auto flex justify-end pointer-events-auto">
                        <a href="{{ route('app.planning.lms.monitor') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500/10 hover:bg-yellow-500/20 text-yellow-400 rounded-lg border border-yellow-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                            <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                            </svg>
                            Gestionar
                        </a>
                    </div>
                </div>
            </div>

        </div>

        {{-- Enlace rápido a la página pública --}}
        <div class="mt-12 pt-8 border-t border-white/5 flex justify-center">
            <a href="{{ url('/') }}"
                class="inline-flex items-center gap-2 px-6 py-2 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-emerald-300 rounded-lg border border-white/5 hover:border-emerald-500/20 transition-all duration-300 text-sm font-medium group">
                <svg class="w-4 h-4 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Ir a la Página Pública
            </a>
        </div>
    </div>
@endsection
