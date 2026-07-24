<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Grados</h1>
            <p class="text-emerald-400 font-medium">Gestión de grados o años académicos por plan de estudio.</p>
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
                Nuevo Grado
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
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Plan de Estudio</label>
                <select wire:model.live="filter_pestudio"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($pestudios as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Ver</label>
                <select wire:model.live="paginate"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
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

    {{-- ===== View Container (Grid/Table toggle) ===== --}}
    <div x-cloak
         x-data="{ mode: localStorage.getItem('grados-view-mode') || 'table' }"
         x-init="if (!localStorage.getItem('grados-view-mode')) localStorage.setItem('grados-view-mode', 'table')">

        {{-- View mode toggle: Grid/Table --}}
        <div wire:ignore.self class="mb-4 flex items-center gap-2">
            <button @click="mode = 'grid'; localStorage.setItem('grados-view-mode', 'grid')"
                :class="mode === 'grid' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-2.5 py-2.5 rounded-lg border transition-all duration-200 text-[10px] font-bold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span class="hidden sm:inline">Grid</span>
            </button>
            <button @click="mode = 'table'; localStorage.setItem('grados-view-mode', 'table')"
                :class="mode === 'table' ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-gray-800/50 text-gray-500 border-white/5 hover:text-gray-300'"
                class="inline-flex items-center gap-1.5 px-2.5 py-2.5 rounded-lg border transition-all duration-200 text-[10px] font-bold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="hidden sm:inline">Tabla</span>
            </button>
        </div>

        {{-- ── Table Mode ── --}}
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
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Código</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Abrev.</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden md:table-cell">Plan Est.</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">H.Social</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Secciones</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                                <th class="text-right px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($grados as $grado)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-5 py-2 text-sm text-gray-400 font-mono">{{ $grado->id }}</td>
                                    <td class="px-4 py-2">
                                        <span class="text-sm font-bold text-white font-mono">{{ $grado->code }}</span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="text-xs font-mono bg-white/5 text-gray-300 px-2 py-0.5 rounded-md">{{ $grado->code_sm }}</span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="text-sm text-gray-200 font-medium">{{ $grado->name }}</span>
                                        @if($grado->description)
                                            <span class="block text-[10px] text-gray-500 mt-0.5">{{ \Illuminate\Support\Str::limit($grado->description, 40) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-400 hidden md:table-cell">
                                        {{ $grado->pestudio?->code ?? '—' }}
                                    </td>
                                    <td class="px-4 py-2 text-center hidden lg:table-cell">
                                        <span class="text-sm text-gray-300">{{ $grado->hour_social ?? '—' }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-center hidden lg:table-cell">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/5 text-gray-300 text-sm font-bold">
                                            {{ $grado->seccions_count }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        @if($grado->status_active === 'true')
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
                                        {{-- btnGroup: Action buttons with mobile dropdown --}}
                                        <div class="flex items-center justify-end gap-1.5 shrink-0"
                                             x-data="{ actionsOpen: false }"
                                             @click.away="actionsOpen = false">

                                            {{-- Desktop group --}}
                                            <div class="hidden sm:flex items-center gap-1.5">
                                                <button type="button" wire:click="showPreview({{ $grado->id }})"
                                                    class="min-w-[44px] min-h-[44px] p-1.5 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200"
                                                    title="Vista previa" aria-label="Vista previa">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" wire:click="edit({{ $grado->id }})"
                                                    class="min-w-[44px] min-h-[44px] p-1.5 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                                    title="Editar grado" aria-label="Editar grado">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                @php $delDisabled = ($grado->seccions_count > 0); @endphp
                                                <button type="button" wire:click="confirmDelete({{ $grado->id }})"
                                                    class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg border transition-all duration-200
                                                        {{ $delDisabled
                                                            ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed border-transparent'
                                                            : 'bg-white/5 hover:bg-red-500/10 border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400' }}"
                                                    title="Eliminar grado" aria-label="Eliminar grado"
                                                    @if($delDisabled) disabled @endif>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            {{-- Mobile dropdown --}}
                                            <div class="relative sm:hidden">
                                                <button @click="actionsOpen = !actionsOpen"
                                                    class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/30 hover:bg-gray-200 dark:hover:bg-slate-600/50 border border-gray-200 dark:border-slate-600/30 transition-all"
                                                    title="Más acciones" aria-label="Más acciones">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
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
                                                     class="absolute right-0 z-50 mt-1 min-w-[200px] bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl py-1"
                                                     @click="actionsOpen = false">
                                                    <button type="button" wire:click="showPreview({{ $grado->id }})"
                                                        class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                        <svg class="w-4 h-4 shrink-0 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        Vista previa
                                                    </button>
                                                    <button type="button" wire:click="edit({{ $grado->id }})"
                                                        class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                        <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Editar grado
                                                    </button>
                                                    @php $delDisabled = ($grado->seccions_count > 0); @endphp
                                                    @if(!$delDisabled)
                                                        <button type="button" wire:click="confirmDelete({{ $grado->id }})"
                                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                            <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Eliminar grado
                                                        </button>
                                                    @else
                                                        <span class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-500 cursor-not-allowed">
                                                            <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Eliminar grado
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
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
                                            <p class="text-gray-500 font-medium mb-1">No hay grados registrados</p>
                                            <p class="text-gray-600 text-sm">Crea el primer grado usando el botón "Nuevo Grado".</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($grados->hasPages())
                    <x-pagination-wrapper :paginator="$grados" />
                @endif
            </div>
        </div> {{-- end table mode --}}

        {{-- ── Grid Mode ── --}}
        <div x-show="mode === 'grid'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">

            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2.5">
                    @forelse($grados as $grado)
                        <div class="bg-gray-900/60 backdrop-blur-md border border-white/5 rounded-lg p-4 space-y-3">
                            {{-- Header: initials + name --}}
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($grado->code ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-white font-medium truncate">{{ $grado->name }}</span>
                                </div>
                                <span class="text-[10px] text-gray-500 font-mono shrink-0">#{{ $grado->id }}</span>
                            </div>

                            {{-- Details: code + plan --}}
                            <div class="min-w-0">
                                <span class="text-sm text-gray-200">{{ $grado->pestudio?->full_name ?? '—' }}</span>
                                <span class="block text-[10px] text-gray-500 mt-0.5">Código: {{ $grado->code }} · Abrev: {{ $grado->code_sm }}</span>
                            </div>

                            {{-- Badges --}}
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 bg-cyan-500/10 text-cyan-400 text-[10px] font-bold rounded-md border border-cyan-500/20">
                                    {{ $grado->pestudio?->code ?? '—' }}
                                </span>
                                @if($grado->status_active === 'true')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-400 bg-red-500/10 px-2 py-0.5 rounded-md border border-red-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </div>

                            {{-- Stats row --}}
                            <div class="flex items-center gap-3 text-[10px] text-gray-400">
                                <span>HS: {{ $grado->hour_social ?? '—' }}</span>
                                <span>·</span>
                                <span>Secc: {{ $grado->seccions_count }}</span>
                            </div>

                            {{-- Footer: actions --}}
                            <div class="flex items-center justify-between pt-2 border-t border-white/5">
                                <span class="inline-flex items-center gap-1.5 text-[10px] text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    {{ $grado->seccions_count }} secciones
                                </span>

                                <div class="flex items-center gap-1.5 shrink-0"
                                     x-data="{ gActions: false }"
                                     @click.away="gActions = false">
                                    <div class="hidden sm:flex items-center gap-1.5">
                                        <button type="button" wire:click="showPreview({{ $grado->id }})"
                                            class="min-w-[44px] min-h-[44px] p-1.5 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200"
                                            title="Vista previa" aria-label="Vista previa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button type="button" wire:click="edit({{ $grado->id }})"
                                            class="min-w-[44px] min-h-[44px] p-1.5 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                            title="Editar grado" aria-label="Editar grado">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        @php $delDisabled = ($grado->seccions_count > 0); @endphp
                                        <button type="button" wire:click="confirmDelete({{ $grado->id }})"
                                            class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg border transition-all duration-200
                                                {{ $delDisabled
                                                    ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed border-transparent'
                                                    : 'bg-white/5 hover:bg-red-500/10 border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400' }}"
                                            title="Eliminar grado" aria-label="Eliminar grado"
                                            @if($delDisabled) disabled @endif>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="relative sm:hidden">
                                        <button @click="gActions = !gActions"
                                            class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-slate-700/30 hover:bg-gray-200 dark:hover:bg-slate-600/50 border border-gray-200 dark:border-slate-600/30 transition-all"
                                            title="M&aacute;s acciones" aria-label="M&aacute;s acciones">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4z"/>
                                                <path d="M10 12a2 2 0 110-4 2 2 0 010 4z"/>
                                                <path d="M10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                            </svg>
                                        </button>
                                        <div x-show="gActions"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="opacity-0 scale-95"
                                             x-transition:enter-end="opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="opacity-100 scale-100"
                                             x-transition:leave-end="opacity-0 scale-95"
                                             class="absolute right-0 z-50 mt-1 min-w-[200px] bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl py-1"
                                             @click="gActions = false">
                                            <button type="button" wire:click="showPreview({{ $grado->id }})"
                                                class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                <svg class="w-4 h-4 shrink-0 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                Vista previa
                                            </button>
                                            <button type="button" wire:click="edit({{ $grado->id }})"
                                                class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                Editar grado
                                            </button>
                                            @if($delDisabled)
                                                <span class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-500 cursor-not-allowed">
                                                    <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Eliminar grado
                                                </span>
                                            @else
                                                <button type="button" wire:click="confirmDelete({{ $grado->id }})"
                                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-700 dark:text-slate-200 hover:bg-gray-100 dark:hover:bg-slate-700/50 transition-colors text-left">
                                                    <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    Eliminar grado
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-gray-500 font-medium mb-1">No hay grados registrados</p>
                            <p class="text-gray-600 text-sm">Crea el primer grado usando el botón "Nuevo Grado".</p>
                        </div>
                    @endforelse
                </div>

                @if($grados->hasPages())
                    <div class="mt-4 px-2 py-2 border-t border-white/5">
                        {{ $grados->links('vendor.livewire.custom-tailwind') }}
                    </div>
                @endif
            </div>
        </div> {{-- end grid mode --}}

    </div> {{-- end view container --}}

    <!-- ===== MODAL: Confirmar Eliminación ===== -->
    <x-modal title="Eliminar Grado" blur="lg" wire:model="confirmDeleteId" max-width="md" x-on:close="confirmDeleteId = null" persistent>
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar este grado?</h3>
            <p class="text-sm text-gray-400 mb-6">Esta acción eliminará el grado. Solo se puede eliminar si no tiene secciones asociadas.</p>
            <div class="flex justify-center gap-3">
                <x-button flat label="Cancelar" x-on:click="confirmDeleteId = null" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Vista Previa ===== -->
    <x-modal title="Detalles del Grado" blur="lg" wire:model="previewMode" max-width="2xl">
        @if($previewGrado)
        <div class="relative p-6 space-y-6">
            <button type="button" wire:click="closePreview"
                class="absolute top-6 right-6 p-1.5 bg-white/10 hover:bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-all duration-200 z-10"
                title="Cerrar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Código</span>
                    <p class="text-sm text-white font-bold font-mono mt-1">{{ $previewGrado->code }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Abreviación</span>
                    <p class="text-sm text-white font-mono mt-1">{{ $previewGrado->code_sm }}</p>
                </div>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre</span>
                <p class="text-sm text-white mt-1">{{ $previewGrado->name }}</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Plan de Estudio</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewGrado->pestudio?->full_name ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</span>
                    <p class="mt-1">
                        @if($previewGrado->status_active === 'true')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-500/20">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Activo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-500/10 text-red-400 text-[10px] font-bold rounded-md border border-red-500/20">Inactivo</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">H. Sociales</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewGrado->hour_social ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total H. Sociales</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewGrado->total_hour_social ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Secciones</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewGrado->seccions_count }}</p>
                </div>
            </div>
            @if($previewGrado->description)
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Descripción</span>
                <p class="text-sm text-gray-300 mt-1">{{ $previewGrado->description }}</p>
            </div>
            @endif
            <div class="flex justify-end border-t border-white/5 pt-4">
                <x-button flat label="Cerrar" wire:click="closePreview" />
            </div>
        </div>
        @endif
    </x-modal>

    <!-- ===== MODAL: Formulario Crear/Editar ===== -->
    <x-modal-card title="{{ $isEditing ? 'Editar Grado' : 'Nuevo Grado' }}" blur="lg" wire:model="modeForm" max-width="3xl" persistent>
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

            {{-- Sección 1: Datos del Grado --}}
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Datos del Grado
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Plan de Estudio *</label>
                        <select wire:model="pestudio_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($pestudios as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('pestudio_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nombre *</label>
                        <input type="text" wire:model="name" placeholder="Ej: 1ER AÑO"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Orden</label>
                        <input type="number" wire:model="order" min="0"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Código * (máx 10)</label>
                        <input type="text" wire:model="code" placeholder="Ej: 1A" maxlength="10" style="text-transform:uppercase"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('code') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Abreviación * (máx 4)</label>
                        <input type="text" wire:model="code_sm" placeholder="Ej: 1A" maxlength="4" style="text-transform:uppercase"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('code_sm') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Descripción</label>
                        <input type="text" wire:model="description" placeholder="Descripción opcional del grado"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('description') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 2: Configuración --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                    </svg>
                    Configuración
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Horas Sociales Requeridas</label>
                        <input type="number" wire:model="hour_social" min="0" max="255"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Horas Sociales Totales</label>
                        <input type="number" wire:model="total_hour_social" min="0" max="255"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Estado</label>
                        <select wire:model="status_active"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="true">Activo</option>
                            <option value="false">Inactivo</option>
                        </select>
                        @error('status_active') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button primary label="{{ $isEditing ? 'Actualizar Grado' : 'Guardar Grado' }}" wire:click="save" spinner="save" />
            </div>
        </x-slot>
    </x-modal-card>
</div>
