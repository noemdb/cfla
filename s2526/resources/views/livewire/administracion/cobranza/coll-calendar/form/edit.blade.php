<div class="bg-white shadow-lg p-4">

    <h5 class="alert-primary py-3 px-2 text-dark font-weight-bolder rounded">
        Actualizar <b>fecha</b>
        <button type="button" class="close" wire:click='close()'>
            <span aria-hidden="true">×</span>
        </button>
    </h5>

    @include('livewire.administracion.cobranza.coll-calendar.form.fields')

    <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
        {!! Form::button('Guardar',['class' => 'form-control btn pt-1 mt-1 btn-primary','wire:click'=>"update($calendar_id)"]) !!}
    </div> 

</div>
