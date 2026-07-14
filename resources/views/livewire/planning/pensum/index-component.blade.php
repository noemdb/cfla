<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Pensums</h1>
            <p class="text-emerald-400 font-medium">Pivote central del sistema: vincula planes de estudio, grados y asignaturas.</p>
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
                Nuevo Pensum
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
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Plan, grado, asignatura..."
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
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Grado</label>
                <select wire:model.live="filter_grado"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($listGrados as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
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
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                            <button wire:click="sortBy('id')" class="flex items-center gap-1 hover:text-white transition-colors">
                                #
                                @if($sortField === 'id')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                            <button wire:click="sortBy('pestudio_id')" class="flex items-center gap-1 hover:text-white transition-colors">
                                Plan Estudio
                                @if($sortField === 'pestudio_id')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                            <button wire:click="sortBy('grado_id')" class="flex items-center gap-1 hover:text-white transition-colors">
                                Grado
                                @if($sortField === 'grado_id')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                            <button wire:click="sortBy('asignatura_id')" class="flex items-center gap-1 hover:text-white transition-colors">
                                Asignatura
                                @if($sortField === 'asignatura_id')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">
                            <button wire:click="sortBy('status_component')" class="inline-flex items-center gap-1 hover:text-white transition-colors">
                                Comp.
                                @if($sortField === 'status_component')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">
                            <button wire:click="sortBy('status_active')" class="inline-flex items-center gap-1 hover:text-white transition-colors">
                                Activo
                                @if($sortField === 'status_active')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">
                            <button wire:click="sortBy('diag_competencies_count')" class="inline-flex items-center gap-1 hover:text-white transition-colors">
                                Comp. x Ref.
                                @if($sortField === 'diag_competencies_count')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">
                            <button wire:click="sortBy('pevaluacions_count')" class="inline-flex items-center gap-1 hover:text-white transition-colors">
                                Cargas
                                @if($sortField === 'pevaluacions_count')
                                    <svg class="w-3 h-3 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-right px-5 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($pensums as $pensum)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-5 py-4 text-sm text-gray-400 font-mono">{{ $pensum->id }}</td>
                            <td class="px-4 py-4">
                                <span class="text-sm font-bold text-white">{{ $pensum->pestudio?->code ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-gray-200 font-medium">{{ $pensum->grado?->name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-gray-200">{{ $pensum->asignatura?->name ?? '—' }}</span>
                                <span class="block text-[10px] text-gray-500 mt-0.5 font-mono">{{ $pensum->asignatura?->code ?? '' }}</span>
                            </td>
                            <td class="px-4 py-4 text-center hidden lg:table-cell">
                                @if($pensum->status_component === 'true')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-500/10 text-purple-400 text-[10px] font-bold rounded-md border border-purple-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Sí
                                    </span>
                                @else
                                    <span class="text-gray-500 text-[10px]">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center hidden lg:table-cell">
                                @if($pensum->status_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-500/10 text-red-400 text-[10px] font-bold rounded-md border border-red-500/20">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            {{-- Competencias agrupadas por referente normativo --}}
                            <td class="px-4 py-4 text-center hidden lg:table-cell">
                                @php
                                    $groups = $pensum->diagCompetencies
                                        ->groupBy(fn($c) => $c->referent?->code ?? 'N/A')
                                        ->sortKeys();
                                @endphp
                                @if($groups->isNotEmpty())
                                    <div class="flex flex-wrap items-center justify-center gap-1">
                                        @foreach($groups as $code => $group)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20"
                                                  title="{{ $group->first()?->referent?->name ?? 'Sin referente' }}">
                                                {{ $code }}
                                                <span class="text-amber-500/60 font-mono">({{ $group->count() }})</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-500 text-[10px]">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center hidden lg:table-cell">
                                <span class="inline-flex items-center justify-center min-w-[2rem] h-7 rounded-lg bg-white/5 text-gray-300 text-sm font-bold px-2">
                                    {{ $pensum->pevaluacions_count }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" wire:click="showPreview({{ $pensum->id }})"
                                        class="p-2 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200"
                                        title="Vista previa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="edit({{ $pensum->id }})"
                                        class="p-2 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                        title="Editar pensum">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $pensum->id }})"
                                        class="p-2 bg-white/5 hover:bg-red-500/10 rounded-lg border border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                                        title="Eliminar pensum">
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
                                    <svg class="w-14 h-14 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium mb-1">No hay pensums registrados</p>
                                    <p class="text-gray-600 text-sm">Crea el primer pensum usando el botón "Nuevo Pensum".</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pensums->hasPages())
            <div class="px-5 py-4 border-t border-white/5">
                {{ $pensums->links('vendor.livewire.custom-tailwind') }}
            </div>
        @endif
    </div>

    <!-- ===== MODAL: Confirmar Eliminación ===== -->
    <x-modal title="Eliminar Pensum" blur="lg" wire:model="confirmDeleteId" width="md" x-on:close="confirmDeleteId = null" persistent>
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar este pensum?</h3>
            <p class="text-sm text-gray-400 mb-6">Esta acción eliminará el pensum. Solo se puede eliminar si no tiene cargas académicas asociadas.</p>
            <div class="flex justify-center gap-3">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Vista Previa ===== -->
    <x-modal title="Detalles del Pensum" blur="lg" wire:model="previewMode" width="max-w-[90vw]">
        @if($previewPensum)
        <div class="relative bg-gray-900 rounded-lg border border-white/10 p-6 space-y-6">
            <button type="button" wire:click="closePreview"
                class="absolute top-6 right-6 p-1.5 bg-white/10 hover:bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-all duration-200 z-10"
                title="Cerrar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            {{-- Fila 1: Plan Estudio + Estado --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Plan de Estudio</span>
                    <p class="text-sm text-white font-bold mt-1">{{ $previewPensum->pestudio?->full_name ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</span>
                    <p class="mt-1">
                        @if($previewPensum->status_active)
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

            {{-- Fila 2: Grado + Asignatura --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Grado</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPensum->grado?->full_name ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Asignatura</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPensum->asignatura?->full_name ?? '—' }}</p>
                </div>
            </div>

            {{-- Fila 3: Componentes + Diagnóstico + Cargas --}}
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Componentes Form.</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPensum->status_component === 'true' ? 'Sí' : 'No' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Diagnóstico Activo</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPensum->status_active_diagnostic ? 'Sí' : 'No' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Cargas Académicas</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPensum->pevaluacions_count }}</p>
                </div>
            </div>

            {{-- Observaciones --}}
            @if($previewPensum->observations)
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Observaciones</span>
                <p class="text-sm text-gray-300 mt-1">{{ $previewPensum->observations }}</p>
            </div>
            @endif

            {{-- Referentes normativos con competencias e indicadores --}}
            @php
                $compByReferent = $previewPensum->diagCompetencies
                    ->groupBy(fn($c) => $c->referent?->code ?? 'N/A')
                    ->sortKeys();
            @endphp
            @if($compByReferent->isNotEmpty())
                <div class="border-t border-white/10 pt-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-300 uppercase tracking-wider">Referentes Normativos</span>
                        <span class="text-[10px] text-gray-500 font-mono">({{ $previewPensum->diag_competencies_count }} comp.)</span>
                    </div>
                    <div class="space-y-3">
                        @foreach($compByReferent as $code => $group)
                            @php
                                $first = $group->first();
                            @endphp
                            <div class="bg-gray-800/60 border border-white/10 rounded-lg overflow-hidden">
                                <div class="flex items-center justify-between px-3 py-2 bg-gray-800/80">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-amber-300">{{ $first?->referent?->name ?? $code }}</span>
                                        <span class="text-[10px] text-gray-500 font-mono">({{ $code }})</span>
                                    </div>
                                    <span class="text-[10px] text-gray-400">
                                        {{ $group->count() }} competencia(s)
                                    </span>
                                </div>
                                @if($group->isNotEmpty())
                                    <div class="px-3 py-2 space-y-2">
                                        @foreach($group as $competency)
                                            <div class="pl-3 border-l-2 border-emerald-500/40">
                                                <p class="text-xs font-medium text-emerald-300">{{ $competency->name }}</p>
                                                @php
                                                    $indicators = $competency->indicators ?? collect();
                                                @endphp
                                                @if($indicators->isNotEmpty())
                                                    <ul class="mt-1 space-y-0.5">
                                                        @foreach($indicators as $indicator)
                                                            <li class="text-[11px] text-gray-400 flex items-start gap-1.5">
                                                                <span class="text-gray-600 mt-0.5 select-none">•</span>
                                                                <span>{{ $indicator->description }}</span>
                                                                @if($indicator->code)
                                                                    <span class="text-[10px] text-gray-600 font-mono shrink-0">[{{ $indicator->code }}]</span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-[11px] text-gray-600 italic ml-4">Sin indicadores asociados</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="border-t border-white/10 pt-4">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-gray-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Referentes Normativos</span>
                    </div>
                    <p class="text-sm text-gray-600 italic">Este pensum no tiene competencias asociadas a ningún referente normativo.</p>
                </div>
            @endif

            <div class="flex justify-end border-t border-white/10 pt-4">
                <x-button flat label="Cerrar" wire:click="closePreview" />
            </div>
        </div>
        @endif
    </x-modal>

    <!-- ===== MODAL: Formulario Crear/Editar ===== -->
    <x-modal-card title="{{ $form->isEditing ? 'Editar Pensum' : 'Nuevo Pensum' }}" blur="lg" wire:model="modeForm" width="2xl" persistent>
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

            {{-- Sección 1: Selección de relaciones --}}
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Vinculación Académica
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Plan de Estudio *</label>
                        <select wire:model.live="form.pestudio_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($pestudios as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('form.pestudio_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Grado *</label>
                        <select wire:model="form.grado_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($grados as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @if(empty($grados) && $form->pestudio_id)
                            <p class="text-amber-400 text-[10px] mt-1">No hay grados activos para este plan.</p>
                        @endif
                        @error('form.grado_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Asignatura *</label>
                        <select wire:model="form.asignatura_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($asignaturas as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @if(empty($asignaturas) && $form->pestudio_id)
                            <p class="text-amber-400 text-[10px] mt-1">No hay asignaturas para este plan.</p>
                        @endif
                        @error('form.asignatura_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 2: Flags --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                    </svg>
                    Configuración
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Componentes de Formación</label>
                        <select wire:model="form.status_component"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="false">No</option>
                            <option value="true">Sí</option>
                        </select>
                        @error('form.status_component') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Estado</label>
                        <label class="flex items-center gap-2 cursor-pointer group mt-2">
                            <input type="checkbox" wire:model="form.status_active" value="1"
                                {{ $form->status_active ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-500 bg-white/5 border-white/10 rounded focus:ring-emerald-500/50 focus:ring-2">
                            <span class="text-xs text-gray-300 group-hover:text-white transition-colors">Pensum activo</span>
                        </label>
                        @error('form.status_active') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Diagnóstico</label>
                        <label class="flex items-center gap-2 cursor-pointer group mt-2">
                            <input type="checkbox" wire:model="form.status_active_diagnostic" value="1"
                                {{ $form->status_active_diagnostic ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-500 bg-white/5 border-white/10 rounded focus:ring-emerald-500/50 focus:ring-2">
                            <span class="text-xs text-gray-300 group-hover:text-white transition-colors">Activo para diagnóstico</span>
                        </label>
                        @error('form.status_active_diagnostic') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 3: Observaciones --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Observaciones
                </h3>
                <div>
                    <input type="text" wire:model="form.observations" placeholder="Observaciones adicionales del pensum"
                        class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                    @error('form.observations') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button primary label="{{ $form->isEditing ? 'Actualizar Pensum' : 'Guardar Pensum' }}" wire:click="save" spinner="save" />
            </div>
        </x-slot>
    </x-modal-card>
</div>
