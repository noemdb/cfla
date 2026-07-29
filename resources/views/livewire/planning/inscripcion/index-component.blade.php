<div class="fade-in">
    {{-- ================================================================ --}}
    {{-- MODE: LISTADO (Index)                                            --}}
    {{-- ================================================================ --}}
    @if($modeIndex)

    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Inscripciones</h1>
            <p class="text-emerald-400 font-medium">Gestión de estudiantes inscritos en secciones.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('app.planning.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg border border-cyan-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Planificación
            </a>
            <button type="button" wire:click="create"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Inscripción
            </button>
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refrescar
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-5 rounded-lg mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre, apellido, CI..."
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Plan de Estudio</label>
                <select wire:model.live="filterPestudio"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($pestudios as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Grado</label>
                <select wire:model.live="filterGrado"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($grados as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Sección</label>
                <select wire:model.live="filterSeccion"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todas</option>
                    @foreach($secciones as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Tipo</label>
                <select wire:model.live="filterTipo"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($tipos as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Ver</label>
                <select wire:model.live="paginate"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="15">15</option>
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>

    {{-- ─── crud-mode-toggle: Grid / Table ─── --}}
    <div class="flex items-center justify-end mb-4"
         x-data="{ mode: localStorage.getItem('inscripcions-view-mode') || 'table' }"
         x-init="$watch('mode', val => {
             localStorage.setItem('inscripcions-view-mode', val);
             window.dispatchEvent(new CustomEvent('inscripcions-view-mode-changed', { detail: { mode: val } }))
         })">
        <div class="inline-flex items-center bg-gray-900/40 border border-white/5 rounded-lg p-0.5 gap-0.5">
            <button @click="mode = 'grid'"
                :class="mode === 'grid' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-transparent text-gray-500 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-transparent transition-all duration-200 text-[11px] font-bold"
                title="Vista Grid">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span>Grid</span>
            </button>
            <button @click="mode = 'table'"
                :class="mode === 'table' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-transparent text-gray-500 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-transparent transition-all duration-200 text-[11px] font-bold"
                title="Vista Tabla">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <span>Tabla</span>
            </button>
        </div>
    </div>

    {{-- ─── Mode Container ─── --}}
    <div x-cloak
         x-data="{ mode: localStorage.getItem('inscripcions-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('inscripcions-view-mode')) localStorage.setItem('inscripcions-view-mode', 'table') }"
         x-on:inscripcions-view-mode-changed.window="mode = $event.detail.mode">

        {{-- ═══ TABLE MODE ═══ --}}
        <div x-show="mode === 'table'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/5">
                                <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">#</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estudiante</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">CI</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Sección</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden md:table-cell">Grado</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Plan</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Tipo</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden xl:table-cell">Escolaridad</th>
                                <th class="text-right px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($inscripcions as $inscripcion)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-5 py-2 text-sm text-gray-400 font-mono">{{ $inscripcion->id }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-sm font-bold text-white">
                                            {{ $inscripcion->estudiant?->name ?? '—' }}
                                            {{ $inscripcion->estudiant?->lastname ?? '' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs font-mono bg-white/5 text-gray-300 px-2 py-0.5 rounded-md">
                                            {{ $inscripcion->estudiant?->ci_estudiant ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="text-sm text-gray-200 font-medium">{{ $inscripcion->seccion?->name ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-400 hidden md:table-cell">
                                        {{ $inscripcion->seccion?->grado?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-400 hidden lg:table-cell">
                                        {{ $inscripcion->seccion?->grado?->pestudio?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 hidden lg:table-cell">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20">
                                            {{ $inscripcion->tipo?->name ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-400 hidden xl:table-cell">
                                        {{ $inscripcion->escolaridad?->name ?? '—' }}
                                    </td>
                                    <td class="px-5 py-2 text-right">
                                        <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button type="button" wire:click="viewStudent({{ $inscripcion->id }})"
                                                class="p-2 bg-white/5 hover:bg-sky-500/10 rounded-lg border border-white/5 hover:border-sky-500/20 text-gray-400 hover:text-sky-400 transition-all duration-200"
                                                title="Ver perfil del estudiante">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="edit({{ $inscripcion->id }})"
                                                class="p-2 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                                title="Editar inscripción">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="confirmDelete({{ $inscripcion->id }})"
                                                class="p-2 bg-white/5 hover:bg-red-500/10 rounded-lg border border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                                                title="Eliminar inscripción">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-5 py-16 text-center">
                                        <div>
                                            <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-gray-500 font-medium mb-1">No hay inscripciones registradas</p>
                                            <p class="text-gray-600 text-sm">Crea la primera inscripción usando el botón "Nueva Inscripción".</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($inscripcions->hasPages())
                    <x-pagination-wrapper :paginator="$inscripcions" />
                @endif
            </div>
        </div>

        {{-- ═══ GRID MODE (bento-grid-modile) ═══ --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="bg-gray-900/60 border border-white/5 rounded-2xl p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @forelse($inscripcions as $inscripcion)
                        <div class="rounded-2xl border border-white/5 bg-gray-900 hover:border-emerald-500/30 transition-all duration-200 group flex flex-col overflow-hidden min-h-[280px]">

                            {{-- Header: Nombre estudiante + CI badge + Sección --}}
                            <div class="flex items-start justify-between px-4 pt-4 pb-3 border-b border-white/5 gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-white truncate"
                                        title="{{ $inscripcion->estudiant?->name }} {{ $inscripcion->estudiant?->lastname }}">
                                        {{ $inscripcion->estudiant?->name ?? '—' }}
                                        {{ $inscripcion->estudiant?->lastname ?? '' }}
                                    </h3>
                                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/12 text-purple-400 border border-purple-500/20">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                                            </svg>
                                            {{ $inscripcion->estudiant?->ci_estudiant ?? '—' }}
                                        </span>
                                        @if($inscripcion->seccion)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20">
                                                {{ $inscripcion->seccion->grado?->name ?? '' }} {{ $inscripcion->seccion->name }}
                                            </span>
                                        @endif
                                        @if($inscripcion->tipo)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20">
                                                {{ $inscripcion->tipo->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Body: Detalles --}}
                            <div class="px-4 py-3 space-y-2 flex-1">
                                @if($inscripcion->seccion?->grado?->pestudio)
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                        </svg>
                                        <span class="text-gray-400 truncate" title="{{ $inscripcion->seccion->grado->pestudio->name }}">
                                            {{ $inscripcion->seccion->grado->pestudio->name }}
                                        </span>
                                    </div>
                                @endif
                                @if($inscripcion->escolaridad)
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <span class="text-gray-400">{{ $inscripcion->escolaridad->name }}</span>
                                    </div>
                                @endif
                                @if($inscripcion->programacion)
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-gray-400">{{ $inscripcion->programacion->name }}</span>
                                    </div>
                                @endif
                                @if($inscripcion->grupoEstable)
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="text-gray-400">{{ $inscripcion->grupoEstable->name }}</span>
                                    </div>
                                @endif
                                @if($inscripcion->observations)
                                    <div class="flex items-start gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span class="text-gray-500 line-clamp-2 leading-relaxed">{{ $inscripcion->observations }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Footer: Created_at --}}
                            <div class="flex items-center justify-between px-4 py-2.5 border-t border-white/5 bg-white/[0.03]">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-[10px] font-bold bg-blue-500/12 text-blue-400">
                                        {{ $inscripcion->id }}
                                    </span>
                                    <span class="text-[10px] text-gray-500 font-medium">inscripción</span>
                                </div>
                                <span class="text-[9px] text-gray-600 truncate max-w-[120px]" title="{{ $inscripcion->created_at?->format('d/m/Y') }}">
                                    {{ $inscripcion->created_at?->diffForHumans() ?? '' }}
                                </span>
                            </div>

                            {{-- Actions: btnGroup --}}
                            <div class="px-4 pb-4 pt-2.5 border-t border-white/5 flex items-center gap-2"
                                 x-data="{ actionsOpen: false }"
                                 @click.away="actionsOpen = false">

                                {{-- Desktop group --}}
                                <div class="hidden sm:flex items-center gap-2 w-full">
                                    <button type="button" wire:click="viewStudent({{ $inscripcion->id }})"
                                        class="flex-1 min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-xs font-bold bg-sky-500/12 text-sky-400 hover:bg-sky-500/20 border border-sky-500/20 transition-all duration-200"
                                        title="Ver perfil">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span class="text-[10px]">Ver</span>
                                    </button>
                                    <button type="button" wire:click="edit({{ $inscripcion->id }})"
                                        class="flex-1 min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-xs font-bold bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200"
                                        title="Editar">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span class="text-[10px]">Editar</span>
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $inscripcion->id }})"
                                        class="flex-1 min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-xs font-bold bg-red-500/12 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200"
                                        title="Eliminar">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span class="text-[10px]">Eliminar</span>
                                    </button>
                                </div>

                                {{-- Mobile dropdown --}}
                                <div class="relative sm:hidden w-full">
                                    <button @click="actionsOpen = !actionsOpen"
                                        class="w-full min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/30 hover:bg-gray-200 dark:hover:bg-slate-600/50 border border-gray-200 dark:border-slate-600/30 transition-all"
                                        title="Más acciones">
                                        <svg class="w-4 h-4 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4z"/>
                                            <path d="M10 12a2 2 0 110-4 2 2 0 010 4z"/>
                                            <path d="M10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>
                                    <div x-show="actionsOpen"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute right-0 z-50 mt-1 min-w-[160px] bg-gray-800 border border-white/10 rounded-lg shadow-xl py-1"
                                         @click="actionsOpen = false">
                                        <button wire:click="viewStudent({{ $inscripcion->id }})"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                            <svg class="w-4 h-4 shrink-0 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Ver perfil
                                        </button>
                                        <button wire:click="edit({{ $inscripcion->id }})"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </button>
                                        <button wire:click="confirmDelete({{ $inscripcion->id }})"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                            <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 font-medium mb-1">No hay inscripciones registradas</p>
                            <p class="text-gray-600 text-sm">Crea la primera inscripción usando el botón "Nueva Inscripción".</p>
                        </div>
                    @endforelse
                </div>

                @if($inscripcions->hasPages())
                    <div class="mt-6">
                        <x-pagination-wrapper :paginator="$inscripcions" />
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== MODAL: Confirmar Eliminación ===== --}}
    <x-modal title="Eliminar Inscripción" blur="lg" wire:model="confirmDeleteId" max-width="md" x-on:close="confirmDeleteId = null" persistent>
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar esta inscripción?</h3>
            <p class="text-sm text-gray-400 mb-6">Esta acción eliminará la inscripción del estudiante.</p>
            <div class="flex justify-center gap-3">
                <x-button flat label="Cancelar" x-on:click="confirmDeleteId = null" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    {{-- ===== MODAL: Ver Perfil del Estudiante ===== --}}
    <x-modal title="Perfil del Estudiante" blur="lg" wire:model="showStudentModal" max-width="7xl" rounded="2xl" x-on:close="showStudentModal = false; viewingStudent = null" persistent>
        <style>
            [wireui-modal] > div:last-child {
                max-width: 90% !important;
                width: 90% !important;
            }
        </style>
        <div class="p-6 space-y-6 border border-white/10 rounded-2xl mx-4 my-2">

            @if($viewingStudent)
                @php
                    $s = $viewingStudent;
                    $e = $s['estudiant'] ?? [];
                    $r = $e['representant'] ?? [];
                    $sc = $s['seccion'] ?? [];
                    $g = $sc['grado'] ?? [];
                    $pest = $g['pestudio'] ?? [];
                @endphp

                {{-- ═══ Header: Avatar + Name + CI + Status ═══ --}}
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 pb-6 border-b border-white/10">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-sky-500/30 to-emerald-500/30 flex items-center justify-center text-white font-bold text-2xl shrink-0 border-2 border-sky-500/30">
                        {{ strtoupper(substr($e['name'] ?? '?', 0, 1)) }}{{ strtoupper(substr($e['lastname'] ?? '?', 0, 1)) }}
                    </div>
                    <div class="text-center sm:text-left flex-1 min-w-0">
                        <h2 class="text-xl font-extrabold text-white">
                            {{ $e['name'] ?? '—' }} {{ $e['lastname'] ?? '—' }}
                        </h2>
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-purple-500/12 text-purple-400 border border-purple-500/20 font-mono">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                                </svg>
                                {{ $e['ci_estudiant'] ?? '—' }}
                            </span>
                            @if(($e['status_active'] ?? '') === 'true' || ($e['status_active'] ?? false))
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-red-500/12 text-red-400 border border-red-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                    Inactivo
                                </span>
                            @endif
                            @if($e['gender'] ?? false)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20">
                                    {{ strtoupper(substr($e['gender'], 0, 1)) === 'M' ? 'Masculino' : (strtoupper(substr($e['gender'], 0, 1)) === 'F' ? 'Femenino' : $e['gender']) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                                {{-- ═══ Cards: Información del Estudiante ═══ --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Personal Information --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-sky-400 mb-4 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Información Personal
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                <span class="text-gray-500 text-xs">Nombre</span>
                                <span class="text-gray-200 font-medium text-right">{{ $e['name'] ?? '—' }} {{ $e['lastname'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                <span class="text-gray-500 text-xs">Cédula</span>
                                <span class="text-gray-200 font-medium font-mono">{{ $e['ci_estudiant'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                <span class="text-gray-500 text-xs">Género</span>
                                <span class="text-gray-200 font-medium">{{ $e['gender'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                <span class="text-gray-500 text-xs">Fecha Nac.</span>
                                <span class="text-gray-200 font-medium">
                                    {{ isset($e['date_birth']) && $e['date_birth'] && $e['date_birth'] !== '0000-00-00'
                                        ? \Carbon\Carbon::parse($e['date_birth'])->format('d/m/Y')
                                        : '—' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-xs">Edad</span>
                                <span class="text-gray-200 font-medium">
                                    {{ isset($e['date_birth']) && $e['date_birth'] && $e['date_birth'] !== '0000-00-00'
                                        ? \Carbon\Carbon::parse($e['date_birth'])->age . ' años'
                                        : '—' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Information --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-4 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Contacto
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                <span class="text-gray-500 text-xs">Correo</span>
                                <span class="text-gray-200 font-medium text-right break-all max-w-[180px]">{{ $e['email'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                <span class="text-gray-500 text-xs">Celular</span>
                                <span class="text-gray-200 font-medium">{{ $e['cellphone'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                <span class="text-gray-500 text-xs">Teléfono</span>
                                <span class="text-gray-200 font-medium">{{ $e['phone'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-gray-500 text-xs shrink-0">Dirección</span>
                                <span class="text-gray-200 font-medium text-right break-all max-w-[200px] leading-relaxed">{{ $e['dir_address'] ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Birth Place --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-amber-400 mb-4 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Lugar de Nacimiento
                        </h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                <span class="text-gray-500 text-xs">País</span>
                                <span class="text-gray-200 font-medium">{{ $e['country_birth'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                <span class="text-gray-500 text-xs">Estado</span>
                                <span class="text-gray-200 font-medium">{{ $e['state_birth'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-white/5">
                                <span class="text-gray-500 text-xs">Municipio</span>
                                <span class="text-gray-200 font-medium">{{ $e['town_hall_birth'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-xs">Ciudad</span>
                                <span class="text-gray-200 font-medium">{{ $e['city_birth'] ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Representant --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-5 md:col-span-3">
                        <h3 class="text-[10px] font-bold uppercase tracking-widest text-purple-400 mb-4 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Representante
                        </h3>
                        @if($r)
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <span class="block text-[10px] text-gray-500 mb-1">Nombre</span>
                                    <span class="text-gray-200 font-medium text-sm">{{ $r['name'] ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-gray-500 mb-1">Cédula</span>
                                    <span class="text-gray-200 font-medium font-mono text-sm">{{ $r['ci_representant'] ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-gray-500 mb-1">Teléfono</span>
                                    <span class="text-gray-200 font-medium text-sm">{{ $r['cellphone'] ?? ($r['phone'] ?? '—') }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-gray-500 mb-1">Correo</span>
                                    <span class="text-gray-200 font-medium text-sm break-all">{{ $r['email'] ?? '—' }}</span>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-6 text-gray-500">
                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs">Sin representante registrado</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ═══ Inscription Details ═══ --}}
                <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                    <h3 class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-4 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Detalles de la Inscripción
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div class="bg-white/[0.03] rounded-lg p-3 border border-white/5">
                            <span class="block text-[10px] text-gray-500 mb-1">Plan de Estudio</span>
                            <span class="font-bold text-white text-xs">{{ $pest['name'] ?? '—' }}</span>
                        </div>
                        <div class="bg-white/[0.03] rounded-lg p-3 border border-white/5">
                            <span class="block text-[10px] text-gray-500 mb-1">Grado</span>
                            <span class="font-bold text-white text-xs">{{ $g['name'] ?? '—' }}</span>
                        </div>
                        <div class="bg-white/[0.03] rounded-lg p-3 border border-white/5">
                            <span class="block text-[10px] text-gray-500 mb-1">Sección</span>
                            <span class="font-bold text-white text-xs">{{ $sc['name'] ?? '—' }}</span>
                        </div>
                        <div class="bg-white/[0.03] rounded-lg p-3 border border-white/5">
                            <span class="block text-[10px] text-gray-500 mb-1">Tipo</span>
                            <span class="font-bold text-amber-400 text-xs">{{ $s['tipo']['name'] ?? '—' }}</span>
                        </div>
                        <div class="bg-white/[0.03] rounded-lg p-3 border border-white/5">
                            <span class="block text-[10px] text-gray-500 mb-1">Escolaridad</span>
                            <span class="font-bold text-white text-xs">{{ $s['escolaridad']['name'] ?? '—' }}</span>
                        </div>
                        <div class="bg-white/[0.03] rounded-lg p-3 border border-white/5">
                            <span class="block text-[10px] text-gray-500 mb-1">Programación</span>
                            <span class="font-bold text-white text-xs">{{ $s['programacion']['name'] ?? '—' }}</span>
                        </div>
                        <div class="bg-white/[0.03] rounded-lg p-3 border border-white/5">
                            <span class="block text-[10px] text-gray-500 mb-1">Grupo Estable</span>
                            <span class="font-bold text-white text-xs">{{ $s['grupo_estable']['name'] ?? 'Ninguno' }}</span>
                        </div>
                        <div class="bg-white/[0.03] rounded-lg p-3 border border-white/5">
                            <span class="block text-[10px] text-gray-500 mb-1">ID Inscripción</span>
                            <span class="font-bold text-white text-xs font-mono">#{{ $s['id'] ?? '—' }}</span>
                        </div>
                    </div>
                    @if(($s['observations'] ?? false))
                        <div class="mt-4 pt-3 border-t border-white/5">
                            <span class="block text-[10px] text-gray-500 mb-1">Observaciones</span>
                            <p class="text-sm text-gray-300 leading-relaxed">{{ $s['observations'] }}</p>
                        </div>
                    @endif
                </div>

            @else
                <div class="py-12 text-center">
                    <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-500 font-medium">No se pudo cargar la información del estudiante.</p>
                </div>
            @endif
        </div>

        <div class="flex justify-end px-6 pb-4">
            <x-button flat label="Cerrar" wire:click="closeViewStudent" />
        </div>
    </x-modal>

    {{-- ================================================================ --}}
    {{-- MODE: FORMULARIO (Crear/Editar)                                  --}}
    {{-- ================================================================ --}}
    @elseif($modeForm)

    <div class="max-w-4xl mx-auto">

        {{-- Header --}}
        <div class="mb-8 flex items-center gap-3">
            <button type="button" wire:click="cancelForm"
                class="p-2 bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 text-gray-400 hover:text-gray-300 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </button>
            <div>
                <h1 class="text-lg font-extrabold text-white">
                    {{ $isEditing ? 'Editar' : 'Nueva' }} Inscripción
                </h1>
                <p class="text-emerald-400 font-medium text-sm">
                    {{ $isEditing ? 'Actualiza los datos de la inscripción.' : 'Registra un estudiante en una sección.' }}
                </p>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- WIZARD (solo para creación)                                  --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @if(!$isEditing)

        {{-- ─── Step indicator ─── --}}
        @php
            $steps = [
                1 => ['label' => 'Estudiante', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                2 => ['label' => 'Sección',    'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                3 => ['label' => 'Detalles',   'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'],
                4 => ['label' => 'Confirmar',  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp

        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg">
            {{-- Step progress bar --}}
            <div class="px-8 pt-6 pb-4 border-b border-white/5">
                <div class="flex items-center justify-between max-w-2xl mx-auto">
                    @foreach($steps as $num => $s)
                        <div class="flex items-center">
                            {{-- Circle --}}
                            <button type="button"
                                @if($num < $wizardStep)
                                    wire:click="goToStep({{ $num }})"
                                @endif
                                class="flex items-center gap-2 transition-all duration-300
                                    {{ $num < $wizardStep ? 'cursor-pointer' : 'cursor-default' }}">
                                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-xs font-bold border-2 transition-all duration-300
                                    {{ $num < $wizardStep ? 'bg-emerald-500 border-emerald-500 text-white' : '' }}
                                    {{ $num === $wizardStep ? 'border-emerald-400 text-emerald-400 bg-emerald-500/10' : '' }}
                                    {{ $num > $wizardStep ? 'border-white/10 text-gray-500 bg-white/5' : '' }}">
                                    @if($num < $wizardStep)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @else
                                        {{ $num }}
                                    @endif
                                </span>
                                <span class="text-xs font-bold hidden sm:inline transition-colors duration-300
                                    {{ $num <= $wizardStep ? 'text-gray-200' : 'text-gray-500' }}">
                                    {{ $s['label'] }}
                                </span>
                            </button>
                            {{-- Connector line --}}
                            @if($num < count($steps))
                                <div class="w-12 sm:w-20 h-0.5 mx-2 sm:mx-4 rounded transition-colors duration-300
                                    {{ $num < $wizardStep ? 'bg-emerald-500/50' : 'bg-white/5' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ─── Step content ─── --}}
            <div class="p-8">

                {{-- ═══ STEP 1: Seleccionar Estudiante ═══ --}}
                @if($wizardStep === 1)
                <div x-data>
                    <div class="mb-6">
                        <h2 class="text-base font-extrabold text-white">Seleccionar Estudiante</h2>
                        <p class="text-sm text-gray-400 mt-1">Busca al estudiante por nombre, apellido o cédula de identidad.</p>
                    </div>

                    {{-- Search input --}}
                    <div class="relative mb-6">
                        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="searchStudent"
                            placeholder="Buscar por nombre, apellido o CI..."
                            class="w-full bg-white/5 border border-white/10 text-gray-200 rounded-lg pl-10 pr-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @if($searchStudent)
                            <button type="button" wire:click="$set('searchStudent', '')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        @endif
                    </div>

                    @error('estudiant_id')
                        <div class="mb-4 flex items-center gap-2 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-4 py-2.5">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror

                    {{-- Selected student badge --}}
                    @if($selectedStudentData)
                        <div class="mb-6 flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl px-5 py-4">
                            <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold text-lg">
                                {{ strtoupper(substr($selectedStudentData['name'], 0, 1)) }}{{ strtoupper(substr($selectedStudentData['lastname'], 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-white truncate">{{ $selectedStudentData['name'] }} {{ $selectedStudentData['lastname'] }}</p>
                                <div class="flex items-center gap-2 mt-0.5 text-xs text-gray-400">
                                    <span class="font-mono">{{ $selectedStudentData['ci_estudiant'] }}</span>
                                    @if($selectedStudentData['representant'])
                                        <span class="text-gray-500">·</span>
                                        <span>{{ $selectedStudentData['representant']['name'] ?? '' }}</span>
                                    @endif
                                </div>
                            </div>
                            <button type="button" wire:click="$set('selectedStudentData', null); $set('estudiant_id', null)"
                                class="text-gray-500 hover:text-red-400 transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    {{-- Search results --}}
                    @if($studentSearchResults && !$selectedStudentData)
                        <div class="space-y-2 max-h-80 overflow-y-auto pr-1 custom-scrollbar">
                            @foreach($studentSearchResults as $s)
                                <button type="button"
                                    wire:click="selectStudent({{ $s['id'] }})"
                                    class="w-full text-left flex items-center gap-4 px-4 py-3 rounded-xl border transition-all duration-200
                                        {{ $estudiant_id === $s['id']
                                            ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400'
                                            : 'bg-white/5 border-white/5 text-gray-300 hover:border-emerald-500/20 hover:bg-emerald-500/5' }}">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                        {{ $estudiant_id === $s['id'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-white/10 text-gray-400' }}">
                                        {{ strtoupper(substr($s['name'], 0, 1)) }}{{ strtoupper(substr($s['lastname'], 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold truncate">{{ $s['name'] }} {{ $s['lastname'] }}</p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[11px] font-mono text-gray-500 bg-white/5 px-1.5 py-0.5 rounded">{{ $s['ci_estudiant'] }}</span>
                                            @if(($s['representant']['name'] ?? ''))
                                                <span class="text-[11px] text-gray-600 truncate">{{ $s['representant']['name'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 shrink-0 {{ $estudiant_id === $s['id'] ? 'text-emerald-400' : 'text-transparent' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            @endforeach
                        </div>

                        <p class="text-[11px] text-gray-600 mt-3 text-center">
                            Mostrando hasta 30 resultados — refina tu búsqueda si es necesario.
                        </p>
                    @elseif(strlen($searchStudent) >= 2 && !$studentSearchResults && !$selectedStudentData)
                        <div class="text-center py-10">
                            <svg class="w-12 h-12 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-gray-500">No se encontraron estudiantes con "{{ $searchStudent }}"</p>
                        </div>
                    @elseif(!$selectedStudentData)
                        <div class="text-center py-10">
                            <svg class="w-14 h-14 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <p class="text-sm text-gray-500 font-medium mb-1">Busca un estudiante para inscribir</p>
                            <p class="text-xs text-gray-600">Escribe al menos 2 caracteres para comenzar la búsqueda.</p>
                        </div>
                    @endif
                </div>
                @endif

                {{-- ═══ STEP 2: Sección ═══ --}}
                @if($wizardStep === 2)
                <div>
                    <div class="mb-6">
                        <h2 class="text-base font-extrabold text-white">Asignar Sección</h2>
                        <p class="text-sm text-gray-400 mt-1">Selecciona el plan de estudio, grado y sección para el estudiante.</p>
                    </div>

                    {{-- Selected student summary --}}
                    @if($selectedStudentData)
                        <div class="flex items-center gap-3 bg-white/5 border border-white/10 rounded-lg px-4 py-3 mb-6">
                            <span class="text-xs text-gray-500">Estudiante:</span>
                            <span class="text-sm font-bold text-white">{{ $selectedStudentData['name'] }} {{ $selectedStudentData['lastname'] }}</span>
                            <span class="text-[11px] font-mono text-gray-500">CI: {{ $selectedStudentData['ci_estudiant'] }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                                Plan de Estudio <span class="text-red-400">*</span>
                            </label>
                            <select wire:model.live="pestudio_id"
                                class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                                <option value="">Seleccionar...</option>
                                @foreach($pestudiosForm as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Grado</label>
                            <select wire:model.live="grado_id"
                                class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                                <option value="">Seleccionar...</option>
                                @foreach($gradosForm as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                                Sección <span class="text-red-400">*</span>
                            </label>
                            <select wire:model="seccion_id"
                                class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                                <option value="">Seleccionar...</option>
                                @foreach($seccionesForm as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('seccion_id')
                                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

                {{-- ═══ STEP 3: Detalles ═══ --}}
                @if($wizardStep === 3)
                <div>
                    <div class="mb-6">
                        <h2 class="text-base font-extrabold text-white">Detalles de la Inscripción</h2>
                        <p class="text-sm text-gray-400 mt-1">Configura el tipo, escolaridad y programación.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                                Tipo de Inscripción <span class="text-red-400">*</span>
                            </label>
                            <select wire:model="tipo_id"
                                class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                                <option value="">Seleccionar...</option>
                                @foreach($tiposForm as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('tipo_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                                Escolaridad <span class="text-red-400">*</span>
                            </label>
                            <select wire:model="escolaridad_id"
                                class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                                <option value="">Seleccionar...</option>
                                @foreach($escolaridadsForm as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('escolaridad_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                                Programación <span class="text-red-400">*</span>
                            </label>
                            <select wire:model="programacion_id"
                                class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                                <option value="">Seleccionar...</option>
                                @foreach($programacionsForm as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('programacion_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Grupo Estable</label>
                        <select wire:model="grupo_estable_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Ninguno</option>
                            @foreach($grupoEstablesForm as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('grupo_estable_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Observaciones</label>
                        <textarea wire:model="observations" rows="3" maxlength="250"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600 resize-none"
                            placeholder="Observaciones adicionales (opcional)"></textarea>
                        @error('observations') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif

                {{-- ═══ STEP 4: Confirmar ═══ --}}
                @if($wizardStep === 4)
                <div>
                    <div class="mb-6">
                        <h2 class="text-base font-extrabold text-white">Confirmar Inscripción</h2>
                        <p class="text-sm text-gray-400 mt-1">Revisa los datos antes de guardar la inscripción.</p>
                    </div>

                    <div class="space-y-3">
                        {{-- Student card --}}
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                            <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-3">Estudiante</h3>
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold text-lg">
                                    {{ strtoupper(substr(($selectedStudentData['name'] ?? '?'), 0, 1)) }}{{ strtoupper(substr(($selectedStudentData['lastname'] ?? '?'), 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $selectedStudentData['name'] ?? '' }} {{ $selectedStudentData['lastname'] ?? '' }}</p>
                                    <p class="text-xs text-gray-400 font-mono">CI: {{ $selectedStudentData['ci_estudiant'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Section card --}}
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                            <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-3">Sección Asignada</h3>
                            <p class="text-sm font-bold text-white">
                                @php
                                    $selSeccion = \App\Models\app\Academy\Seccion::with('grado.pestudio')->find($seccion_id);
                                @endphp
                                {{ $selSeccion?->grado?->pestudio?->name ?? '—' }}
                                · {{ $selSeccion?->grado?->name ?? '—' }}
                                · Sección {{ $selSeccion?->name ?? '—' }}
                            </p>
                        </div>

                        {{-- Details card --}}
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                            <h3 class="text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-3">Detalles</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="block text-[10px] text-gray-500 mb-0.5">Tipo</span>
                                    <span class="font-bold text-white">{{ \App\Models\app\Academy\Tinscripcion::find($tipo_id)?->name ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-gray-500 mb-0.5">Escolaridad</span>
                                    <span class="font-bold text-white">{{ \App\Models\app\Academy\Escolaridad::find($escolaridad_id)?->name ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-gray-500 mb-0.5">Programación</span>
                                    <span class="font-bold text-white">{{ \App\Models\app\Academy\Programacion::find($programacion_id)?->name ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="block text-[10px] text-gray-500 mb-0.5">Grupo Estable</span>
                                    <span class="font-bold text-white">{{ $grupo_estable_id ? \App\Models\app\Academy\GrupoEstable::find($grupo_estable_id)?->name : 'Ninguno' }}</span>
                                </div>
                            </div>
                            @if($observations)
                                <div class="mt-4 pt-3 border-t border-white/5">
                                    <span class="block text-[10px] text-gray-500 mb-0.5">Observaciones</span>
                                    <p class="text-sm text-gray-300">{{ $observations }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                {{-- ─── Navigation Buttons ─── --}}
                <div class="flex items-center justify-between pt-6 mt-6 border-t border-white/5">
                    <div>
                        @if($wizardStep > 1)
                            <button type="button" wire:click="prevStep"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-bold text-gray-400 hover:text-gray-300 bg-white/5 hover:bg-white/10 border border-white/5 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Anterior
                            </button>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="cancelForm"
                            class="px-5 py-2.5 rounded-lg text-sm font-bold text-gray-500 hover:text-gray-300 transition-colors">
                            Cancelar
                        </button>
                        @if($wizardStep < 4)
                            <button type="button" wire:click="nextStep"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-all duration-200">
                                Siguiente
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </button>
                        @else
                            <button type="button" wire:click="save" wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-8 py-2.5 rounded-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-all duration-200 disabled:opacity-50">
                                <span wire:loading.remove wire:target="save">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </span>
                                <span wire:loading wire:target="save">Guardando...</span>
                                <span wire:loading.remove wire:target="save">Confirmar & Guardar</span>
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- EDIT MODE: formulario simple (sin wizard)                   --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        @else

        <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-8">
            <form wire:submit="save" class="space-y-6">
                {{-- Fila: Pestudio + Grado + Seccion (cascada) --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                            Plan de Estudio <span class="text-red-400">*</span>
                        </label>
                        <select wire:model.live="pestudio_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccionar...</option>
                            @foreach($pestudiosForm as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('pestudio_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Grado</label>
                        <select wire:model.live="grado_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccionar...</option>
                            @foreach($gradosForm as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('grado_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                            Sección <span class="text-red-400">*</span>
                        </label>
                        <select wire:model="seccion_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccionar...</option>
                            @foreach($seccionesForm as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('seccion_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Fila: Estudiante + Tipo --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                            Estudiante <span class="text-red-400">*</span>
                        </label>
                        <select wire:model="estudiant_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccionar estudiante...</option>
                            @foreach($estudiantsList as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('estudiant_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                            Tipo de Inscripción <span class="text-red-400">*</span>
                        </label>
                        <select wire:model="tipo_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccionar...</option>
                            @foreach($tiposForm as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('tipo_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Fila: Escolaridad + Programacion + Grupo Estable --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                            Escolaridad <span class="text-red-400">*</span>
                        </label>
                        <select wire:model="escolaridad_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccionar...</option>
                            @foreach($escolaridadsForm as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('escolaridad_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">
                            Programación <span class="text-red-400">*</span>
                        </label>
                        <select wire:model="programacion_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccionar...</option>
                            @foreach($programacionsForm as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('programacion_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Grupo Estable</label>
                        <select wire:model="grupo_estable_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Ninguno</option>
                            @foreach($grupoEstablesForm as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('grupo_estable_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Observaciones --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Observaciones</label>
                    <textarea wire:model="observations" rows="3" maxlength="250"
                        class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600 resize-none"
                        placeholder="Observaciones adicionales (opcional)"></textarea>
                    @error('observations') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-white/5">
                    <button type="button" wire:click="cancelForm"
                        class="px-5 py-2.5 rounded-lg text-sm font-bold text-gray-400 hover:text-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-all duration-200">
                        Actualizar Inscripción
                    </button>
                </div>
            </form>
        </div>

        @endif {{-- end wizard vs edit --}}
    </div>

    @endif
</div>
