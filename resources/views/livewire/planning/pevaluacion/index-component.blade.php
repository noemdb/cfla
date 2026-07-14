<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Carga Académica</h1>
            <p class="text-emerald-400 font-medium">Asignación de profesores a áreas de formación, secciones y lapsos.</p>
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
                Nueva Asignación
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Profesor, asignatura, sección..."
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
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Profesor</label>
                <select wire:model.live="filter_profesor"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($profesors as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
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
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Sección</label>
                <select wire:model.live="filter_seccion"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($secciones as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Momento</label>
                <select wire:model.live="filter_lapso"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    @foreach($lapsos as $id => $name)
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
                <button wire:click="resetFilters"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 rounded-lg border border-amber-500/20 transition-all duration-300 text-sm font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Limpiar Filtros
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
                            <button wire:click="sortBy('pevaluacions.id')" class="flex items-center gap-1 hover:text-white transition-colors">
                                #
                                @if($sortField === 'pevaluacions.id')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                            <button wire:click="sortBy('profesor_id')" class="flex items-center gap-1 hover:text-white transition-colors">
                                Profesor
                                @if($sortField === 'profesor_id')
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden md:table-cell">Plan Est.</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Área / Grado / Sección</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Momento</th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Act.</th>
                        <th class="text-right px-5 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($pevaluacions as $pevaluacion)
                        @php $lapsoClosed = $pevaluacion->is_lapso_closed; @endphp
                        <tr class="hover:bg-white/[0.02] transition-colors group {{ $lapsoClosed ? 'opacity-60' : '' }}">
                            <td class="px-5 py-4 text-sm text-gray-400 font-mono">{{ $pevaluacion->id }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($pevaluacion->profesor?->name ?? '?', 0, 1)) }}{{ strtoupper(substr($pevaluacion->profesor?->lastname ?? '', 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-white font-medium">{{ $pevaluacion->profesor?->full_name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 hidden md:table-cell">
                                <span class="inline-flex items-center px-2 py-0.5 bg-cyan-500/10 text-cyan-400 text-[10px] font-bold rounded-md border border-cyan-500/20">
                                    {{ $pevaluacion->pensum?->pestudio?->code ?? '—' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-sm text-gray-200">{{ $pevaluacion->pensum?->asignatura?->name ?? '—' }}</span>
                                <span class="block text-[10px] text-gray-500 mt-0.5">
                                    {{ $pevaluacion->seccion?->grado?->name ?? '?' }} - Secc. {{ $pevaluacion->seccion?->name ?? '?' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 hidden lg:table-cell">
                                <span class="text-sm text-gray-300">{{ $pevaluacion->lapso?->name ?? '—' }}</span>
                                @if($lapsoClosed)
                                    <br><span class="text-[10px] text-red-400 font-medium">Cerrado</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2rem] h-7 rounded-lg bg-white/5 text-gray-300 text-sm font-bold px-2">
                                    {{ $pevaluacion->activities_count }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" wire:click="showPreview({{ $pevaluacion->id }})"
                                        class="p-2 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200"
                                        title="Vista previa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="edit({{ $pevaluacion->id }})"
                                        class="p-2 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                        title="{{ $lapsoClosed ? 'Lapso cerrado: no se puede editar' : 'Editar asignación' }}"
                                        @if($lapsoClosed) disabled @endif>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $pevaluacion->id }})"
                                        class="p-2 bg-white/5 hover:bg-red-500/10 rounded-lg border border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                                        title="Eliminar asignación"
                                        @if($pevaluacion->activities_count > 0) disabled @endif>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <div>
                                    <svg class="w-14 h-14 text-gray-700 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium mb-1">No hay asignaciones registradas</p>
                                    <p class="text-gray-600 text-sm">Crea la primera asignación usando el botón "Nueva Asignación".</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pevaluacions->hasPages())
            <div class="px-5 py-4 border-t border-white/5">
                {{ $pevaluacions->links('vendor.livewire.custom-tailwind') }}
            </div>
        @endif
    </div>

    <!-- ===== MODAL: Confirmar Eliminación ===== -->
    <x-modal title="Eliminar Carga Académica" blur="lg" wire:model="confirmDeleteId" max-width="md" persistent>
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar esta asignación?</h3>
            <p class="text-sm text-gray-400 mb-6">Esta acción eliminará la asignación de carga académica. Solo se puede eliminar si no tiene actividades registradas.</p>
            <div class="flex justify-center gap-4">
                <x-button flat label="Cancelar" x-on:click="confirmDeleteId = null" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Vista Previa ===== -->
    <x-modal title="Detalles de la Carga Académica" blur="lg" wire:model="previewMode" max-width="2xl">
        @if($previewPevaluacion)
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
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Profesor</span>
                    <p class="text-sm text-white font-bold mt-1">{{ $previewPevaluacion->profesor?->full_name ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</span>
                    <p class="mt-1">
                        @if($previewPevaluacion->status_official)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-500/20">Oficial</span>
                        @endif
                        @if($previewPevaluacion->status_note_report)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[10px] font-bold rounded-md border border-blue-500/20 ml-1">En Inf. Notas</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Área de Formación</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPevaluacion->pensum?->asignatura?->full_name ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Plan de Estudio</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPevaluacion->pensum?->pestudio?->full_name ?? '—' }}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Sección</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPevaluacion->seccion?->full_name ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Momento</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPevaluacion->lapso?->full_name ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Tipo de Nota</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPevaluacion->nota_type ?? '—' }}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Actividades</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPevaluacion->activities_count }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Grupo Estable</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPevaluacion->grupoEstable?->name ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Escala</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPevaluacion->escala?->name ?? '—' }}</p>
                </div>
            </div>
            @if($previewPevaluacion->description)
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Descripción</span>
                <p class="text-sm text-gray-300 mt-1">{{ $previewPevaluacion->description }}</p>
            </div>
            @endif
            @if($previewPevaluacion->observations)
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Observaciones</span>
                <p class="text-sm text-gray-300 mt-1">{{ $previewPevaluacion->observations }}</p>
            </div>
            @endif
            <div class="flex justify-end border-t border-white/5 pt-4">
                <x-button flat label="Cerrar" wire:click="closePreview" />
            </div>
        </div>
        @endif
    </x-modal>

    <!-- ===== MODAL: Formulario Crear/Editar ===== -->
    <x-modal-card title="{{ $form->isEditing ? 'Editar Carga Académica' : 'Nueva Carga Académica' }}" blur="lg" wire:model="modeForm" max-width="6xl" persistent>
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

            {{-- Sección 1: Vinculación Académica --}}
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Vinculación Académica
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Grado/Año *</label>
                        <select wire:model.live="form.grado_id"
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
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Sección *</label>
                        <select wire:model="form.seccion_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($secciones as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @if(empty($secciones) && $form->grado_id)
                            <p class="text-amber-400 text-[10px] mt-1">No hay secciones activas para este grado.</p>
                        @endif
                        @error('form.seccion_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Área de Formación (Pensum) *</label>
                        <select wire:model="form.pensum_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($pensums as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @if(empty($pensums) && $form->grado_id)
                            <p class="text-amber-400 text-[10px] mt-1">No hay pensums activos para este grado.</p>
                        @endif
                        @error('form.pensum_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 2: Asignación Docente --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Docente y Configuración
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Profesor *</label>
                        <select wire:model="form.profesor_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($profesors as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('form.profesor_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Momento (Lapso) *</label>
                        <select wire:model="form.lapso_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($lapsos as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('form.lapso_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Tipo de Nota</label>
                        <select wire:model="form.nota_type"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="ACUMULATIVA">Acumulativa</option>
                            <option value="PROMEDIADA">Promediada</option>
                        </select>
                        @error('form.nota_type') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Escala de Notas</label>
                        <select wire:model="form.escala_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($escalas as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('form.escala_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Grupo Estable</label>
                        <select wire:model="form.grupo_estable_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($grupos_estables as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('form.grupo_estable_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 3: Flags --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                    </svg>
                    Configuración
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" wire:model="form.status_official" value="1"
                                {{ $form->status_official ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-500 bg-white/5 border-white/10 rounded focus:ring-emerald-500/50 focus:ring-2">
                            <span class="text-xs text-gray-300 group-hover:text-white transition-colors">Documento oficial</span>
                        </label>
                        @error('form.status_official') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" wire:model="form.status_note_report" value="1"
                                {{ $form->status_note_report ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-500 bg-white/5 border-white/10 rounded focus:ring-emerald-500/50 focus:ring-2">
                            <span class="text-xs text-gray-300 group-hover:text-white transition-colors">En Informe de Notas</span>
                        </label>
                        @error('form.status_note_report') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Baremo</label>
                        <input type="text" wire:model="form.status_baremo" placeholder="Estado baremo"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('form.status_baremo') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 4: Descripción y Observaciones --}}
            <div class="border-t border-white/5 pt-5">
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Descripción
                </h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Objetivo</label>
                        <input type="text" wire:model="form.objetivo" placeholder="Objetivo general de la asignación"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('form.objetivo') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Descripción</label>
                        <input type="text" wire:model="form.description" placeholder="Descripción de la asignación"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('form.description') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Observaciones</label>
                        <input type="text" wire:model="form.observations" placeholder="Observaciones adicionales"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('form.observations') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Categoría</label>
                        <input type="text" wire:model="form.category" placeholder="Categoría (opcional)"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('form.category') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="$wire.modeForm = false" />
                <x-button primary label="{{ $form->isEditing ? 'Actualizar Asignación' : 'Guardar Asignación' }}" wire:click="save" spinner="save" />
            </div>
        </x-slot>
    </x-modal-card>
</div>
