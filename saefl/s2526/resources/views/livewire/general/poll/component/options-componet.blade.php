<div>

    @if (Session::has('operp_ok'))
        <div class="alert alert-success alert-dismissible fade show fw-bold small my-2" role="alert">
            {{Session::get('operp_ok')}}.
        </div>
    @endif

    <div class="p-2">
        <small wire:loading.delay.shortest class="text-muted small px-2">
            Obteniendo opciones...
        </small>
    </div>

    <div wire:loading.class="d-none">

        @if ($modeSelect)
            @if ($poll_question)

                <div class="my-3">Opciones:</div>

                @if ($poll_question->status_grid == 'true')

                    @include('livewire.general.poll.component.partials.modeGrid')

                @else

                    @include('livewire.general.poll.component.partials.modeList')

                @endif

            @endif
        @endif

        @if ($modeSelected)
            <div>
                @include('livewire.general.poll.component.partials.option_selected')
            </div>
        @endif

    </div>

</div>
