<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-warning py-3 px-2 text-dark font-weight-bolder rounded">
            Crear <b>Descripción tabulada.</b>
            <button type="button" class="close" wire:click='close()'>
                <span aria-hidden="true">×</span>
            </button>
        </h5>

        <div class=" p-2 m-2">

            @include('administracion.elements.forms.errors')

            @include('livewire.bienestar.description.form.fields')

            <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                {!! Form::button('Guardar',['class' => 'form-control btn pt-1 mt-1 btn-primary','wire:click'=>"save()"]) !!}
            </div>

        </div>

    </div>

</div>
