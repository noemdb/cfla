{{--
    Gestor de REVISIONES del proyecto de aula (`eiprojectreviews`).

    Estados: `review` (lista + formulario de alta) y `edit-review` (edición).
    Se alimenta de `openModal('review', $id)` / `openModal('edit-review', $id)`.

    ─────────────────────────────────────────────────────────────────
    BLOQUE PROPIO DEL PROYECTO DE AULA
    ─────────────────────────────────────────────────────────────────
    La revisión documenta cómo se eligió el tema (fase 2 del proyecto): qué
    interés se detectó, qué sabe el grupo, qué desea aprender, qué falta y con
    quién se cuenta. No tiene equivalente en la semanal ni en la quincenal.

    A diferencia de los otros resúmenes, aquí el legacy exigía LOS SEIS campos
    (`saveReview()`), sin discrepancia entre lo documentado y lo aplicado. Se
    mantiene: es lo que le da sentido al proyecto frente a un plan suelto.

    `estrategias` es una columna que el legacy no validaba: se ofrece como texto
    libre opcional en vez de inventarle un formato que el documento original no
    define.

    Reglas: App\Http\Requests\Inicial\EiprojectreviewRequest
--}}
<div class="space-y-5">

    @php
        $project = $eiprojectk_id
            ? \App\Models\app\Inicial\Eiprojectk::find($eiprojectk_id)
            : null;
        $revisiones = $project ? $project->getOrderedViews() : collect();
    @endphp

    @if (! $project)
        <p class="text-sm text-gray-500">No se ha seleccionado un proyecto válido.</p>
    @else
        {{-- ═══ Revisiones existentes ═══ --}}
        <div>
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">
                Revisiones ({{ $revisiones->count() }})
            </h4>

            @forelse ($revisiones as $revision)
                <div class="rounded-lg border border-white/10 bg-gray-800/40 px-4 py-3 mb-2 flex items-start justify-between gap-3">
                    <div class="min-w-0 space-y-0.5">
                        <p class="text-sm font-medium text-amber-300 truncate">
                            {{ $revision->eleccion_tema_nombre ?: 'Sin nombre de tema' }}
                        </p>
                        @if ($revision->posibles_temas_interes)
                            <p class="text-xs text-gray-500 truncate">
                                {{ \Illuminate\Support\Str::limit($revision->posibles_temas_interes, 90) }}
                            </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <button wire:click="openModal('edit-review', {{ $revision->id }})" type="button"
                            class="text-[11px] px-2 py-1 rounded-md bg-amber-500/15 text-amber-300 hover:bg-amber-500/25 border border-amber-500/20 transition-colors">
                            Editar
                        </button>
                        <button wire:click="confirmDeleteReview({{ $revision->id }})" type="button"
                            class="p-1.5 rounded-md bg-white/5 hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition-colors"
                            aria-label="Eliminar revisión">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m4-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 rounded-lg border border-dashed border-white/10 px-4 py-6 text-center">
                    Este proyecto aún no tiene revisiones.
                </p>
            @endforelse
        </div>

        {{-- ═══ Formulario ═══ --}}
        <div class="space-y-4 border-t border-white/10 pt-4">
            <h4 class="text-xs font-bold uppercase tracking-widest text-gray-500">
                {{ $eiprojectreview_id ? 'Editar revisión' : 'Nueva revisión' }}
            </h4>

            <div>
                <label for="r-temas" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Posibles temas de interés <span class="text-red-400">*</span>
                </label>
                <textarea id="r-temas" rows="2" wire:model.live="eiprojectreview.posibles_temas_interes"
                    placeholder="Temas que despertaban la curiosidad del grupo…"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none"></textarea>
                @error('eiprojectreview.posibles_temas_interes')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="r-nombre" class="block text-xs font-medium text-gray-400 mb-1.5">
                    Elección del nombre del tema <span class="text-red-400">*</span>
                </label>
                <input id="r-nombre" type="text" wire:model.live="eiprojectreview.eleccion_tema_nombre"
                    placeholder="Con qué nombre se finalmente el tema y por qué"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none">
                @error('eiprojectreview.eleccion_tema_nombre')
                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="r-sabe" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Qué sabe <span class="text-red-400">*</span>
                    </label>
                    <textarea id="r-sabe" rows="3" wire:model.live="eiprojectreview.que_sabe"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none"></textarea>
                    @error('eiprojectreview.que_sabe')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="r-desean" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Qué desean aprender <span class="text-red-400">*</span>
                    </label>
                    <textarea id="r-desean" rows="3" wire:model.live="eiprojectreview.que_desean_aprender"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none"></textarea>
                    @error('eiprojectreview.que_desean_aprender')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="r-necesitamos" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Qué necesitamos <span class="text-red-400">*</span>
                    </label>
                    <textarea id="r-necesitamos" rows="3" wire:model.live="eiprojectreview.que_necesitamos"
                        placeholder="Materiales, espacios, personas…"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none"></textarea>
                    @error('eiprojectreview.que_necesitamos')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="r-apoyo" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Quiénes nos pueden apoyar <span class="text-red-400">*</span>
                    </label>
                    <textarea id="r-apoyo" rows="3" wire:model.live="eiprojectreview.quienes_nos_pueden_apoyar"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none"></textarea>
                    @error('eiprojectreview.quienes_nos_pueden_apoyar')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-[1fr_7rem] items-end">
                <div>
                    <label for="r-estrategias" class="block text-xs font-medium text-gray-400 mb-1.5">
                        Estrategias <span class="text-gray-500">(opcional)</span>
                    </label>
                    <textarea id="r-estrategias" rows="2" wire:model.live="eiprojectreview.estrategias"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none"></textarea>
                    @error('eiprojectreview.estrategias')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="r-order" class="block text-xs font-medium text-gray-400 mb-1">Orden</label>
                    <input id="r-order" type="number" min="1" step="1" wire:model.live="eiprojectreview.order"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 outline-none">
                    @error('eiprojectreview.order')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    @endif
</div>