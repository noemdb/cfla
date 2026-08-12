<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Asignaturas</h1>
            <p class="text-emerald-400 font-medium">Catálogo de asignaturas del pensum académico.</p>
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
                Nueva Asignatura
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre, código..."
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Plan de Estudio</label>
                <select wire:model.live="filter_pestudio"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($pestudios as $id => $name)
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

    {{-- Mode Toggle: Grid / Table --}}
    <div class="flex items-center justify-end mb-4" x-data="{ mode: localStorage.getItem('asignaturas-view-mode') || 'table' }"
         x-init="$watch('mode', val => {
             localStorage.setItem('asignaturas-view-mode', val);
             window.dispatchEvent(new CustomEvent('asignaturas-view-mode-changed', { detail: { mode: val } }))
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

    {{-- Mode Container: Table / Grid --}}
    <div x-cloak
         x-data="{ mode: localStorage.getItem('asignaturas-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('asignaturas-view-mode')) localStorage.setItem('asignaturas-view-mode', 'table') }"
         x-on:asignaturas-view-mode-changed.window="mode = $event.detail.mode">

        {{-- ── TABLE MODE ── --}}
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
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Código</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Abrev.</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden md:table-cell">Plan</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">H.T</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">H.P</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Pensums</th>
                                <th class="text-right px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($asignaturas as $asignatura)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-5 py-2 text-sm text-gray-400 font-mono">{{ $asignatura->id }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-sm font-bold text-white font-mono">{{ $asignatura->code }}</span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs font-mono bg-white/5 text-gray-300 px-2 py-0.5 rounded-md">{{ $asignatura->code_sm }}</span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="text-sm text-gray-200 font-medium">{{ $asignatura->name }}</span>
                                        @if($asignatura->observations)
                                            <span class="block text-[10px] text-gray-500 mt-0.5">{{ \Illuminate\Support\Str::limit($asignatura->observations, 40) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-400 hidden md:table-cell">
                                        {{ $asignatura->pestudio?->code ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-center hidden lg:table-cell">
                                        <span class="text-sm text-gray-300">{{ $asignatura->hour_t_week ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-center hidden lg:table-cell">
                                        <span class="text-sm text-gray-300">{{ $asignatura->hour_p_week ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-center hidden lg:table-cell">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/5 text-gray-300 text-sm font-bold">
                                            {{ $asignatura->pensums_count }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-2 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" wire:click="showPreview({{ $asignatura->id }})"
                                                class="p-2 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200"
                                                title="Vista previa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="edit({{ $asignatura->id }})"
                                                class="p-2 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                                title="Editar asignatura">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button type="button" wire:click="confirmDelete({{ $asignatura->id }})"
                                                class="p-2 bg-white/5 hover:bg-red-500/10 rounded-lg border border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                                                title="Eliminar asignatura">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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
                                            <p class="text-gray-500 font-medium mb-1">No hay asignaturas registradas</p>
                                            <p class="text-gray-600 text-sm">Crea la primera asignatura usando el botón "Nueva Asignatura".</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($asignaturas->hasPages())
                    <x-pagination-wrapper :paginator="$asignaturas" />
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
                    @forelse($asignaturas as $asignatura)
                        <div class="rounded-2xl border border-white/5 bg-gray-900 hover:border-emerald-500/30 transition-all duration-200 group flex flex-col overflow-hidden min-h-[280px]">

                            {{-- Header: Name + Order + Code_sm badge + Tescala + Pestudio --}}
                            <div class="flex items-start justify-between px-4 pt-4 pb-3 border-b border-white/5 gap-3">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-sm font-bold text-white truncate" title="{{ $asignatura->name }}">{{ $asignatura->name }}</h3>
                                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/12 text-purple-400 border border-purple-500/20">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            {{ $asignatura->code_sm }}
                                        </span>
                                        @if($asignatura->order)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-500/12 text-gray-400 border border-white/5">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                </svg>
                                                Ord. {{ $asignatura->order }}
                                            </span>
                                        @endif
                                        @if($asignatura->tescala)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20">
                                                {{ $asignatura->tescala }}
                                            </span>
                                        @endif
                                        @if($asignatura->pestudio)
                                            <span class="text-[9px] text-gray-500 truncate max-w-[100px]">{{ $asignatura->pestudio->code }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Body: Código, HT/HP, Créditos, Observaciones, Prelaciones --}}
                            <div class="px-4 py-3 space-y-2 flex-1">
                                <div class="flex items-center gap-2 text-[11px]">
                                    <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                    </svg>
                                    <span class="text-gray-400 font-mono">{{ $asignatura->code }}</span>
                                </div>
                                @if($asignatura->hour_t_week || $asignatura->hour_p_week)
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-gray-400">{{ $asignatura->hour_t_week ?? 0 }}T / {{ $asignatura->hour_p_week ?? 0 }}P</span>
                                    </div>
                                @endif
                                @if($asignatura->unid_credit || $asignatura->approved_credit_unir)
                                    <div class="flex items-center gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span class="text-gray-400">
                                            @if($asignatura->unid_credit){{ $asignatura->unid_credit }} UC @endif
                                            @if($asignatura->approved_credit_unir)
                                                <span class="text-gray-500">·</span> {{ $asignatura->approved_credit_unir }} créd. aprob.
                                            @endif
                                        </span>
                                    </div>
                                @endif
                                @if($asignatura->observations)
                                    <div class="flex items-start gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span class="text-gray-500 line-clamp-2 leading-relaxed">{{ $asignatura->observations }}</span>
                                    </div>
                                @endif
                                @if($asignatura->prelacions)
                                    <div class="flex items-start gap-2 text-[11px]">
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                        <span class="text-gray-500 line-clamp-2 leading-relaxed">Prelación: {{ $asignatura->prelacions }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Footer: Flags + Pensums count --}}
                            <div class="px-4 py-2.5 border-t border-white/5 bg-white/[0.03] space-y-2">
                                {{-- Flags activos --}}
                                @php
                                    $flags = [
                                        ['key' => 'enable_academic_index', 'label' => 'Índice', 'color' => 'bg-emerald-500/12 text-emerald-400 border-emerald-500/20'],
                                        ['key' => 'enable_lost_regulation', 'label' => 'Pérdida Reg.', 'color' => 'bg-amber-500/12 text-amber-400 border-amber-500/20'],
                                        ['key' => 'enable_official_doc', 'label' => 'Oficial', 'color' => 'bg-blue-500/12 text-blue-400 border-blue-500/20'],
                                        ['key' => 'enable_repairable', 'label' => 'Reparable', 'color' => 'bg-cyan-500/12 text-cyan-400 border-cyan-500/20'],
                                        ['key' => 'enable_grupo_estable', 'label' => 'Grupo Est.', 'color' => 'bg-violet-500/12 text-violet-400 border-violet-500/20'],
                                    ];
                                    $activeFlags = array_filter($flags, fn($f) => data_get($asignatura, $f['key']) === 'true');
                                @endphp
                                @if(count($activeFlags) > 0)
                                    <div class="flex items-center flex-wrap gap-1">
                                        @foreach($activeFlags as $flag)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-bold border {{ $flag['color'] }}">
                                                <svg class="w-2 h-2 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                {{ $flag['label'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span @class([
                                            'inline-flex items-center justify-center w-6 h-6 rounded-lg text-[10px] font-bold',
                                            'bg-blue-500/12 text-blue-400' => $asignatura->pensums_count > 0,
                                            'bg-gray-500/12 text-gray-500' => $asignatura->pensums_count === 0,
                                        ])>
                                            {{ $asignatura->pensums_count }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 font-medium">pensums</span>
                                    </div>
                                    <span class="text-[9px] text-gray-600 truncate max-w-[130px]" title="{{ $asignatura->pestudio?->name ?? '' }}">{{ $asignatura->pestudio?->name ?? '' }}</span>
                                </div>
                            </div>

                            {{-- Actions: btnGroup --}}
                            <div class="px-4 pb-4 pt-2.5 border-t border-white/5 flex items-center gap-2"
                                 x-data="{ actionsOpen: false }"
                                 @click.away="actionsOpen = false">
                                {{-- Primary: Ver Detalle (siempre visible) --}}
                                <button type="button" wire:click="showPreview({{ $asignatura->id }})"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-bold bg-cyan-500/12 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver Detalle
                                </button>

                                {{-- Desktop group --}}
                                <div class="hidden sm:flex items-center gap-2">
                                    <button type="button" wire:click="edit({{ $asignatura->id }})"
                                        class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-xs font-bold bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200"
                                        title="Editar">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $asignatura->id }})"
                                        class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-xs font-bold bg-red-500/12 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200"
                                        title="Eliminar">
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
                                        <button wire:click="edit({{ $asignatura->id }})"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </button>
                                        <button wire:click="confirmDelete({{ $asignatura->id }})"
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
                            <p class="text-gray-500 font-medium mb-1">No hay asignaturas registradas</p>
                            <p class="text-gray-600 text-sm">Crea la primera asignatura usando el botón "Nueva Asignatura".</p>
                        </div>
                    @endforelse
                </div>

                @if($asignaturas->hasPages())
                    <div class="mt-6">
                        <x-pagination-wrapper :paginator="$asignaturas" />
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ===== MODAL: Confirmar Eliminación ===== -->
    <x-modal title="Eliminar Asignatura" blur="lg" wire:model="confirmDeleteId" max-width="md" x-on:close="confirmDeleteId = null" persistent>
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar esta asignatura?</h3>
            <p class="text-sm text-gray-400 mb-6">Esta acción eliminará la asignatura. Solo se puede eliminar si no tiene pensums asociados.</p>
            <div class="flex justify-center gap-3">
                <x-button flat label="Cancelar" x-on:click="confirmDeleteId = null" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Vista Previa (usando x-preview-modal reutilizable) ===== -->
    <x-preview-modal
        wire:model="previewMode"
        title="{{ $previewAsignatura->name ?? '' }}"
        x-on:close="$wire.closePreview()"
    >
        @if($previewAsignatura)
        {{-- Badges en el header debajo del título --}}
        <x-slot:header>
            <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-500/12 text-purple-400 border border-purple-500/20">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    {{ $previewAsignatura->code_sm }}
                </span>
                <span class="text-[10px] text-gray-500 font-mono">{{ $previewAsignatura->code }}</span>
                @if($previewAsignatura->order)
                    <span class="text-[10px] text-gray-600">Ord. {{ $previewAsignatura->order }}</span>
                @endif
                @if($previewAsignatura->tescala)
                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-gray-500/12 text-gray-400 border border-white/5">{{ $previewAsignatura->tescala }}</span>
                @endif
            </div>
        </x-slot:header>

        {{-- Grid: Identificación + Plan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Código</p>
                <p class="text-sm text-white font-bold font-mono">{{ $previewAsignatura->code }}</p>
            </div>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Abreviación</p>
                <p class="text-sm text-white font-mono">{{ $previewAsignatura->code_sm }}</p>
            </div>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Plan de Estudio</p>
                <p class="text-sm text-gray-300">{{ $previewAsignatura->pestudio?->full_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Tipo Escala</p>
                <p class="text-sm text-gray-300">{{ $previewAsignatura->tescala ?? '—' }}</p>
            </div>
        </div>

        {{-- Carga Académica --}}
        <div class="border-t border-white/5 pt-4">
            <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-3">Carga Académica</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <div class="bg-white/[0.03] border border-white/5 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-emerald-400">{{ $previewAsignatura->hour_t_week ?? 0 }}</p>
                    <p class="text-[9px] text-gray-500 font-medium uppercase">H. Teóricas</p>
                </div>
                <div class="bg-white/[0.03] border border-white/5 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-cyan-400">{{ $previewAsignatura->hour_p_week ?? 0 }}</p>
                    <p class="text-[9px] text-gray-500 font-medium uppercase">H. Prácticas</p>
                </div>
                <div class="bg-white/[0.03] border border-white/5 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-amber-400">{{ $previewAsignatura->unid_credit ?? '—' }}</p>
                    <p class="text-[9px] text-gray-500 font-medium uppercase">U. Crédito</p>
                </div>
                <div class="bg-white/[0.03] border border-white/5 rounded-lg p-3 text-center">
                    <p class="text-lg font-bold text-violet-400">{{ $previewAsignatura->approved_credit_unir ?? '—' }}</p>
                    <p class="text-[9px] text-gray-500 font-medium uppercase">Créd. Aprob.</p>
                </div>
            </div>
        </div>

        {{-- Configuración (Flags) --}}
        @php
            $allFlags = [
                ['key' => 'enable_academic_index', 'label' => 'Índice académico'],
                ['key' => 'enable_lost_regulation', 'label' => 'Pérdida reglamento'],
                ['key' => 'enable_official_doc', 'label' => 'Documentos oficiales'],
                ['key' => 'enable_repairable', 'label' => 'Reparable'],
                ['key' => 'enable_grupo_estable', 'label' => 'Grupo estable'],
            ];
        @endphp
        <div class="border-t border-white/5 pt-4">
            <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-2">Configuración</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach($allFlags as $flag)
                    @php $enabled = data_get($previewAsignatura, $flag['key']) === 'true'; @endphp
                    <span @class([
                        'inline-flex items-center gap-1 px-2 py-1 rounded text-[10px] font-medium border',
                        'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' => $enabled,
                        'bg-gray-500/8 text-gray-600 border-white/5' => !$enabled,
                    ])>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if($enabled)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            @endif
                        </svg>
                        {{ $flag['label'] }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Observaciones + Prelaciones --}}
        <div class="border-t border-white/5 pt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Observaciones</p>
                <p class="text-sm text-gray-400 {{ $previewAsignatura->observations ? '' : 'italic text-gray-600' }}">{{ $previewAsignatura->observations ?? 'Sin observaciones' }}</p>
            </div>
            <div>
                <p class="text-[9px] font-bold uppercase tracking-widest text-gray-500 mb-1">Prelaciones</p>
                <p class="text-sm text-gray-400 {{ $previewAsignatura->prelacions ? '' : 'italic text-gray-600' }}">{{ $previewAsignatura->prelacions ?? 'Sin prelaciones' }}</p>
            </div>
        </div>

        {{-- Estadísticas --}}
        <div class="border-t border-white/5 pt-4 flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/12 text-blue-400 text-xs font-bold">{{ $previewAsignatura->pensums_count }}</span>
                <span class="text-xs text-gray-500">pensums asociados</span>
            </div>
        </div>

        {{-- Footer --}}
        <x-slot:footer>
            <span class="text-[9px] text-gray-600">ID: {{ $previewAsignatura->id }}</span>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="edit({{ $previewAsignatura->id }})"
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
    <x-modal-card title="{{ $isEditing ? 'Editar Asignatura' : 'Nueva Asignatura' }}" blur="lg" wire:model="modeForm" max-width="4xl" persistent>
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

            {{-- Sección 1: Datos de la Asignatura --}}
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Datos de la Asignatura
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Plan de Estudio *</label>
                        <select wire:model="pestudio_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($pestudios as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('pestudio_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Tipo Escala</label>
                        <select wire:model="tescala"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Sin asignar</option>
                            @foreach($tescala_options as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Código * (máx 10)</label>
                        <input type="text" wire:model="code" placeholder="Ej: MT-01" maxlength="10" style="text-transform:uppercase"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('code') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Abreviación * (máx 4)</label>
                        <input type="text" wire:model="code_sm" placeholder="Ej: MT" maxlength="4" style="text-transform:uppercase"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('code_sm') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nombre *</label>
                        <input type="text" wire:model="name" placeholder="Nombre completo de la asignatura"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Orden</label>
                        <select wire:model="order"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('order') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Horas Teóricas</label>
                        <select wire:model="hour_t_week"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">—</option>
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Horas Prácticas</label>
                        <select wire:model="hour_p_week"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">—</option>
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Unidades de Crédito</label>
                        <input type="number" wire:model="unid_credit" min="0"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Créditos Aprobados</label>
                        <input type="number" wire:model="approved_credit_unir" min="0"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                </div>
            </div>

            {{-- Sección 2: Flags y Configuración --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                    </svg>
                    Configuración
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @php
                        $flags = [
                            'enable_academic_index' => 'Afecta índice académico',
                            'enable_lost_regulation' => 'Pérdida por reglamento',
                            'enable_official_doc' => 'Documentos oficiales',
                            'enable_repairable' => 'Reparable',
                            'enable_grupo_estable' => 'Grupo estable',
                        ];
                    @endphp
                    @foreach($flags as $field => $label)
                        <label class="flex items-center gap-2 cursor-pointer group p-3 bg-white/5 rounded-lg border border-white/5">
                            <input type="checkbox" wire:model="{{ $field }}" value="true"
                                {{ in_array(${$field}, [true, 'true', 1, '1'], true) ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-500 bg-white/5 border-white/10 rounded focus:ring-emerald-500/50 focus:ring-2">
                            <span class="text-xs text-gray-300 group-hover:text-white transition-colors font-medium">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Sección 3: Observaciones --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Observaciones y Prelaciones
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Observaciones</label>
                        <input type="text" wire:model="observations" placeholder="Observaciones adicionales"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Prelaciones</label>
                        <input type="text" wire:model="prelacions" placeholder="Materias previas requeridas"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button primary label="{{ $isEditing ? 'Actualizar Asignatura' : 'Guardar Asignatura' }}" wire:click="save" spinner="save" />
            </div>
        </x-slot>
    </x-modal-card>
</div>
