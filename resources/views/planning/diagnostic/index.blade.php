@extends('planning.layouts.app')

@section('title', 'Diagnóstico - Planificación - ' . config('app.name', 'SAEFL'))

@section('navbar-info')
<div class="hidden lg:flex items-center gap-3 ml-6">
    <div class="flex items-center gap-1.5 text-xs text-gray-400">
        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
        <span class="text-white font-medium">Diagnóstico</span>
    </div>
    <span class="w-px h-4 bg-white/5"></span>
</div>
@endsection

@section('content')
<div class="fade-in">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-lg font-extrabold text-white mb-2">
            <svg class="w-6 h-6 inline mr-2 -mt-0.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            Diagnóstico
        </h1>
        <p class="text-emerald-400 font-medium">Gestión Operativa, activación de áreas de formación y configuración del diagnóstico académico.</p>
    </div>

    {{-- Selector de instrumento diagnóstico (w-full, sin selección por defecto) --}}
    @livewire('planning.diagnostic.diag-main-viewer')

    {{-- Secciones dependientes de un DiagMain seleccionado — ocultas por defecto (active null) --}}
    <div x-data="{ diagMainId: null }" x-on:diag-main-selected.window="diagMainId = $event.detail.id" x-show="diagMainId" x-cloak x-transition.opacity>
        {{-- Módulo: Cabeceras de Diagnóstico (DiagMain CRUD) --}}
        @livewire('planning.diagnostic.diag-main-crud')

        {{-- Módulo: Activación de Áreas de Formación --}}
        <div class="mb-8">
            @livewire('admin.diagnostic.index-component')
        </div>

        {{-- Módulo: Preguntas por Pensum agrupadas por Grupo Estable --}}
        <div class="mb-8">
            @livewire('planning.diagnostic.question-by-pensum')
        </div>
    </div>


</div>

{{-- State loading — fallback global para cualquier Livewire en esta página --}}
<div id="diagnostico-global-loading" class="hidden fixed bottom-6 right-6 z-[60]">
    <div class="flex items-center gap-2 rounded-full bg-gray-900/70 px-4 py-2.5 text-xs font-bold tracking-widest uppercase text-white backdrop-blur-md border border-white/10 shadow-xl shadow-black/30">
        <svg class="h-4 w-4 animate-spin text-emerald-400" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <span>Cargando ...</span>
    </div>
</div>
@endsection

@section('styles')
<style>
    .diagnostic-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .diagnostic-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.1);
    }
    .fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('script')
<script>
document.addEventListener('livewire:init', () => {
    const el = document.getElementById('diagnostico-global-loading');
    if (!el) return;
    Livewire.hook('request', ({ fail }) => {
        el.classList.remove('hidden');
        fail(() => el.classList.add('hidden'));
    });
    Livewire.hook('commit', ({ succeed }) => {
        succeed(() => el.classList.add('hidden'));
    });
});
</script>
@endsection
