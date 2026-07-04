<div class="">
    <div class="overlay border rounded m-4 p-0 shadow-lg ">

        <div class="d-flex justify-content-betwee table-secondary p-1 m-1 rounded">
            <div class="flex-grow-1 h6 p-1">
                <strong>Registrar/Editar Pregunta</strong>
            </div>
            <div>
                <span class="h4 text-muted font-weight-bold" wire:click="close()" style="cursor: pointer">&times;</span>
            </div>
        </div>

        <div class="px-1 mx-1 pt-2">

            @include('livewire.profesor.debate.form.question')

            <div class="input-group p-1 m-1">
                {!! Form::button('Guardar', ['class' => 'form-control btn btn-primary', 'wire:click' => 'saveQuestion()']) !!}
            </div>

        </div>

    </div>
</div>

<div class="overlay-background"></div>