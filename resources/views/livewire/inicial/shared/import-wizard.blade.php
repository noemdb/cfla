{{--
    Asistente de importación de planificaciones semanales desde s2526.

    Componente: App\Livewire\Inicial\EiplanningwkComponent
                (openImport / closeImport / importarSeleccionados / importarPlan +
                 filtros, ordenamiento, paginación y modo de vista)

    ─────────────────────────────────────────────────────────────────────────────
    QUÉ HACE Y QUÉ NO
    ─────────────────────────────────────────────────────────────────────────────
    Lista los planes legacy cuyo (grado, sección) coincide con la carga VIGENTE
    del docente (lapso en curso) y los importa con ID NUEVO: las estrategias se
    copian íntegras y los resúmenes se remapean a la pevaluación actual (misma
    asignatura+sección) o se saltan y se cuentan en el reporte. El proyecto
    vinculado no se copia (sus ids son del período viejo).

    La selección vive en el componente (`importSeleccionados`), NO en la página:
    marcar en la página 1, pasar a la 2 y volver conserva lo marcado.

    El diseño sigue el patrón del modal S2526 de `profesors/activities/create`:
    cabecera con contexto, toolbar con búsqueda/filtros/orden, tarjetas con menú
    por plan (ver detalle / importar este plan) y paginación numerada.
--}}
@if ($showImport)
    <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" wire:key="modal-import">
        <div class="flex min-h-full items-end justify-center p-4 sm:items-start sm:pt-10 bg-gray-950/70 backdrop-blur-sm"
            wire:click.self="closeImport">

            <div class="w-[90%] max-w-[90%] rounded-xl bg-gray-900 border border-white/10 shadow-2xl"
                x-data="{ open: true }" x-show="open"
                x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0 translate-y-4">

                {{-- ─── Cabecera con contexto ─── --}}
                <div class="px-5 py-3 border-b border-white/10 flex items-center justify-between bg-cyan-500/5 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-cyan-500/10 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Planes del período anterior</h3>
                            <p class="text-[11px] text-gray-500 mt-0.5">{{ $this->importSubtitulo() }}</p>
                        </div>
                    </div>
                    <button wire:click="closeImport" type="button" aria-label="Cerrar"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
                    @if ($importReporte)
                        {{-- ─── Reporte de la importación ─── --}}
                        <div class="space-y-3">
                            @foreach ($importReporte['creados'] as $creado)
                                <div class="rounded-lg border border-emerald-500/20 bg-emerald-500/5 px-4 py-3">
                                    <p class="text-sm text-gray-200">
                                        Plan #{{ $creado['legacy_id'] }} de s2526 → nuevo plan #{{ $creado['nuevo_id'] }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $creado['estrategias'] }} estrategia(s) ·
                                        {{ $creado['resumenes_ok'] }} resumen(es)
                                        @if (! empty($creado['revisiones']))
                                            · {{ $creado['revisiones'] }} revisión(es)
                                        @endif
                                        @if ($creado['resumenes_omitidos'] > 0)
                                            · {{ $creado['resumenes_omitidos'] }} resumen(es) sin pevaluación vigente (omitidos)
                                        @endif
                                        @if ($creado['proyecto_desvinculado'])
                                            · proyecto del período anterior desvinculado
                                        @endif
                                    </p>
                                </div>
                            @endforeach

                            @foreach ($importReporte['omitidos'] as $omitido)
                                <div class="rounded-lg border border-amber-500/20 bg-amber-500/5 px-4 py-3">
                                    <p class="text-sm text-gray-200">Plan #{{ $omitido['legacy_id'] }} omitido</p>
                                    <p class="mt-1 text-xs text-gray-400">{{ $omitido['motivo'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @elseif ($importCandidatos->isEmpty())
                        <div class="rounded-xl border border-dashed border-white/10 py-14 text-center">
                            <p class="text-sm text-gray-400">
                                No hay planes del período anterior para su carga vigente.
                            </p>
                            <p class="mt-1 text-xs text-gray-600">
                                Se listan los planes de s2526 cuyo grado y sección coinciden con
                                sus pevaluaciones del lapso en curso.
                            </p>
                        </div>
                    @else
                        @php
                            $filtrados = $this->importFiltrados();
                            $pagina = $this->importPagina();
                            $totalPaginas = $this->importTotalPaginas();
                        @endphp

                        {{-- ─── Toolbar: búsqueda + filtros + orden + vista ─── --}}
                        <div class="px-1 py-2 border-b border-white/5 flex flex-wrap items-center gap-3 bg-gray-800/20 shrink-0 rounded-lg mb-4">
                            <div class="relative flex-1 min-w-44">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input id="i-search" type="text" wire:model.live.debounce.300ms="importSearch"
                                    placeholder="Buscar en planes anteriores…"
                                    class="w-full bg-gray-800/50 border border-white/10 rounded-lg pl-9 pr-3 py-1.5 text-xs text-gray-300 placeholder-gray-600 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 transition-all duration-200">
                            </div>

                            <select wire:model.live="importGrado" aria-label="Filtrar por grado"
                                class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 transition-all duration-200">
                                <option value="">Todos los grados</option>
                                @foreach ($this->importGradosOpciones() as $grado)
                                    <option value="{{ $grado }}">{{ $grado }}</option>
                                @endforeach
                            </select>

                            <select wire:model.live="importProfesor" aria-label="Filtrar por docente origen"
                                class="bg-gray-800/50 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-gray-300 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/20 transition-all duration-200">
                                <option value="">Todos los docentes</option>
                                @foreach ($this->importProfesoresOpciones() as $id => $nombre)
                                    <option value="{{ $id }}">{{ $nombre }}</option>
                                @endforeach
                            </select>

                            <div class="flex items-center gap-1">
                                @foreach (['finicial' => 'Fecha', 'grado' => 'Grado'] as $campo => $etiqueta)
                                    <button wire:click="sortImport('{{ $campo }}')" type="button"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold transition-all duration-200
                                            {{ $importSort === $campo ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10' }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if ($importSort === $campo && $importSortDir === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-4v12m0 0l-4-4m4 4l4-4" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                            @endif
                                        </svg>
                                        {{ $etiqueta }}
                                    </button>
                                @endforeach
                            </div>

                            <button wire:click="toggleImportView" type="button"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[10px] font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200"
                                title="{{ $importViewMode === 'grid' ? 'Ver como tabla' : 'Ver como tarjetas' }}">
                                {{ $importViewMode === 'grid' ? 'Tabla' : 'Tarjetas' }}
                            </button>

                            <span class="text-[11px] text-gray-500 shrink-0">
                                {{ $filtrados->count() }} plan(es)
                                @if (count($importSeleccionados) > 0)
                                    · <span class="text-cyan-300 font-medium">{{ count($importSeleccionados) }} marcado(s)</span>
                                @endif
                            </span>
                        </div>

                        @if ($filtrados->isEmpty())
                            <div class="rounded-xl border border-dashed border-white/10 py-10 text-center">
                                <p class="text-sm text-gray-400">Ningún plan coincide con los filtros.</p>
                            </div>
                        @elseif ($importViewMode === 'grid')
                            {{-- ─── Vista tarjetas con menú por plan ─── --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach ($pagina as $indice => $candidato)
                                    <div class="bg-gray-800/30 border border-white/5 rounded-lg hover:border-cyan-500/30 transition-all duration-200 flex flex-col"
                                        x-data="{ openMenu: false }" @click.away="openMenu = false">

                                        <div class="flex items-center justify-between px-4 pt-3 pb-2 border-b border-white/5">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <input type="checkbox" wire:model="importSeleccionados"
                                                    value="{{ $candidato['id'] }}" aria-label="Marcar plan #{{ $candidato['id'] }}"
                                                    class="rounded border-white/20 bg-gray-900 text-cyan-600 focus:ring-cyan-500/50 shrink-0">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-400 shrink-0">
                                                    {{ ($importPage - 1) * $importPerPage + $indice + 1 }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 shrink-0 truncate">
                                                    {{ $candidato['grado'] }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-500/10 text-blue-400 border border-blue-500/20 shrink-0">
                                                    {{ $candidato['seccion'] }}
                                                </span>
                                            </div>

                                            <div class="relative shrink-0">
                                                <button @click="openMenu = !openMenu" type="button" aria-label="Acciones del plan"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-500 hover:text-gray-300 hover:bg-gray-700/50 transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                    </svg>
                                                </button>
                                                <div x-show="openMenu" x-cloak
                                                    class="absolute right-0 top-full mt-1 w-48 bg-gray-800 border border-white/10 rounded-lg shadow-xl overflow-hidden z-10">
                                                    <div class="py-1">
                                                        <button wire:click="verImportDetalle({{ $candidato['id'] }})" type="button"
                                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-gray-300 hover:bg-white/5 transition-all" @click="openMenu = false">
                                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            Ver detalle
                                                        </button>
                                                        <button wire:click="importarPlan({{ $candidato['id'] }})" type="button"
                                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs text-gray-300 hover:bg-white/5 transition-all" @click="openMenu = false">
                                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                            </svg>
                                                            Importar este plan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="px-4 py-2 flex-1 space-y-1.5">
                                            <div class="flex items-center gap-2 text-[11px] text-gray-500 font-mono mb-1">
                                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $candidato['finicial'] }} — {{ $candidato['ffinal'] }}
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600 block mb-0.5">Origen</span>
                                                <p class="text-xs text-gray-200 leading-relaxed truncate" title="{{ $candidato['profesor_origen'] }}">
                                                    {{ $candidato['profesor_origen'] }}
                                                </p>
                                            </div>
                                            @if ($candidato['asignaturas'] !== [])
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach ($candidato['asignaturas'] as $area)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-500/10 text-purple-300 border border-purple-500/20">
                                                            {{ $area }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if ($candidato['diagnostico'])
                                                <div>
                                                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-600 block mb-0.5">Diagnóstico</span>
                                                    <p class="text-xs text-gray-400 leading-relaxed">
                                                        {{ \Illuminate\Support\Str::limit($candidato['diagnostico'], 140) }}
                                                    </p>
                                                </div>
                                            @endif

                                            @if ($importDetalleId === $candidato['id'])
                                                <div class="rounded-lg bg-black/20 border border-white/5 px-3 py-2 space-y-1">
                                                    <p class="text-[11px] text-gray-400">Plan s2526 #{{ $candidato['id'] }}</p>
                                                    <p class="text-[11px] text-gray-400">Estrategias: {{ $candidato['estrategias'] }} de 50 celdas (se copian íntegras)</p>
                                                    <p class="text-[11px] text-gray-400">Resúmenes: {{ $candidato['resumenes'] }} (se anclan a su pevaluación vigente o se omiten)</p>
                                                    @if ($candidato['diagnostico'])
                                                        <p class="text-[11px] text-gray-300 leading-relaxed whitespace-pre-line">{{ $candidato['diagnostico'] }}</p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <div class="px-4 py-2 border-t border-white/5 flex items-center justify-between bg-white/[0.015]">
                                            <span class="text-[10px] text-gray-500 tabular-nums">{{ $candidato['estrategias'] }} est. · {{ $candidato['resumenes'] }} res.</span>
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-[10px] text-gray-600 font-mono">#{{ $candidato['id'] }}</span>
                                                <button wire:click="verImportDetalle({{ $candidato['id'] }})" type="button"
                                                    class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[10px] font-bold transition-all duration-200
                                                        {{ $importDetalleId === $candidato['id'] ? 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/20' : 'bg-white/5 text-gray-400 hover:text-gray-200 border border-white/5' }}"
                                                    title="{{ $importDetalleId === $candidato['id'] ? 'Ocultar detalle' : 'Ver información ampliada del plan' }}">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    {{ $importDetalleId === $candidato['id'] ? 'Menos' : 'Detalle' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- ─── Vista tabla ─── --}}
                            <div class="overflow-x-auto rounded-xl border border-white/10">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-white/5 text-xs uppercase tracking-wider text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-3 py-2.5 text-left font-medium w-10"></th>
                                            <th scope="col" class="px-3 py-2.5 text-left font-medium whitespace-nowrap">Plan</th>
                                            <th scope="col" class="px-3 py-2.5 text-left font-medium whitespace-nowrap">Grado · Sección</th>
                                            <th scope="col" class="px-3 py-2.5 text-left font-medium whitespace-nowrap">Período</th>
                                            <th scope="col" class="px-3 py-2.5 text-left font-medium whitespace-nowrap">Origen</th>
                                            <th scope="col" class="px-3 py-2.5 text-right font-medium whitespace-nowrap" title="Estrategias copiadas de 50 celdas">Estrategias</th>
                                            <th scope="col" class="px-3 py-2.5 text-right font-medium whitespace-nowrap" title="Resúmenes por área">Resúmenes</th>
                                            <th scope="col" class="px-3 py-2.5 text-left font-medium min-w-56">Diagnóstico</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach ($pagina as $candidato)
                                            <tr class="align-top hover:bg-white/[0.02]">
                                                <td class="px-3 py-2.5">
                                                    <input type="checkbox" wire:model="importSeleccionados"
                                                        value="{{ $candidato['id'] }}"
                                                        class="rounded border-white/20 bg-gray-900 text-cyan-600 focus:ring-cyan-500/50">
                                                </td>
                                                <td class="px-3 py-2.5 text-gray-300 whitespace-nowrap font-mono">#{{ $candidato['id'] }}</td>
                                                <td class="px-3 py-2.5 text-gray-200 whitespace-nowrap font-medium">{{ $candidato['grado'] }} · {{ $candidato['seccion'] }}</td>
                                                <td class="px-3 py-2.5 text-gray-300 whitespace-nowrap">{{ $candidato['finicial'] }} → {{ $candidato['ffinal'] }}</td>
                                                <td class="px-3 py-2.5 text-gray-300 whitespace-nowrap" title="{{ $candidato['profesor_origen'] }}">{{ \Illuminate\Support\Str::limit($candidato['profesor_origen'], 24) }}</td>
                                                <td class="px-3 py-2.5 text-gray-300 text-right whitespace-nowrap tabular-nums"
                                                    title="{{ $candidato['estrategias'] }} de 50 celdas se copiarán">{{ $candidato['estrategias'] }}/50</td>
                                                <td class="px-3 py-2.5 text-gray-300 text-right whitespace-nowrap tabular-nums"
                                                    title="Se anclan a su pevaluación vigente o se omiten">{{ $candidato['resumenes'] }}</td>
                                                <td class="px-3 py-2.5 text-gray-400 min-w-56 max-w-xs"
                                                    title="{{ $candidato['diagnostico'] }}">
                                                    {{ $candidato['diagnostico'] ? \Illuminate\Support\Str::limit($candidato['diagnostico'], 90) : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @error('importSeleccionados')
                            <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- ─── Paginación numerada ─── --}}
                        @if ($totalPaginas > 1)
                            <div class="mt-3 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1">
                                    <button wire:click="importPaginaAnterior" type="button" aria-label="Página anterior"
                                        @disabled($importPage <= 1)
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all duration-200 bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 disabled:opacity-40 disabled:cursor-not-allowed">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>

                                    @foreach ($this->importPaginas() as $p)
                                        @if ($p === '…')
                                            <span class="text-xs text-gray-600 px-1">…</span>
                                        @elseif ($p === $importPage)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                                {{ $p }}
                                            </span>
                                        @else
                                            <button wire:click="gotoImportPage({{ $p }})" type="button"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold bg-gray-700/50 text-gray-400 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                                                {{ $p }}
                                            </button>
                                        @endif
                                    @endforeach

                                    <button wire:click="importPaginaSiguiente" type="button" aria-label="Página siguiente"
                                        @disabled($importPage >= $totalPaginas)
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-bold transition-all duration-200 bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 disabled:opacity-40 disabled:cursor-not-allowed">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>

                                    <span class="text-[10px] text-gray-600 ml-2">Pág. {{ $importPage }} de {{ $totalPaginas }}</span>
                                </div>

                                <button wire:click="closeImport" type="button"
                                    class="rounded-lg px-4 py-2 text-xs font-bold bg-gray-700/50 text-gray-300 hover:bg-gray-700 border border-white/10 transition-all duration-200">
                                    Cerrar
                                </button>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="flex items-center justify-end gap-2 px-5 py-4 border-t border-white/10 bg-gray-900/60 rounded-b-xl">
                    <button wire:click="closeImport" type="button"
                        class="rounded-lg px-4 py-2 text-sm text-gray-300 hover:bg-white/5 transition-colors">
                        {{ $importReporte ? 'Cerrar' : 'Cancelar' }}
                    </button>

                    @if (! $importReporte && $importCandidatos->isNotEmpty())
                        <button wire:click="importarSeleccionados" type="button"
                            class="rounded-lg bg-cyan-600 hover:bg-cyan-500 px-4 py-2 text-sm font-medium text-white transition-colors">
                            Importar seleccionados
                            @if (count($importSeleccionados) > 0)
                                ({{ count($importSeleccionados) }})
                            @endif
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
