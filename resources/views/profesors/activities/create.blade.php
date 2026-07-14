@extends('profesors.layouts.app')

@section('title', 'Registrar Actividades - ' . config('app.name', 'SAEFL'))

@section('navbar-info')
<div class="hidden lg:flex items-center gap-3 ml-6">
    <div class="flex items-center gap-1.5 text-xs text-gray-400">
        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
        </svg>
        <span class="text-white font-medium">Actividades</span>
        <span class="text-gray-600">·</span>
        <span class="text-gray-500">{{ $pevaluacion->pensum?->asignatura?->name ?? '—' }}</span>
    </div>
    <span class="w-px h-4 bg-white/5"></span>
    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
        {{ $pevaluacion->lapso?->name ?? '—' }}
    </span>
</div>
@endsection

@section('content')
<div class="fade-in">

    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-1">Registrar Actividades</h1>
            <p class="text-sm text-emerald-400 font-medium">{{ $pevaluacion->pensum?->asignatura?->name ?? '—' }} — {{ $pevaluacion->pensum?->grado?->name ?? '—' }} {{ $pevaluacion->seccion?->name ?? '—' }}</p>
        </div>
        @include('profesors.activities.menus.create')
    </div>

    {{-- Main grid: sidebar + Livewire --}}
    <div class="grid grid-cols-1 gap-6"
         x-data="{ resumenOpen: true }"
         :class="resumenOpen ? 'lg:grid-cols-4' : 'lg:grid-cols-1'">

        {{-- Sidebar Resumen --}}
        <div x-show="resumenOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 -translate-x-4"
             class="lg:col-span-1">
            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5 sticky top-24">
                {{-- Toggle button inside sidebar --}}
                <div class="flex items-center justify-between mb-2 pb-3 border-b border-white/5">
                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-emerald-400">
                        <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Resumen
                    </h4>
                    <button @click="resumenOpen = false"
                            class="p-1 rounded-lg hover:bg-white/5 text-gray-500 hover:text-gray-300 transition-colors"
                            title="Ocultar resumen">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                        </svg>
                    </button>
                </div>
                @include('profesors.activities.partials.resumen')
            </div>
        </div>

        {{-- Livewire Component --}}
        <div :class="resumenOpen ? 'lg:col-span-3' : 'lg:col-span-1'">
            {{-- Button to show resumen again (visible when collapsed) --}}
            <div x-show="!resumenOpen"
                 x-transition
                 class="mb-2">
                <button @click="resumenOpen = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Mostrar Resumen
                </button>
            </div>

            @livewire('profesor.activity.index-component', ['id' => $pevaluacion->id], key($pevaluacion->id))
        </div>

    </div>

    {{-- Modal de Competencias --}}
    @livewire('profesor.activity.competencias-dialog')

</div>
@endsection
