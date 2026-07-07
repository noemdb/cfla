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
    <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold text-white mb-0.5">Plan de Actividades</h1>
            <p class="text-xs text-emerald-400 font-medium">Gestión de áreas de formación y actividades académicas</p>
        </div>
        @include('profesors.activities.menus.index')
    </div>

    {{-- Main card --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-xl overflow-hidden">

        {{-- Header --}}
        <div class="border-b border-white/5 px-5 py-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">
                    <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Módulo Planificación Académica
                </h3>
                <span class="text-[10px] text-gray-500 font-medium tracking-wider">Diseñado por: Prof. Carmin Cortez</span>
            </div>
        </div>

        {{-- Lapso Tabs --}}
        @if(isset($lapsos) && $lapsos->isNotEmpty())
        <div class="border-b border-white/5">
            <nav class="flex overflow-x-auto">
                @foreach($lapsos as $index => $lapsoItem)
                    @php
                        $tabLapsoId = $lapsoItem->id;
                        $isActive = ($lapso_id ?? '') == $tabLapsoId;
                        $url = route('app.profesors.activities.index', array_merge(request()->query(), ['lapso_id' => $tabLapsoId]));
                    @endphp
                    <a href="{{ $url }}"
                        class="flex-1 px-4 py-3 text-[11px] font-bold uppercase tracking-widest transition-all duration-200 border-b-2 whitespace-nowrap
                        {{ $isActive ? 'text-emerald-400 border-emerald-500 bg-emerald-500/5' : 'text-gray-500 border-transparent hover:text-gray-300 hover:border-gray-600' }}">
                        <svg class="w-3.5 h-3.5 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $lapsoItem->name }}
                        <span class="block text-[8px] font-normal text-gray-500 normal-case">{{ $lapsoItem->code }}</span>
                    </a>
                @endforeach
            </nav>
        </div>
        @endif

        {{-- Body --}}
        <div class="p-4">

            {{-- Search Filters --}}
            @include('profesors.activities.partials.search', ['route' => 'app.profesors.activities.index'])

            {{-- Subtitle --}}
            <p class="text-[11px] text-gray-400 mb-3 font-medium">
                <span class="text-emerald-400">Listado</span> de Áreas de Formación
            </p>

            {{-- Card Grid --}}
            @include('profesors.activities.table.index')

            {{-- Pagination --}}
            @if(method_exists($pevaluacions, 'links'))
                <div class="mt-3 pt-3 border-t border-white/5">
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
