{{--
    Raíz del componente de seguimiento (documentos de consulta y revisión separada).

    Bloques:
      1. Pestañas por documento (con los filtros actuales)
      2. KPI de la entidad activa
      3. Filtros
      4. Listado (tarjetas o tabla) y paginación
      5. Modal de detalle

    No hay `wire:model` sobre campos del documento; el formulario independiente
    solo permite registrar `revision_seguimiento`.
--}}
<div class="space-y-6">

    @include('livewire.inicial.seguimiento.partials.tabs')

    @include('livewire.inicial.seguimiento.partials.kpis')

    @include('livewire.inicial.seguimiento.partials.filters')

    @if ($filtrosActivos !== [])
        <section class="flex flex-wrap items-center gap-2" aria-label="Filtros activos">
            <span class="text-xs font-medium text-gray-500">Filtros activos</span>
            @foreach ($filtrosActivos as $filtro)
                <button type="button" wire:click="quitarFiltro('{{ $filtro['campo'] }}')"
                    aria-label="Quitar filtro {{ $filtro['etiqueta'] }}: {{ $filtro['valor'] }}"
                    class="inline-flex items-center gap-1.5 rounded-md border border-white/10 bg-white/5 px-2 py-1 text-xs text-gray-300 transition-colors hover:border-cyan-500/30 hover:bg-cyan-500/10 hover:text-white">
                    <span class="text-gray-500">{{ $filtro['etiqueta'] }}:</span>
                    <span class="max-w-48 truncate">{{ $filtro['valor'] }}</span>
                    <span aria-hidden="true" class="text-gray-500">×</span>
                </button>
            @endforeach
        </section>
    @endif

    @if (in_array($entidad, ['eiplanningwks', 'eispecialks'], true))
        <section class="space-y-3"
            aria-label="{{ $entidad === 'eiplanningwks' ? 'Accesos rápidos y resumen por docente' : 'Accesos rápidos de avance y revisión' }}">
            <div class="flex flex-wrap items-center gap-2">
                <span class="mr-1 text-xs font-medium text-gray-400">Accesos rápidos</span>
                @foreach ([
                    ['estado', 'sin_iniciar', 'Sin iniciar'],
                    ['estado', 'en_progreso', 'En progreso'],
                    ['estado', 'completo', 'Completo'],
                    ['revisionEstado', 'sin_revision', 'Sin revisión'],
                    ['revisionEstado', 'revisado', 'Revisado'],
                ] as [$campo, $valor, $etiqueta])
                    @php
                        $activo = $campo === 'estado'
                            ? $this->estado === $valor
                            : ($this->revisionEstado === $valor
                                || ($this->revisionEstado === '' && $this->estado === $valor));
                    @endphp
                    <button type="button" wire:click="aplicarFiltroRapido('{{ $campo }}', '{{ $valor }}')"
                        aria-pressed="{{ $activo ? 'true' : 'false' }}"
                        aria-label="{{ $campo === 'estado' ? 'Filtrar por estado: ' : 'Filtrar por revisión: ' }}{{ $etiqueta }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1.5 text-xs font-medium transition-colors
                            focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500/60
                            {{ $activo
                                ? 'border-cyan-500/40 bg-cyan-500/15 text-cyan-200'
                                : 'border-white/10 bg-white/5 text-gray-300 hover:bg-white/10' }}">
                        {{ $etiqueta }}
                        <span class="tabular-nums text-gray-400">{{ $conteosRapidos[$valor] ?? 0 }}</span>
                    </button>
                @endforeach
            </div>

            @if ($entidad === 'eispecialks')
                <p class="text-xs text-gray-400">
                    Un plan especial se completa con 50 estrategias. Las actividades son complementarias y no sustituyen estrategias.
                </p>
            @endif

            @if ($entidad === 'eiplanningwks')
            <section class="rounded-xl border border-white/10 bg-gray-900/40">
                <button type="button" wire:click="toggleResumenDocentes"
                    aria-expanded="{{ $resumenDocentesAbierto ? 'true' : 'false' }}"
                    class="flex w-full flex-wrap items-center gap-x-2 gap-y-1 px-4 py-3 text-left text-sm text-gray-200 hover:bg-white/[0.03] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-cyan-500/60">
                    <span>Resumen por docente</span>
                    <span class="text-xs text-gray-500">
                        {{ $resumenDocentesAbierto
                            ? 'Ocultar'
                            : 'Ver planes, incompletos y sin revisión' }}
                    </span>
                </button>
                @if ($resumenDocentesAbierto)
                    @if ($resumenDocentes->isNotEmpty())
                    <div class="overflow-x-auto border-t border-white/10">
                        <table class="w-full min-w-[34rem] text-left text-xs">
                            <thead class="bg-white/[0.03] text-gray-500">
                                <tr>
                                    <th scope="col" class="px-4 py-2 font-medium">Docente</th>
                                    <th scope="col" class="px-3 py-2 text-right font-medium">Planes</th>
                                    <th scope="col" class="px-3 py-2 text-right font-medium">Incompletos</th>
                                    <th scope="col" class="px-3 py-2 text-right font-medium">Sin revisión</th>
                                    <th scope="col" class="px-4 py-2 text-right font-medium">Filtro</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach ($resumenDocentes as $filaDocente)
                                    <tr wire:key="resumen-docente-{{ $filaDocente['profesor_id'] }}">
                                        <th scope="row" class="px-4 py-2.5 font-medium text-gray-200">
                                            {{ $filaDocente['nombre'] }}
                                        </th>
                                        <td class="px-3 py-2.5 text-right tabular-nums text-gray-300">
                                            {{ $filaDocente['total'] }}
                                        </td>
                                        <td class="px-3 py-2.5 text-right tabular-nums text-amber-300">
                                            {{ $filaDocente['incompletos'] }}
                                        </td>
                                        <td class="px-3 py-2.5 text-right tabular-nums text-amber-300">
                                            {{ $filaDocente['sin_revision'] }}
                                        </td>
                                        <td class="px-4 py-2.5 text-right">
                                            <button type="button"
                                                wire:click="filtrarDocente({{ $filaDocente['profesor_id'] }})"
                                                class="rounded-md px-2 py-1 text-cyan-300 hover:bg-cyan-500/10 hover:text-cyan-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan-500/60">
                                                {{ (string) $profesor_id === (string) $filaDocente['profesor_id']
                                                    ? 'Quitar'
                                                    : 'Ver planes' }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <p class="border-t border-white/10 px-4 py-3 text-xs text-gray-400">
                            No hay planes por docente para los filtros generales seleccionados.
                        </p>
                    @endif
                @endif
            </section>
            @endif
        </section>
    @endif

    {{-- Aviso de consulta en curso (accesible) --}}
    <div wire:loading.flex wire:target="profesor_id,grado_id,seccion_id,lapso_id,desde,hasta,estado,revisionEstado,orden,search,alternarVista,limpiarFiltros,aplicarFiltroRapido,filtrarDocente,toggleResumenDocentes"
        class="hidden items-center gap-2 rounded-lg border border-cyan-500/20 bg-cyan-500/10 px-3 py-2 text-xs font-medium text-cyan-200"
        role="status" aria-live="polite">
        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
        Consultando…
    </div>

    <p class="text-xs text-gray-500" role="status" aria-live="polite">
        @if ($documentos->total() > 0)
            Mostrando <span class="font-medium text-gray-300">{{ $documentos->firstItem() }}–{{ $documentos->lastItem() }}</span>
            de <span class="font-medium text-gray-300">{{ number_format($documentos->total()) }}</span> registros
        @else
            0 registros para los filtros seleccionados
        @endif
    </p>

    @if ($documentos->isEmpty())
        @include('livewire.inicial.seguimiento.partials.empty')
    @else
        @if ($viewMode === 'grid')
            @include('livewire.inicial.seguimiento.partials.grid')
        @else
            @include('livewire.inicial.seguimiento.partials.table')
        @endif

        <div class="pt-2">
            {{ $documentos->links() }}
        </div>
    @endif

    @if ($detalleId)
        @include('livewire.inicial.seguimiento.partials.detalle-modal')
    @endif

    {{-- Modal de calendario --}}
    @include('livewire.inicial.seguimiento.partials.calendar-modal')
</div>
