<div>

    <div class="p-2 m-2 border rounded shadow-lg">

        <h5 class="alert-danger py-3 px-2 text-dark font-weight-bolder rounded">
            Eliminar <b>mensaje</b>

            <button type="button" class="close" wire:click='closeDeleteMode()'>
                <span aria-hidden="true">×</span>
            </button>
        </h5>

        <div class=" p-2 m-2">

            <fieldset disabled="disabled">
                @include('livewire.academico.mailer.form.fields')                
            </fieldset>    

            <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                <button wire:click="destroy({{$mailer->id}})" class="btn btn-danger btn-sm">Confirmar</button>
            </div>            

        </div>

    </div>

</div>
