<div class="fade-in">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-white mb-2">Etiquetas de Actividades</h1>
            <p class="text-emerald-400 font-medium">Labels de Activity/Achievement por programa educativo. Se usan en el form, las listas y los PDFs del profesor.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" wire:click="create"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/20 transition-all duration-300 text-sm font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Nueva Etiqueta
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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
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
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Modelo</label>
                <select wire:model.live="filter_model"
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    <option value="activity">Actividad</option>
                    <option value="achievement">Indicador</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Campo o etiqueta..."
                    class="w-full bg-white/5 border border-white/10 text-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 outline-none transition-all placeholder:text-gray-600">
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-gray-900/40 backdrop-blur-md border border-white/5 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="text-left px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Programa</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Modelo</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Campo</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Etiqueta</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500 hidden lg:table-cell">Por defecto</th>
                        <th class="text-center px-4 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Estado</th>
                        <th class="text-right px-5 py-2.5 text-[10px] font-bold uppercase tracking-widest text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($labels as $row)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-5 py-2 text-sm text-gray-300 font-medium whitespace-nowrap">{{ $row->peducativo?->name ?? '—' }}</td>
                            <td class="px-4 py-2">
                                @if($row->model === 'achievement')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-bold rounded-md border border-blue-500/20">Indicador</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-bold rounded-md border border-emerald-500/20">Actividad</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-400 font-mono">{{ $row->field }}</td>
                            <td class="px-4 py-2">
                                <span class="text-sm font-bold text-white">{{ $row->label }}</span>
                                @if($row->placeholder)
                                    <span class="block text-[10px] text-gray-500 mt-0.5">ph: {{ \Illuminate\Support\Str::limit($row->placeholder, 60) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500 hidden lg:table-cell">{{ $this->defaultFor($row->model, $row->field) ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">
                                @if($this->isCustomized($row))
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-500/10 text-amber-400 text-[10px] font-bold rounded-md border border-amber-500/20">
                                        Personalizada
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-gray-500/10 text-gray-400 text-[10px] font-bold rounded-md border border-white/10">
                                        Por defecto
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-2 text-right">
                                <div class="flex items-center justify-end">
                                    {{-- Segmented btn-group (Editar + Clonar + Restablecer + Eliminar) --}}
                                    <div class="inline-flex items-stretch rounded-lg overflow-hidden border border-white/10 divide-x divide-white/5"
                                        role="group" aria-label="Acciones de la etiqueta">
                                        <button type="button" wire:click="edit({{ $row->id }})"
                                            class="w-9 inline-flex items-center justify-center py-2 text-xs font-bold bg-emerald-500/12 text-emerald-400 hover:bg-emerald-500/20 transition-all duration-200"
                                            title="Editar etiqueta" aria-label="Editar etiqueta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button type="button" wire:click="openClone({{ $row->id }})"
                                            class="w-9 inline-flex items-center justify-center py-2 text-xs font-bold bg-cyan-500/12 text-cyan-400 hover:bg-cyan-500/20 transition-all duration-200"
                                            title="Clonar etiqueta a otro programa" aria-label="Clonar etiqueta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                        @if($this->isCustomized($row))
                                            <button type="button" wire:click="confirmReset({{ $row->id }})"
                                                class="w-9 inline-flex items-center justify-center py-2 text-xs font-bold bg-amber-500/12 text-amber-400 hover:bg-amber-500/20 transition-all duration-200"
                                                title="Restablecer valor por defecto" aria-label="Restablecer etiqueta">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        <button type="button" wire:click="confirmDelete({{ $row->id }})"
                                            class="w-9 inline-flex items-center justify-center py-2 text-xs font-bold bg-red-500/12 text-red-400 hover:bg-red-500/20 transition-all duration-200"
                                            title="Eliminar etiqueta" aria-label="Eliminar etiqueta">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center">
                                <div>
                                    <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <p class="text-gray-500 font-medium mb-1">No hay etiquetas para este filtro</p>
                                    <p class="text-gray-600 text-sm">Crea la primera usando el botón "Nueva Etiqueta".</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($labels->hasPages())
            <x-pagination-wrapper :paginator="$labels" />
        @endif
    </div>

    {{-- EDIT MODAL --}}
    @if($modeForm)
        <div class="fixed inset-0 z-[9998] overflow-y-auto" wire:key="label-edit-modal-{{ $label_id }}">
            <div class="fixed inset-0 bg-black/70 backdrop-blur-sm" wire:click="close"></div>
            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-gray-900 border border-white/10 rounded-lg shadow-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-2 border-b border-white/5 bg-gray-800/50">
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">{{ $isEditing ? 'Editar Etiqueta' : 'Nueva Etiqueta' }}</h3>
                            @if($isEditing)
                                <p class="text-xs text-gray-500">{{ $peducativos[$peducativo_id] ?? '' }} · {{ $model === 'achievement' ? 'Indicador' : 'Actividad' }} · <span class="font-mono">{{ $field }}</span></p>
                            @else
                                <p class="text-xs text-gray-500">Define programa, modelo, campo y etiqueta.</p>
                            @endif
                        </div>
                        <button wire:click="close"
                            class="p-1.5 text-gray-500 hover:text-white hover:bg-white/5 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        @if(!$isEditing)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Programa Educativo</label>
                                    <select wire:model="peducativo_id"
                                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500/50 outline-none transition-all">
                                        <option value="">Seleccione...</option>
                                        @foreach($peducativos as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('peducativo_id') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Modelo</label>
                                    <select wire:model="model"
                                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500/50 outline-none transition-all">
                                        <option value="activity">Actividad</option>
                                        <option value="achievement">Indicador</option>
                                    </select>
                                    @error('model') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Campo</label>
                                    <input type="text" wire:model="field"
                                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 font-mono focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
                                        placeholder="topic">
                                    @error('field') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Etiqueta</label>
                            <input type="text" wire:model="label"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
                                placeholder="{{ $this->defaultFor($model ?? '', $field ?? '') ?? '' }}">
                            @error('label') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                            <p class="text-[10px] text-gray-600 mt-1">Por defecto: {{ $this->defaultFor($model ?? '', $field ?? '') ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-1.5">Placeholder (opcional)</label>
                            <input type="text" wire:model="placeholder"
                                class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all duration-200"
                                placeholder="Texto de ayuda dentro del campo...">
                            @error('placeholder') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="px-6 py-2 border-t border-white/5 bg-gray-800/30 flex items-center justify-between">
                        <button wire:click="close"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                            Cancelar
                        </button>
                        <button wire:click="save"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-xs font-bold bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 border border-emerald-500/20 transition-all duration-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- RESET MODAL --}}
    @if($confirmResetId)
        <x-modal title="Restablecer Etiqueta" blur="lg" wire:model="confirmResetId" max-width="md" persistent>
            <p class="text-sm text-gray-400">La etiqueta volverá a su valor por defecto. Esta acción es inmediata en form, listas y PDFs (vía caché).</p>
            <x-slot name="footer">
                <div class="flex items-center justify-end gap-2">
                    <x-button flat label="Cancelar" wire:click="cancelReset" />
                    <x-button red label="Restablecer" wire:click="resetToDefault" />
                </div>
            </x-slot>
        </x-modal>
    @endif

    <!-- ===== DIALOG: Clonar Etiqueta (WireUI x-dialog) ===== -->
    <x-dialog id="label-clone" title="Clonar Etiqueta" width="md" blur="lg">
        <div class="text-left">
            @if($cloneSource)
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-cyan-500/10 text-cyan-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-700 dark:text-slate-200">
                            ¿Clonar la etiqueta
                            <span class="font-bold text-gray-900 dark:text-white">"{{ $cloneSource->label }}"</span>
                            ({{ $cloneSource->peducativo?->name }})?
                        </p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">
                            Campo <span class="font-mono">{{ $cloneSource->model }}.{{ $cloneSource->field }}</span> — se copiará etiqueta y placeholder al programa destino.
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 dark:text-slate-400 mb-1.5">Programa destino</label>
                    <select wire:model="cloneTargetPeducativoId"
                        class="w-full bg-gray-800/50 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:border-cyan-500/50 outline-none transition-all">
                        <option value="">Seleccione...</option>
                        @foreach($peducativos as $id => $name)
                            @if((int) $id !== (int) $cloneSource->peducativo_id)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('cloneTargetPeducativoId') <span class="text-red-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Si el destino ya tiene esa etiqueta, se sobrescribe con estos valores.</p>
                </div>
            @endif

            <div class="mt-5 flex items-center justify-end gap-3 border-t border-white/10 pt-4">
                <button type="button" x-on:click="close(); $wire.closeClone()"
                    class="px-4 py-2 rounded-lg text-sm font-bold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 transition-all duration-200">
                    Cancelar
                </button>
                <button type="button" wire:click="cloneToTarget"
                    x-on:click="close()"
                    wire:loading.attr="disabled" wire:target="cloneToTarget"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold text-white bg-cyan-600 hover:bg-cyan-500 transition-all duration-200 disabled:opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span wire:loading.remove wire:target="cloneToTarget">Sí, clonar</span>
                    <span wire:loading wire:target="cloneToTarget">Clonando...</span>
                </button>
            </div>
        </div>
    </x-dialog>

    {{-- DELETE MODAL --}}
    @if($confirmDeleteId)
        <x-modal title="Eliminar Etiqueta" blur="lg" wire:model="confirmDeleteId" max-width="md" persistent>
            <p class="text-sm text-gray-400">Se eliminará la fila y el campo volverá a su valor por defecto. Usa "Sincronizar" para recrearla después si la necesitas.</p>
            <x-slot name="footer">
                <div class="flex items-center justify-end gap-2">
                    <x-button flat label="Cancelar" wire:click="cancelDelete" />
                    <x-button red label="Eliminar" wire:click="destroy" />
                </div>
            </x-slot>
        </x-modal>
    @endif
</div>
