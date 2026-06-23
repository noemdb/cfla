<div>
    <div class="d-flex justify-content-between mb-2">
        <h5 class="font-weight-bold">Listado de Asignaciones</h5>
        <button type="button" class="btn btn-primary btn-sm" wire:click="setAssign()">
            <i class="fa fa-plus"></i> Nueva Asignación
        </button>
    </div>

    @include('livewire.planning.pevaluacion.partials.filters')

    <div class="container-fluid">
        <div class="row">
            <div class="col">
                @include('livewire.planning.pevaluacion.table.index')
            </div>

            @if ($modeAssign)
                <div class="col-md-4">
                    @include('livewire.planning.pevaluacion.partials.assign')
                </div>
            @endif

            @if ($modeEdit)
                <div class="col-md-4">
                    @include('livewire.planning.pevaluacion.partials.edit')
                </div>
            @endif
        </div>
    </div>
</div>
