<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-primary py-3 px-2 text-dark font-weight-bolder rounded">
            Registrar nuevo <b>mensaje</b>
            <button type="button" class="close" wire:click='closeCreateMode()'>
                <span aria-hidden="true">×</span>
            </button>
        </h5>

        <div class=" p-2 m-2">

            {!! Form::open(['wire:submit.prevent' => 'schedule']) !!}

                @include('livewire.academico.mailer.form.fields')                

                <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                    {!! Form::button('Registrar',['class' => 'form-control btn pt-1 mt-1 btn-primary w-50','wire:click'=>"store()"]) !!}
                </div>

            {!! Form::close() !!}

            

        </div>

    </div>

</div>
