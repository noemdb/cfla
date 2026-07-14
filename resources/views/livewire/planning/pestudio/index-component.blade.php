<div class="fade-in" x-data="{
    modeForm: @entangle('modeForm'),
}">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Planes de Estudio</h1>
            <p class="text-emerald-400 font-medium">Gestión de los planes de estudio del sistema académico.</p>
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
                Nuevo Plan
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre, código..."
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
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
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Estado</label>
                <select wire:model.live="filter_status"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
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

    <!-- Table -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">#</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Código</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden md:table-cell">Programa</th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Grados</th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Planning</th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                        <th class="text-right px-5 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($pestudios as $pestudio)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-5 py-4 text-sm text-gray-400 font-mono">{{ $pestudio->id }}</td>
                            <td class="px-4 py-4">
                                <span class="text-sm font-bold text-white">{{ $pestudio->code }}</span>
                                @if($pestudio->code_oficial)
                                    <span class="block text-[10px] text-gray-500 mt-0.5">{{ $pestudio->code_oficial }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-gray-200 font-medium">{{ $pestudio->name }}</span>
                                <span class="block text-[10px] text-gray-500 mt-0.5">{{ \Illuminate\Support\Str::limit($pestudio->description, 50) }}</span>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-400 hidden md:table-cell">
                                {{ $pestudio->peducativo?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-center hidden lg:table-cell">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/5 text-gray-300 text-sm font-bold">
                                    {{ $pestudio->grados_count }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center hidden lg:table-cell">
                                @if($pestudio->planning_module === 'true' || $pestudio->planning_module == 1)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-500/10 text-gray-400 text-[10px] font-bold rounded-md border border-gray-500/20">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($pestudio->status_active === 'true' || $pestudio->status_active == 1)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-500/10 text-red-400 text-[10px] font-bold rounded-md border border-red-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" wire:click="showPreview({{ $pestudio->id }})"
                                        class="p-2 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200"
                                        title="Vista previa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="edit({{ $pestudio->id }})"
                                        class="p-2 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                        title="Editar plan de estudio">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $pestudio->id }})"
                                        class="p-2 bg-white/5 hover:bg-red-500/10 rounded-lg border border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                                        title="Eliminar plan de estudio">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <div>
                                    <svg class="w-14 h-14 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium mb-1">No hay planes de estudio registrados</p>
                                    <p class="text-gray-600 text-sm">Crea el primer plan de estudio usando el botón "Nuevo Plan".</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pestudios->hasPages())
            <x-pagination-wrapper :paginator="$pestudios" />
        @endif
    </div>

    <!-- ===== MODAL: Confirmar Eliminación ===== -->
    <x-modal title="Eliminar Plan de Estudio" blur="lg" wire:model="confirmDeleteId" max-width="md" x-on:close="confirmDeleteId = null" persistent>
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar este plan de estudio?</h3>
            <p class="text-sm text-gray-400 mb-6">Esta acción eliminará el plan de estudio. Solo se puede eliminar si no tiene grados asociados.</p>
            <div class="flex justify-center gap-4">
                <x-button flat label="Cancelar" x-on:click="confirmDeleteId = null" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Vista Previa ===== -->
    <x-modal title="Detalles del Plan de Estudio" blur="lg" wire:model="previewMode" max-width="3xl">
        @if($previewPestudio)
        <div class="relative p-6 space-y-6">
            <button type="button" wire:click="closePreview"
                class="absolute top-6 right-6 p-1.5 bg-white/10 hover:bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-all duration-200 z-10"
                title="Cerrar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Código</span>
                    <p class="text-sm text-white font-bold mt-1">{{ $previewPestudio->code }} @if($previewPestudio->code_oficial)<span class="text-gray-400 font-normal ml-2">({{ $previewPestudio->code_oficial }})</span>@endif</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</span>
                    <p class="mt-1">
                        @if($previewPestudio->status_active === 'true')
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
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre</span>
                <p class="text-sm text-white mt-1">{{ $previewPestudio->name }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Programa Educativo</span>
                <p class="text-sm text-gray-300 mt-1">{{ $previewPestudio->peducativo?->name ?? '—' }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Descripción</span>
                <p class="text-sm text-gray-300 mt-1">{{ $previewPestudio->description ?? '—' }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Responsable</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPestudio->manager?->username ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Grados Asociados</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPestudio->grados_count }}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Orden</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPestudio->order }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Color</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPestudio->color ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Planning</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPestudio->planning_module == 1 ? 'Sí' : 'No' }}</p>
                </div>
            </div>
            @if($previewPestudio->description_aux || $previewPestudio->mention || $previewPestudio->title)
            <div class="grid grid-cols-3 gap-4">
                @if($previewPestudio->description_aux)
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Desc. Auxiliar</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPestudio->description_aux }}</p>
                </div>
                @endif
                @if($previewPestudio->mention)
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Mención</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPestudio->mention }}</p>
                </div>
                @endif
                @if($previewPestudio->title)
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Título</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPestudio->title }}</p>
                </div>
                @endif
            </div>
            @endif
            <div class="flex justify-end border-t border-white/5 pt-4">
                <x-button flat label="Cerrar" wire:click="closePreview" />
            </div>
        </div>
        @endif
    </x-modal>

    <!-- ===== MODAL: Formulario Crear/Editar ===== -->
    <x-modal-card title="{{ $isEditing ? 'Editar Plan de Estudio' : 'Nuevo Plan de Estudio' }}" blur="lg" wire:model="modeForm" max-width="7xl" persistent>
        <div class="space-y-8">

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

            {{-- Sección 1: Información General --}}
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Información General
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Programa Educativo *</label>
                        <select wire:model="peducativo_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($peducativos as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('peducativo_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Código *</label>
                        <input type="text" wire:model="code" placeholder="Ej: MG-2025"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('code') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Código Oficial</label>
                        <input type="text" wire:model="code_oficial" placeholder="Ej: MG-OF-001"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nombre *</label>
                        <input type="text" wire:model="name" placeholder="Nombre completo del plan de estudio"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Orden</label>
                        <select wire:model="order"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('order') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Color</label>
                        <select wire:model="color"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            @foreach($color_options as $opt)
                                <option value="{{ $opt }}">{{ ucfirst($opt) }}</option>
                            @endforeach
                        </select>
                        @error('color') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Formato Papel</label>
                        <select wire:model="paper"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="letter">Carta</option>
                            <option value="oficial">Oficio</option>
                        </select>
                        @error('paper') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Responsable (admin)</label>
                        <select wire:model="manager_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Sin asignar</option>
                            @foreach($users as $id => $username)
                                <option value="{{ $id }}">{{ $username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Descripción *</label>
                        <input type="text" wire:model="description" placeholder="Breve descripción del plan"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('description') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Descripción Auxiliar</label>
                        <input type="text" wire:model="description_aux"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Mención</label>
                        <input type="text" wire:model="mention"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Título</label>
                        <input type="text" wire:model="title" placeholder="Título que otorga"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Componente de Formación</label>
                        <input type="text" wire:model="description_trainig_component"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Activities AVR</label>
                        <input type="number" wire:model="activities_avr" min="0"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        @error('activities_avr') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Perfil de Egreso</label>
                        <textarea wire:model="profile" rows="3"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- Sección 2: Configuración --}}
            <div class="border-t border-white/5 pt-6">
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                    </svg>
                    Configuración
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Escala de Evaluación *</label>
                        <select wire:model="scale"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($escalas as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('scale') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="lg:col-span-2">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mt-1">
                            @php
                                // ENUM('true','false') fields — usan value="true"
                                $enumToggles = [
                                    'status_active' => 'Activo',
                                    'status_build_promotion' => 'Genera Promoción',
                                    'show_hr' => 'Cuadro de Honor',
                                    'status_a_cualitative' => 'Cualitativo',
                                    'status_baremo' => 'Nota Literal',
                                    'status_carga_notas' => 'Carga de Notas',
                                    'show_quantitative_indicators' => 'Indic. Cuantitativos',
                                ];
                                // TINYINT(1) fields — usan value="1"
                                $tinyintToggles = [
                                    'status_inscripcion_active' => 'Inscripciones Act.',
                                    'status_socials' => 'Actividad Social',
                                    'planning_module' => 'Módulo Planning',
                                ];
                            @endphp
                            @foreach($enumToggles as $field => $label)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" wire:model="{{ $field }}" value="true"
                                        {{ in_array(${$field}, [true, 'true', 1, '1'], true) ? 'checked' : '' }}
                                        class="w-4 h-4 text-emerald-500 bg-white/5 border-white/10 rounded focus:ring-emerald-500/50 focus:ring-2">
                                    <span class="text-xs text-gray-300 group-hover:text-white transition-colors">{{ $label }}</span>
                                </label>
                            @endforeach
                            @foreach($tinyintToggles as $field => $label)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" wire:model="{{ $field }}" value="1"
                                        {{ in_array(${$field}, [true, 'true', 1, '1'], true) ? 'checked' : '' }}
                                        class="w-4 h-4 text-emerald-500 bg-white/5 border-white/10 rounded focus:ring-emerald-500/50 focus:ring-2">
                                    <span class="text-xs text-gray-300 group-hover:text-white transition-colors">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sección 3: Fechas de Cierre --}}
            <div class="border-t border-white/5 pt-6">
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Fechas de Cierre
                </h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Remisión Resumen</label>
                        <input type="date" wire:model="remision_resumen_final"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        @error('remision_resumen_final') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Informe Final</label>
                        <input type="date" wire:model="fecha_informe_final"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        @error('fecha_informe_final') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Certificación</label>
                        <input type="date" wire:model="fecha_certificacion"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        @error('fecha_certificacion') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Descriptivo</label>
                        <input type="date" wire:model="fecha_descriptivo"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        @error('fecha_descriptivo') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Promoción</label>
                        <input type="date" wire:model="fecha_promocion"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        @error('fecha_promocion') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Prosecución</label>
                        <input type="date" wire:model="fecha_prosecucion"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                        @error('fecha_prosecucion') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button primary label="{{ $isEditing ? 'Actualizar Plan' : 'Guardar Plan' }}" wire:click="save" spinner="save" />
            </div>
        </x-slot>
    </x-modal-card>
</div>
