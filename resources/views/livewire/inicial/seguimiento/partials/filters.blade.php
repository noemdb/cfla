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
        : 'Diagnóstico, observación o revisión';
@endphp

<section class="rounded-xl border border-white/10 bg-gray-900/60 p-4 space-y-3">
    {{-- Fila 1: docente, nivel, sección y lapso o fechas. --}}
    <div class="flex flex-wrap items-end gap-3">

        <div class="w-full sm:w-52">
            <label for="f-docente" class="block text-xs font-medium text-gray-400 mb-1">Docente</label>
            <select id="f-docente" wire:model.live="profesor_id"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                <option value="">Todos los docentes</option>
                @foreach ($listProfesores as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full sm:w-40">
            <label for="f-nivel" class="block text-xs font-medium text-gray-400 mb-1">Nivel</label>
            <select id="f-nivel" wire:model.live="grado_id"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                <option value="">Todos los niveles</option>
                @foreach ($listGrados as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full sm:w-40">
            <label for="f-seccion" class="block text-xs font-medium text-gray-400 mb-1">Sección</label>
            <select id="f-seccion" wire:model.live="seccion_id" @disabled(! $grado_id)
                title="{{ $grado_id ? 'Secciones del nivel elegido' : 'Elija primero un nivel' }}"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none disabled:opacity-50">
                <option value="">{{ $grado_id ? 'Todas las secciones' : 'Elija primero un nivel' }}</option>
                @foreach ($listSecciones as $id => $nombre)
                    <option value="{{ $id }}">{{ $nombre }}</option>
                @endforeach
            </select>
        </div>

        @if ($soportaLapso)
            <div class="w-full sm:w-40">
                <label for="f-lapso" class="block text-xs font-medium text-gray-400 mb-1">Lapso</label>
                <select id="f-lapso" wire:model.live="lapso_id"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                    <option value="">Todos los lapsos</option>
                    @foreach ($listLapsos as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if ($soportaFechas)
            <div class="w-full sm:w-40">
                <label for="f-desde" class="block text-xs font-medium text-gray-400 mb-1">Desde</label>
                <input id="f-desde" type="date" wire:model.live="desde"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            </div>

            <div class="w-full sm:w-40">
                <label for="f-hasta" class="block text-xs font-medium text-gray-400 mb-1">Hasta</label>
                <input id="f-hasta" type="date" wire:model.live="hasta"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
            </div>
        @endif
    </div>

    {{-- Fila 2: buscar, avance, revisión, orden y acciones. --}}
    <div class="flex flex-wrap items-end gap-3">
        <div class="w-full sm:w-56">
            <label for="f-buscar" class="block text-xs font-medium text-gray-400 mb-1">Buscar</label>
            <input id="f-buscar" type="search" wire:model.live.debounce.400ms="search"
                placeholder="{{ $placeholderBusqueda }}"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
        </div>

        @if (! empty($opcionesEstado))
            <div class="w-full sm:w-40">
                <label for="f-estado" class="block text-xs font-medium text-gray-400 mb-1">Avance</label>
                <select id="f-estado" wire:model.live="estado"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                    <option value="">Todos</option>
                    @foreach ($opcionesEstado as $valor => $etiqueta)
                        <option value="{{ $valor }}">{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if (! empty($opcionesRevision))
            <div class="w-full sm:w-40">
                <label for="f-revision" class="block text-xs font-medium text-gray-400 mb-1">Revisión</label>
                <select id="f-revision" wire:model.live="revisionEstado"
                    class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                    <option value="">Todas</option>
                    @foreach ($opcionesRevision as $valor => $etiqueta)
                        <option value="{{ $valor }}">{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="w-full sm:w-48">
            <label for="f-orden" class="block text-xs font-medium text-gray-400 mb-1">Ordenar por</label>
            <select id="f-orden" wire:model.live="orden"
                class="w-full bg-gray-800/50 border border-white/10 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500/50 focus:border-cyan-500/50 outline-none">
                @foreach ($opcionesOrden as $valor => $etiqueta)
                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2 ml-auto">
            <button wire:click="limpiarFiltros" type="button"
                class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 px-3 py-2 text-sm font-medium text-gray-300 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
                Limpiar
            </button>

            <button wire:click="alternarVista" type="button"
                aria-pressed="{{ $viewMode === 'table' ? 'true' : 'false' }}"
                title="{{ $viewMode === 'grid' ? 'Ver como tabla' : 'Ver como tarjetas' }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 px-3 py-2 text-sm font-medium text-gray-300 transition-colors">
                @if ($viewMode === 'grid')
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 6h18M3 12h18M3 18h18" />
                    </svg>
                    Tabla
                @else
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2-2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Tarjetas
                @endif
            </button>
        </div>
    </div>
</section>
