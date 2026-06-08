<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-info py-3 px-2 text-dark font-weight-bolder rounded">
            Actualizar <b>mensaje</b>

            <button type="button" class="close" wire:click='closeEditMode()'>
                <span aria-hidden="true">×</span>
            </button>
        </h5>

        <div class=" p-2 m-2">

            {{-- {!! Form::open(['wire:submit.prevent' => 'schedule']) !!} --}}

                {{-- @include('livewire.academico.mailer.form.fields')                 --}}

                <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                    {!! Form::button('Actualizar',['class' => 'form-control btn pt-1 mt-1 btn-info w-50','wire:click'=>"update()"]) !!}
                </div>

            {{-- {!! Form::close() !!} --}}



        </div>

    </div>

</div>
