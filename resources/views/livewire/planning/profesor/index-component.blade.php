<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Profesores</h1>
            <p class="text-emerald-400 font-medium">Gestión de profesores del módulo de planificación académica.</p>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Nuevo Profesor
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre, CI, username..."
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Plan Educativo</label>
                <select wire:model.live="filter_peducativo"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($peducativos as $id => $name)
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
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Carga / Actividades</label>
                <div class="flex gap-2">
                    <select wire:model.live="filter_pevaluacions"
                        class="flex-1 bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        <option value="">Carga: Todos</option>
                        <option value="SI">Con carga</option>
                        <option value="NO">Sin carga</option>
                    </select>
                    <select wire:model.live="filter_activities"
                        class="flex-1 bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        <option value="">Activ: Todos</option>
                        <option value="SI">Con activ.</option>
                        <option value="NO">Sin activ.</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <button wire:click="resetFilters"
                class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-lg border border-amber-500/20 transition-all duration-300 text-xs font-bold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Limpiar Filtros
            </button>
        </div>
    </div>

    {{-- Mode Toggle: Grid / Table (crud-mode-toggle pattern) --}}
    <div class="flex items-center justify-end mb-4" x-data="{ mode: localStorage.getItem('profesors-view-mode') || 'table' }"
         x-init="$watch('mode', val => {
             localStorage.setItem('profesors-view-mode', val);
             window.dispatchEvent(new CustomEvent('profesors-view-mode-changed', { detail: { mode: val } }))
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
         x-data="{ mode: localStorage.getItem('profesors-view-mode') || 'table' }"
         x-init="() => { if (!localStorage.getItem('profesors-view-mode')) localStorage.setItem('profesors-view-mode', 'table') }"
         x-on:profesors-view-mode-changed.window="mode = $event.detail.mode">

        {{-- ── TABLE MODE ── --}}
        <div x-show="mode === 'table'"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">

            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
                <div class="overflow-x-auto [&::-webkit-scrollbar]:h-1.5" style="scrollbar-width: thin;">
                    <table x-data="{ expandedRows: {} }" class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/5">
                                <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                    <button wire:click="sortBy('profesors.id')" class="flex items-center gap-1 hover:text-white transition-colors">N°
                                        @if($sortField === 'profesors.id')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>@endif
                                    </button>
                                </th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                    <button wire:click="sortBy('profesors.lastname')" class="flex items-center gap-1 hover:text-white transition-colors">Nombre
                                        @if($sortField === 'profesors.lastname')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path></svg>@endif
                                    </button>
                                </th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden md:table-cell">Plan Educ.</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">C.Académica</th>
                                <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">P.Activid.</th>
                                <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Usuario</th>
                                <th class="text-right px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($profesors as $profesor)
                                <tr class="hover:bg-white/[0.02] transition-colors group {{ $profesor->user && $profesor->user->is_active === 'disable' ? 'opacity-50' : '' }}">
                                    <td class="px-5 py-2 text-sm text-gray-400 font-mono">{{ $profesor->id }}</td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 text-xs font-bold shrink-0">
                                                {{ strtoupper(substr($profesor->name ?? '?', 0, 1)) }}{{ strtoupper(substr($profesor->lastname ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="text-sm text-white font-medium">{{ $profesor->full_name }}</span>
                                                <span class="block text-[10px] text-gray-500 mt-0.5 font-mono">{{ $profesor->ci_profesor }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 hidden md:table-cell">
                                        @php
                                            $pevaluacionPestudios = $profesor->pevaluacions
                                                ->groupBy(fn($p) => $p->pensum?->pestudio?->id)
                                                ->map(fn($items, $pestudioId) => [
                                                    'name' => $items->first()->pensum?->pestudio?->code ?? '?',
                                                    'count' => $items->count(),
                                                ]);
                                        @endphp
                                        @forelse($pevaluacionPestudios as $pe)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 mb-0.5 bg-cyan-500/10 text-cyan-400 text-[10px] font-bold rounded-md border border-cyan-500/20">
                                                {{ $pe['name'] }}
                                                <span class="text-[9px] text-cyan-500">[{{ $pe['count'] }}]</span>
                                            </span><br>
                                        @empty
                                            <span class="text-gray-600 text-[10px]">—</span>
                                        @endforelse
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center justify-center gap-1">
                                            @foreach($lapsos as $lapso)
                                                @php
                                                    $count = $profesor->pevaluacions
                                                        ->where('lapso_id', $lapso->id)
                                                        ->count();
                                                @endphp
                                                <span class="inline-flex items-center justify-center min-w-[1.8rem] h-6 rounded-md text-[10px] font-bold px-1.5
                                                    {{ $count > 0 ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20' : 'bg-white/5 text-gray-600 border border-white/5' }}">
                                                    {{ str_pad($count, 2, '0', STR_PAD_LEFT) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 hidden lg:table-cell">
                                        <div class="flex items-center justify-center gap-1">
                                            @foreach($lapsos as $lapso)
                                                @php
                                                    $count = \App\Models\app\Academy\Pevaluacion::where('profesor_id', $profesor->id)
                                                        ->where('lapso_id', $lapso->id)
                                                        ->join('activities', 'pevaluacions.id', '=', 'activities.pevaluacion_id')
                                                        ->count();
                                                @endphp
                                                <span class="inline-flex items-center justify-center min-w-[1.8rem] h-6 rounded-md text-[10px] font-bold px-1.5
                                                    {{ $count > 0 ? 'bg-blue-500/15 text-blue-400 border border-blue-500/20' : 'bg-white/5 text-gray-600 border border-white/5' }}">
                                                    {{ str_pad($count, 2, '0', STR_PAD_LEFT) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 hidden lg:table-cell">
                                        @if($profesor->user)
                                            <span class="text-sm text-gray-300 font-mono">{{ $profesor->user->username }}</span>
                                        @else
                                            <span class="text-gray-600 text-[10px]">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-2 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" @click="expandedRows['e{{ $profesor->id }}'] = !expandedRows['e{{ $profesor->id }}']"
                                                class="p-2 bg-white/5 hover:bg-amber-500/10 rounded-lg border border-white/5 hover:border-amber-500/20 text-gray-400 hover:text-amber-400 transition-all duration-200"
                                                title="Info ampliada">
                                                <svg x-show="!expandedRows['e{{ $profesor->id }}']" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                <svg x-show="expandedRows['e{{ $profesor->id }}']" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                            </button>
                                            <button type="button" wire:click="showPreview({{ $profesor->id }})"
                                                class="p-2 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200"
                                                title="Vista previa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <button type="button" wire:click="edit({{ $profesor->id }})"
                                                class="p-2 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                                title="Editar profesor">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            @if($profesor->user)
                                                <button type="button" wire:click="editUser({{ $profesor->id }})"
                                                    class="p-2 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200"
                                                    title="Editar usuario">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                </button>
                                                <button type="button" wire:click="confirmToggleActive({{ $profesor->id }})"
                                                    class="p-2 rounded-lg border transition-all duration-200
                                                        {{ $profesor->user->is_active === 'enable'
                                                            ? 'bg-white/5 hover:bg-red-500/10 border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400'
                                                            : 'bg-white/5 hover:bg-emerald-500/10 border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400' }}"
                                                    title="{{ $profesor->user->is_active === 'enable' ? 'Desactivar usuario' : 'Activar usuario' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                </button>
                                            @endif
                                            @php $delDisabled = ($profesor->pevaluacions_count > 0); @endphp
                                            <button type="button" wire:click="confirmDelete({{ $profesor->id }})"
                                                class="p-2 rounded-lg border transition-all duration-200
                                                    {{ $delDisabled
                                                        ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed border-transparent'
                                                        : 'bg-white/5 hover:bg-red-500/10 border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400' }}"
                                                title="Eliminar profesor"
                                                @if($delDisabled) disabled @endif>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                {{-- Fila expandible con info ampliada --}}
                                <tr x-show="expandedRows['e{{ $profesor->id }}']" x-cloak>
                                    <td colspan="7" class="px-6 py-5 bg-gray-900/60 border-b border-white/5">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                            {{-- Columna 1: Contacto --}}
                                            <div>
                                                <h4 class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-3 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                    Contacto
                                                </h4>
                                                <div class="space-y-2 text-xs">
                                                    @if($profesor->email)
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-500 w-20 shrink-0">Email:</span>
                                                            <span class="text-gray-200">{{ $profesor->email }}</span>
                                                        </div>
                                                    @endif
                                                    @if($profesor->phone)
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-500 w-20 shrink-0">Teléfono:</span>
                                                            <span class="text-gray-200">{{ $profesor->phone }}</span>
                                                        </div>
                                                    @endif
                                                    @if($profesor->cellphone)
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-500 w-20 shrink-0">Celular:</span>
                                                            <span class="text-gray-200">{{ $profesor->cellphone }}</span>
                                                        </div>
                                                    @endif
                                                    @if($profesor->whatsapp)
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-500 w-20 shrink-0">WhatsApp:</span>
                                                            <span class="text-gray-200">{{ $profesor->whatsapp }}</span>
                                                        </div>
                                                    @endif
                                                    @if($profesor->gsemail)
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-500 w-20 shrink-0">GSuite:</span>
                                                            <span class="text-gray-200">{{ $profesor->gsemail }}</span>
                                                        </div>
                                                    @endif
                                                    @if($profesor->dir_address)
                                                        <div class="flex items-start gap-2">
                                                            <span class="text-gray-500 w-20 shrink-0 mt-0.5">Dirección:</span>
                                                            <span class="text-gray-200">{{ $profesor->dir_address }}</span>
                                                        </div>
                                                    @endif
                                                    @unless($profesor->email || $profesor->phone || $profesor->cellphone || $profesor->whatsapp || $profesor->gsemail || $profesor->dir_address)
                                                        <span class="text-gray-600 italic">Sin datos de contacto</span>
                                                    @endunless
                                                </div>
                                            </div>

                                            {{-- Columna 2: Cuenta y Rol --}}
                                            <div>
                                                <h4 class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-3 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                    Cuenta y Rol
                                                </h4>
                                                <div class="space-y-2 text-xs">
                                                    @if($profesor->user)
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-500 w-24 shrink-0">Usuario:</span>
                                                            <span class="text-gray-200 font-mono">{{ $profesor->user->username }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-gray-500 w-24 shrink-0">Estado:</span>
                                                            @if($profesor->user->is_active === 'enable')
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-500/20">
                                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                                    Activo
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-red-500/10 text-red-400 text-[10px] font-bold rounded-md border border-red-500/20">
                                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                                    Inactivo
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-gray-600 italic">Sin usuario asignado</span>
                                                    @endif
                                                    @if($profesor->user)
                                                        @php
                                                            $rol = \App\Models\sys\Rol::where('user_id', $profesor->user_id)
                                                                ->where('area', 'PROFESORADO')
                                                                ->latest()
                                                                ->first();
                                                        @endphp
                                                        @if($rol)
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-gray-500 w-24 shrink-0">Rol desde:</span>
                                                                <span class="text-gray-200">{{ $rol->finicial?->format('d/m/Y') ?? '—' }}</span>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-gray-500 w-24 shrink-0">Rol hasta:</span>
                                                                <span class="text-gray-200">{{ $rol->ffinal?->format('d/m/Y') ?? '—' }}</span>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-gray-500 w-24 shrink-0">Tipo Fac.:</span>
                                                        <span class="text-gray-200">{{ $profesor->ti_teacher ?? '—' }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-gray-500 w-24 shrink-0">Género:</span>
                                                        <span class="text-gray-200">{{ $profesor->gender === 'M' ? 'Masculino' : 'Femenino' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Columna 3: Resumen de cargas --}}
                                            <div>
                                                <h4 class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-3 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                    Cargas Académicas
                                                </h4>
                                                @php
                                                    $cargas = $profesor->pevaluacions->groupBy(fn($p) => $p->pensum?->asignatura?->name ?? '?');
                                                @endphp
                                                @forelse($cargas as $asignaturaName => $items)
                                                    <div class="flex items-center justify-between py-1.5 border-b border-white/5 last:border-0">
                                                        <span class="text-xs text-gray-200 truncate mr-2">{{ $asignaturaName }}</span>
                                                        <div class="flex items-center gap-1 shrink-0">
                                                            @foreach($items->groupBy('lapso_id') as $lapsoId => $lapsoItems)
                                                                @php
                                                                    $lapsoNombre = optional($lapsoItems->first()->lapso)->name ?? "L{$lapsoId}";
                                                                @endphp
                                                                <span class="inline-flex items-center px-1.5 py-0.5 bg-white/5 text-gray-400 text-[9px] font-bold rounded border border-white/5">
                                                                    {{ $lapsoNombre }}:{{ $lapsoItems->count() }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @empty
                                                    <span class="text-gray-600 italic text-xs">Sin cargas académicas</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-16 text-center">
                                        <div>
                                            <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            <p class="text-gray-500 font-medium mb-1">No hay profesores registrados</p>
                                            <p class="text-gray-600 text-sm">Crea el primer profesor usando el botón "Nuevo Profesor".</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($profesors->hasPages())
                    <x-pagination-wrapper :paginator="$profesors" />
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
                    @forelse($profesors as $profesor)
                        <div class="rounded-2xl border border-white/5 bg-gray-900 hover:border-emerald-500/30 transition-all duration-200 group flex flex-col overflow-hidden min-h-[280px] {{ $profesor->user && $profesor->user->is_active === 'disable' ? 'opacity-50' : '' }}">

                            {{-- Header: Avatar + Full Name + CI + Status --}}
                            <div class="flex items-start justify-between px-4 pt-4 pb-3 border-b border-white/5 gap-3">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <div class="w-10 h-10 rounded-full bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 text-sm font-bold shrink-0">
                                        {{ strtoupper(substr($profesor->name ?? '?', 0, 1)) }}{{ strtoupper(substr($profesor->lastname ?? '', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-white truncate" title="{{ $profesor->full_name }}">{{ $profesor->full_name }}</h3>
                                        <span class="text-[10px] text-gray-500 font-mono">{{ $profesor->ci_profesor }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    @if($profesor->user && $profesor->user->is_active === 'enable')
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/12 text-emerald-400 border border-emerald-500/20">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Activo
                                        </span>
                                    @elseif($profesor->user && $profesor->user->is_active === 'disable')
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-500/12 text-red-400 border border-red-500/20">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Inactivo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-500/12 text-gray-500 border border-gray-500/20">
                                            Sin usuario
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Body: Contact + Tipo + Cargas Counts + Planes --}}
                            <div class="px-4 py-3 space-y-2.5 flex-1">
                                {{-- Email + Tipo Teacher --}}
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-1.5 text-[11px] min-w-0">
                                        @if($profesor->email)
                                            <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                            <span class="text-gray-400 truncate" title="{{ $profesor->email }}">{{ $profesor->email }}</span>
                                        @else
                                            <span class="text-gray-600 italic">Sin email</span>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/12 text-purple-400 border border-purple-500/20 shrink-0">
                                        {{ $profesor->ti_teacher ?? '—' }}
                                    </span>
                                </div>

                                {{-- Planes educativos (badges compactos) --}}
                                @php
                                    $pestudioBadges = $profesor->pevaluacions
                                        ->groupBy(fn($p) => $p->pensum?->pestudio?->id)
                                        ->map(fn($items) => [
                                            'code' => $items->first()->pensum?->pestudio?->code ?? '?',
                                            'count' => $items->count(),
                                        ]);
                                @endphp
                                @if($pestudioBadges->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($pestudioBadges as $pb)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 text-[9px] font-bold bg-cyan-500/12 text-cyan-400 border border-cyan-500/20 rounded">
                                                {{ $pb['code'] }}
                                                <span class="text-cyan-500">[{{ $pb['count'] }}]</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Cargas por lapso --}}
                                <div>
                                    <p class="text-[9px] font-bold uppercase tracking-widest text-gray-600 mb-1.5">Carga Académica por Lapso</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($lapsos as $lapso)
                                            @php
                                                $count = $profesor->pevaluacions
                                                    ->where('lapso_id', $lapso->id)
                                                    ->count();
                                            @endphp
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold
                                                {{ $count > 0
                                                    ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20'
                                                    : 'bg-white/5 text-gray-600 border border-white/5' }}">
                                                {{ Str::limit($lapso->code_sm, 3, '') }}:{{ str_pad($count, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Nombre de usuario si tiene --}}
                                <div class="flex items-center gap-2 text-[11px]">
                                    @if($profesor->user)
                                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span class="text-gray-400 font-mono truncate" title="{{ $profesor->user->username }}">{{ $profesor->user->username }}</span>
                                    @else
                                        <span class="text-gray-600 italic text-[10px]">Sin usuario</span>
                                    @endif
                                </div>

                                {{-- Género --}}
                                <div class="text-[10px] text-gray-500">
                                    {{ $profesor->gender === 'M' ? 'Masculino' : 'Femenino' }}
                                    @if($profesor->phone || $profesor->cellphone)
                                        · {{ $profesor->phone ?: $profesor->cellphone }}
                                    @endif
                                </div>
                            </div>

                            {{-- Footer Stats: Cargas totales + Actividades --}}
                            <div class="px-4 py-2.5 border-t border-white/5 bg-white/[0.03] space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span @class([
                                            'inline-flex items-center justify-center w-6 h-6 rounded-lg text-[10px] font-bold',
                                            'bg-emerald-500/12 text-emerald-400' => $profesor->pevaluacions_count > 0,
                                            'bg-gray-500/12 text-gray-500' => $profesor->pevaluacions_count === 0,
                                        ])>
                                            {{ $profesor->pevaluacions_count }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 font-medium">cargas</span>
                                    </div>
                                    @php
                                        $totalActivities = $profesor->pevaluacions->sum(fn($p) => $p->activities?->count() ?? 0);
                                    @endphp
                                    <span class="text-[9px] text-gray-600">
                                        <span class="font-bold">{{ $totalActivities }}</span> activ.
                                    </span>
                                </div>
                            </div>

                            {{-- Actions: btnGroup --}}
                            <div class="px-4 pb-4 pt-2.5 border-t border-white/5 flex items-center gap-2"
                                 x-data="{ actionsOpen: false }"
                                 @click.away="actionsOpen = false">

                                {{-- Primary: Vista previa --}}
                                <button type="button" wire:click="showPreview({{ $profesor->id }})"
                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-bold bg-cyan-500/12 text-cyan-400 hover:bg-cyan-500/20 border border-cyan-500/20 transition-all duration-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Ver Detalle
                                </button>

                                {{-- Desktop group --}}
                                <div class="hidden sm:flex items-center gap-2">
                                    <button type="button" wire:click="edit({{ $profesor->id }})"
                                        class="min-w-[44px] min-h-[44px] p-1.5 rounded-lg text-xs font-bold bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200"
                                        title="Editar">
                                        <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    @php $delDisabled = ($profesor->pevaluacions_count > 0); @endphp
                                    <button type="button" wire:click="confirmDelete({{ $profesor->id }})"
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
                                         class="absolute right-0 z-50 mt-1 min-w-[180px] bg-gray-800 border border-white/10 rounded-lg shadow-xl py-1"
                                         @click="actionsOpen = false">
                                        <button wire:click="edit({{ $profesor->id }})"
                                            class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                            <svg class="w-4 h-4 shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Editar
                                        </button>
                                        @if($profesor->user)
                                            <button wire:click="editUser({{ $profesor->id }})"
                                                class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                                <svg class="w-4 h-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                Editar Usuario
                                            </button>
                                            <button wire:click="confirmToggleActive({{ $profesor->id }})"
                                                class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                                <svg class="w-4 h-4 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                {{ $profesor->user->is_active === 'enable' ? 'Desactivar' : 'Activar' }} Usuario
                                            </button>
                                        @endif
                                        @if(!$delDisabled)
                                            <button wire:click="confirmDelete({{ $profesor->id }})"
                                                class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-300 hover:bg-white/5 transition-colors text-left">
                                                <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Eliminar
                                            </button>
                                        @else
                                            <span class="w-full flex items-center gap-2 px-3 py-2.5 text-xs text-gray-500 cursor-not-allowed">
                                                <svg class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Eliminar
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center">
                            <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <p class="text-gray-500 font-medium mb-1">No hay profesores registrados</p>
                            <p class="text-gray-600 text-sm">Crea el primer profesor usando el botón "Nuevo Profesor".</p>
                        </div>
                    @endforelse
                </div>

                @if($profesors->hasPages())
                    <div class="mt-6">
                        <x-pagination-wrapper :paginator="$profesors" />
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ===== MODAL: Confirmar Eliminación ===== -->
    <x-modal title="Eliminar Profesor" blur="lg" wire:model="confirmDeleteId" max-width="md" x-on:close="confirmDeleteId = null" persistent>
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar este profesor?</h3>
            <p class="text-sm text-gray-400 mb-6">Esta acción eliminará el profesor del sistema. Solo se puede eliminar si no tiene cargas académicas asociadas.</p>
            <div class="flex justify-center gap-3">
                <x-button flat label="Cancelar" x-on:click="confirmDeleteId = null" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Confirmar Activar/Desactivar Usuario ===== -->
    <x-modal title="Cambiar Estado del Usuario" blur="lg" wire:model="confirmToggleActiveId" max-width="md" persistent>
        <div class="rounded-2xl border border-white/10 p-6 text-center">
            @if($toggleActiveCurrentStatus)
                <svg class="w-16 h-16 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <h3 class="text-lg font-bold text-white mb-2">¿Desactivar usuario?</h3>
                <p class="text-sm text-gray-400 mb-6">
                    Se desactivará el usuario de <strong class="text-white">{{ $toggleActiveName }}</strong>.
                    El profesor no podrá acceder al sistema hasta que sea activado nuevamente.
                </p>
            @else
                <svg class="w-16 h-16 text-emerald-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-bold text-white mb-2">¿Activar usuario?</h3>
                <p class="text-sm text-gray-400 mb-6">
                    Se activará el usuario de <strong class="text-white">{{ $toggleActiveName }}</strong>.
                    El profesor podrá acceder al sistema nuevamente.
                </p>
            @endif
            <div class="flex justify-center gap-3">
                <x-button flat label="Cancelar" x-on:click="close" wire:click="cancelToggleActive" />
                @if($toggleActiveCurrentStatus)
                    <x-button negative label="Desactivar" wire:click="toggleActive" spinner="toggleActive" />
                @else
                    <x-button primary label="Activar" wire:click="toggleActive" spinner="toggleActive" />
                @endif
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Vista Previa (x-preview-modal) ===== -->
    <x-preview-modal
        wire:model="previewMode"
        title="Detalles del Profesor"
        subtitle="{{ $previewProfesorId ? optional(\App\Models\app\Academy\Profesor::find($previewProfesorId))->full_name : '' }}"
        x-on:close="$wire.closePreview()"
    >
        @if($previewMode && $previewProfesorId)
            @livewire('planning.profesor.show-preview', ['profesorId' => $previewProfesorId], key($previewProfesorId))
        @endif
    </x-preview-modal>

    <!-- ===== MODAL: Wizard Crear/Editar Profesor ===== -->
    <x-modal-card title="{{ $isEditing ? 'Editar Profesor' : 'Registrar Nuevo Profesor' }}" blur="lg" wire:model="modeForm" max-width="2xl" persistent>
        <div class="space-y-6">

            <!-- Progress bar -->
            <div class="flex items-center gap-2 mb-6">
                <div class="flex items-center gap-1.5">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $wizardStep >= 1 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-white/5 text-gray-600 border border-white/10' }}">1</div>
                    <span class="text-[10px] font-bold {{ $wizardStep >= 1 ? 'text-emerald-400' : 'text-gray-600' }}">Datos Personales</span>
                </div>
                <div class="flex-1 h-px {{ $wizardStep >= 2 ? 'bg-emerald-500/30' : 'bg-white/10' }}"></div>
                <div class="flex items-center gap-1.5">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $wizardStep >= 2 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-white/5 text-gray-600 border border-white/10' }}">2</div>
                    <span class="text-[10px] font-bold {{ $wizardStep >= 2 ? 'text-emerald-400' : 'text-gray-600' }}">Contacto</span>
                </div>
                <div class="flex-1 h-px {{ $wizardStep >= 3 ? 'bg-emerald-500/30' : 'bg-white/10' }}"></div>
                <div class="flex items-center gap-1.5">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $wizardStep >= 3 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-white/5 text-gray-600 border border-white/10' }}">3</div>
                    <span class="text-[10px] font-bold {{ $wizardStep >= 3 ? 'text-emerald-400' : 'text-gray-600' }}">Cuenta y Rol</span>
                </div>
            </div>

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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

            {{-- ═══ Paso 1: Datos Personales ═══ --}}
            @if($wizardStep === 1)
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Datos Personales
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Cédula de Identidad *</label>
                        <input type="text" wire:model="ci_profesor" placeholder="V-12345678"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('ci_profesor') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Tipo de Facilitador</label>
                        <select wire:model="ti_teacher"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="Titular">Titular</option>
                            <option value="Especialista">Especialista</option>
                            <option value="Auxiliar">Auxiliar</option>
                            <option value="Pasante">Pasante</option>
                        </select>
                        @error('ti_teacher') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nombres *</label>
                        <input type="text" wire:model="name" placeholder="Nombres"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Apellidos *</label>
                        <input type="text" wire:model="lastname" placeholder="Apellidos"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('lastname') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Género *</label>
                        <select wire:model="gender"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                        @error('gender') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Fecha de Nacimiento</label>
                        <input type="date" wire:model="date_birth"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        @error('date_birth') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
            @endif

            {{-- ═══ Paso 2: Contacto ═══ --}}
            @if($wizardStep === 2)
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Información de Contacto
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Correo Electrónico *</label>
                        <input type="email" wire:model="email" placeholder="correo@ejemplo.com"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('email') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Correo GSuite</label>
                        <input type="email" wire:model="gsemail" placeholder="correo@gsuite.edu"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('gsemail') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Teléfono</label>
                        <input type="text" wire:model="phone" placeholder="0212-5551234"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('phone') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Celular</label>
                        <input type="text" wire:model="cellphone" placeholder="0414-5555678"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('cellphone') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">WhatsApp</label>
                        <input type="text" wire:model="whatsapp" placeholder="0414-5555678"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('whatsapp') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Dirección</label>
                    <input type="text" wire:model="dir_address" placeholder="Dirección de residencia"
                        class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                    @error('dir_address') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            @endif

            {{-- ═══ Paso 3: Cuenta y Rol ═══ --}}
            @if($wizardStep === 3)
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Cuenta de Usuario y Rol
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nombre de Usuario *</label>
                        <div class="flex gap-2">
                            <input type="text" wire:model="user_username" placeholder="jperez78"
                                class="flex-1 bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                            <button type="button" wire:click="autoGenerateUsername"
                                class="px-3 py-2 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-lg border border-cyan-500/20 transition-all text-xs font-bold"
                                title="Generar automáticamente">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </button>
                        </div>
                        @error('user_username') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Contraseña</label>
                        <input type="password" wire:model="user_password" placeholder="Dejar vacío = cédula"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        <p class="text-[9px] text-gray-600 mt-1">Si se deja vacío, se usará la cédula como contraseña.</p>
                        @error('user_password') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Fecha Inicial del Rol</label>
                        <input type="date" wire:model="rol_finicial"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        @error('rol_finicial') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Fecha Final del Rol</label>
                        <input type="date" wire:model="rol_ffinal"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        @error('rol_ffinal') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" wire:model="status_active" value="1" {{ $status_active ? 'checked' : '' }}
                            class="w-4 h-4 text-emerald-500 bg-white/5 border-white/10 rounded focus:ring-emerald-500/50 focus:ring-2">
                        <span class="text-xs text-gray-300 group-hover:text-white transition-colors">Profesor activo</span>
                    </label>
                    @error('status_active') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex justify-between w-full">
                <div>
                    @if($wizardStep > 1)
                        <x-button flat label="← Anterior" wire:click="prevStep" />
                    @else
                        <x-button flat label="Cancelar" x-on:click="close" />
                    @endif
                </div>
                <div>
                    @if($wizardStep < 3)
                        <x-button positive label="Siguiente →" wire:click="nextStep" spinner="nextStep" />
                    @else
                        <x-button primary label="{{ $isEditing ? 'Actualizar Profesor' : 'Guardar Profesor' }}" wire:click="save" spinner="save" />
                    @endif
                </div>
            </div>
        </x-slot>
    </x-modal-card>

    <!-- ===== MODAL: Editar Usuario ===== -->
    <x-modal-card title="Editar Usuario" blur="lg" wire:model="showUserEditModal" max-width="lg" persistent>
        <div class="space-y-5">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nombre de Usuario *</label>
                <input type="text" wire:model="editUserUsername"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none transition-all placeholder:text-gray-600">
                @error('editUserUsername') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Correo Electrónico</label>
                <input type="email" wire:model="editUserEmail"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none transition-all placeholder:text-gray-600">
                @error('editUserEmail') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nueva Contraseña</label>
                <input type="password" wire:model="editUserPassword" placeholder="Dejar vacío = no cambiar"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none transition-all placeholder:text-gray-600">
                <p class="text-[9px] text-gray-600 mt-1">Solo si deseas cambiarla. Si se deja vacío, se mantiene la actual.</p>
                @error('editUserPassword') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Estado</label>
                <select wire:model="editUserIsActive"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none transition-all">
                    <option value="enable">Activo</option>
                    <option value="disable">Inactivo</option>
                </select>
                @error('editUserIsActive') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-2 w-full">
                <x-button flat label="Cancelar" x-on:click="close" wire:click="closeUserEdit" />
                <x-button primary label="Guardar Cambios" wire:click="saveUser" spinner="saveUser" />
            </div>
        </x-slot>
    </x-modal-card>
</div>
