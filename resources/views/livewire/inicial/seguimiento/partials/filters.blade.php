{{--
    Filtros del seguimiento.

    · La etiqueta es “Nivel”, nunca “Grado” (ADR-11.9).
    · Sección se deshabilita sin nivel: una sección de otro grado no aplica.
    · Lapso solo en evaluación e informe final; fechas en los otros cuatro.
    · Avance y revisión son ejes independientes y se pueden combinar.
    · Las opciones de orden corresponden a ordenamientos que ejecuta el servicio.
--}}
@php
    $placeholderBusqueda = $entidad === 'eifinalks'
        ? 'Estudiante o título'
        : ($entidad === 'eispecialks'
            ? 'Justificación, actividades, estrategias o revisión'
            : 'Diagnóstico, observación o revisión');
@endphp

<section class="rounded-xl border border-white/10 bg-gray-900/60">
    <button type="button" wire:click="toggleFiltros"
        aria-expanded="{{ $filtrosAbiertos ? 'true' : 'false' }}"
        aria-controls="seguimiento-filtros-contenido"
        class="flex min-h-14 w-full flex-wrap items-center justify-between gap-x-3 gap-y-1 rounded-xl px-4 py-3 text-left text-sm text-gray-200 transition-colors hover:bg-white/[0.03] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-cyan-500/60 sm:px-5">
        <span class="font-semibold">Filtros</span>
        <span class="flex items-center gap-2 text-xs text-gray-400">
            @if ($filtrosAbiertos)
                <span>Ocultar criterios de búsqueda</span>
            @elseif (count($filtrosActivos) > 0)
                <span>{{ count($filtrosActivos) }} {{ count($filtrosActivos) === 1 ? 'filtro activo' : 'filtros activos' }} · Mostrar criterios de búsqueda</span>
            @else
                <span>Mostrar criterios de búsqueda</span>
            @endif
            <svg class="h-4 w-4 transition-transform {{ $filtrosAbiertos ? 'rotate-180' : '' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </span>
    </button>

    <div id="seguimiento-filtros-contenido"
        @class(['border-t border-white/10 p-4 sm:p-5' => $filtrosAbiertos])>
        @if ($filtrosAbiertos)
    <div class="space-y-5">
        <fieldset class="min-w-0 space-y-3">
            <legend class="text-sm font-semibold text-gray-200">Plan y período</legend>
            <div class="grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-12">

        <div @class([
            'sm:col-span-2',
            'xl:col-span-3' => $soportaLapso && $soportaFechas,
            'xl:col-span-4' => ! ($soportaLapso && $soportaFechas),
        ])>
            <label for="f-docente" class="mb-1 block text-xs font-medium text-gray-400">Docente</label>
            <select id="f-docente" wire:model.live="profesor_id"
                class="min-h-11 w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50">
                <option value="">Todos los docentes</option>
                @foreach ($listProfesores as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="xl:col-span-2">
            <label for="f-nivel" class="mb-1 block text-xs font-medium text-gray-400">Nivel</label>
            <select id="f-nivel" wire:model.live="grado_id"
                class="min-h-11 w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50">
                <option value="">Todos los niveles</option>
                @foreach ($listGrados as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="xl:col-span-2">
            <label for="f-seccion" class="mb-1 block text-xs font-medium text-gray-400">Sección</label>
            <select id="f-seccion" wire:model.live="seccion_id" @disabled(! $grado_id)
                title="{{ $grado_id ? 'Secciones del nivel elegido' : 'Elija primero un nivel' }}"
                class="min-h-11 w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50 disabled:opacity-50">
                <option value="">{{ $grado_id ? 'Todas las secciones' : 'Elija primero un nivel' }}</option>
                @foreach ($listSecciones as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>

        @if ($soportaLapso)
            <div class="xl:col-span-2">
                <label for="f-lapso" class="mb-1 block text-xs font-medium text-gray-400">Lapso</label>
                <select id="f-lapso" wire:model.live="lapso_id"
                    class="min-h-11 w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50">
                    <option value="">Todos los lapsos</option>
                    @foreach ($listLapsos as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if ($soportaFechas)
            <div @class([
                'grid grid-cols-1 gap-3 sm:col-span-2 sm:grid-cols-2',
                'xl:col-span-3' => $soportaLapso,
                'xl:col-span-4' => ! $soportaLapso,
            ])>
                <div>
                    <label for="f-desde" class="mb-1 block text-xs font-medium text-gray-400">Desde</label>
                    <input id="f-desde" type="date" wire:model.live="desde"
                        class="min-h-11 w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50">
                </div>

                <div>
                    <label for="f-hasta" class="mb-1 block text-xs font-medium text-gray-400">Hasta</label>
                    <input id="f-hasta" type="date" wire:model.live="hasta"
                        class="min-h-11 w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50">
                </div>
            </div>
        @endif
    </div>
        </fieldset>

        <fieldset class="min-w-0 space-y-3 border-t border-white/10 pt-4">
            <legend class="text-sm font-semibold text-gray-200">Búsqueda y seguimiento</legend>
            <div class="grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-12">
        <div class="sm:col-span-2 xl:col-span-3">
            <label for="f-buscar" class="mb-1 block text-xs font-medium text-gray-400">Buscar</label>
            <input id="f-buscar" type="search" wire:model.live.debounce.400ms="search"
                placeholder="{{ $placeholderBusqueda }}"
                class="min-h-11 w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none placeholder:text-gray-500 focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50">
        </div>

        @if (! empty($opcionesEstado))
            <div class="xl:col-span-2">
                <label for="f-estado" class="mb-1 block text-xs font-medium text-gray-400">Avance</label>
                <select id="f-estado" wire:model.live="estado"
                    class="min-h-11 w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50">
                    <option value="">Todos</option>
                    @foreach ($opcionesEstado as $valor => $etiqueta)
                        <option value="{{ $valor }}">{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if (! empty($opcionesRevision))
            <div class="xl:col-span-2">
                <label for="f-revision" class="mb-1 block text-xs font-medium text-gray-400">Revisión</label>
                <select id="f-revision" wire:model.live="revisionEstado"
                    class="min-h-11 w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50">
                    <option value="">Todas</option>
                    @foreach ($opcionesRevision as $valor => $etiqueta)
                        <option value="{{ $valor }}">{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="sm:col-span-2 xl:col-span-2">
            <label for="f-orden" class="mb-1 block text-xs font-medium text-gray-400">Ordenar por</label>
            <select id="f-orden" wire:model.live="orden"
                class="min-h-11 w-full rounded-lg border border-white/10 bg-gray-800/50 px-3 py-2 text-sm text-gray-200 outline-none focus:border-cyan-500/50 focus:ring-2 focus:ring-cyan-500/50">
                @foreach ($opcionesOrden as $valor => $etiqueta)
                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-wrap items-end gap-2 sm:col-span-2 sm:justify-end xl:col-span-3">
            <button wire:click="limpiarFiltros" type="button"
                class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500/60">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
                Limpiar
            </button>

            <button wire:click="alternarVista" type="button"
                aria-pressed="{{ $viewMode === 'table' ? 'true' : 'false' }}"
                title="{{ $viewMode === 'grid' ? 'Ver como tabla' : 'Ver como tarjetas' }}"
                class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium text-gray-300 transition-colors hover:bg-white/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500/60">
                @if ($viewMode === 'grid')
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 6h18M3 12h18M3 18h18" />
                    </svg>
                    {{-- Tabla --}}
                @else
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2-2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    {{-- Tarjetas --}}
                @endif
            </button>

            @if (in_array($entidad, ['eiplanningwks', 'eiplanningbwks']))
                <button wire:click="openCalendar" type="button"
                    class="inline-flex min-h-11 items-center justify-center gap-1.5 rounded-lg border border-purple-500/20 bg-purple-500/10 px-3 py-2 text-sm font-medium text-purple-300 transition-colors hover:bg-purple-500/20 hover:text-purple-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-purple-500/60"
                    title="Ver calendario de planificaciones">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{-- Calendario --}}
                </button>
            @endif
        </div>
    </div>
        </fieldset>
    </div>
        @endif
    </div>
</section>
