@extends('profesors.layouts.app')

@section('title', 'Plan de Actividades - ' . config('app.name', 'SAEFL'))

@section('navbar-info')
<div class="hidden lg:flex items-center gap-3 ml-6">
    <div class="flex items-center gap-1.5 text-xs text-gray-400">
        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
        </svg>
        <span class="text-white font-medium">Plan de Actividades</span>
    </div>
    <span class="w-px h-4 bg-white/5"></span>
    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        Actividades
    </span>
</div>
@endsection

@section('content')
<div class="fade-in">

    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white mb-1">Plan de Actividades</h1>
            <p class="text-sm text-emerald-400 font-medium">Gestión de áreas de formación y actividades académicas</p>
        </div>
        @include('profesors.activities.menus.index')
    </div>

    {{-- Main card --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl overflow-hidden">

        {{-- Header --}}
        <div class="border-b border-white/5 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Módulo Planificación Académica
                </h3>
                <span class="text-[10px] text-gray-500 font-medium tracking-wider">Diseñado por: Prof. Carmin Cortez</span>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6">

            {{-- Search Filters --}}
            @include('profesors.activities.partials.search', ['route' => 'app.profesors.activities.index'])

            {{-- Subtitle --}}
            <p class="text-xs text-gray-400 mb-4 font-medium">
                <span class="text-emerald-400">Listado</span> de Áreas de Formación
                {{-- <small class="text-gray-600">[Prof: {{ Auth::user()->username }}]</small> --}}
            </p>

            {{-- Table --}}
            @include('profesors.activities.table.index')

            {{-- Pagination --}}
            @if(method_exists($pevaluacions, 'links'))
                <div class="mt-4">
                    {{ $pevaluacions->appends(request()->query())->links('vendor.pagination.custom-tailwind') }}
                </div>
            @endif

        </div>
    </div>

</div>
@endsection

@section('script')
@if(isset($mostrarModalNotificacion) && $mostrarModalNotificacion)
    @include('profesors.partials.modal-notificacion-diag')
@endif
@endsection
