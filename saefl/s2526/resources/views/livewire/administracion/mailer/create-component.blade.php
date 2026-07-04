<div>

    <div wire:target="cuotas" wire:loading.class="d-flex flex-column" class="position-fixed w-100 h-100 bg-dark align-items-center justify-content-center opacity-50" style="top: 0; left: 0; z-index: 1050; display: none;">
        <div class="spinner-border text-success" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <h3 class="text-white font-weight-bold">Calculando...</h3>
    </div>

    <div class="border rounded shadow-lg">

        <h5 class="alert alert-primary font-weight-bolder rounded">
            Registrar nuevo <b>mensaje</b> 
            <button type="button" class="close" wire:click='closeCreateMode()'>
                <span aria-hidden="true">×</span>
            </button>
        </h5>

        <div class=" p-2 m-2">

            {!! Form::open(['wire:submit.prevent' => 'schedule']) !!}

                @include('livewire.administracion.mailer.form.fields')                

                <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                    {!! Form::button('Registrar',['class' => 'form-control btn pt-1 mt-1 btn-primary w-50','wire:click'=>"store()"]) !!}
                    {{-- <button wire:click="alertSweet()" type="button">delete</button> --}}
                </div>

            {!! Form::close() !!}            

        </div>

    </div>

</div>
