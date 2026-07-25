<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Áreas de Conocimiento</h1>
            <p class="text-emerald-400 font-medium">Catálogo de áreas de conocimiento y asignaturas adscritas.</p>
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
                Nueva Área
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
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
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Programa Educativo</label>
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
                    <option value="12">12</option>
                    <option value="24">24</option>
                    <option value="48">48</option>
                    <option value="96">96</option>
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

    <!-- Bento Grid -->
    <div class="bg-gray-900/60 border border-white/5 rounded-2xl p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 auto-rows-auto">
            @forelse($area_conocimientos as $area)
                @php
                    $mod = $loop->index % 5;
                    $isHero  = $mod === 0;              // col-span-2 row-span-2
                    $isWide  = $mod === 3 || $mod === 4; // col-span-2
                    $isSmall = $mod === 1 || $mod === 2; // col-span-1
                @endphp

                <div @class([
                    'rounded-2xl border transition-all duration-200 group flex flex-col overflow-hidden',
                    'bg-gray-900 border-white/5 hover:border-emerald-500/30',
                    'col-span-2 row-span-2' => $isHero,
                    'col-span-2'             => $isWide,
                    'col-span-1'             => $isSmall,
                ])>

                    @if($isHero)
                        {{-- ── HERO 2x2 ── --}}
                        <div class="flex items-start justify-between px-5 pt-4 pb-3 border-b border-white/5">
                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-bold text-white truncate">{{ $area->name }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-500/15 text-purple-400 border border-purple-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        {{ $area->code_sm }}
                                    </span>
                                    @if($area->pestudio)
                                        <span class="text-[10px] text-gray-500 truncate">{{ $area->pestudio->full_name }}</span>
                                    @endif
                                </div>
                            </div>
                            <span @class([
                                'inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold shrink-0',
                                'bg-emerald-500/12 text-emerald-400' => $area->enable_academic_index === 'true',
                                'bg-gray-500/12 text-gray-500' => $area->enable_academic_index !== 'true',
                            ])>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $area->enable_academic_index === 'true' ? 'I. Académico' : '—' }}
                            </span>
                        </div>
                        <div class="px-5 py-3 space-y-2.5 flex-1">
                            <div class="flex items-center gap-2 text-xs">
                                <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span class="text-gray-400 font-mono">{{ $area->code ?: '—' }}</span>
                            </div>
                            @if($area->description)
                                <div class="flex items-start gap-2 text-xs">
                                    <svg class="w-4 h-4 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                    <span class="text-gray-400 line-clamp-3 leading-relaxed">{{ $area->description }}</span>
                                </div>
                            @endif
                            <div class="flex items-center gap-2 text-xs">
                                <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span class="text-gray-400">{{ $area->leader?->username ?? 'Sin jefe' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <svg class="w-4 h-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 9l5-5 5 5M7 15l5 5 5-5"/></svg>
                                <span class="text-gray-400">Orden {{ $area->order }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3 border-t border-white/5 bg-white/[0.03]">
                            <div class="flex items-center gap-2">
                                <span @class([
                                    'inline-flex items-center justify-center w-7 h-7 rounded-lg text-[11px] font-bold',
                                    'bg-blue-500/12 text-blue-400' => $area->campo_conocimientos_count > 0,
                                    'bg-gray-500/12 text-gray-500' => $area->campo_conocimientos_count === 0,
                                ])>
                                    {{ $area->campo_conocimientos_count }}
                                </span>
                                <span class="text-[11px] text-gray-500 font-medium">asignaturas</span>
                            </div>
                            @if($area->observations)
                                <span class="text-[10px] text-gray-600 truncate max-w-[140px]" title="{{ $area->observations }}">{{ \Illuminate\Support\Str::limit($area->observations, 24) }}</span>
                            @endif
                        </div>
                        <div class="px-5 pb-4 pt-3 border-t border-white/5 flex items-center gap-2">
                            <button type="button" wire:click="openCampoManager({{ $area->id }})"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-[11px] font-bold transition-all duration-200 {{ $area->campo_conocimientos_count > 0 ? 'bg-blue-500/12 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20' : 'bg-gray-500/12 text-gray-500 hover:bg-gray-500/20 border border-white/5' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                Asignaturas
                            </button>
                            <button type="button" wire:click="edit({{ $area->id }})"
                                class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-xs font-bold bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" wire:click="confirmDelete({{ $area->id }})"
                                class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-xs font-bold bg-red-500/12 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>

                    @elseif($isWide)
                        {{-- ── WIDE 2x1 ── --}}
                        <div class="flex items-center justify-between px-5 py-3 border-b border-white/5">
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-white truncate">{{ $area->name }}</h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/12 text-purple-400 border border-purple-500/20">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        {{ $area->code_sm }}
                                    </span>
                                    @if($area->pestudio)
                                        <span class="text-[9px] text-gray-500 truncate">{{ $area->pestudio->code }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1">
                                    <span @class([
                                        'inline-flex items-center justify-center w-6 h-6 rounded-lg text-[10px] font-bold',
                                        'bg-blue-500/12 text-blue-400' => $area->campo_conocimientos_count > 0,
                                        'bg-gray-500/12 text-gray-500' => $area->campo_conocimientos_count === 0,
                                    ])>{{ $area->campo_conocimientos_count }}</span>
                                    <span class="text-[10px] text-gray-500">asig.</span>
                                </div>
                                <span @class([
                                    'inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0',
                                    'bg-emerald-500/12 text-emerald-400' => $area->enable_academic_index === 'true',
                                    'bg-gray-500/12 text-gray-500' => $area->enable_academic_index !== 'true',
                                ])>{{ $area->enable_academic_index === 'true' ? 'I.Acad' : '—' }}</span>
                            </div>
                        </div>
                        <div class="flex-1 flex items-center gap-4 px-5 py-2.5">
                            <div class="flex items-center gap-1.5 text-[11px] text-gray-400">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span>{{ $area->code ?: '—' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-[11px] text-gray-400">
                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 9l5-5 5 5M7 15l5 5 5-5"/></svg>
                                <span>Orden {{ $area->order }}</span>
                            </div>
                            @if($area->leader)
                                <div class="flex items-center gap-1.5 text-[11px] text-gray-500">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="truncate max-w-[80px]">{{ $area->leader->username }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5 px-5 pb-3 pt-2 border-t border-white/5">
                            <button type="button" wire:click="openCampoManager({{ $area->id }})"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-lg text-[9px] font-bold transition-all duration-200 {{ $area->campo_conocimientos_count > 0 ? 'bg-blue-500/12 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20' : 'bg-gray-500/12 text-gray-500 hover:bg-gray-500/20 border border-white/5' }}">
                                Adscribir
                            </button>
                            <button type="button" wire:click="edit({{ $area->id }})"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-[9px] font-bold bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button type="button" wire:click="confirmDelete({{ $area->id }})"
                                class="w-7 h-7 inline-flex items-center justify-center rounded-lg text-[9px] font-bold bg-red-500/12 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>

                    @else
                        {{-- ── SMALL 1x1 ── --}}
                        <div class="flex items-start justify-between px-4 pt-3 pb-2 border-b border-white/5">
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-white truncate" title="{{ $area->name }}">{{ $area->name }}</h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/12 text-purple-400 border border-purple-500/20">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        {{ $area->code_sm }}
                                    </span>
                                </div>
                            </div>
                            <span @class([
                                'inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0',
                                'bg-emerald-500/12 text-emerald-400' => $area->enable_academic_index === 'true',
                                'bg-gray-500/12 text-gray-500' => $area->enable_academic_index !== 'true',
                            ])>
                                {{ $area->enable_academic_index === 'true' ? 'I.A' : '—' }}
                            </span>
                        </div>
                        <div class="px-4 py-2 space-y-2 flex-1">
                            <div class="flex items-center gap-1.5 text-[10px]">
                                <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                <span class="text-gray-400 font-mono">{{ $area->code ?: '—' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px]">
                                <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 9l5-5 5 5M7 15l5 5 5-5"/></svg>
                                <span class="text-gray-400">Ord. {{ $area->order }}</span>
                            </div>
                            @if($area->description)
                                <div class="flex items-start gap-1.5 text-[10px]">
                                    <svg class="w-3 h-3 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                    <span class="text-gray-500 line-clamp-2">{{ $area->description }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between px-4 py-2 border-t border-white/5 bg-white/[0.02]">
                            <div class="flex items-center gap-1">
                                <span @class([
                                    'inline-flex items-center justify-center w-5 h-5 rounded text-[9px] font-bold',
                                    'bg-blue-500/12 text-blue-400' => $area->campo_conocimientos_count > 0,
                                    'bg-gray-500/12 text-gray-500' => $area->campo_conocimientos_count === 0,
                                ])>{{ $area->campo_conocimientos_count }}</span>
                                <svg class="w-2.5 h-2.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div class="flex gap-1">
                                <button type="button" wire:click="openCampoManager({{ $area->id }})"
                                    class="p-1.5 rounded-lg transition-all duration-200 bg-blue-500/12 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20"
                                    title="Adscribir">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                </button>
                                <button type="button" wire:click="edit({{ $area->id }})"
                                    class="p-1.5 rounded-lg bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" wire:click="confirmDelete({{ $area->id }})"
                                    class="p-1.5 rounded-lg bg-red-500/12 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            @empty
                <div class="col-span-full py-20 text-center">
                    <svg class="w-16 h-16 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-gray-500 font-medium mb-1">No hay áreas de conocimiento registradas</p>
                    <p class="text-gray-600 text-sm">Crea la primera área usando el botón "Nueva Área".</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($area_conocimientos->hasPages())
        <div class="mt-6">
            <x-pagination-wrapper :paginator="$area_conocimientos" />
        </div>
    @endif

    <!-- ===== MODAL: Confirmar Eliminación ===== -->
    <x-modal title="Eliminar Área de Conocimiento" blur="lg" wire:model="confirmDeleteId" max-width="md" x-on:close="confirmDeleteId = null" persistent>
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar esta área de conocimiento?</h3>
            <p class="text-sm text-gray-400 mb-6">Solo se puede eliminar si no tiene asignaturas adscritas.</p>
            <div class="flex justify-center gap-3">
                <x-button flat label="Cancelar" x-on:click="$wire.confirmDeleteId = null" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Formulario Crear/Editar Área ===== -->
    <x-modal-card title="{{ $isEditing ? 'Editar Área de Conocimiento' : 'Nueva Área de Conocimiento' }}" blur="lg" wire:model="modeForm" max-width="3xl" persistent>
        <div class="space-y-6">

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

            {{-- Sección 1: Identificación --}}
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Identificación
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Plan Educativo</label>
                        <select wire:model="peducativo_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">—</option>
                            @foreach($peducativos as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
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
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nombre *</label>
                        <input type="text" wire:model="name" placeholder="Nombre del área de conocimiento"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Jefe del Área</label>
                        <select wire:model="leader_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">—</option>
                            @foreach($usuarios as $id => $username)
                                <option value="{{ $id }}">{{ $username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Sección 2: Códigos --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    Códigos
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Código *</label>
                        <input type="text" wire:model="code" placeholder="Ej: LEN" maxlength="20" style="text-transform:uppercase"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('code') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Abreviación *</label>
                        <input type="text" wire:model="code_sm" placeholder="Ej: LEN" maxlength="10" style="text-transform:uppercase"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('code_sm') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Orden</label>
                        <select wire:model="order"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            @for($i = 1; $i <= 30; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('order') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Índice Académico</label>
                        <select wire:model="enable_academic_index"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="true">Sí</option>
                            <option value="false">No</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Sección 3: Descripción y Observaciones --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Descripción
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Descripción</label>
                        <textarea wire:model="description" rows="3" placeholder="Descripción del área de conocimiento"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Observaciones</label>
                        <textarea wire:model="observations" rows="3" placeholder="Observaciones adicionales"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600 resize-none"></textarea>
                    </div>
                </div>
            </div>

        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="$wire.modeForm = false" />
                <x-button primary label="{{ $isEditing ? 'Actualizar Área' : 'Guardar Área' }}" wire:click="save" spinner="save" />
            </div>
        </x-slot>
    </x-modal-card>

    <!-- ===== DIALOG 95%: Gestión de Campo Conocimientos (Alpine custom) ===== -->
    <div x-data="{ show: @entangle('modeCampo') }"
         x-show="show"
         x-cloak
         @keydown.escape.window="$wire.closeCampoManager()"
         class="fixed inset-0 z-50 overflow-y-auto">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"
             x-show="show"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @@click="$wire.closeCampoManager()"></div>
        {{-- Panel --}}
        <div class="flex min-h-full items-start justify-center p-2 sm:p-4 pt-8">
            <div class="relative w-full max-w-[95vw] bg-gray-900 border border-white/10 rounded-lg shadow-2xl"
                 x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-2 border-b border-white/10">
                    <h2 class="text-lg font-bold text-white">Asignaturas Adscritas — {{ $campoAreaName }}</h2>
                    <button type="button" @@click="$wire.closeCampoManager()"
                            class="p-1.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200"
                            title="Cerrar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <div class="p-6 space-y-6">

                    {{-- WIZARD 2-STEP: Adscribir Asignaturas --}}
                    @if(!$campoEditingId)
                        {{-- Step indicator --}}
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-bold {{ $wizardStep === 1 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-emerald-500/10 text-emerald-400' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold {{ $wizardStep === 1 ? 'text-emerald-400' : 'text-gray-500' }}">Filtrar</span>
                            </div>
                            <div class="w-8 h-px {{ $wizardStep === 2 ? 'bg-emerald-500/40' : 'bg-white/10' }}"></div>
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-bold {{ $wizardStep === 2 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-white/5 text-gray-500' }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </span>
                                <span class="text-[10px] font-bold {{ $wizardStep === 2 ? 'text-emerald-400' : 'text-gray-500' }}">Seleccionar</span>
                            </div>
                        </div>

                        @if($wizardStep === 1)
                            {{-- Paso 1: Filtrar --}}
                            <div class="bg-white/5 border border-white/10 rounded-lg p-4">
                                <h4 class="text-xs font-bold text-gray-300 mb-3 flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    Filtrar Asignaturas
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Plan de Estudio</label>
                                        <select wire:model.live="wizardFilterPestudio"
                                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                                            <option value="">Todos</option>
                                            @foreach($pestudios as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Grado (opcional)</label>
                                        <select wire:model.live="wizardFilterGrado"
                                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                                            <option value="">Todos los grados</option>
                                            @foreach($gradosList as $id => $name)
                                                <option value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Buscar asignatura</label>
                                        <input type="text" wire:model.live.debounce.300ms="wizardSearch" placeholder="Nombre o código..."
                                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                                    </div>
                                    <div class="flex gap-2 items-end">
                                        <button type="button" wire:click="nextStepWizard"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                                            Ver Asignaturas
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-600 mt-2">
                                    @php
                                        $count = $this->availableSubjects->count();
                                    @endphp
                                    {{ $count }} asignatura(s) disponibles
                                    @if($wizardFilterPestudio)
                                        para el plan de estudio seleccionado
                                    @endif
                                </p>
                            </div>
                        @else
                            {{-- Paso 2: Seleccionar --}}
                            <div class="bg-white/5 border border-white/10 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-xs font-bold text-gray-300 flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Seleccionar Asignaturas
                                    </h4>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" wire:click="selectAllAvailable"
                                            class="px-2 py-1 text-[10px] font-bold bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20 rounded-lg transition-all duration-200">
                                            Seleccionar Todas
                                        </button>
                                        <button type="button" wire:click="deselectAll"
                                            class="px-2 py-1 text-[10px] font-bold bg-gray-500/10 text-gray-400 hover:bg-gray-500/20 border border-white/5 rounded-lg transition-all duration-200">
                                            Deseleccionar
                                        </button>
                                    </div>
                                </div>

                                {{-- Tabla de asignaturas disponibles --}}
                                <div class="overflow-x-auto max-h-[260px] overflow-y-auto rounded-lg border border-white/5">
                                    <table class="w-full text-sm">
                                        <thead class="sticky top-0 bg-gray-800 z-10">
                                            <tr class="border-b border-white/5">
                                                <th class="text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500 w-10"></th>
                                                <th class="text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">Código</th>
                                                <th class="text-left px-3 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">Asignatura</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-white/5">
                                            @forelse($this->availableSubjects as $asignatura)
                                                <tr class="hover:bg-white/[0.02] transition-colors cursor-pointer" wire:click="toggleSubject({{ $asignatura->id }})">
                                                    <td class="px-3 py-1.5">
                                                        <input type="checkbox"
                                                               value="{{ $asignatura->id }}"
                                                               wire:model.live="selectedSubjects"
                                                               class="rounded border-gray-600 text-emerald-500 bg-white/5 focus:ring-emerald-500/50"
                                                               onclick="event.stopPropagation()">
                                                    </td>
                                                    <td class="px-3 py-1.5">
                                                        <span class="text-xs font-mono bg-white/5 text-gray-400 px-1.5 py-0.5 rounded-md">{{ $asignatura->code }}</span>
                                                    </td>
                                                    <td class="px-3 py-1.5">
                                                        <span class="text-sm text-gray-200">{{ $asignatura->name }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="px-3 py-8 text-center">
                                                        <svg class="w-8 h-8 text-gray-700 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                        </svg>
                                                        <p class="text-gray-500 text-xs">No hay asignaturas disponibles para adscribir.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Acciones paso 2 --}}
                                <div class="flex items-center justify-between mt-3">
                                    <button type="button" wire:click="prevStepWizard"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/5 transition-all duration-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Atrás
                                    </button>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] text-gray-500">
                                            <strong class="text-emerald-400">{{ count($selectedSubjects) }}</strong> seleccionada(s) de <strong class="text-gray-300">{{ $this->availableSubjects->count() }}</strong> disponible(s)
                                        </span>
                                        <button type="button" wire:click="assignSelectedSubjects" wire:loading.attr="disabled"
                                            class="inline-flex items-center gap-1.5 px-4 py-1.5 text-[11px] font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-200 {{ empty($selectedSubjects) ? 'opacity-40 cursor-not-allowed' : '' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Adscribir Seleccionadas ({{ count($selectedSubjects) }})
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Formulario de edición (solo cuando se edita una adscripción existente) --}}
                        <div class="bg-white/5 border border-white/10 rounded-lg p-4">
                            <h4 class="text-xs font-bold text-emerald-400 mb-2 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Editar Adscripción
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Asignatura</label>
                                    <select wire:model="campo_asignatura_id" disabled
                                        class="w-full bg-white/5 border border-white/10 text-gray-500 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all cursor-not-allowed">
                                        <option value="">Seleccione...</option>
                                        @foreach($asignaturasList as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Observaciones</label>
                                    <input type="text" wire:model="campo_observations" placeholder="Opcional"
                                        class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="saveCampo" wire:loading.attr="disabled"
                                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                        </svg>
                                        Actualizar
                                    </button>
                                    <button type="button" wire:click="resetCampoForm"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-gray-500/10 hover:bg-gray-500/20 text-gray-400 rounded-lg border border-white/5 transition-all duration-300 text-sm font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Tabla de adscripciones --}}
                    <div class="bg-white/[0.02] border border-white/5 rounded-lg overflow-hidden">
                        <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
                            <table class="w-full text-sm">
                                <thead class="sticky top-0 bg-gray-900 z-10">
                                    <tr class="border-b border-white/5">
                                        <th class="text-left px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500 w-12">#</th>
                                        <th class="text-left px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500">Asignatura</th>
                                        <th class="text-left px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden sm:table-cell">Código</th>
                                        <th class="text-left px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden md:table-cell">Observaciones</th>
                                        <th class="text-right px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-gray-500 w-24">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @forelse($this->campo_conocimientos as $campo)
                                        <tr class="hover:bg-white/[0.02] transition-colors group">
                                            <td class="px-4 py-2 text-xs text-gray-500 font-mono">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-2">
                                                <span class="text-sm text-gray-200 font-medium">{{ $campo->asignatura?->full_name ?? '—' }}</span>
                                            </td>
                                            <td class="px-4 py-2 hidden sm:table-cell">
                                                <span class="text-xs font-mono bg-white/5 text-gray-400 px-1.5 py-0.5 rounded-md">{{ $campo->asignatura?->code ?? '—' }}</span>
                                            </td>
                                            <td class="px-4 py-2 hidden md:table-cell">
                                                <span class="text-xs text-gray-500">{{ $campo->observations ? \Illuminate\Support\Str::limit($campo->observations, 30) : '—' }}</span>
                                            </td>
                                            <td class="px-4 py-2 text-right">
                                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button wire:click="editCampo({{ $campo->id }})"
                                                        class="p-1.5 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                                        title="Editar">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </button>
                                                    <button wire:click="confirmDeleteCampo({{ $campo->id }})"
                                                        class="p-1.5 bg-white/5 hover:bg-red-500/10 rounded-lg border border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                                                        title="Desadscribir">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-10 text-center">
                                                <svg class="w-10 h-10 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                <p class="text-gray-500 text-sm">No hay asignaturas adscritas a esta área.</p>
                                                <p class="text-gray-600 text-xs mt-1">Usa el formulario de arriba para adscribir asignaturas.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Delete campo confirmation --}}
                    @if($confirmDeleteCampoId)
                        <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="text-sm text-red-300 font-medium">¿Desadscribir esta asignatura del área?</span>
                            </div>
                            <div class="flex gap-2">
                                <x-button flat label="Cancelar" wire:click="cancelDeleteCampo" />
                                <x-button negative label="Eliminar" wire:click="destroyCampo" spinner="destroyCampo" />
                            </div>
                        </div>
                    @endif

                </div>
                {{-- Footer --}}
                <div class="flex justify-end px-6 py-2 border-t border-white/10">
                    <x-button flat label="Cerrar" wire:click="closeCampoManager" />
                </div>
            </div>
        </div>
    </div>
</div>
