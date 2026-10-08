{{--
    CRUD del INFORME FINAL por estudiante (Educación Inicial).

    Componente: App\Livewire\Inicial\EifinalkComponent
    Blueprint:  blueprint/inicial · fase F3 (CRUD docente resto) · patrón A.6

    ─────────────────────────────────────────────────────────────────────────────
    DOS PESTAÑAS, NO UNA
    ─────────────────────────────────────────────────────────────────────────────
    · informesList  → el trabajo normal: los informes del docente, con filtros.
    · estudiantesList → índice NIÑO × CARGA que responde "¿quién no tiene
      informe todavía?". Es la diferencia con los otros cinco documentos, que solo
      listan documentos ya escritos: aquí el trabajo pendiente es el punto de
      partida.

    El modal (alta/edición) y el acordeón de expectativas van en `@include`.
--}}
<div class="space-y-6">

    {{-- ═══ PESTAÑAS ═══ --}}
    <div class="flex items-center gap-2 border-b border-white/10" role="tablist">
        <button wire:click="setActiveTab('informesList')" type="button" role="tab"
            aria-selected="{{ $activeTab === 'informesList' ? 'true' : 'false' }}"
            class="px-4 py-2 text-sm font-medium -mb-px border-b-2 transition-colors
                   {{ $activeTab === 'informesList'
                        ? 'border-cyan-500 text-cyan-300'
                        : 'border-transparent text-gray-500 hover:text-gray-300' }}">
            Informes ({{ $totalInformes }})
        </button>
        <button wire:click="setActiveTab('estudiantesList')" type="button" role="tab"
            aria-selected="{{ $activeTab === 'estudiantesList' ? 'true' : 'false' }}"
            class="px-4 py-2 text-sm font-medium -mb-px border-b-2 transition-colors
                   {{ $activeTab === 'estudiantesList'
                        ? 'border-cyan-500 text-cyan-300'
                        : 'border-transparent text-gray-500 hover:text-gray-300' }}">
            Estudiantes
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         PESTAÑA 1 · INFORMES
         ═══════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'informesList')
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex flex-wrap items-end gap-3">
                <div class="w-full sm:w-72">
                    <label for="f-pevaluacion" class="block text-xs font-medium text-gray-400 mb-1">
                        Carga académica
                    </label>
                    <select id="f-pevaluacion" wire:model.live="filterPevaluacion"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                        <option value="">Todas</option>
                        @foreach ($listPevaluacion as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-48">
                    <label for="f-estudiant" class="block text-xs font-medium text-gray-400 mb-1">
                        Estudiante
                    </label>
                    <input id="f-estudiant" type="search" wire:model.live.debounce.400ms="filterEstudiant"
                        placeholder="Nombre o cédula"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                </div>

                <div class="w-full sm:w-56">
                    <label for="f-title" class="block text-xs font-medium text-gray-400 mb-1">
                        Título
                    </label>
                    <input id="f-title" type="search" wire:model.live.debounce.400ms="filterTitle"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                </div>
            </div>

            <button wire:click="openModal()" type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo informe
            </button>
        </div>

        @if ($eifinalks->isEmpty())
            <div class="rounded-xl border border-dashed border-white/10 py-16 text-center">
                <p class="text-sm text-gray-400">Todavía no hay informes finales.</p>
                <button wire:click="setActiveTab('estudiantesList')"
                    class="mt-3 text-sm text-cyan-400 hover:text-cyan-300">
                    Ir al listado de estudiantes para empezar
                </button>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($eifinalks as $report)
                    @php
                        $areaIds = $report->expectations->pluck('eilearningarea_id')->unique();
                    @endphp
                    <article class="rounded-xl border border-white/10 bg-gray-900/60 hover:border-cyan-500/30 transition-colors p-4 flex flex-col sm:flex-row sm:items-start sm:gap-4">
                        {{-- Orden: es lo que gobierna la impresión del boletín. --}}
                        <span class="shrink-0 w-9 h-9 rounded-lg bg-cyan-500/15 text-cyan-300 border border-cyan-500/20 flex items-center justify-center text-sm font-bold">
                            {{ $report->order }}
                        </span>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-100 truncate">
                                {{ $report->expectant?->full_name ?? 'Estudiante sin nombre' }}
                                @if ($this->esOficial($report->pevaluacion_id))
                                    <span class="ml-1 text-[10px] font-bold uppercase tracking-wider rounded px-1.5 py-0.5
                                                 bg-emerald-500/15 text-emerald-300 border border-emerald-500/20"
                                          title="Informe oficial (status_official)">
                                        Oficial
                                    </span>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $report->pevaluacion?->pensum?->grado?->name ?? '—' }} ·
                                {{ $report->pevaluacion?->seccion?->name ?? '—' }} ·
                                {{ $report->pevaluacion?->lapso?->name ?? '—' }} ·
                                {{ $report->pevaluacion?->pensum?->asignatura?->name ?? '—' }}
                            </p>
                            <p class="mt-1 text-sm text-gray-300 truncate">{{ $report->title }}</p>

                            @if ($areaIds->isNotEmpty())
                                <p class="mt-1 text-[11px] text-gray-500">
                                    {{ $report->expectations->count() }} expectativa(s) en
                                    {{ $areaIds->count() }} área(s)
                                </p>
                            @endif
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0 mt-2 sm:mt-0">
                            <a href="{{ route('inicials.eifinalks.format', $report->id) }}" target="_blank"
                                class="p-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors"
                                title="Formato imprimible">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v8H6z" />
                                </svg>
                            </a>
                            <button wire:click="openModal({{ $report->id }})" type="button"
                                class="p-2 rounded-lg bg-white/5 hover:bg-white/10 border border-white/5 text-gray-400 hover:text-gray-200 transition-colors"
                                title="Editar informe">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button wire:click="confirmDeleteReport({{ $report->id }})" type="button"
                                class="p-2 rounded-lg bg-white/5 hover:bg-red-500/20 border border-white/5 text-gray-400 hover:text-red-400 transition-colors"
                                title="Eliminar informe">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>

            <div>{{ $eifinalks->links() }}</div>
        @endif

    {{-- ═══════════════════════════════════════════════════════════
         PESTAÑA 2 · ESTUDIANTES (quién no tiene informe)
         ═══════════════════════════════════════════════════════════ --}}
    @else
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex flex-wrap items-end gap-3">
                <div class="w-full sm:w-80">
                    <label for="e-pevaluacion" class="block text-xs font-medium text-gray-400 mb-1">
                        Carga académica <span class="text-red-400">*</span>
                    </label>
                    <select id="e-pevaluacion" wire:model.live="filterPevaluacionEstudiantes"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                        <option value="">Seleccione una carga</option>
                        @foreach ($listPevaluacion as $id => $nombre)
                            <option value="{{ $id }}">{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-56">
                    <label for="e-buscar" class="block text-xs font-medium text-gray-400 mb-1">
                        Buscar estudiante
                    </label>
                    <input id="e-buscar" type="search" wire:model.live.debounce.400ms="filterEstudiantSearch"
                        placeholder="Nombre o cédula"
                        class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                </div>
            </div>

            <p class="text-xs text-gray-500">
                El boletín se imprime ordenado por el campo <b>orden</b> de cada informe.
            </p>
        </div>

        @if ($filterPevaluacionEstudiantes === '')
            <div class="rounded-xl border border-dashed border-white/10 py-16 text-center">
                <p class="text-sm text-gray-400">Elige una carga académica para ver sus estudiantes.</p>
            </div>
        @else
            @php
                // Filtro por nombre/cédula en memoria: la lista es de una sola
                // sección (decenas de niños) y así no se dispara una consulta por
                // tecla.
                $termino = mb_strtolower(trim($filterEstudiantSearch));
                $visibles = $termino === ''
                    ? $estudiantes
                    : $estudiantes->filter(function ($e) use ($termino) {
                        return str_contains(mb_strtolower($e->lastname.' '.$e->name), $termino)
                            || str_contains((string) $e->ci_estudiant, $termino);
                    });
            @endphp

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($visibles as $estudiante)
                    @php $hecho = $this->tieneInforme($estudiante->id); @endphp
                    <article class="rounded-lg border border-white/10 bg-gray-900/60 p-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm text-gray-200 truncate">{{ $estudiante->full_name }}</p>
                            <p class="text-[11px] text-gray-500">
                                @if ($hecho)
                                    <span class="text-emerald-400">● Informe creado</span>
                                @else
                                    <span class="text-amber-400">○ Sin informe</span>
                                @endif
                            </p>
                        </div>

                        @if ($hecho)
                            {{-- Ya existe: se ofrece editar, no crear otro. --}}
                            <button wire:click="openModal(\App\Models\app\Inicial\Eifinalk::where('pevaluacion_id', filterPevaluacionEstudiantes)->where('estudiant_id', {{ $estudiante->id }})->value('id'))"
                                type="button"
                                class="shrink-0 text-[11px] px-2 py-1 rounded-md bg-emerald-500/15 text-emerald-300 hover:bg-emerald-500/25 border border-emerald-500/20 transition-colors">
                                Editar
                            </button>
                        @else
                            <button wire:click="nuevoInformePara({{ $estudiante->id }})" type="button"
                                class="shrink-0 text-[11px] px-2 py-1 rounded-md bg-cyan-500/15 text-cyan-300 hover:bg-cyan-500/25 border border-cyan-500/20 transition-colors">
                                Crear informe
                            </button>
                        @endif
                    </article>
                @empty
                    <p class="col-span-full text-sm text-gray-500 rounded-lg border border-dashed border-white/10 px-4 py-6 text-center">
                        @if ($estudiantes->isEmpty())
                            Ningún estudiante está matriculado en la sección de esta carga.
                        @else
                            Ningún estudiante coincide con la búsqueda.
                        @endif
                    </p>
                @endforelse
            </div>
        @endif
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         MODAL ÚNICO (alta / edición)
         ═══════════════════════════════════════════════════════════ --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
            wire:key="modal-{{ $modalType }}-{{ $editingId }}">
            <div class="flex min-h-full items-end justify-center p-4 sm:items-start sm:pt-10 bg-gray-950/70 backdrop-blur-sm"
                wire:click.self="closeModal">

                <div class="w-full max-w-4xl rounded-xl bg-gray-900 border border-white/10 shadow-2xl"
                    x-data="{ open: true }" x-show="open"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0 translate-y-4">

                    <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
                        <h3 class="text-base font-semibold text-gray-100">
                            {{ $modalType === 'edit' ? 'Editar informe final' : 'Nuevo informe final' }}
                            @if ($lapso_id)
                                <span class="ml-2 text-xs font-normal text-gray-500">
                                    {{ \App\Models\app\Academy\Lapso::find($lapso_id)?->name }}
                                </span>
                            @endif
                        </h3>
                        <button wire:click="closeModal" type="button"
                            class="p-1.5 rounded-lg text-gray-500 hover:text-gray-200 hover:bg-white/5 transition-colors"
                            aria-label="Cerrar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
                        @include('livewire.inicial.eifinalk.partials.report-form')
                    </div>

                    <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-white/10 bg-gray-900/60 rounded-b-xl">
                        <button wire:click="closeModal" type="button"
                            class="rounded-lg px-4 py-2 text-sm text-gray-300 hover:bg-white/5 transition-colors">
                            Cancelar
                        </button>
                        <button wire:click="{{ $modalType === 'edit' ? 'update' : 'save' }}" type="button"
                            class="rounded-lg bg-cyan-600 hover:bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition-colors">
                            Guardar informe
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>