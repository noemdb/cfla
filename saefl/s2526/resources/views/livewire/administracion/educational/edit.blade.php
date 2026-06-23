<div class="card">  

    <h5 class="alert-warning py-3 px-2 text-dark font-weight-bolder rounded">
        Editar <b>Competencia</b>
        <button type="button" class="close" wire:click='close()'>
            <span aria-hidden="true">×</span>
        </button>
    </h5>

    <div class="card-body p-2">

        @include('livewire.administracion.educational.form.competition')

        <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
            {!! Form::button('Guardar',['class' => 'form-control btn pt-1 mt-1 btn-primary','wire:click'=>"save()"]) !!}
        </div> 

    </div>
</div>