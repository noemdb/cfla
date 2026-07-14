<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white mb-2">Programas Educativos</h1>
            <p class="text-emerald-400 font-medium">Gestión de los programas educativos del sistema académico.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('app.planning.index') }}"
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
                Nuevo Programa
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Nombre..."
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Estado</label>
                <select wire:model.live="filter_status"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
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

    <!-- Table -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="text-left px-5 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">#</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden md:table-cell">Periodo</th>
                        <th class="text-left px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Responsable</th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Planes</th>
                        <th class="text-center px-4 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                        <th class="text-right px-5 py-3.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($peducativos as $peducativo)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-5 py-4 text-sm text-gray-400 font-mono">{{ $peducativo->id }}</td>
                            <td class="px-4 py-4">
                                <span class="text-sm font-bold text-white">{{ $peducativo->name }}</span>
                                @if($peducativo->description)
                                    <span class="block text-[10px] text-gray-500 mt-0.5">{{ \Illuminate\Support\Str::limit($peducativo->description, 60) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-400 hidden md:table-cell">
                                {{ $peducativo->pescolar?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-400 hidden lg:table-cell">
                                {{ $peducativo->manager?->username ?? '—' }}
                            </td>
                            <td class="px-4 py-4 text-center hidden lg:table-cell">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white/5 text-gray-300 text-sm font-bold">
                                    {{ $peducativo->pestudios_count }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($peducativo->status_active === 'true' || $peducativo->status_active == 1)
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
                                    <button type="button" wire:click="showPreview({{ $peducativo->id }})"
                                        class="p-2 bg-white/5 hover:bg-cyan-500/10 rounded-lg border border-white/5 hover:border-cyan-500/20 text-gray-400 hover:text-cyan-400 transition-all duration-200"
                                        title="Vista previa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="edit({{ $peducativo->id }})"
                                        class="p-2 bg-white/5 hover:bg-emerald-500/10 rounded-lg border border-white/5 hover:border-emerald-500/20 text-gray-400 hover:text-emerald-400 transition-all duration-200"
                                        title="Editar programa educativo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button type="button" wire:click="confirmDelete({{ $peducativo->id }})"
                                        class="p-2 bg-white/5 hover:bg-red-500/10 rounded-lg border border-white/5 hover:border-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                                        title="Eliminar programa educativo">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium mb-1">No hay programas educativos registrados</p>
                                    <p class="text-gray-600 text-sm">Crea el primer programa usando el botón "Nuevo Programa".</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($peducativos->hasPages())
            <div class="px-5 py-4 border-t border-white/5">
                {{ $peducativos->links() }}
            </div>
        @endif
    </div>

    <!-- ===== MODAL: Confirmar Eliminación ===== -->
    <x-modal title="Eliminar Programa Educativo" blur="lg" wire:model="confirmDeleteId" max-width="md" x-on:close="confirmDeleteId = null" persistent>
        <div class="p-6 text-center">
            <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <h3 class="text-lg font-bold text-white mb-2">¿Eliminar este programa educativo?</h3>
            <p class="text-sm text-gray-400 mb-6">Esta acción eliminará el programa educativo. Solo se puede eliminar si no tiene planes de estudio asociados.</p>
            <div class="flex justify-center gap-4">
                <x-button flat label="Cancelar" x-on:click="confirmDeleteId = null" />
                <x-button negative label="Eliminar" wire:click="destroy" spinner="destroy" />
            </div>
        </div>
    </x-modal>

    <!-- ===== MODAL: Vista Previa ===== -->
    <x-modal title="Detalles del Programa Educativo" blur="lg" wire:model="previewMode" max-width="2xl">
        @if($previewPeducativo)
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
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Nombre</span>
                    <p class="text-sm text-white font-bold mt-1">{{ $previewPeducativo->name }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</span>
                    <p class="mt-1">
                        @if($previewPeducativo->status_active === 'true')
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
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Periodo Escolar</span>
                <p class="text-sm text-gray-300 mt-1">{{ $previewPeducativo->pescolar?->name ?? '—' }}</p>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Descripción</span>
                <p class="text-sm text-gray-300 mt-1">{{ $previewPeducativo->description ?? '—' }}</p>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Orden</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPeducativo->order }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Planes de Estudio</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPeducativo->pestudios_count }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Ind. Cuantitativos</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPeducativo->show_quantitative_indicators === 'true' ? 'Sí' : 'No' }}</p>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Responsable</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPeducativo->manager?->username ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Subdirector</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPeducativo->deputy?->username ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Asistente</span>
                    <p class="text-sm text-gray-300 mt-1">{{ $previewPeducativo->assistant?->username ?? '—' }}</p>
                </div>
            </div>
            <div class="flex justify-end border-t border-white/5 pt-4">
                <x-button flat label="Cerrar" wire:click="closePreview" />
            </div>
        </div>
        @endif
    </x-modal>

    <!-- ===== MODAL: Formulario Crear/Editar ===== -->
    <x-modal-card title="{{ $isEditing ? 'Editar Programa Educativo' : 'Nuevo Programa Educativo' }}" blur="lg" wire:model="modeForm" max-width="4xl" persistent>
        <div class="space-y-8">

            {{-- Errores globales de validación --}}
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

            {{-- Sección 1: Información General --}}
            <div>
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Información General
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Periodo Escolar *</label>
                        <select wire:model="pescolar_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Seleccione...</option>
                            @foreach($pescolars as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('pescolar_id') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Nombre *</label>
                        <input type="text" wire:model="name" placeholder="Nombre del programa educativo"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('name') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Orden</label>
                        <select wire:model="order"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('order') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Estado</label>
                        <select wire:model="status_active"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="true">Activo</option>
                            <option value="false">Inactivo</option>
                        </select>
                        @error('status_active') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Descripción</label>
                        <input type="text" wire:model="description" placeholder="Breve descripción del programa"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
                        @error('description') <p class="text-red-400 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Sección 2: Equipo Directivo --}}
            <div class="border-t border-white/5 pt-6">
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Equipo Directivo
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Responsable</label>
                        <select wire:model="manager_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Sin asignar</option>
                            @foreach($users as $id => $username)
                                <option value="{{ $id }}">{{ $username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Subdirector</label>
                        <select wire:model="deputy_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Sin asignar</option>
                            @foreach($users as $id => $username)
                                <option value="{{ $id }}">{{ $username }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1.5">Asistente</label>
                        <select wire:model="assistant_id"
                            class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                            <option value="">Sin asignar</option>
                            @foreach($users as $id => $username)
                                <option value="{{ $id }}">{{ $username }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Sección 3: Indicadores --}}
            <div class="border-t border-white/5 pt-6">
                <h3 class="text-sm font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Indicadores
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center gap-2 cursor-pointer group p-3 bg-white/5 rounded-xl border border-white/5">
                        <input type="checkbox" wire:model="show_quantitative_indicators" value="true"
                            {{ in_array($show_quantitative_indicators, [true, 'true', 1, '1'], true) ? 'checked' : '' }}
                            class="w-4 h-4 text-emerald-500 bg-white/5 border-white/10 rounded focus:ring-emerald-500/50 focus:ring-2">
                        <span class="text-xs text-gray-300 group-hover:text-white transition-colors font-medium">Mostrar indicadores cuantitativos</span>
                    </label>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                <x-button flat label="Cancelar" x-on:click="close" />
                <x-button primary label="{{ $isEditing ? 'Actualizar Programa' : 'Guardar Programa' }}" wire:click="save" spinner="save" />
            </div>
        </x-slot>
    </x-modal-card>
</div>
