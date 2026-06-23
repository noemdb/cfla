<div>
    <!-- Header Móvil Optimizado -->
    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
        <h6 class="mb-0 text-dark fw-bold">Seguimiento de Evaluaciones</h6>
        <button type="button" class="btn btn-info btn-sm" wire:click="toggleGuiaModal">
            <i class="fas fa-question-circle mr-1"></i> 
            <span class="d-none d-sm-inline">{{ $showGuiaModal ? 'Cerrar' : 'Guía' }}</span>
        </button>
    </div>

    @include('livewire.movile.evaluacion.execution.partials.search')
    
    @include('livewire.movile.evaluacion.execution.partials.table')

    <!-- Paginación Bootstrap 5 Optimizada para Móvil -->
    <div class="d-flex justify-content-between align-items-center mt-2 p-2">
        <div class="text-muted small">
            {{ $evaluaciones->firstItem() ?? 0 }}-{{ $evaluaciones->lastItem() ?? 0 }} de {{ $evaluaciones->total() }}
        </div>
        <div>
            {{ $evaluaciones->onEachSide(1)->links() }}
        </div>
    </div>

    <!-- Modal de Guía del Usuario - Optimizado para Móvil -->
    @if($showGuiaModal)
        @include('livewire.movile.evaluacion.execution.modals.guia')
    @endif
</div>