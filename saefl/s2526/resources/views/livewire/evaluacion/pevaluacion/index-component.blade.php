<div>

    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
        <div class=" flex-grow-1 font-weight-bold">Listado de Asignaciones</div>
        <div>
            <div class="font-weight-bold small text-muted" wire:loading>Procesando...</div>
            <button type="button" class="btn btn-primary btn-sm" wire:click="setAssign()">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col">
                @include('livewire.evaluacion.pevaluacion.table.index')
            </div>
            @if ($modeAssign)                
                <div class="col">
                    @include('livewire.evaluacion.pevaluacion.partials.assign')
                </div>            
            @endif

            @if ($modeEdit)                
                <div class="col">
                    @include('livewire.evaluacion.pevaluacion.partials.edit')
                </div>            
            @endif
        </div>
    </div>

    

    

</div>
