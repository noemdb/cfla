<div>
    {{-- Botón Listado (siempre visible, mismo estilo que el menú) --}}
    <button wire:click="open"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-800/50 hover:bg-gray-700/50 text-gray-400 hover:text-gray-200 rounded-lg border border-white/5 transition-all duration-200 text-[11px] font-bold cursor-pointer">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
        </svg>
        Listado de actividades
    </button>

    {{-- ─── MODAL ─── --}}
    @if($showModal)
        <div class="fixed inset-0 z-[9999] overflow-y-auto" wire:key="listado-global-modal">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="close"></div>

            <div class="relative min-h-screen flex items-start justify-center p-4 pt-8 pb-24">
                <div class="relative w-[95vw] max-w-[95vw] bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden"
                     @click.away="$wire.close()">

                    {{-- ─── HEADER ─── --}}
                    <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between bg-emerald-500/5 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Listado Global de Actividades</h3>
                                <p class="text-[11px] text-gray-500 mt-0.5">
                                    Todas las actividades registradas · {{ $total }} actividad{{ $total !== 1 ? 'es' : '' }}
                                </p>
                            </div>
                        </div>
                        <button wire:click="close"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- ─── FILTERS BAR ─── --}}
                    <div class="px-6 py-3 border-b border-white/5 flex flex-wrap items-center gap-3 bg-gray-800/20 shrink-0">
                        {{-- Search --}}
                        <div class="relative flex-1 min-w-[200px]">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por tema, enseñanza, asignatura…"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-9 pr-3 py-1.5 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                            @if($search)
                                <button wire:click="$set('search', '')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Lapso filter --}}
                        <select wire:model.live="lapso_id"
                            class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
                            <option value="">Todos los momentos</option>
                            @foreach($lapsos as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>

                        {{-- Sort controls --}}
                        <div class="flex items-center gap-1 flex-wrap">
                            <button wire:click="sortBy('activities.finicial')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition-all duration-200
                                    {{ $sortField === 'activities.finicial' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortField === 'activities.finicial')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l-4-4"></path>
                                    @endif
                                </svg>
                                Fecha
                            </button>
                            <button wire:click="sortBy('activities.topic')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition-all duration-200
                                    {{ $sortField === 'activities.topic' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortField === 'activities.topic')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l-4-4"></path>
                                    @endif
                                </svg>
                                Tema
                            </button>
                            <button wire:click="sortBy('asignaturas.name')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition-all duration-200
                                    {{ $sortField === 'asignaturas.name' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortField === 'asignaturas.name')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l-4-4"></path>
                                    @endif
                                </svg>
                                Asignatura
                            </button>
                            <button wire:click="sortBy('activities.status')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition-all duration-200
                                    {{ $sortField === 'activities.status' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10' }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortField === 'activities.status')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l-4-4"></path>
                                    @endif
                                </svg>
                                Estado
                            </button>
                        </div>
                    </div>

                    {{-- ─── TABLE ─── --}}
                    <div class="overflow-x-auto overscroll-contain" style="max-height: calc(100vh - 260px);">
                        @if(!empty($activities))
                            <table class="w-full text-left">
                                {{-- Table Header --}}
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-800/60 border-b border-white/5">
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 w-10">#</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 cursor-pointer hover:text-gray-300 transition-colors" wire:click="sortBy('asignaturas.name')">
                                            <span class="inline-flex items-center gap-1">
                                                Asignatura / Grado / Sección
                                                @if($sortField === 'asignaturas.name')
                                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Momento</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 cursor-pointer hover:text-gray-300 transition-colors" wire:click="sortBy('activities.finicial')">
                                            <span class="inline-flex items-center gap-1">
                                                Fechas
                                                @if($sortField === 'activities.finicial')
                                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 cursor-pointer hover:text-gray-300 transition-colors" wire:click="sortBy('activities.topic')">
                                            <span class="inline-flex items-center gap-1">
                                                Tema Generador
                                                @if($sortField === 'activities.topic')
                                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Enseñanza</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden xl:table-cell">Act. Evaluativa</th>
                                        <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 w-24 cursor-pointer hover:text-gray-300 transition-colors" wire:click="sortBy('activities.status')">
                                            <span class="inline-flex items-center gap-1">
                                                Estado
                                                @if($sortField === 'activities.status')
                                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </th>
                                    </tr>
                                </thead>

                                {{-- Table Body --}}
                                <tbody>
                                    @foreach($activities as $i => $act)
                                        <tr class="border-b border-white/5 hover:bg-white/[0.015] transition-colors {{ $act['status'] ? '' : 'opacity-80' }}">
                                            <td class="px-4 py-2.5 text-xs text-gray-500 font-mono">
                                                {{ $from + $loop->index }}
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <div class="flex flex-col">
                                                    <span class="text-xs font-bold text-white truncate max-w-[180px]" title="{{ $act['asignatura_name'] }}">
                                                        {{ $act['asignatura_name'] ?? '—' }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-500">
                                                        {{ $act['grado_name'] ?? '—' }} {{ $act['seccion_name'] ?? '' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2.5">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                    {{ $act['lapso_name'] ?? '—' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2.5 whitespace-nowrap">
                                                <div class="flex flex-col text-[11px] font-mono text-gray-400">
                                                    @if($act['finicial'])
                                                        <span>{{ \Carbon\Carbon::parse($act['finicial'])->format('d/m/Y') }}</span>
                                                        @if($act['ffinal'])
                                                            <span class="text-gray-600">→ {{ \Carbon\Carbon::parse($act['ffinal'])->format('d/m/Y') }}</span>
                                                        @endif
                                                    @else
                                                        <span class="text-gray-600">—</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-2.5 max-w-[220px]">
                                                <p class="text-xs text-gray-200 leading-relaxed truncate" title="{{ $act['topic'] ?? '' }}">
                                                    {{ $act['topic'] ?? '—' }}
                                                </p>
                                            </td>
                                            <td class="px-4 py-2.5 max-w-[200px] hidden lg:table-cell">
                                                @if(!empty($act['teaching']))
                                                    <p class="text-[11px] text-gray-400 leading-relaxed truncate" title="{{ $act['teaching'] }}">
                                                        {{ \Illuminate\Support\Str::limit($act['teaching'], 80) }}
                                                    </p>
                                                @else
                                                    <span class="text-[11px] text-gray-600">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2.5 hidden xl:table-cell">
                                                @if(!empty($act['description']))
                                                    <p class="text-[11px] text-gray-400 leading-relaxed truncate max-w-[180px]" title="{{ $act['description'] }}">
                                                        {{ \Illuminate\Support\Str::limit($act['description'], 60) }}
                                                    </p>
                                                @else
                                                    <span class="text-[11px] text-gray-600">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2.5">
                                                @if($act['status'])
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-green-500/10 text-green-400 border border-green-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                                                        Aprobado
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                        Revisión
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            {{-- Empty state --}}
                            <div class="text-center py-16">
                                <div class="w-14 h-14 bg-gray-800/50 rounded-lg flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-400">Sin actividades</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    @if($search || $lapso_id)
                                        No se encontraron actividades con los filtros seleccionados.
                                    @else
                                        No hay actividades registradas para este profesor.
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>

                    {{-- ─── PAGINATION ─── --}}
                    @if($total > $perPage)
                        <div class="px-6 py-3 border-t border-white/5 flex items-center justify-between bg-gray-800/30 shrink-0">
                            <span class="text-[11px] text-gray-500">
                                Mostrando <span class="text-gray-300 font-medium">{{ $from }}</span>
                                – <span class="text-gray-300 font-medium">{{ $to }}</span>
                                de <span class="text-gray-300 font-medium">{{ $total }}</span> actividad{{ $total !== 1 ? 'es' : '' }}
                            </span>
                            <div class="flex items-center gap-2">
                                <button wire:click="gotoPage({{ $page - 1 }})"
                                    @if($page <= 1) disabled @endif
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all duration-200
                                        {{ $page <= 1 ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed' : 'bg-gray-700/50 text-gray-400 hover:bg-gray-700 hover:text-gray-200 border border-white/10' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Anterior
                                </button>
                                <div class="flex items-center gap-1">
                                    @php
                                        $start = max(1, $page - 2);
                                        $end = min($lastPage, $page + 2);
                                    @endphp
                                    @if($start > 1)
                                        <button wire:click="gotoPage(1)" class="px-2 py-1 rounded text-[11px] text-gray-500 hover:text-gray-300 hover:bg-gray-700/50 transition-colors">1</button>
                                        @if($start > 2)
                                            <span class="px-1 text-[11px] text-gray-600">…</span>
                                        @endif
                                    @endif
                                    @for($p = $start; $p <= $end; $p++)
                                        <button wire:click="gotoPage({{ $p }})"
                                            class="px-2.5 py-1 rounded text-[11px] font-bold transition-all duration-200
                                                {{ $p === $page ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-gray-500 hover:text-gray-300 hover:bg-gray-700/50' }}">
                                            {{ $p }}
                                        </button>
                                    @endfor
                                    @if($end < $lastPage)
                                        @if($end < $lastPage - 1)
                                            <span class="px-1 text-[11px] text-gray-600">…</span>
                                        @endif
                                        <button wire:click="gotoPage({{ $lastPage }})" class="px-2 py-1 rounded text-[11px] text-gray-500 hover:text-gray-300 hover:bg-gray-700/50 transition-colors">{{ $lastPage }}</button>
                                    @endif
                                </div>
                                <button wire:click="gotoPage({{ $page + 1 }})"
                                    @if($page >= $lastPage) disabled @endif
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-[11px] font-bold transition-all duration-200
                                        {{ $page >= $lastPage ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed' : 'bg-gray-700/50 text-gray-400 hover:bg-gray-700 hover:text-gray-200 border border-white/10' }}">
                                    Siguiente
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif
</div>
