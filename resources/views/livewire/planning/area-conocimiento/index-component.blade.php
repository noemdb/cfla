<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white mb-2">Áreas de Conocimiento</h1>
            <p class="text-emerald-400 font-medium">Catálogo de áreas de conocimiento y asignaturas adscritas.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('planning.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 rounded-xl border border-cyan-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Planificación
            </a>
            <button type="button" wire:click="create"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-xl border border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Área
            </button>
            <button wire:click="$refresh"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl border border-white/5 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Refrescar
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 p-5 rounded-2xl mb-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre, código..."
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Plan de Estudio</label>
                <select wire:model.live="filter_pestudio"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($pestudios as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Programa Educativo</label>
                <select wire:model.live="filter_peducativo"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($peducativos as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Ver</label>
                <select wire:model.live="paginate"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="12">12</option>
                    <option value="24">24</option>
                    <option value="48">48</option>
                    <option value="96">96</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="$refresh"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-xl border border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Filtrar
                </button>
            </div>
        </div>
    </div>

    <!-- Grid Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($area_conocimientos as $area)
            <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl hover:border-emerald-500/30 hover:bg-gray-800/50 transition-all duration-200 group flex flex-col">

                {{-- Card Header — plan badge + delete --}}
                <div class="flex items-start justify-between px-4 pt-3.5 pb-2 border-b border-white/5">
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-bold text-white truncate" title="{{ $area->name }}">{{ $area->name }}</h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ $area->code_sm }}
                            </span>
                            @if($area->pestudio)
                                <span class="text-[9px] text-gray-500 truncate">{{ $area->pestudio->code }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold {{ $area->enable_academic_index === 'true' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-gray-500/10 text-gray-500' }} shrink-0" title="{{ $area->enable_academic_index === 'true' ? 'Afecta índice académico' : 'No afecta índice académico' }}">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $area->enable_academic_index === 'true' ? 'I. Acad' : '—' }}
                    </span>
                </div>

                {{-- Card Body — details --}}
                <div class="px-4 py-3 space-y-1.5 flex-1">
                    {{-- Código --}}
                    <div class="flex items-center gap-2 text-[11px]">
                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                        <span class="text-gray-400">{{ $area->code ?: '—' }}</span>
                    </div>

                    {{-- Descripción --}}
                    @if($area->description)
                        <div class="flex items-start gap-2 text-[11px]">
                            <svg class="w-3.5 h-3.5 text-gray-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            <span class="text-gray-400 line-clamp-2">{{ $area->description }}</span>
                        </div>
                    @endif

                    {{-- Jefe --}}
                    <div class="flex items-center gap-2 text-[11px]">
                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-gray-400">{{ $area->leader?->username ?? 'Sin jefe' }}</span>
                    </div>

                    {{-- Orden --}}
                    <div class="flex items-center gap-2 text-[11px]">
                        <svg class="w-3.5 h-3.5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 9l5-5 5 5M7 15l5 5 5-5"></path>
                        </svg>
                        <span class="text-gray-400">Orden {{ $area->order }}</span>
                    </div>
                </div>

                {{-- Stats Row --}}
                <div class="flex items-center justify-between px-4 py-2.5 border-t border-white/5 bg-white/[0.02]">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1" title="Asignaturas adscritas">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-[10px] font-bold {{ $area->campo_conocimientos_count > 0 ? 'bg-blue-500/10 text-blue-400' : 'bg-gray-500/10 text-gray-500' }}">
                                {{ $area->campo_conocimientos_count }}
                            </span>
                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="text-[10px] text-gray-500">Asignaturas</span>
                        </div>
                    </div>
                    @if($area->observations)
                        <span class="text-[9px] text-gray-600 truncate max-w-[100px]" title="{{ $area->observations }}">
                            {{ \Illuminate\Support\Str::limit($area->observations, 18) }}
                        </span>
                    @endif
                </div>

                {{-- Card Actions --}}
                <div class="px-4 pb-3.5 pt-2.5 border-t border-white/5 flex items-center gap-1.5">
                    <button type="button" wire:click="openCampoManager({{ $area->id }})"
                        title="Gestionar Asignaturas Adscritas"
                        class="inline-flex items-center justify-center flex-1 gap-1.5 px-3 py-2 rounded-xl text-[10px] font-bold transition-all duration-200 {{ $area->campo_conocimientos_count > 0 ? 'bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 border border-blue-500/20' : 'bg-gray-500/10 text-gray-500 hover:bg-gray-500/20 border border-white/5' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Asignaturas
                    </button>
                    <button type="button" wire:click="edit({{ $area->id }})"
                        title="Editar Área"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button type="button" wire:click="confirmDelete({{ $area->id }})"
                        title="Eliminar Área"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl text-xs font-bold bg-red-500/10 text-red-400 hover:bg-red-500/20 border border-red-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <svg class="w-14 h-14 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-gray-500 font-medium mb-1">No hay áreas de conocimiento registradas</p>
                <p class="text-gray-600 text-sm">Crea la primera área usando el botón "Nueva Área".</p>
            </div>
        @endforelse
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
            <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar esta área de conocimiento?</h3>
            <p class="text-sm text-gray-400 mb-6">Solo se puede eliminar si no tiene asignaturas adscritas.</p>
            <div class="flex justify-center gap-4">
                <x-button flat label="Cancelar" x-on:click="$wire.confirmDeleteId = null" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Formulario Crear/Editar Área ===== -->
    <x-modal-card title="{{ $isEditing ? 'Editar Área de Conocimiento' : 'Nueva Área de Conocimiento' }}" blur="lg" wire:model="modeForm" max-width="3xl" persistent>
        <div class="space-y-6">

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4">
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
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Identificación
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Plan Educativo</label>
                        <select wire:model="peducativo_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">—</option>
                            @foreach($peducativos as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Plan de Estudio *</label>
                        <select wire:model="pestudio_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
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
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Jefe del Área</label>
                        <select wire:model="leader_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
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
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    Códigos
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Código *</label>
                        <input type="text" wire:model="code" placeholder="Ej: LEN" maxlength="20" style="text-transform:uppercase"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('code') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Abreviación *</label>
                        <input type="text" wire:model="code_sm" placeholder="Ej: LEN" maxlength="10" style="text-transform:uppercase"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('code_sm') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Orden</label>
                        <select wire:model="order"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            @for($i = 1; $i <= 30; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('order') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Índice Académico</label>
                        <select wire:model="enable_academic_index"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="true">Sí</option>
                            <option value="false">No</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Sección 3: Descripción y Observaciones --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Descripción
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Descripción</label>
                        <textarea wire:model="description" rows="3" placeholder="Descripción del área de conocimiento"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Observaciones</label>
                        <textarea wire:model="observations" rows="3" placeholder="Observaciones adicionales"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600 resize-none"></textarea>
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
            <div class="relative w-full max-w-[95vw] bg-gray-900 border border-white/10 rounded-2xl shadow-2xl"
                 x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
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

                    {{-- Formulario rápido --}}
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                        <h4 class="text-xs font-bold text-emerald-400 mb-3 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            {{ $campoEditingId ? 'Editar Adscripción' : 'Adscribir Asignatura' }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                            <div class="md:col-span-1">
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Asignatura *</label>
                                <select wire:model="campo_asignatura_id"
                                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                                    <option value="">Seleccione...</option>
                                    @foreach($asignaturasList as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('campo_asignatura_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Observaciones</label>
                                <input type="text" wire:model="campo_observations" placeholder="Opcional"
                                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                            </div>
                            <div class="flex gap-2">
                                <button type="button" wire:click="saveCampo" wire:loading.attr="disabled"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-xl border border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    {{ $campoEditingId ? 'Actualizar' : 'Adscribir' }}
                                </button>
                                @if($campoEditingId)
                                    <button type="button" wire:click="resetCampoForm"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-gray-500/10 hover:bg-gray-500/20 text-gray-400 rounded-xl border border-white/5 transition-all duration-300 text-sm font-bold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Tabla de adscripciones --}}
                    <div class="bg-white/[0.02] border border-white/5 rounded-xl overflow-hidden">
                        <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
                            <table class="w-full text-sm">
                                <thead class="sticky top-0 bg-gray-900 z-10">
                                    <tr class="border-b border-white/5">
                                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 w-12">#</th>
                                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500">Asignatura</th>
                                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden sm:table-cell">Código</th>
                                        <th class="text-left px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden md:table-cell">Observaciones</th>
                                        <th class="text-right px-4 py-3 text-[10px] font-bold uppercase tracking-widest text-gray-500 w-24">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    @forelse($this->campo_conocimientos as $campo)
                                        <tr class="hover:bg-white/[0.02] transition-colors group">
                                            <td class="px-4 py-3 text-xs text-gray-500 font-mono">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-3">
                                                <span class="text-sm text-gray-200 font-medium">{{ $campo->asignatura?->full_name ?? '—' }}</span>
                                            </td>
                                            <td class="px-4 py-3 hidden sm:table-cell">
                                                <span class="text-xs font-mono bg-white/5 text-gray-400 px-1.5 py-0.5 rounded-md">{{ $campo->asignatura?->code ?? '—' }}</span>
                                            </td>
                                            <td class="px-4 py-3 hidden md:table-cell">
                                                <span class="text-xs text-gray-500">{{ $campo->observations ? \Illuminate\Support\Str::limit($campo->observations, 30) : '—' }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-right">
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
                        <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-4 flex items-center justify-between">
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
                <div class="flex justify-end px-6 py-4 border-t border-white/10">
                    <x-button flat label="Cerrar" wire:click="closeCampoManager" />
                </div>
            </div>
        </div>
    </div>
</div>
