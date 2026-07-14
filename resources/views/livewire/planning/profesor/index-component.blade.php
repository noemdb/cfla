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
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Carga Académica</label>
                <select wire:model.live="filter_pevaluacions"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    <option value="SI">Con carga</option>
                    <option value="NO">Sin carga</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Plan de Actividades</label>
                <select wire:model.live="filter_activities"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    <option value="SI">Con actividades</option>
                    <option value="NO">Sin actividades</option>
                </select>
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

    <!-- Table -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
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
                        <tr class="hover:bg-white/[0.02] transition-colors group">
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
                                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
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
                                    <button type="button" wire:click="confirmDelete({{ $profesor->id }})"
                                        class="p-2 bg-white/5 hover:bg-red-500/10 rounded-lg border border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                                        title="Eliminar profesor">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
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

        @include('components.pagination-wrapper', ['paginator' => $profesors])
    </div>

    <!-- ===== MODAL: Confirmar Eliminación ===== -->
    <x-modal title="Eliminar Profesor" blur="lg" wire:model="confirmDeleteId" max-width="md" persistent>
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

    <!-- ===== MODAL: Vista Previa ===== -->
    <x-modal title="Detalles del Profesor" blur="lg" wire:model="previewMode" max-width="2xl">
        @if($previewProfesor)
        <div class="relative p-6 space-y-6">
            <button type="button" wire:click="closePreview"
                class="absolute top-6 right-6 p-1.5 bg-white/10 hover:bg-red-500/20 rounded-lg text-gray-400 hover:text-red-400 transition-all duration-200 z-10"
                title="Cerrar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-14 h-14 rounded-full bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 text-lg font-bold">
                    {{ strtoupper(substr($previewProfesor->name ?? '?', 0, 1)) }}{{ strtoupper(substr($previewProfesor->lastname ?? '', 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">{{ $previewProfesor->full_name }}</h3>
                    <p class="text-sm text-gray-400 font-mono">{{ $previewProfesor->ci_profesor }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Tipo Facilitador</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewProfesor->ti_teacher ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Género</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewProfesor->gender === 'M' ? 'Masculino' : 'Femenino' }}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Carga Académica</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewProfesor->pevaluacions_count }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Actividades</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewProfesor->activities_count }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Usuario</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewProfesor->user?->username ?? '—' }}</p>
                </div>
            </div>
            @if($previewProfesor->email || $previewProfesor->cellphone)
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Email</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewProfesor->email ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Celular</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewProfesor->cellphone ?? '—' }}</p>
                </div>
            </div>
            @endif
            <div class="flex justify-end border-t border-white/5 pt-4">
                <x-button flat label="Cerrar" wire:click="closePreview" />
            </div>
        </div>
        @endif
    </x-modal>

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
</div>
