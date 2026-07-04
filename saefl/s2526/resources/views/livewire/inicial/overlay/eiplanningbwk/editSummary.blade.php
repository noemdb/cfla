<div class="overlay">

    <div class="alert alert-primary" role="alert">
        <div class="d-flex justify-content-between">
            <div>
                <strong>Editar items de la tabla resumen</strong>
            </div>
            <div>
                <span class="h4 text-muted font-weight-bold" wire:click.prevent="close()" style="cursor: pointer">&times;</span>
            </div>
        </div>
        
    </div>

    <div class="p-2 m-2 border rounded">

        @include('livewire.inicial.forms.eiplanningbwk.summary') 

        {!! Form::button('Guardar', ['class' => 'form-control btn btn-primary px-2', 'wire:click' => 'saveSummary()']) !!}

    </div>

</div>