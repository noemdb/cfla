{{--
    Gestor de actividades por área de aprendizaje de la planificación.

    Estados: `summary` (lista + formulario de alta) y `edit-activity` (edición).

    R5: componente, objetivo, aprendizaje esperado e indicadores son
    obligatorios; línea de investigación y énfasis curriculares son opcionales
    (el trait del legacy los exigía, el runtime no — decisión del blueprint).
--}}
<div class="space-y-5">

    @if ($eispecialk_id)
        @php
            $plan = \App\Models\app\Inicial\Eispecialk::find($eispecialk_id);
            $actividades = $plan ? $plan->getOrderedActivities() : collect();
        @endphp

        {{-- ═══ Lista de actividades existentes ═══ --}}
        <div class="space-y-2">
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">
                Actividades guardados ({{ $actividades->count() }})
            </h4>

            @forelse ($actividades as $actividad)
                <div class="flex items-start justify-between gap-3 rounded-lg border border-white/10 bg-gray-800/40 px-4 py-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-200 truncate">
                            {{ $actividad->pevaluacion?->pensum?->asignatura?->name ?? 'Área sin nombre' }}
                        </p>
                        @if ($actividad->componente)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $actividad->componente }}</p>
                        @endif
                        @if ($actividad->objetivo)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $actividad->objetivo }}</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button wire:click="openModal('edit-activity', {{ $actividad->id }})" type="button"
                            class="p-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors"
                            title="Editar actividad">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="confirmDeleteActivity({{ $actividad->id }})" type="button"
                            class="p-2 rounded-lg bg-white/5 hover:bg-red-500/20 border border-white/5 text-gray-400 hover:text-red-400 transition-colors"
                            title="Eliminar actividad">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 rounded-lg border border-dashed border-white/10 px-4 py-6 text-center">
                    Este plan aún no tiene actividades por área.
                </p>
            @endforelse
        </div>

        <hr class="border-white/10">

        {{-- ═══ Formulario ═══ --}}
        <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">
            {{ $eispecialact_id ? 'Editando actividad' : 'Nuevo actividad' }}
        </h4>

        <div class="space-y-4">
            {{-- Área de aprendizaje: filtro por momento (lapso) --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="s-lapso" class="block text-xs font-medium text-gray-400 mb-1.5">Momento</label>
                    <select id="s-lapso" wire:model.live="lapso_id"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                        <option value="">Todos los momentos</option>
                        @foreach ($listLapso as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="s-area" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Área de aprendizaje <span class="text-red-400">*</span>
                    </label>
                    <select id="s-area" wire:model.live="eispecialact.pevaluacion_id"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                        <option value="">Seleccione un área</option>
                        @foreach ($listPevaluacion as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                    @error('eispecialact.pevaluacion_id')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="s-componente" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Componente <span class="text-red-400">*</span>
                </label>
                <input id="s-componente" type="text" wire:model.live="eispecialact.componente"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                @error('eispecialact.componente')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="s-objetivo" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Objetivo <span class="text-red-400">*</span>
                </label>
                <textarea id="s-objetivo" rows="2" wire:model.live="eispecialact.objetivo"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none"></textarea>
                @error('eispecialact.objetivo')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="s-aprendizaje" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Aprendizaje esperado <span class="text-red-400">*</span>
                </label>
                <textarea id="s-aprendizaje" rows="2" wire:model.live="eispecialact.aprendizaje_esperado"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none"></textarea>
                @error('eispecialact.aprendizaje_esperado')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="s-indicadores" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Indicadores <span class="text-red-400">*</span>
                </label>
                <textarea id="s-indicadores" rows="2" wire:model.live="eispecialact.indicadores"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none"></textarea>
                @error('eispecialact.indicadores')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Opcionales: el trait legacy los exigía, el runtime no (R5). --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="s-linea" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Línea de investigación <span class="text-gray-500">(opcional)</span>
                    </label>
                    <input id="s-linea" type="text" wire:model.live="eispecialact.linea_investigacion"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                    @error('eispecialact.linea_investigacion')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="s-enfasis" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Énfasis curriculares <span class="text-gray-500">(opcional)</span>
                    </label>
                    <input id="s-enfasis" type="text" wire:model.live="eispecialact.enfasis_curriculares"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                    @error('eispecialact.enfasis_curriculares')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="w-28">
                <label for="s-order" class="block text-xs font-medium text-gray-400 mb-1">Orden</label>
                <input id="s-order" type="number" min="1" step="1" wire:model.live="eispecialact.order"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                @error('eispecialact.order')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>
    @else
        <p class="text-sm text-gray-500">No se ha seleccionado un plan válido.</p>
    @endif
</div>
