<div class="">
    <div class="overlay border rounded m-4 p-0 shadow-lg">

        <div class="d-flex justify-content-betwee table-secondary p-1 m-1 rounded">
            <div class="flex-grow-1 h6 p-1">
                <strong>Generaor de debates IA</strong>
                <span class="input-text px-4 font-weight-bold" wire:loading>
                    <strong>Procesando...</strong>
                </span>
            </div>
            <div>
                <span class="h4 text-muted font-weight-bold" wire:click="close()" style="cursor: pointer">&times;</span>
            </div>
        </div>

        <div class="px-1 mx-1 pt-2 bg-white p-2 rounded">

            <div class="p-2 mb-2 border rounded shadow-sm">
                <div class="text-dark font-weight-bold">Descripción teórica de las actividades para este debate</div>
                <div class="text-muted small">{{ ucfirst_accents($context) }}</div>
            </div>

            @include('livewire.profesor.debate.overlay.partials.debate')



            <div class="input-group p-1 m-1">
                {!! Form::button('Generar', [
                    'class' => 'form-control btn btn-primary',
                    'wire:click' => 'generateAiDebate(' . $competition_id . ')',
                ]) !!}
            </div>

        </div>

    </div>
</div>

{{-- <div class="overlay-background"></div> --}}
