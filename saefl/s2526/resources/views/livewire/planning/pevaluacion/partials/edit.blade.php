<div class="container-fluid border rounded p-1 m-1">

    {{-- <div class="alert alert-info pb-1 mb-1" role="alert">
        <strong>Edición de la asignación de Carga Académica</strong>
    </div> --}}

    <div class="alert alert-info pb-1 mb-1" role="alert">
        <div class="d-flex justify-content-between">
            <div>
                <strong>Edición de la asignación de Carga Académica</strong>
            </div>
            <div>
                <span class="h4 text-muted font-weight-bold" wire:click="close()" style="cursor: pointer">&times;</span>
            </div>
        </div>
    </div>

    <div class="row py-1">        
                
        <div class="col">

            <div class="d-flex">
                <div class="flex-grow-1">
                    
                    @include('livewire.planning.pevaluacion.form.fileds')

                    <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                        {!! Form::button('Guardar', ['class' => 'form-control btn pt-1 mt-1 btn-info w-75 btn-sm', 'wire:click' => 'save()']) !!}
                        {{-- {!! Form::button('Cerrar', ['class' => 'form-control btn pt-1 mt-1 btn-secondary w-25 btn-sm', 'wire:click' => 'close()']) !!} --}}
                    </div> 

                </div>
            </div>
        </div>        
    </div>

</div>

