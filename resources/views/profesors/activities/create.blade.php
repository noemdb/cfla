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
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white mb-1">Registrar Actividades</h1>
            <p class="text-sm text-emerald-400 font-medium">{{ $pevaluacion->pensum?->asignatura?->name ?? '—' }} — {{ $pevaluacion->pensum?->grado?->name ?? '—' }} {{ $pevaluacion->seccion?->name ?? '—' }}</p>
        </div>
        @include('profesors.activities.menus.create')
    </div>

    {{-- Main grid: sidebar + Livewire --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Sidebar Resumen --}}
        <div class="lg:col-span-1">
            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl p-5 sticky top-24">
                <h4 class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-4 pb-3 border-b border-white/5">
                    <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Resumen
                </h4>
                @include('profesors.activities.partials.resumen')
            </div>
        </div>

        {{-- Livewire Component --}}
        <div class="lg:col-span-3">
            @livewire('profesor.activity.index-component', ['id' => $pevaluacion->id], key($pevaluacion->id))
        </div>

    </div>

</div>
@endsection
