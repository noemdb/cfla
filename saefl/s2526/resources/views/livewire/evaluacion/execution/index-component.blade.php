<div>
    <!-- Botón para abrir la guía -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Seguimiento de Evaluaciones</h4>
        <button type="button" class="btn btn-info btn-sm" wire:click="toggleGuiaModal">
            <i class="fas fa-question-circle mr-1"></i> Guía del Usuario
        </button>
    </div>

    @include('livewire.evaluacion.execution.partials.search')

    @include('livewire.evaluacion.execution.table.index')

    <!-- Paginación Bootstrap 4 -->
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
            Mostrando {{ $evaluaciones->firstItem() ?? 0 }} - {{ $evaluaciones->lastItem() ?? 0 }} de {{ $evaluaciones->total() }} registros
        </div>
        <div>
            {{ $evaluaciones->links() }}
        </div>
    </div>

    <!-- Modal de Guía del Usuario -->
    @includeWhen($showGuiaModal,'livewire.evaluacion.execution.modals.guia')
</div>
