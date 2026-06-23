<div class="border rounded shadow p-2 m-2">  

    <h5 class="alert-secondary py-3 px-2 text-dark font-weight-bolder rounded">
        Registrar nueva <b>Opción</b>
        <button type="button" class="close" wire:click='close()'>
            <span aria-hidden="true">×</span>
        </button>
    </h5>

    <div class="">

        @include('livewire.administracion.educational.form.options.fields')

        <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
            {!! Form::button('Guardar',['class' => 'form-control btn pt-1 mt-1 btn-primary','wire:click'=>"save()"]) !!}
        </div> 

    </div>

</div>