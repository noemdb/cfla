<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Secciones</h1>
            <p class="text-emerald-400 font-medium">Gestión de secciones (aulas) por grado académico.</p>
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
                Nueva Sección
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

    <!-- Filters -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-5 rounded-lg mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre o descripción..."
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Grado</label>
                <select wire:model.live="filter_grado"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($grados as $id => $name)
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
            <div class="flex items-end">
                <button wire:click="$refresh"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Filtrar
                </button>
            </div>
        </div>
    </div>

    {{-- Mode Toggle: Grid / Table (crud-mode-toggle pattern) --}}
    <div class="flex items-center justify-end mb-4" x-data="{ mode: localStorage.getItem('seccions-view-mode') || 'table' }"
         x-init="$watch('mode', val => {
             localStorage.setItem('seccions-view-mode', val);
             window.dispatchEvent(new CustomEvent('seccions-view-mode-changed', { detail: { mode: val } }))
         })">
        <div class="inline-flex items-center bg-gray-900/40 border border-white/5 rounded-lg p-0.5 gap-0.5">
            <button @click="mode = 'grid'"
                :class="mode === 'grid' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-transparent text-gray-500 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-transparent transition-all duration-200 text-[11px] font-bold"
                title="Vista Grid">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="hidden sm:inline">Grid</span>
            </button>
            <button @click="mode = 'table'"
                :class="mode === 'table' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-transparent text-gray-500 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md border border-transparent transition-all duration-200 text-[11px] font-bold"
                title="Vista Tabla">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="hidden sm:inline">Tabla</span>
            </button>
        </div>
    </div>

    {{-- Mode Container: Table / Grid --}}
    <div x-cloak
         x-data="{ mode: localStorage.getItem('seccions-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('seccions-view-mode')) localStorage.setItem('seccions-view-mode', 'table') }"
         x-on:seccions-view-mode-changed.window="mode = $event.detail.mode">

        {{-- ── TABLE MODE ── --}}
        <div x-show="mode === 'table'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
                <div class="overflow-x-auto [&::-webkit-scrollbar]:h-1.5" style="scrollbar-width: thin;">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/5">
                                <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">#</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Secc.</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Descripción</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden md:table-cell">Grado</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Plan</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Capac.</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Inscrip.</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                                <th class="text-right px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($seccions as $seccion)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-5 py-2 text-sm text-gray-400 font-mono">{{ $seccion->id }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-lg font-bold text-white">{{ $seccion->name }}</span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="text-sm text-gray-200 font-medium">{{ $seccion->description ?: '—' }}</span>
                                        @if($seccion->observation)
                                            <span class="block text-[10px] text-gray-500 mt-0.5">{{ \Illuminate\Support\Str::limit($seccion->observation, 40) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-400 hidden md:table-cell">
                                        {{ $seccion->grado?->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-400 hidden lg:table-cell">
                                        {{ $seccion->grado?->pestudio?->code ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] h-8 rounded-lg bg-white/5 text-gray-300 text-sm font-bold px-2">
                                            {{ $seccion->amount_student ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-center hidden lg:table-cell">
                                        <span class="text-sm {{ $seccion->inscripcions_count > 0 ? 'text-cyan-400' : 'text-gray-500' }}">
                                            {{ $seccion->inscripcions_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        @if($seccion->status_active === 'true')
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-red-400 bg-red-500/10 px-2.5 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-2 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" wire:click="showPreview({{ $seccion->id }})"
                                                class="p-2 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200"
                                                title="Vista previa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="edit({{ $seccion->id }})"
                                                class="p-2 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                                title="Editar sección">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            @php $delDisabled = ($seccion->inscripcions_count > 0 || $seccion->profesor_guias_count > 0); @endphp
                                            <button type="button" wire:click="confirmDelete({{ $seccion->id }})"
                                                class="p-2 rounded-lg border transition-all duration-200
                                                    {{ $delDisabled
                                                        ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed border-transparent'
                                                        : 'bg-white/5 hover:bg-red-500/10 border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400' }}"
                                                title="Eliminar sección"
                                                @if($delDisabled) disabled @endif>
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
                                            <p class="text-gray-500 font-medium mb-1">No hay secciones registradas</p>
                                            <p class="text-gray-600 text-sm">Crea la primera sección usando el botón "Nueva Sección".</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($seccions->hasPages())
                    <x-pagination-wrapper :paginator="$seccions" />
                @endif
            </div>
        </div>

        {{-- ── GRID MODE (Bento-Grid) ── --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="bg-gray-900/60 border border-white/5 rounded-2xl p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @forelse($seccions as $seccion)
                        <div class="rounded-2xl border border-white/5 bg-gray-900 hover:border-emerald-500/30 transition-all duration-200 group flex flex-col overflow-hidden min-h-[280px]">

                            {{-- Header: Name + Grado badge + Status --}}
                            <div class="flex items-start justify-between px-4 pt-4 pb-3 border-b border-white/5 gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-bold text-white truncate" title="Sección {{ $seccion->name }}">Sección {{ $seccion->name }}</h3>
                                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                        @if($seccion->grado)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/12 text-purple-400 border border-purple-500/20">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                </svg>
                                                {{ $seccion->grado->code }}
                                            </span>
                                            <span class="text-[9px] text-gray-500 truncate max-w-[100px]">{{ $seccion->grado->pestudio?->code ?? '' }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if($seccion->status_active === 'true')
                                    <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-500/12 text-red-400 border border-red-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </div>

                            {{-- Body: Description, Capacity, Observations --}}
                            <div class="px-4 py-3 space-y-2.5 flex-1">
                                @if($seccion->description)
                                    <div class="flex items-start gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span class="text-gray-500 line-clamp-2 leading-relaxed">{{ $seccion->description }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2 text-[11px]">
                                    <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span class="text-gray-400">Capacidad: <span class="font-bold text-white">{{ $seccion->amount_student ?? '—' }}</span></span>
                                    @if($seccion->status_inscription_affects === 'true')
                                        <span class="text-gray-600">·</span>
                                        <span class="text-gray-500">Cont. inscripción</span>
                                    @endif
                                </div>
                                @if($seccion->observation)
                                    <div class="flex items-start gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span class="text-gray-500 line-clamp-2 leading-relaxed">{{ $seccion->observation }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Footer Stats: Inscripciones + Prof. Guías --}}
                            <div class="px-4 py-2.5 border-t border-white/5 bg-white/[0.03] space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span @class([
                                            'inline-flex items-center justify-center w-6 h-6 rounded-lg text-[10px] font-bold',
                                            'bg-blue-500/12 text-blue-400' => $seccion->inscripcions_count > 0,
                                            'bg-gray-500/12 text-gray-500' => $seccion->inscripcions_count === 0,
                                        ])>
                                            {{ $seccion->inscripcions_count }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 font-medium">inscripciones</span>
                                    </div>
                                    <span class="text-[9px] text-gray-600">
                                        <span class="font-bold">{{ $seccion->profesor_guias_count }}</span> prof. guía
                                    </span>
                                </div>
                                <div class="text-[9px] text-gray-600 truncate max-w-full" title="{{ $seccion->grado?->full_name ?? '' }}">
                                    {{ $seccion->grado?->full_name ?? '' }}
                                </div>
                            </div>

                            {{-- Actions: btnGroup --}}
                            <div class="px-4 pb-4 pt-2.5 border-t border-white/5 flex items-center gap-2"
                                 x-data="{ actionsOpen: false }"
                                 @click.away="actionsOpen = false">

                                {{-- Primary: Vista previa (siempre visible) --}}
                                <button type="button" wire:click="showPreview({{ $seccion->id }})"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-bold bg-cyan-500/12 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver Detalle
                                </button>

                                {{-- Desktop group --}}
                                <div class="hidden sm:flex items-center gap-2">
                                    <button type="button" wire:click="edit({{ $seccion->id }})"
                                        class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-xs font-bold bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200"
                                        title="Editar">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @php $delDisabled = ($seccion->inscripcions_count > 0 || $seccion->profesor_guias_count > 0); @endphp
                                    <button type="button" wire:click="confirmDelete({{ $seccion->id }})"
                                        class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-xs font-bold transition-all duration-200
                                            {{ $delDisabled
                                                ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed border border-transparent'
                                                : 'bg-red-500/12 text-red-400 hover:bg-red-500/20 border border-red-500/20' }}"
                                        title="Eliminar"
                                        @if($delDisabled) disabled @endif>
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Mobile dropdown --}}
                                <div class="relative sm:hidden">
                                    <button @click="actionsOpen = !actionsOpen"
                                        class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/30 hover:bg-gray-200 dark:hover:bg-slate-600/50 border border-gray-200 dark:border-slate-600/30 transition-all"
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
                                        <button wire:click="edit({{ $seccion->id }})"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </button>
                                        @if(!$delDisabled)
                                            <button wire:click="confirmDelete({{ $seccion->id }})"
                                                class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                                <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Eliminar
                                            </button>
                                        @else
                                            <span class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-500 cursor-not-allowed">
                                                <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Eliminar
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 font-medium mb-1">No hay secciones registradas</p>
                            <p class="text-gray-600 text-sm">Crea la primera sección usando el botón "Nueva Sección".</p>
                        </div>
                    @endforelse
                </div>

                @if($seccions->hasPages())
                    <div class="mt-6">
                        <x-pagination-wrapper :paginator="$seccions" />
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ===== MODAL: Confirmar Eliminación ===== -->
    <x-modal title="Eliminar Sección" blur="lg" wire:model="confirmDeleteId" max-width="md" x-on:close="confirmDeleteId = null" persistent>
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar esta sección?</h3>
            <p class="text-sm text-gray-400 mb-6">Esta acción eliminará la sección. Solo se puede eliminar si no tiene inscripciones ni profesores guía asociados.</p>
            <div class="flex justify-center gap-3">
                <x-button flat label="Cancelar" x-on:click="confirmDeleteId = null" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Vista Previa (x-preview-modal) ===== -->
    <x-preview-modal
        wire:model="previewMode"
        title="Sección {{ $previewSeccion->name ?? '' }}"
        subtitle="Grado: {{ $previewSeccion?->grado?->full_name ?? '—' }}"
        x-on:close="$wire.closePreview()"
    >
        @if($previewSeccion)
        {{-- Badges en el header debajo del título --}}
        <x-slot:header>
            <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-500/12 text-purple-400 border border-purple-500/20">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Secc. {{ $previewSeccion->name }}
                </span>
                @if($previewSeccion->grado)
                    <span class="text-[10px] text-gray-500 font-mono">{{ $previewSeccion->grado->code }}</span>
                @endif
                @if($previewSeccion->status_active === 'true')
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Activo
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-500/12 text-red-400 border border-red-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                        Inactivo
                    </span>
                @endif
            </div>
        </x-slot:header>

        {{-- Grid: Identificación + Grado --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Sección</p>
                <p class="text-lg text-white font-bold">{{ $previewSeccion->name }}</p>
            </div>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Estado</p>
                @if($previewSeccion->status_active === 'true')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Activo
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-red-500/12 text-red-400 border border-red-500/20">Inactivo</span>
                @endif
            </div>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Grado</p>
                <p class="text-sm text-gray-300">{{ $previewSeccion->grado?->full_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Plan de Estudio</p>
                <p class="text-sm text-gray-300">{{ $previewSeccion->grado?->pestudio?->full_name ?? '—' }}</p>
            </div>
        </div>

        {{-- Estadísticas --}}
        <div class="border-t border-white/5 pt-4">
            <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-3">Estadísticas</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <div class="bg-white/[0.03] border border-white/5 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-emerald-400">{{ $previewSeccion->amount_student ?? 0 }}</p>
                    <p class="text-[9px] text-gray-500 font-medium uppercase">Capacidad</p>
                </div>
                <div class="bg-white/[0.03] border border-white/5 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-cyan-400">{{ $previewSeccion->inscripcions_count }}</p>
                    <p class="text-[9px] text-gray-500 font-medium uppercase">Inscripciones</p>
                </div>
                <div class="bg-white/[0.03] border border-white/5 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-violet-400">{{ $previewSeccion->profesor_guias_count }}</p>
                    <p class="text-[9px] text-gray-500 font-medium uppercase">Prof. Guía</p>
                </div>
            </div>
        </div>

        {{-- Configuración --}}
        <div class="border-t border-white/5 pt-4">
            <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-2">Configuración</p>
            <div class="flex flex-wrap gap-1.5">
                <span @class([
                    'inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-medium border',
                    'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' => $previewSeccion->status_inscription_affects === 'true',
                    'bg-gray-500/8 text-gray-600 border-white/5' => $previewSeccion->status_inscription_affects !== 'true',
                ])>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($previewSeccion->status_inscription_affects === 'true')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        @endif
                    </svg>
                    Contabiliza inscripción
                </span>
            </div>
        </div>

        {{-- Observaciones + Comentario --}}
        <div class="border-t border-white/5 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Observaciones</p>
                <p class="text-sm text-gray-400 {{ $previewSeccion->observation ? '' : 'italic text-gray-600' }}">{{ $previewSeccion->observation ?? 'Sin observaciones' }}</p>
            </div>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Comentario Final</p>
                <p class="text-sm text-gray-400 {{ $previewSeccion->comment_final ? '' : 'italic text-gray-600' }}">{{ $previewSeccion->comment_final ?? 'Sin comentario final' }}</p>
            </div>
        </div>

        {{-- Descripción --}}
        @if($previewSeccion->description)
        <div class="border-t border-white/5 pt-4">
            <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Descripción</p>
            <p class="text-sm text-gray-400">{{ $previewSeccion->description }}</p>
        </div>
        @endif

        {{-- Footer --}}
        <x-slot:footer>
            <span class="text-[9px] text-gray-600">ID: {{ $previewSeccion->id }}</span>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="edit({{ $previewSeccion->id }})"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Editar
                </button>
                <button type="button" wire:click="closePreview"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium text-gray-400 hover:text-white transition-colors">
                    Cerrar
                </button>
            </div>
        </x-slot:footer>
        @endif
    </x-preview-modal>

    <!-- ===== MODAL: Formulario Crear/Editar ===== -->
    <x-modal-card title="{{ $isEditing ? 'Editar Sección' : 'Nueva Sección' }}" blur="lg" wire:model="modeForm" max-width="3xl" persistent>
        <div class="space-y-6">

            {{-- Errores globales de validación --}}
            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-red-300 text-sm font-bold mb-1">Hay errores en el formulario</p>
                            <ul class="text-red-400 text-xs space-y-0.5">
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Sección 1: Datos de la Sección --}}
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Datos de la Sección
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Grado *</label>
                        <select wire:model="grado_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($grados as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('grado_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nombre * (1 carácter)</label>
                        <input type="text" wire:model="name" placeholder="Ej: A" maxlength="1" style="text-transform:uppercase"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Capacidad</label>
                        <select wire:model="amount_student"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            @for($i = 1; $i <= 50; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Descripción</label>
                        <input type="text" wire:model="description" placeholder="Descripción opcional de la sección"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('description') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Estado</label>
                        <select wire:model="status_active"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="true">Activo</option>
                            <option value="false">Inactivo</option>
                        </select>
                        @error('status_active') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Contabiliza Inscripción</label>
                        <select wire:model="status_inscription_affects"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="true">Sí</option>
                            <option value="false">No</option>
                        </select>
                        @error('status_inscription_affects') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 2: Observaciones --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Observaciones
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Observaciones</label>
                        <input type="text" wire:model="observation" placeholder="Observaciones adicionales"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Comentario Final</label>
                        <textarea wire:model="comment_final" rows="2" placeholder="Resumen o comentario final"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600 resize-none"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button primary label="{{ $isEditing ? 'Actualizar Sección' : 'Guardar Sección' }}" wire:click="save" spinner="save" />
            </div>
        </x-slot>
    </x-modal-card>
</div>
