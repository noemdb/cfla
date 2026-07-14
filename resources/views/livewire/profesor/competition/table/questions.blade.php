<div class="space-y-4">
    {{-- Filters bar --}}
    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-3 mb-3">
        {{-- Search --}}
        <div class="relative flex-1 w-full">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar pregunta..."
                class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-9 pr-3 py-2 text-xs text-gray-300 placeholder-gray-600 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200">
            @if($search)
                <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>

        {{-- Category --}}
        <div class="relative w-full lg:w-56">
            <select wire:model.live="selectedCategory"
                class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-3 pr-8 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 appearance-none cursor-pointer">
                <option value="">Todas las categorías</option>
                @foreach($categoriesWithCounts as $cat)
                    <option value="{{ $cat['key'] }}">{{ $cat['key'] }} ({{ $cat['count'] }})</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        {{-- Debate filter --}}
        <div class="relative w-full lg:w-52">
            <select wire:model.live="filterDebate"
                class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-3 pr-8 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 appearance-none cursor-pointer">
                <option value="">Todos los debates</option>
                @foreach($debates as $debate)
                    <option value="{{ $debate->id }}">{{ $debate->name }}</option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        {{-- Status filter --}}
        <div class="relative w-full lg:w-40">
            <select wire:model.live="filterStatus"
                class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-3 pr-8 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 appearance-none cursor-pointer">
                <option value="">Todos</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        {{-- Per page --}}
        <div class="relative w-20 shrink-0">
            <select wire:model.live="paginate"
                class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-3 pr-8 py-2 text-xs text-gray-300 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200 appearance-none cursor-pointer">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        {{-- New question button --}}
        <button wire:click="setCreate"
            class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            + Nueva
        </button>
    </div>

    {{-- Active filters badges --}}
    @if($search || $selectedCategory || $filterDebate || $filterStatus)
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-[10px] text-gray-600 font-medium uppercase tracking-wider">Filtros activos:</span>
            @if($search)
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                    Búsqueda: "{{ $search }}"
                    <button wire:click="$set('search', '')" class="hover:text-white ml-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
            @if($selectedCategory)
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                    {{ $selectedCategory }}
                    <button wire:click="$set('selectedCategory', '')" class="hover:text-white ml-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
            @if($filterDebate)
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                    {{ $debates->firstWhere('id', (int)$filterDebate)?->name ?? 'Debate' }}
                    <button wire:click="$set('filterDebate', '')" class="hover:text-white ml-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
            @if($filterStatus)
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                    {{ $filterStatus === 'active' ? 'Activos' : 'Inactivos' }}
                    <button wire:click="$set('filterStatus', '')" class="hover:text-white ml-0.5">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            @endif
        </div>
    @endif

    {{-- Results count --}}
    <div class="text-[10px] text-gray-600 font-medium">
        {{ $questions->total() }} pregunta(s) encontrada(s)
        @if($questions->total() > $paginate)
            · Mostrando página {{ $questions->currentPage() }} de {{ $questions->lastPage() }}
        @endif
    </div>

    {{-- Questions list --}}
    @if($questions->isEmpty())
        <div class="text-center py-10">
            <svg class="w-10 h-10 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm text-gray-500">No hay preguntas registradas</p>
            @if($search || $selectedCategory || $filterDebate || $filterStatus)
                <p class="text-xs text-gray-600 mt-1">No se encontraron preguntas con los filtros aplicados.</p>
                <button wire:click="$set('search', ''); $set('selectedCategory', ''); $set('filterDebate', ''); $set('filterStatus', '')"
                    class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                    Limpiar filtros
                </button>
            @else
                <p class="text-xs text-gray-600 mt-1">Presione <span class="text-emerald-400 font-medium">"+ Nueva Pregunta"</span> para comenzar.</p>
            @endif
        </div>
    @else
        <div class="space-y-2">
            @foreach($questions as $item)
                @php
                    $canDelete = $item->user_id === auth()->id();
                    $disabled = !$canDelete;
                @endphp
                <div class="bg-gray-800/30 border border-white/5 rounded-lg p-4 hover:border-white/10 transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            {{-- Header: category + time/weight --}}
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-400 shrink-0">
                                    {{ $questions->firstItem() + $loop->index }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                    {{ $item->category }}
                                </span>
                                <span class="text-[11px] text-gray-500 font-mono">
                                    ⏱ {{ $item->time }}s · {{ $item->weighting }} pts
                                </span>
                                @if(!$item->status_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-500/10 text-red-400 border border-red-500/20">
                                        Inactivo
                                    </span>
                                @endif
                                @if($item->debate)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                        {{ $item->debate->name }}
                                    </span>
                                @endif
                            </div>

                            {{-- Question text --}}
                            <p class="text-sm text-gray-200 leading-relaxed">{{ Str::limit($item->text, 200) }}</p>

                            {{-- Meta info --}}
                            <div class="flex items-center gap-3 mt-2 text-[10px] text-gray-600">
                                @if($item->user)
                                    <span>Creado por: {{ $item->user->username }}</span>
                                @endif
                                @if($item->observation)
                                    <span title="{{ $item->observation }}">📝 {{ Str::limit($item->observation, 40) }}</span>
                                @endif
                                @if($item->attachment)
                                    <a href="{{ $item->attachment_url }}" target="_blank" class="text-emerald-400 hover:text-emerald-300 underline">📎 Adjunto</a>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button wire:click="setShowDetail({{ $item->id }})"
                                title="Ver detalle de la pregunta"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-sky-500/10 text-sky-400 hover:bg-sky-500/20 border border-sky-500/20 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                            <button wire:click="toggleActive({{ $item->id }})"
                                title="{{ $item->status_active ? 'Desactivar pregunta' : 'Activar pregunta' }}"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all duration-200 {{ $item->status_active ? 'bg-gray-700/50 text-gray-400 hover:bg-emerald-500/20 hover:text-emerald-400 border border-white/10' : 'bg-gray-700/50 text-gray-500 hover:bg-emerald-500/20 hover:text-emerald-400 border border-white/10' }}">
                                @if($item->status_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                @endif
                            </button>
                            <button wire:click="manageOptions({{ $item->id }})"
                                title="Gestionar opciones"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-violet-500/10 text-violet-400 hover:bg-violet-500/20 border border-violet-500/20 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                            </button>
                            <button wire:click="setEdit({{ $item->id }})"
                                title="Editar pregunta"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button wire:click="questionDelete({{ $item->id }})"
                                title="Eliminar pregunta"
                                {{ $disabled ? 'disabled' : '' }}
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all duration-200 {{ $disabled ? 'bg-gray-800/50 text-gray-600 cursor-not-allowed' : 'bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $questions->links('vendor.livewire.custom-tailwind') }}
        </div>
    @endif
</div>
