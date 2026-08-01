@extends('planning.layouts.app')

@section('title', 'Planificación - Diagramas de Flujo')

@section('navbar-info')
    <div class="hidden lg:flex items-center gap-3 ml-4">
        <div class="flex items-center gap-1.5 text-xs text-gray-400">
            <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            <span>{{ count($diagrams) === 1 ? '1 diagrama disponible' : count($diagrams).' diagramas disponibles' }}</span>
        </div>
    </div>
@endsection

@section('content')
    <div class="fade-in">
        {{-- Encabezado --}}
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-3" aria-label="Breadcrumb">
                <a href="{{ route('app.planning.index') }}" class="hover:text-emerald-300 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Planificación
                </a>
                <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-300">Diagramas de Flujo</span>
            </nav>

            <h1 class="text-lg font-extrabold text-white mb-2">Diagramas de Flujo</h1>
            <p class="text-emerald-400 font-medium">Recursos visuales que explican los procesos académicos de la institución.</p>
        </div>

        {{-- Grilla de diagramas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($diagrams as $diagram)
                @php($diagramUrl = route('app.planning.diagram.flow.show', $diagram['slug']))
                <div class="diagnostic-card group relative bg-gray-900/40 backdrop-blur-md border border-white/5 p-6 rounded-lg overflow-hidden transition-all duration-300 border-t-4 border-t-cyan-500 hover:border-cyan-500/30">
                    <a href="{{ $diagramUrl }}" target="_blank" rel="noopener"
                        class="absolute inset-0 z-0" title="Abrir en una pestaña nueva"></a>
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity pointer-events-none">
                        <svg class="w-20 h-20 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    </div>
                    <div class="relative z-10 flex flex-col h-full pointer-events-none">
                        <div class="w-12 h-12 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-2 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 mb-3">{{ $diagram['badge'] }}</span>
                        <h3 class="text-lg font-bold text-white mb-2">{{ $diagram['title'] }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-6">{{ $diagram['description'] }}</p>

                        <div class="mt-auto flex justify-end pointer-events-auto">
                            <a href="{{ $diagramUrl }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg border border-cyan-500/20 transition-all duration-300 text-xs font-bold uppercase tracking-widest group/btn">
                                <svg class="w-4 h-4 transition-transform group-hover/btn:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"/></svg>
                                Ver Diagrama
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-lg border border-dashed border-white/10 bg-gray-900/40 p-10 text-center">
                    <p class="text-gray-500 text-sm">Aún no hay diagramas de flujo publicados.</p>
                </div>
            @endforelse
        </div>

        {{-- Nota sobre el patrón de URLs --}}
        <div class="mt-10 rounded-lg border border-white/5 bg-gray-900/40 p-5">
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                ¿Cómo se publican estos recursos?
            </h4>
            <p class="text-sm text-gray-400 leading-relaxed mb-3">
                Cada infografía vive como un archivo estático en <code class="text-cyan-400 bg-cyan-500/10 px-1.5 py-0.5 rounded text-xs">docs/infografia/flujo{{ '{' }}Nombre{{ '}' }}.html</code> y queda disponible automáticamente bajo la URL:
            </p>
            <div class="flex items-center gap-2 flex-wrap">
                <code class="text-xs bg-black/40 border border-white/10 text-emerald-300 px-3 py-1.5 rounded-lg font-mono">{{ url('app/planning/diagram/flow') }}/*</code>
                <span class="text-xs text-gray-500">→</span>
                <code class="text-xs bg-black/40 border border-white/10 text-gray-300 px-3 py-1.5 rounded-lg font-mono">{{ route('app.planning.diagram.flow.show', 'activity-lesson') }}</code>
            </div>
        </div>
    </div>
@endsection
