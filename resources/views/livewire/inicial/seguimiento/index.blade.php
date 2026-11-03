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

    {{-- Aviso de consulta en curso (accesible) --}}
    <div wire:loading.flex wire:target="profesor_id,grado_id,seccion_id,lapso_id,desde,hasta,estado,revisionEstado,orden,search,alternarVista,limpiarFiltros"
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
</div>
