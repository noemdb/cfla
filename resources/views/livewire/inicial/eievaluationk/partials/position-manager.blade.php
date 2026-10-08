{{--
    Gestor de POSICIONES de evaluación (`eievaluationps`).

    Estados: `position` (lista + formulario de alta) y `edit-position` (edición).
    Se alimenta de `openModal('position', $id)` / `openModal('edit-position', $id)`.

    ─────────────────────────────────────────────────────────────────────────────
    UNA POSICIÓN NO ES UN RESUMEN
    ─────────────────────────────────────────────────────────────────────────────
    En los otros documentos la unidad es un ÁREA (o una celda día × momento). Aquí
    es el REGISTRO de una actividad de evaluación: qué grupo de niños participó,
    qué se alcanzó, con qué instrumento y qué se observó. Por eso el legacy solo
    exigía el área (`pevaluacion_id`) y dejó el resto opcional: la docente abre
    una fila por niño o por grupo a medida que recoge el dato.

    La lista se agrupa por área para que la revisión sea legible: un plan con 30
    posiciones de 5 áreas no se lee en una tabla plana.

    Reglas: App\Http\Requests\Inicial\EievaluationpRequest
--}}
<div class="space-y-5">

    @php
        $plan = $eievaluationk_id
            ? \App\Models\app\Inicial\Eievaluationk::find($eievaluationk_id)
            : null;
        $posiciones = $plan ? $plan->getOrderedEvaluationps() : collect();

        // Agrupadas por área: la clave natural de una posición es (área, orden).
        $porArea = $posiciones->groupBy('pevaluacion_id');
    @endphp

    @if (! $plan)
        <p class="text-sm text-gray-500">No se ha seleccionado un plan válido.</p>
    @else
        {{-- ═══ Posiciones existentes, agrupadas por área ═══ --}}
        <div class="space-y-4">
            @forelse ($porArea as $pevaluacionId => $delArea)
                @php
                    $area = $delArea->first();
                    $asignatura = $area->pevaluacion?->pensum?->asignatura?->name ?? 'Área sin nombre';
                @endphp
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">
                        {{ $asignatura }}
                        <span class="text-purple-300 normal-case tracking-normal">({{ $delArea->count() }})</span>
                    </h4>

                    <div class="space-y-2">
                        @foreach ($delArea as $position)
                            <div class="rounded-lg border border-white/10 bg-gray-800/40 px-4 py-3 flex items-start justify-between gap-3">
                                <div class="min-w-0 space-y-1">
                                    <p class="text-sm text-gray-200 truncate">
                                        @if ($position->nombre_ninos)
                                            {{ $position->nombre_ninos }}
                                        @else
                                            <span class="text-gray-500 italic">Participantes sin nombrar</span>
                                        @endif
                                        @if ($position->fecha)
                                            <span class="text-xs text-gray-500">
                                                · {{ $position->fecha->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </p>

                                    @if ($position->aprendizaje_alcanzado)
                                        <p class="text-xs text-gray-400 line-clamp-2">
                                            {{ \Illuminate\Support\Str::limit($position->aprendizaje_alcanzado, 140) }}
                                        </p>
                                    @endif

                                    @if ($position->instrumento)
                                        <p class="text-xs text-gray-500">
                                            <span class="text-gray-500">Instrumento:</span>
                                            {{ \Illuminate\Support\Str::limit($position->instrumento, 60) }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5 shrink-0">
                                    <button wire:click="openModal('edit-position', {{ $position->id }})" type="button"
                                        class="text-[11px] px-2 py-1 rounded-md bg-purple-500/15 text-purple-300 hover:bg-purple-500/25 border border-purple-500/20 transition-colors">
                                        Editar
                                    </button>
                                    <button wire:click="confirmDeletePosition({{ $position->id }})" type="button"
                                        class="p-1.5 rounded-md bg-white/5 hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition-colors"
                                        aria-label="Eliminar posición">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m4-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 rounded-lg border border-dashed border-white/10 px-4 py-6 text-center">
                    Este plan aún no tiene posiciones registradas.
                </p>
            @endforelse
        </div>

        {{-- ═══ Formulario ═══ --}}
        <div class="space-y-4 border-t border-white/10 pt-4">
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">
                {{ $eievaluationp_id ? 'Editar posición' : 'Nueva posición' }}
            </h4>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="p-area" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Área de aprendizaje <span class="text-red-400">*</span>
                    </label>
                    <select id="p-area" wire:model.live="eievaluationp.pevaluacion_id"
                        @disabled(empty($listPevaluacion))
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none disabled:opacity-50">
                        <option value="">Seleccione un área</option>
                        @foreach ($listPevaluacion as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                    @error('eievaluationp.pevaluacion_id')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror

                    @if (empty($listPevaluacion))
                        <p class="mt-1 text-xs text-amber-400/90">
                            No hay áreas evaluables en el lapso de este plan.
                        </p>
                    @endif
                </div>

                <div>
                    <label for="p-fecha" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Fecha de la actividad <span class="text-gray-500">(opcional)</span>
                    </label>
                    {{-- Datepicker nativo. El legacy validaba `string` sobre una
                         columna DATE: un texto tipo "05/10/2026" se guardaba como
                         NULL con un warning silencioso. --}}
                    <input id="p-fecha" type="date" wire:model.live="eievaluationp.fecha"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                    @error('eievaluationp.fecha')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="p-ninos" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Niños participantes <span class="text-gray-500">(opcional)</span>
                </label>
                <input id="p-ninos" type="text" wire:model.live="eievaluationp.nombre_ninos"
                    placeholder="Ana, Luis y María"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                @error('eievaluationp.nombre_ninos')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="p-aprendizaje" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Aprendizaje alcanzado <span class="text-gray-500">(opcional)</span>
                </label>
                <textarea id="p-aprendizaje" rows="2" wire:model.live="eievaluationp.aprendizaje_alcanzado"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none"></textarea>
                @error('eievaluationp.aprendizaje_alcanzado')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="p-componente" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Componente <span class="text-gray-500">(opcional)</span>
                    </label>
                    <input id="p-componente" type="text" wire:model.live="eievaluationp.componente"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                    @error('eievaluationp.componente')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="p-instrumento" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Instrumento <span class="text-gray-500">(opcional)</span>
                    </label>
                    <input id="p-instrumento" type="text" wire:model.live="eievaluationp.instrumento"
                    placeholder="Observación directa, lista de cotejo, portafolio…"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                    @error('eievaluationp.instrumento')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="p-indicadores" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Indicadores <span class="text-gray-500">(opcional)</span>
                </label>
                <textarea id="p-indicadores" rows="2" wire:model.live="eievaluationp.indicadores"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none"></textarea>
                @error('eievaluationp.indicadores')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-[1fr_7rem] items-end">
                <div>
                    <label for="p-observacion" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Observación <span class="text-gray-500">(opcional)</span>
                    </label>
                    <textarea id="p-observacion" rows="2" wire:model.live="eievaluationp.observacion"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none"></textarea>
                    @error('eievaluationp.observacion')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="p-order" class="block text-xs font-medium text-gray-400 mb-1">Orden</label>
                    <input id="p-order" type="number" min="1" step="1" wire:model.live="eievaluationp.order"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 outline-none">
                    @error('eievaluationp.order')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    @endif
</div>