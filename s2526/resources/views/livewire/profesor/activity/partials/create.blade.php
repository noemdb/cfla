<div class="border rounded m-4 p-2 shadow-lg overlay">

    <div class="d-flex justify-content-between">
        <div>
            <strong>Registro de la Actividad</strong>
        </div>
        <div>
            <span class="h4 text-muted font-weight-bold" wire:click="close()" style="cursor: pointer">&times;</span>
        </div>
    </div>

    {{-- <h5 class="alert alert-secondary mb-0">Registro de la Actividad</h5> --}}

    <div class="px-1 mx-1 pt-2">

        @include('livewire.profesor.activity.form.fields')

        {{-- <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example"> --}}

            <div class="input-group">
                {!! Form::button('Guardar', ['class' => 'form-control btn btn-primary', 'wire:click' => 'save()']) !!}
                {{-- <div class="input-group-append">
                    <button wire:click="close()" class="btn btn-secondary" type="button" id="button-addon2">
                        <i class="{{$icon_menus['close'] ?? ''}}" aria-hidden="true"></i>
                    </button>
                </div> --}}
            </div>

    </div>

</div>